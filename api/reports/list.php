<?php
/**
 * API para listar relatórios disponíveis no servidor FTP
 */

// Verifica se está em ambiente de desenvolvimento local
$isLocal = (isset($_SERVER['HTTP_HOST']) && 
           (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
            strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false));

// Configurações de exibição de erros
if ($isLocal) {
    // Em desenvolvimento local, mostra todos os erros
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    // Em produção, desativa a exibição de erros
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

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

// Função para obter o diretório de logs seguro
function getLogDir() {
    // Tenta o diretório temporário primeiro, que é garantido em open_basedir
    $tempDir = sys_get_temp_dir();
    if (is_writable($tempDir)) {
        return $tempDir;
    }
    
    // Se não conseguir, tenta o diretório de uploads
    $uploadDir = ini_get('upload_tmp_dir') ?: sys_get_temp_dir();
    if (is_writable($uploadDir)) {
        return $uploadDir;
    }
    
    // Se tudo mais falhar, tenta o diretório atual
    return __DIR__;
}

// Função para registrar erros
function logError($message, $context = []) {
    // Desativa a exibição de erros para evitar problemas com headers
    $displayErrors = ini_get('display_errors');
    ini_set('display_errors', 0);
    
    // Obtém o diretório de logs seguro
    $logDir = getLogDir();
    $logFile = $logDir . '/cccrj_api_errors.log';
    
    // Prepara a mensagem de log
    $logMessage = date('[Y-m-d H:i:s] ') . $message . "\n";
    if (!empty($context)) {
        $logMessage .= 'Context: ' . json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
    }
    
    // Adiciona a stack trace se disponível
    $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
    $logMessage .= 'Called from: ' . ($backtrace[0]['file'] ?? 'unknown') . ' on line ' . ($backtrace[0]['line'] ?? '0') . "\n";
    
    // Tenta gravar no arquivo de log de forma segura
    @file_put_contents($logFile, $logMessage, FILE_APPEND);
    
    // Restaura a configuração de exibição de erros
    ini_set('display_errors', $displayErrors);
    
    // Log no console para ambiente de desenvolvimento
    if (isset($_SERVER['HTTP_HOST']) && 
        (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
         strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false)) {
        error_log($logMessage);
    }
}

// Desativa a exibição de erros em produção
if (!(isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false)) {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

// Verifica se a extensão FTP está habilitada
if (!extension_loaded('ftp')) {
    $errorMsg = 'A extensão FTP não está habilitada no PHP. Por favor, ative a extensão no php.ini';
    $errorData = [
        'php_ini_loaded' => php_ini_loaded_file(),
        'extensions_dir' => ini_get('extension_dir'),
        'php_version' => phpversion(),
        'os' => PHP_OS,
        'loaded_extensions' => get_loaded_extensions()
    ];
    
    // Usa a função logError para registrar o erro
    logError($errorMsg, $errorData);
    
    // Retorna a resposta de erro como JSON
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro de configuração do servidor.',
        'error' => 'A extensão FTP não está habilitada no servidor.',
        'details' => $isLocal ? $errorData : null
    ], JSON_PRETTY_PRINT);
    exit();
}

// Verifica se as constantes necessárias estão definidas
$requiredConstants = ['FTP_HOST', 'FTP_USERNAME', 'FTP_PASSWORD', 'FTP_REPORTS_PATH'];
$missingConstants = [];

// Tenta carregar o arquivo de configuração
$configFile = __DIR__ . '/../config/ftp_config.php';
if (!file_exists($configFile)) {
    $errorMsg = 'Arquivo de configuração FTP não encontrado: ' . $configFile;
    logError($errorMsg, ['current_dir' => __DIR__]);
    sendJsonResponse([
        'success' => false,
        'message' => 'Erro de configuração do servidor.',
        'error' => $errorMsg,
        'config_file' => $configFile
    ], 500);
}

// Inclui o arquivo de configuração
require_once $configFile;

// Verifica as constantes após incluir o arquivo de configuração
foreach ($requiredConstants as $constant) {
    if (!defined($constant)) {
        $missingConstants[] = $constant;
    }
}

if (!empty($missingConstants)) {
    $errorMessage = 'Configuração do FTP incompleta. Constantes ausentes: ' . implode(', ', $missingConstants);
    logError($errorMessage, [
        'file' => __FILE__,
        'config_file' => $configFile,
        'defined_constants' => get_defined_constants(true)['user'] ?? []
    ]);
    
    sendJsonResponse([
        'success' => false,
        'message' => 'Erro de configuração do servidor.',
        'error' => $errorMessage,
        'missing_constants' => $missingConstants
    ], 500);
}

// Usa a função getLogDir() para obter um diretório de logs gravável
$logDir = getLogDir();
$logFile = $logDir . '/cccrj_api_errors.log';

// Inclui a classe FtpService
$ftpServiceFile = __DIR__ . '/../ftp_service.php';
if (!file_exists($ftpServiceFile)) {
    logError('Arquivo ftp_service.php não encontrado', ['path' => $ftpServiceFile]);
    sendJsonResponse([
        'success' => false,
        'message' => 'Erro interno do servidor. Arquivo de serviço FTP não encontrado.'
    ], 500);
}

require_once $ftpServiceFile;

// Verifica se todas as constantes necessárias estão definidas
$requiredConstants = [
    'FTP_HOST', 
    'FTP_USERNAME', 
    'FTP_PASSWORD',
    'FTP_PORT',
    'FTP_REPORTS_PATH'
];

$missingConstants = [];
foreach ($requiredConstants as $constant) {
    if (!defined($constant)) {
        $missingConstants[] = $constant;
    }
}

if (!empty($missingConstants)) {
    logError('Constantes FTP ausentes', ['missing' => $missingConstants]);
    sendJsonResponse([
        'success' => false,
        'message' => 'Erro de configuração do servidor. Contate o administrador.'
    ], 500);
}

// Tenta listar os relatórios
try {
    logError('Iniciando listagem de relatórios', [
        'host' => FTP_HOST,
        'path' => FTP_REPORTS_PATH,
        'php_version' => phpversion()
    ]);

    // Cria uma instância do serviço FTP
    $ftp = new FtpService(
        FTP_HOST,
        FTP_USERNAME,
        FTP_PASSWORD,
        defined('FTP_PORT') ? FTP_PORT : 21
    );
    
    logError('Instância FTP criada, tentando conectar...');
    
    // Conecta ao servidor FTP
    $ftp->connect();
    logError('Conexão FTP estabelecida com sucesso');
    
    // Obtém a lista de arquivos
    logError('Buscando arquivos no diretório: ' . FTP_REPORTS_PATH);
    $files = $ftp->listFiles(FTP_REPORTS_PATH);
    logError('Arquivos encontrados: ' . count($files), ['files' => $files]);
    
    // Processa os arquivos encontrados
    $reports = [];
    $processedFiles = 0;
    
    foreach ($files as $file) {
        try {
            logError('Processando arquivo: ' . $file);
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if ($extension === 'pdf') {
                $fileInfo = [
                    'name' => basename($file),
                    'path' => $file,
                    'size' => $ftp->getFileSize($file),
                    'modified' => $ftp->getFileModifiedTime($file),
                    'url' => '/api/reports/download?file=' . urlencode($file)
                ];
                $reports[] = $fileInfo;
                $processedFiles++;
                logError('Arquivo adicionado: ' . json_encode($fileInfo));
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
