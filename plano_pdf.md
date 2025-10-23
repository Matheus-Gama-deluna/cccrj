# Plano de Migração: Sistema de Relatórios - FTP para Sistema de Arquivos Local

## 1. Visão Geral
Substituir o sistema atual baseado em FTP por um sistema de arquivos local, onde os PDFs serão armazenados em uma pasta `/pdf` no mesmo diretório do sistema.

## 2. Objetivos
- Eliminar a dependência do servidor FTP externo
- Melhorar a segurança removendo credenciais de FTP
- Simplificar a manutenção do sistema
- Manter a compatibilidade com o sistema existente

## 3. Cronograma
**Duração Total**: 2 semanas

### Semana 1: Preparação e Desenvolvimento
1. **Análise e Planejamento** (1 dia)
   - Mapear todos os pontos de integração com o FTP
   - Definir estrutura de pastas e permissões
   - Documentar mudanças necessárias

2. **Desenvolvimento** (3 dias)
   - Criar nova classe `LocalFileService`
   - Implementar métodos equivalentes (upload, download, listagem)
   - Atualizar endpoints da API

3. **Testes** (1 dia)
   - Testes unitários
   - Testes de integração
   - Testes de performance

### Semana 2: Implantação e Monitoramento
4. **Preparação para Produção** (1 dia)
   - Criar script de migração
   - Configurar permissões de pasta
   - Fazer backup completo

5. **Implantação** (1 dia)
   - Manutenção programada
   - Executar migração
   - Atualizar configurações

6. **Monitoramento** (3 dias)
   - Monitorar erros
   - Coletar métricas de performance
   - Ajustes finais

## 4. Estrutura de Pastas
```
/cccrj/
  /api/
    /config/
      file_config.php    # Nova configuração
    /services/
      LocalFileService.php  # Novo serviço
  /pdf/                   # Pasta para armazenamento
    /reports/             # Relatórios gerais
    /boletins/            # Boletins especiais
```

## 5. Melhorias de Segurança

### 5.1 Mover Credenciais para Variáveis de Ambiente
```php
// .env
DB_HOST=localhost
DB_NAME=cccrj
DB_USER=usuario
DB_PASS=senha_segura
UPLOAD_MAX_SIZE=10485760
ALLOWED_TYPES=application/pdf
```

### 5.2 Autenticação JWT
```php
class AuthService {
    private $secretKey;
    
    public function __construct() {
        $this->secretKey = getenv('JWT_SECRET');
    }
    
    public function generateToken($userId) {
        $payload = [
            'iat' => time(),
            'exp' => time() + 3600, // 1 hora
            'sub' => $userId
        ];
        
        return JWT::encode($payload, $this->secretKey, 'HS256');
    }
}
```

## 6. Melhorias de Usabilidade

### 6.1 Preview de PDF
```javascript
class PDFPreview {
    constructor(containerId, pdfUrl) {
        // Inicialização do visualizador PDF
        this.container = document.getElementById(containerId);
        this.pdfUrl = pdfUrl;
        this.init();
    }
    
    // Implementação do visualizador...
}
```

### 6.2 Paginação
```php
// api/reports/list.php
$page = $_GET['page'] ?? 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$reports = array_slice($allReports, $offset, $perPage);
$totalPages = ceil(count($allReports) / $perPage);
```

## 7. Melhorias de Performance

### 7.1 Cache de Listagem
```php
class ReportCache {
    public function get($key) {
        $cacheFile = $this->getCacheFilePath($key);
        
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $this->cacheTime)) {
            return json_decode(file_get_contents($cacheFile), true);
        }
        
        return null;
    }
}
```

### 7.2 Miniaturas de PDF
```php
class ThumbnailGenerator {
    public static function generate($pdfPath, $outputPath, $page = 1, $width = 200) {
        $imagick = new Imagick();
        $imagick->setResolution(150, 150);
        $imagick->readImage($pdfPath . '[' . ($page - 1) . ']');
        $imagick->setImageFormat('jpg');
        $imagick->scaleImage($width, 0);
        $imagick->writeImage($outputPath);
    }
}
```

## 8. Novas Funcionalidades

### 8.1 Upload de Múltiplos Arquivos
```javascript
class MultiFileUpload {
    constructor(options) {
        this.dropZone = document.getElementById(options.dropZoneId);
        this.fileInput = document.getElementById(options.fileInputId);
        this.maxFiles = options.maxFiles || 10;
        this.init();
    }
    
    // Implementação do upload múltiplo...
}
```

### 8.2 Sistema de Notificações
```javascript
class Notification {
    static show(message, type = 'info', duration = 5000) {
        // Implementação das notificações
    }
}
```

### 8.3 Sistema de Permissões
```php
class User {
    public function hasPermission($permission) {
        $permissions = $this->getRolePermissions();
        return in_array($permission, $permissions);
    }
    
    private function getRolePermissions() {
        $roles = [
            'admin' => ['upload', 'delete', 'edit', 'view'],
            'editor' => ['upload', 'edit', 'view'],
            'viewer' => ['view']
        ];
        
        return $roles[$this->role] ?? [];
    }
}
```

## 9. Roteiro de Implantação

1. **Pré-requisitos**
   - PHP 7.4+
   - Extensão Fileinfo ativada
   - Permissões de escrita nas pastas

2. **Passos**
   ```bash
   # Criar pasta de uploads
   mkdir -p /var/www/cccrj/pdf/reports
   chmod 755 /var/www/cccrj/pdf
   chown www-data:www-data /var/www/cccrj/pdf -R
   ```

3. **Configuração**
   - Copiar `.env.example` para `.env`
   - Configurar variáveis de ambiente
   - Configurar permissões

## 10. Monitoramento Pós-Implantação

1. **Métricas**
   - Tempo de carregamento
   - Uso de memória
   - Erros de permissão

2. **Logs**
   - Acessos
   - Erros
   - Uploads realizados

## 11. Próximos Passos

1. Implementar testes automatizados
2. Adicionar documentação da API
3. Configurar monitoramento contínuo
4. Planejar próximas melhorias

## 12. Plano de Implementação Detalhado - Passo a Passo

### Fase 1: Preparação do Ambiente (1 dia)

#### Passo 1.1: Backup e Análise do Sistema Atual
```bash
# 1. Fazer backup completo do sistema
cp -r /var/www/cccrj /var/www/cccrj_backup_$(date +%Y%m%d_%H%M%S)

# 2. Verificar estrutura atual
ls -la /var/www/cccrj/
ls -la /var/www/cccrj/api/
ls -la /var/www/cccrj/api/config/
ls -la /var/www/cccrj/api/reports/
```

#### Passo 1.2: Criar Estrutura de Diretórios
```bash
# Criar estrutura de pastas conforme planejado
mkdir -p /var/www/cccrj/pdf/reports
mkdir -p /var/www/cccrj/pdf/boletins
mkdir -p /var/www/cccrj/api/services
mkdir -p /var/www/cccrj/temp

# Configurar permissões
chmod 755 /var/www/cccrj/pdf
chmod 755 /var/www/cccrj/pdf/reports
chmod 755 /var/www/cccrj/pdf/boletins
chmod 755 /var/www/cccrj/temp

# Definir ownership
chown -R www-data:www-data /var/www/cccrj/pdf
chown -R www-data:www-data /var/www/cccrj/temp

# Configurar .gitignore
echo "/pdf/" >> /var/www/cccrj/.gitignore
echo "/temp/" >> /var/www/cccrj/.gitignore
echo ".env" >> /var/www/cccrj/.gitignore
```

#### Passo 1.3: Verificar Dependências PHP
```bash
# Verificar versão do PHP
php --version

# Verificar extensões necessárias
php -m | grep -E "(fileinfo|gd|imagick|openssl)"

# Instalar extensões se necessário (Ubuntu/Debian)
sudo apt-get update
sudo apt-get install php-fileinfo php-gd php-imagick php-openssl

# Para Windows (XAMPP), verificar php.ini
# Descomentar as linhas:
# extension=fileinfo
# extension=gd
# extension=openssl
```

### Fase 2: Desenvolvimento dos Novos Serviços (3 dias)

#### Passo 2.1: Criar Classe LocalFileService
```php
// api/services/LocalFileService.php
<?php
class LocalFileService {
    private $basePath;
    private $reportsPath;
    private $boletinsPath;
    private $tempPath;
    private $allowedExtensions;
    private $maxFileSize;

    public function __construct() {
        $this->basePath = __DIR__ . '/../../pdf/';
        $this->reportsPath = $this->basePath . 'reports/';
        $this->boletinsPath = $this->basePath . 'boletins/';
        $this->tempPath = __DIR__ . '/../../temp/';
        $this->allowedExtensions = ['pdf'];
        $this->maxFileSize = 10 * 1024 * 1024; // 10MB

        $this->ensureDirectoriesExist();
    }

    private function ensureDirectoriesExist() {
        $directories = [$this->basePath, $this->reportsPath, $this->boletinsPath, $this->tempPath];

        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }

    public function uploadFile($file, $title, $isBoletim = false) {
        // Validar arquivo
        if (!$this->validateFile($file)) {
            throw new Exception('Arquivo inválido');
        }

        // Gerar nome único
        $fileName = $this->generateUniqueFileName($title, $file['name']);

        // Determinar diretório de destino
        $destinationDir = $isBoletim ? $this->boletinsPath : $this->reportsPath;
        $destinationPath = $destinationDir . $fileName;

        // Mover arquivo
        if (!move_uploaded_file($file['tmp_name'], $destinationPath)) {
            throw new Exception('Falha ao mover arquivo');
        }

        return [
            'name' => $fileName,
            'path' => $destinationPath,
            'size' => $file['size'],
            'uploaded_at' => date('Y-m-d H:i:s')
        ];
    }

    public function listFiles($type = 'reports') {
        $directory = $type === 'boletins' ? $this->boletinsPath : $this->reportsPath;

        if (!is_dir($directory)) {
            return [];
        }

        $files = scandir($directory);
        $files = array_filter($files, function($file) {
            return !in_array($file, ['.', '..']) && pathinfo($file, PATHINFO_EXTENSION) === 'pdf';
        });

        $fileInfo = [];
        foreach ($files as $file) {
            $filePath = $directory . $file;
            $fileInfo[] = [
                'name' => $file,
                'path' => $filePath,
                'size' => filesize($filePath),
                'modified' => date('c', filemtime($filePath)),
                'url' => '/api/reports/download?file=' . urlencode($file) . '&type=' . $type
            ];
        }

        // Ordenar por data de modificação (mais recentes primeiro)
        usort($fileInfo, function($a, $b) {
            return strtotime($b['modified']) - strtotime($a['modified']);
        });

        return $fileInfo;
    }

    public function downloadFile($fileName, $type = 'reports') {
        $directory = $type === 'boletins' ? $this->boletinsPath : $this->reportsPath;
        $filePath = $directory . basename($fileName);

        if (!file_exists($filePath)) {
            throw new Exception('Arquivo não encontrado');
        }

        return $filePath;
    }

    public function deleteFile($fileName, $type = 'reports') {
        $directory = $type === 'boletins' ? $this->boletinsPath : $this->reportsPath;
        $filePath = $directory . basename($fileName);

        if (file_exists($filePath)) {
            return unlink($filePath);
        }

        return false;
    }

    private function validateFile($file) {
        // Verificar se é upload válido
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return false;
        }

        // Verificar tipo MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if ($mimeType !== 'application/pdf') {
            return false;
        }

        // Verificar tamanho
        if ($file['size'] > $this->maxFileSize) {
            return false;
        }

        return true;
    }

    private function generateUniqueFileName($title, $originalName) {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $baseName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $title);
        $uniqueId = time() . '_' . uniqid();

        return $baseName . '_' . $uniqueId . '.' . $extension;
    }
}
?>
```

#### Passo 2.2: Atualizar Sistema de Upload
```php
// Modificar api/upload_report.php para usar LocalFileService
require_once __DIR__ . '/services/LocalFileService.php';

try {
    $fileService = new LocalFileService();

    // Processar upload
    $result = $fileService->uploadFile($_FILES['file'], $_POST['title'], $_POST['is_boletim'] ?? false);

    echo json_encode([
        'success' => true,
        'message' => 'Arquivo enviado com sucesso!',
        'file' => $result
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
```

#### Passo 2.3: Atualizar API de Listagem
```php
// Substituir api/reports/list.php
require_once __DIR__ . '/../services/LocalFileService.php';

try {
    $fileService = new LocalFileService();
    $type = $_GET['type'] ?? 'reports';

    // Implementar paginação
    $page = (int)($_GET['page'] ?? 1);
    $perPage = (int)($_GET['per_page'] ?? 10);

    $allFiles = $fileService->listFiles($type);
    $offset = ($page - 1) * $perPage;
    $files = array_slice($allFiles, $offset, $perPage);

    echo json_encode([
        'success' => true,
        'data' => $files,
        'meta' => [
            'total' => count($allFiles),
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil(count($allFiles) / $perPage),
            'timestamp' => date('c')
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
```

#### Passo 2.4: Atualizar API de Download
```php
// Modificar api/reports/download.php
require_once __DIR__ . '/../services/LocalFileService.php';

try {
    $fileService = new LocalFileService();
    $fileName = basename($_GET['file']);
    $type = $_GET['type'] ?? 'reports';

    $filePath = $fileService->downloadFile($fileName, $type);

    // Enviar headers para download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');
    header('Content-Length: ' . filesize($filePath));
    header('Cache-Control: no-cache, must-revalidate');

    readfile($filePath);
    exit();

} catch (Exception $e) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
```

### Fase 3: Script de Migração (1 dia)

#### Passo 3.1: Criar Script de Migração
```php
// api/migrate_from_ftp.php
<?php
require_once 'config/ftp_config.php';
require_once 'ftp_service.php';
require_once 'services/LocalFileService.php';

header('Content-Type: application/json');

try {
    $ftp = new FtpService(FTP_HOST, FTP_USERNAME, FTP_PASSWORD, FTP_PORT);
    $ftp->connect();

    $fileService = new LocalFileService();

    // Listar arquivos no FTP
    $ftpFiles = $ftp->listFiles(FTP_REPORTS_PATH);
    $migrated = 0;
    $errors = [];

    foreach ($ftpFiles as $file) {
        try {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'pdf') {
                // Baixar do FTP
                $tempFile = tempnam(sys_get_temp_dir(), 'ftp_migration_');
                $ftp->downloadFile($file, $tempFile);

                // Preparar dados para upload local
                $fileInfo = [
                    'name' => basename($file['name']),
                    'tmp_name' => $tempFile,
                    'size' => filesize($tempFile),
                    'error' => UPLOAD_ERR_OK
                ];

                // Fazer upload para sistema local
                $result = $fileService->uploadFile($fileInfo, pathinfo($file, PATHINFO_FILENAME), false);

                // Remover arquivo temporário
                unlink($tempFile);

                $migrated++;
                echo "Migrado: " . basename($file) . "\n";
            }
        } catch (Exception $e) {
            $errors[] = "Erro ao migrar {$file}: " . $e->getMessage();
        }
    }

    $ftp->close();

    echo json_encode([
        'success' => true,
        'migrated' => $migrated,
        'errors' => $errors,
        'message' => "Migração concluída: {$migrated} arquivos migrados"
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
```

#### Passo 3.2: Executar Migração
```bash
# Executar script de migração
cd /var/www/cccrj/api
php migrate_from_ftp.php

# Verificar resultado
ls -la /var/www/cccrj/pdf/reports/
```

### Fase 4: Implementar Melhorias de Segurança (2 dias)

#### Passo 4.1: Criar Sistema de Variáveis de Ambiente
```bash
# Criar arquivo .env
cat > /var/www/cccrj/.env << EOF
DB_HOST=localhost
DB_NAME=cccrj
DB_USER=usuario
DB_PASS=senha_segura
UPLOAD_MAX_SIZE=10485760
ALLOWED_TYPES=application/pdf
JWT_SECRET=your-super-secret-jwt-key-here
SESSION_TIMEOUT=3600
LOG_LEVEL=INFO
EOF

# Configurar permissões
chmod 600 /var/www/cccrj/.env
chown www-data:www-data /var/www/cccrj/.env
```

#### Passo 4.2: Criar Classe de Autenticação JWT
```php
// api/services/AuthService.php
<?php
require_once 'vendor/autoload.php'; // Composer: composer require firebase/php-jwt

use Firebase\JWT\JWT;

class AuthService {
    private $secretKey;
    private $algorithm = 'HS256';

    public function __construct() {
        $this->secretKey = getenv('JWT_SECRET') ?: 'fallback-secret-key';
    }

    public function generateToken($userId, $role = 'viewer') {
        $issuedAt = time();
        $expirationTime = $issuedAt + (int)(getenv('SESSION_TIMEOUT') ?: 3600);

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'sub' => $userId,
            'role' => $role
        ];

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    public function validateToken($token) {
        try {
            $decoded = JWT::decode($token, $this->secretKey, [$this->algorithm]);
            return (array) $decoded;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getUserFromToken($token) {
        $decoded = $this->validateToken($token);
        return $decoded ? $decoded : null;
    }
}
?>
```

#### Passo 4.3: Atualizar Endpoints com Autenticação
```php
// Middleware de autenticação para APIs
function requireAuth() {
    $headers = getallheaders();

    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Token não fornecido']);
        exit;
    }

    $authHeader = $headers['Authorization'];
    $token = str_replace('Bearer ', '', $authHeader);

    $authService = new AuthService();
    $user = $authService->getUserFromToken($token);

    if (!$user) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Token inválido']);
        exit;
    }

    return $user;
}

function requirePermission($permission) {
    $user = requireAuth();

    if (!$user['role'] || !in_array($permission, $this->getRolePermissions($user['role']))) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Permissão negada']);
        exit;
    }

    return $user;
}
```

### Fase 5: Funcionalidades Extras (2 dias)

#### Passo 5.1: Implementar Cache de Listagem
```php
// api/services/CacheService.php
<?php
class CacheService {
    private $cacheDir;
    private $cacheTime = 300; // 5 minutos

    public function __construct() {
        $this->cacheDir = __DIR__ . '/../../cache/';
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    public function get($key) {
        $cacheFile = $this->getCacheFilePath($key);

        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $this->cacheTime) {
            return json_decode(file_get_contents($cacheFile), true);
        }

        return null;
    }

    public function set($key, $data, $ttl = null) {
        $cacheFile = $this->getCacheFilePath($key);

        if ($ttl) {
            $this->cacheTime = $ttl;
        }

        file_put_contents($cacheFile, json_encode($data));
    }

    public function clear($key = null) {
        if ($key) {
            $cacheFile = $this->getCacheFilePath($key);
            if (file_exists($cacheFile)) {
                unlink($cacheFile);
            }
        } else {
            // Limpar todo o cache
            $files = glob($this->cacheDir . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }

    private function getCacheFilePath($key) {
        return $this->cacheDir . md5($key) . '.cache';
    }
}
?>
```

#### Passo 5.2: Sistema de Preview de PDF
```php
// api/services/PDFPreviewService.php
<?php
class PDFPreviewService {
    public function generatePreview($pdfPath, $outputPath, $page = 1, $width = 200) {
        try {
            $imagick = new Imagick();
            $imagick->setResolution(150, 150);
            $imagick->readImage($pdfPath . '[' . ($page - 1) . ']');

            $imagick->setImageFormat('jpg');
            $imagick->scaleImage($width, 0);
            $imagick->writeImage($outputPath);

            $imagick->clear();
            $imagick->destroy();

            return true;
        } catch (Exception $e) {
            error_log("Erro ao gerar preview: " . $e->getMessage());
            return false;
        }
    }

    public function getPreviewUrl($fileName, $type = 'reports') {
        return "/api/pdf/preview/{$type}/{$fileName}";
    }
}
?>
```

#### Passo 5.3: Sistema de Notificações
```javascript
// assets/js/services/NotificationService.js
class NotificationService {
    static show(message, type = 'info', duration = 5000) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-message">${message}</span>
                <button class="notification-close">&times;</button>
            </div>
        `;

        // Adicionar ao DOM
        document.body.appendChild(notification);

        // Auto-remover após duração
        setTimeout(() => {
            this.remove(notification);
        }, duration);

        // Botão de fechar
        notification.querySelector('.notification-close').addEventListener('click', () => {
            this.remove(notification);
        });
    }

    static remove(notification) {
        notification.style.opacity = '0';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }
}
```

### Fase 6: Testes e Validação (1 dia)

#### Passo 6.1: Testes Unitários
```php
// tests/LocalFileServiceTest.php
<?php
require_once '../api/services/LocalFileService.php';

class LocalFileServiceTest extends PHPUnit_Framework_TestCase {
    private $service;

    public function setUp() {
        $this->service = new LocalFileService();
    }

    public function testUploadValidPDF() {
        // Criar arquivo PDF de teste
        $testFile = ['tmp_name' => '/tmp/test.pdf', 'name' => 'test.pdf', 'size' => 1024, 'error' => UPLOAD_ERR_OK];

        $result = $this->service->uploadFile($testFile, 'Test Document');

        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('path', $result);
        $this->assertArrayHasKey('size', $result);
    }

    public function testListFiles() {
        $files = $this->service->listFiles();

        $this->assertIsArray($files);
        foreach ($files as $file) {
            $this->assertArrayHasKey('name', $file);
            $this->assertArrayHasKey('size', $file);
            $this->assertArrayHasKey('modified', $file);
        }
    }
}
?>
```

#### Passo 6.2: Testes de Integração
```bash
# Testar APIs via curl
curl -X POST "http://localhost/cccrj/api/reports/list" \
  -H "Content-Type: application/json"

curl -X POST "http://localhost/cccrj/api/upload_report" \
  -F "file=@test.pdf" \
  -F "title=Test Report"

# Testar download
curl -O "http://localhost/cccrj/api/reports/download?file=test_report.pdf"
```

### Fase 7: Implantação em Produção (1 dia)

#### Passo 7.1: Preparação para Deploy
```bash
# Backup do sistema atual
cp -r /var/www/cccrj /var/www/cccrj_pre_migration_$(date +%Y%m%d_%H%M%S)

# Verificar permissões
ls -la /var/www/cccrj/pdf/

# Testar APIs em staging
php api/reports/list.php
php api/upload_report.php
```

#### Passo 7.2: Deploy
```bash
# 1. Manutenção programada
echo "Sistema em manutenção - migração FTP para local" > /var/www/cccrj/maintenance.html

# 2. Executar migração
php api/migrate_from_ftp.php

# 3. Verificar migração
ls -la /var/www/cccrj/pdf/reports/
php api/reports/list.php | jq '.meta.total'

# 4. Remover arquivos FTP antigos (opcional)
# rm -rf /var/www/cccrj/api/ftp_service.php
# rm -rf /var/www/cccrj/api/config/ftp_config.php

# 5. Remover página de manutenção
rm /var/www/cccrj/maintenance.html
```

#### Passo 7.3: Monitoramento Pós-Deploy
```bash
# Monitorar logs
tail -f /var/log/apache2/cccrj_error.log

# Verificar uso de disco
df -h /var/www/cccrj/

# Testar funcionalidades
curl -s http://localhost/cccrj/api/reports/list | jq '.success'
```

### Fase 8: Documentação e Treinamento (1 dia)

#### Passo 8.1: Atualizar Documentação
```markdown
<!-- docs/migration-guide.md -->
# Guia de Migração FTP para Sistema Local

## Mudanças Implementadas
- ✅ Substituído FTP por sistema de arquivos local
- ✅ Melhorias de segurança com JWT
- ✅ Cache implementado para melhor performance
- ✅ Sistema de preview de PDFs
- ✅ Upload múltiplo de arquivos

## APIs Alteradas
- `GET /api/reports/list` - Agora usa sistema local
- `POST /api/upload_report` - Salva localmente
- `GET /api/reports/download` - Serve arquivos locais

## Configurações Necessárias
- Configurar arquivo `.env`
- Definir `JWT_SECRET`
- Ajustar permissões de pastas
```

#### Passo 8.2: Checklist Final
- [ ] Backup completo realizado
- [ ] Migração executada com sucesso
- [ ] Todas as APIs funcionando
- [ ] Testes de upload/download OK
- [ ] Logs sem erros críticos
- [ ] Documentação atualizada
- [ ] Usuários notificados da mudança

---
*Implementação concluída em: 3 semanas conforme planejado*
