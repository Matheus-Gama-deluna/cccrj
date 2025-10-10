<?php
/**
 * API para listar relatórios disponíveis no servidor FTP
 */

// Configurações de cabeçalho HTTP
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Cache-Control: no-cache, must-revalidate');

// Responde imediatamente para requisições OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Função para enviar resposta JSON padronizada
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit();
}

// Função para registrar erros
function logError($message, $context = []) {
    $logMessage = date('[Y-m-d H:i:s] ') . $message . "\n";
    if (!empty($context)) {
        $logMessage .= 'Context: ' . json_encode($context, JSON_PRETTY_PRINT) . "\n";
    }
    error_log($logMessage, 3, __DIR__ . '/../../../logs/api_errors.log');
}

// Verifica se as constantes necessárias estão definidas
$requiredConstants = ['FTP_HOST', 'FTP_USERNAME', 'FTP_PASSWORD', 'FTP_REPORTS_PATH'];
$missingConstants = [];

foreach ($requiredConstants as $constant) {
    if (!defined($constant)) {
        $missingConstants[] = $constant;
    }
}

if (!empty($missingConstants)) {
    // Tenta carregar o arquivo de configuração se não estiver carregado
    $configFile = __DIR__ . '/../config/ftp_config.php';
    if (file_exists($configFile)) {
        require_once $configFile;
        
        // Verifica novamente as constantes
        $missingConstants = [];
        foreach ($requiredConstants as $constant) {
            if (!defined($constant)) {
                $missingConstants[] = $constant;
            }
        }
    }
    
    if (!empty($missingConstants)) {
        $errorMessage = 'Configuração do FTP incompleta. Constantes ausentes: ' . implode(', ', $missingConstants);
        logError($errorMessage, ['file' => __FILE__]);
        sendJsonResponse([
            'success' => false,
            'message' => 'Erro de configuração do servidor.',
            'error' => $errorMessage
        ], 500);
    }
}

// Inclui a classe FtpService
$ftpServiceFile = __DIR__ . '/../ftp_service.php';
if (!file_exists($ftpServiceFile)) {
    logError('Arquivo ftp_service.php não encontrado', ['path' => $ftpServiceFile]);
    sendJsonResponse([
        'success' => false,
        'message' => 'Erro interno do servidor.'
    ], 500);
}

require_once $ftpServiceFile;

// Tenta listar os relatórios
try {
    // Cria uma instância do serviço FTP
    $ftp = new FtpService(
        FTP_HOST,
        FTP_USERNAME,
        FTP_PASSWORD,
        defined('FTP_PORT') ? FTP_PORT : 21
    );
    
    // Conecta ao servidor FTP
    $ftp->connect();
    
    // Obtém a lista de arquivos
    $files = $ftp->listFiles(FTP_REPORTS_PATH);
    
    // Processa os arquivos encontrados
    $reports = [];
    $processedFiles = 0;
    
    foreach ($files as $file) {
        try {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if ($extension === 'pdf') {
                $reports[] = [
                    'name' => basename($file),
                    'path' => $file,
                    'size' => $ftp->getFileSize($file),
                    'modified' => $ftp->getFileModifiedTime($file),
                    'url' => '/api/reports/download?file=' . urlencode($file)
                ];
                $processedFiles++;
            }
        } catch (Exception $e) {
            // Registra o erro, mas continua processando outros arquivos
            logError("Erro ao processar arquivo: " . $e->getMessage(), [
                'file' => $file,
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    // Fecha a conexão FTP
    $ftp->close();
    
    // Ordena os relatórios por data de modificação (mais recentes primeiro)
    usort($reports, function($a, $b) {
        return strtotime($b['modified']) - strtotime($a['modified']);
    });
    
    // Retorna a resposta de sucesso
    sendJsonResponse([
        'success' => true,
        'data' => $reports,
        'meta' => [
            'total' => count($reports),
            'processed' => $processedFiles,
            'timestamp' => date('c')
        ]
    ]);
    
} catch (Exception $e) {
    // Registra o erro
    logError("Erro ao listar relatórios: " . $e->getMessage(), [
        'trace' => $e->getTraceAsString(),
        'ftp_host' => FTP_HOST,
        'ftp_path' => FTP_REPORTS_PATH
    ]);
    
    // Retorna uma resposta de erro amigável
    sendJsonResponse([
        'success' => false,
        'message' => 'Não foi possível listar os relatórios no momento.',
        'error' => $e->getMessage(),
        'code' => $e->getCode()
    ], 500);
}
?>