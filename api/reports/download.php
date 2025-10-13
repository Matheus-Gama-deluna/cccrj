<?php
// Configurações de cabeçalho HTTP
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

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

// Verifica se o arquivo foi especificado
if (!isset($_GET['file']) || empty($_GET['file'])) {
    sendJsonResponse([
        'success' => false,
        'message' => 'Nome do arquivo não especificado.'
    ], 400);
}

// Carrega as configurações FTP
$configFile = __DIR__ . '/../config/ftp_config.php';
if (!file_exists($configFile)) {
    logError('Arquivo de configuração FTP não encontrado', ['path' => $configFile]);
    sendJsonResponse([
        'success' => false,
        'message' => 'Erro de configuração do servidor.'
    ], 500);
}
require_once $configFile;

// Verifica se as constantes necessárias estão definidas
$requiredConstants = ['FTP_HOST', 'FTP_USERNAME', 'FTP_PASSWORD', 'FTP_PORT', 'FTP_REPORTS_PATH'];
$missingConstants = [];

foreach ($requiredConstants as $constant) {
    if (!defined($constant)) {
        $missingConstants[] = $constant;
    }
}

if (!empty($missingConstants)) {
    logError('Configuração do FTP incompleta', ['missing' => $missingConstants]);
    sendJsonResponse([
        'success' => false,
        'message' => 'Erro de configuração do servidor.'
    ], 500);
}

// Sanitiza o nome do arquivo
$filename = basename($_GET['file']);
$fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
$remoteFile = rtrim(FTP_REPORTS_PATH, '/') . '/' . $filename;

// Valida a extensão do arquivo
if ($fileExtension !== 'pdf') {
    logError('Tentativa de download de arquivo não permitido', [
        'filename' => $filename,
        'extension' => $fileExtension
    ]);
    
    sendJsonResponse([
        'success' => false,
        'message' => 'Somente arquivos PDF são permitidos para download.'
    ], 400);
}

// Configura o diretório temporário
define('TEMP_DIR', __DIR__ . '/../../temp/');

// Garante que o diretório temporário existe e tem permissões corretas
if (!file_exists(TEMP_DIR)) {
    if (!mkdir(TEMP_DIR, 0755, true)) {
        logError('Não foi possível criar o diretório temporário', ['path' => TEMP_DIR]);
        sendJsonResponse([
            'success' => false,
            'message' => 'Erro ao preparar o download.'
        ], 500);
    }
} elseif (!is_writable(TEMP_DIR)) {
    logError('Diretório temporário sem permissão de escrita', ['path' => TEMP_DIR]);
    sendJsonResponse([
        'success' => false,
        'message' => 'Erro de permissão no servidor.'
    ], 500);
}

$localFile = TEMP_DIR . $filename;

try {
    // Inclui a classe FtpService
    $ftpServiceFile = __DIR__ . '/../../ftp_service.php';
    if (!file_exists($ftpServiceFile)) {
        throw new Exception('Arquivo de serviço FTP não encontrado');
    }
    require_once $ftpServiceFile;
    
    // Cria e configura a conexão FTP
    $ftp = new FtpService(
        FTP_HOST,
        FTP_USERNAME,
        FTP_PASSWORD,
        defined('FTP_PORT') ? FTP_PORT : 21
    );
    
    $ftp->connect();
    
    // Verifica se o arquivo existe no servidor FTP
    $fileList = $ftp->listFiles(dirname($remoteFile));
    $fileExists = false;
    
    foreach ($fileList as $file) {
        if (basename($file) === $filename) {
            $fileExists = true;
            break;
        }
    }
    
    if (!$fileExists) {
        throw new Exception('Arquivo não encontrado no servidor.');
    }
    
    // Baixa o arquivo
    if (!$ftp->downloadFile($remoteFile, $localFile)) {
        throw new Exception('Falha ao baixar o arquivo do servidor FTP.');
    }
    
    $ftp->close();
    
    // Verifica se o arquivo foi baixado corretamente
    if (!file_exists($localFile) || filesize($localFile) === 0) {
        throw new Exception('Arquivo baixado está vazio ou não pôde ser salvo.');
    }
    
    // Define os cabeçalhos para download
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($localFile));
    header('Cache-Control: no-cache, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Envia o arquivo e remove o arquivo temporário
    if (readfile($localFile) === false) {
        throw new Exception('Erro ao enviar o arquivo para o cliente.');
    }
    
    // Remove o arquivo temporário após o envio
    register_shutdown_function(function() use ($localFile) {
        if (file_exists($localFile)) {
            @unlink($localFile);
        }
    });
    
    exit();
    
} catch (Exception $e) {
    // Limpa qualquer saída que possa ter sido gerada
    if (ob_get_level() > 0) {
        ob_clean();
    }
    
    // Remove o arquivo temporário em caso de erro
    if (isset($localFile) && file_exists($localFile)) {
        @unlink($localFile);
    }
    
    // Fecha a conexão FTP se ainda estiver aberta
    if (isset($ftp)) {
        try {
            $ftp->close();
        } catch (Exception $e) {
            // Ignora erros ao fechar a conexão
        }
    }
    
    // Log do erro
    logError('Erro ao processar download', [
        'file' => $filename,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    
    // Retorna a resposta de erro
    sendJsonResponse([
        'success' => false,
        'message' => 'Não foi possível concluir o download do arquivo.',
        'error' => $e->getMessage()
    ], 500);
}
?>