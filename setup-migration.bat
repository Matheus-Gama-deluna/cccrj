@echo off
REM Script de Automação - Migração FTP para Sistema Local (Windows)
REM Data: %date% %time%
REM Uso: setup-migration.bat

echo 🚀 Iniciando Migração FTP para Sistema Local - CCCRJ
echo =================================================

REM Verificar se está no diretório correto
if not exist "plano_pdf.md" (
    echo ❌ Erro: Execute este script a partir do diretório raiz do projeto CCCRJ
    pause
    exit /b 1
)

echo 📍 Diretório atual: %cd%

REM Fase 1: Preparação do Ambiente
echo.
echo 📋 FASE 1: Preparação do Ambiente
echo ================================

REM Backup
echo 💾 Fazendo backup do sistema...
for /f "tokens=2-4 delims=/ " %%a in ("%date%") do (
    for /f "tokens=1-3 delims=:." %%i in ("%time%") do (
        set "timestamp=%%a%%b%%c_%%i%%j%%k"
    )
)
set "BACKUP_DIR=../cccrj_backup_%timestamp%"
xcopy /E /I /H /Y . "%BACKUP_DIR%"
echo ✅ Backup criado em: %BACKUP_DIR%

REM Criar estrutura de diretórios
echo 📁 Criando estrutura de diretórios...
if not exist "pdf" mkdir pdf
if not exist "pdf\reports" mkdir pdf\reports
if not exist "pdf\boletins" mkdir pdf\boletins
if not exist "api\services" mkdir api\services
if not exist "temp" mkdir temp
if not exist "cache" mkdir cache

REM Configurar permissões (Windows)
echo 🔐 Configurando permissões...
icacls "pdf" /grant Everyone:(OI)(CI)F /T
icacls "temp" /grant Everyone:(OI)(CI)F /T
icacls "cache" /grant Everyone:(OI)(CI)F /T

REM Configurar .gitignore
echo 📝 Atualizando .gitignore...
findstr /C:"/pdf/" .gitignore >nul 2>&1
if errorlevel 1 echo /pdf/>> .gitignore

findstr /C:"/temp/" .gitignore >nul 2>&1
if errorlevel 1 echo /temp/>> .gitignore

findstr /C:".env" .gitignore >nul 2>&1
if errorlevel 1 echo .env>> .gitignore

echo ✅ Fase 1 concluída!

REM Fase 2: Criar arquivos base
echo.
echo 📋 FASE 2: Criando Arquivos Base
echo ===============================

REM Criar LocalFileService
echo 🔧 Criando LocalFileService...
(
echo ^<?php
echo class LocalFileService {
echo     private $basePath;
echo     private $reportsPath;
echo     private $boletinsPath;
echo     private $tempPath;
echo     private $allowedExtensions;
echo     private $maxFileSize;
echo.
echo     public function __construct() {
echo         $this-^>basePath = __DIR__ . '/../../pdf/';
echo         $this-^>reportsPath = $this-^>basePath . 'reports/';
echo         $this-^>boletinsPath = $this-^>basePath . 'boletins/';
echo         $this-^>tempPath = __DIR__ . '/../../temp/';
echo         $this-^>allowedExtensions = ['pdf'];
echo         $this-^>maxFileSize = 10 * 1024 * 1024; // 10MB
echo.
echo         $this-^>ensureDirectoriesExist();
echo     }
echo.
echo     private function ensureDirectoriesExist() {
echo         $directories = [$this-^>basePath, $this-^>reportsPath, $this-^>boletinsPath, $this-^>tempPath];
echo         foreach ($directories as $dir) {
echo             if (!is_dir($dir)) {
echo                 mkdir($dir, 0755, true);
echo             }
echo         }
echo     }
echo.
echo     public function uploadFile($file, $title, $isBoletim = false) {
echo         if (!$this-^>validateFile($file)) {
echo             throw new Exception('Arquivo inválido');
echo         }
echo.
echo         $fileName = $this-^>generateUniqueFileName($title, $file['name']);
echo         $destinationDir = $isBoletim ? $this-^>boletinsPath : $this-^>reportsPath;
echo         $destinationPath = $destinationDir . $fileName;
echo.
echo         if (!move_uploaded_file($file['tmp_name'], $destinationPath)) {
echo             throw new Exception('Falha ao mover arquivo');
echo         }
echo.
echo         return [
echo             'name' =^> $fileName,
echo             'path' =^> $destinationPath,
echo             'size' =^> $file['size'],
echo             'uploaded_at' =^> date('Y-m-d H:i:s')
echo         ];
echo     }
echo.
echo     public function listFiles($type = 'reports') {
echo         $directory = $type === 'boletins' ? $this-^>boletinsPath : $this-^>reportsPath;
echo         if (!is_dir($directory)) return [];
echo.
echo         $files = scandir($directory);
echo         $files = array_filter($files, function($file) {
echo             return !in_array($file, ['.', '..']) ^&^& pathinfo($file, PATHINFO_EXTENSION) === 'pdf';
echo         });
echo.
echo         $fileInfo = [];
echo         foreach ($files as $file) {
echo             $filePath = $directory . $file;
echo             $fileInfo[] = [
echo                 'name' =^> $file,
echo                 'path' =^> $filePath,
echo                 'size' =^> filesize($filePath),
echo                 'modified' =^> date('c', filemtime($filePath)),
echo                 'url' =^> '/api/reports/download?file=' . urlencode($file) . '^&type=' . $type
echo             ];
echo         }
echo.
echo         usort($fileInfo, function($a, $b) {
echo             return strtotime($b['modified']) - strtotime($a['modified']);
echo         });
echo.
echo         return $fileInfo;
echo     }
echo.
echo     private function validateFile($file) {
echo         if (!isset($file['tmp_name']) ^|^| !is_uploaded_file($file['tmp_name'])) {
echo             return false;
echo         }
echo.
echo         $finfo = finfo_open(FILEINFO_MIME_TYPE);
echo         $mimeType = finfo_file($finfo, $file['tmp_name']);
echo         finfo_close($finfo);
echo.
echo         if ($mimeType !== 'application/pdf') return false;
echo         if ($file['size'] ^> $this-^>maxFileSize) return false;
echo.
echo         return true;
echo     }
echo.
echo     private function generateUniqueFileName($title, $originalName) {
echo         $extension = pathinfo($originalName, PATHINFO_EXTENSION);
echo         $baseName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $title);
echo         $uniqueId = time() . '_' . uniqid();
echo         return $baseName . '_' . $uniqueId . '.' . $extension;
echo     }
echo }
echo ?^>
) > api\services\LocalFileService.php

REM Criar arquivo .env
echo 🔐 Criando arquivo .env...
(
echo DB_HOST=localhost
echo DB_NAME=cccrj
echo DB_USER=usuario
echo DB_PASS=senha_segura
echo UPLOAD_MAX_SIZE=10485760
echo ALLOWED_TYPES=application/pdf
echo JWT_SECRET=%RANDOM%%RANDOM%%RANDOM%%RANDOM%%RANDOM%%RANDOM%%RANDOM%%RANDOM%
echo SESSION_TIMEOUT=3600
echo LOG_LEVEL=INFO
) > .env

echo ✅ Fase 2 concluída!

REM Fase 3: Verificar dependências
echo.
echo 📋 FASE 3: Verificação de Dependências
echo ====================================

echo 🐘 Verificando PHP...
php --version | findstr /R /C:"PHP [0-9]" || echo PHP não encontrado!

echo 📦 Verificando extensões...
php -m | findstr /I /C:"fileinfo\|openssl\|gd" || echo ⚠️  Algumas extensões podem estar faltando

echo 🔍 Verificando se todas as pastas foram criadas...
dir /B pdf
dir /B api\services

echo ✅ Fase 3 concluída!

echo.
echo 🎉 SETUP INICIAL CONCLUÍDO!
echo ==========================
echo.
echo 📝 PRÓXIMOS PASSOS:
echo 1. Configure as credenciais no arquivo .env
echo 2. Execute o script de migração: php api\migrate_from_ftp.php
echo 3. Teste as APIs atualizadas
echo 4. Implemente as funcionalidades extras ^(cache, preview, etc.^)
echo.
echo 📋 Consulte o checklist: docs\checklist-implementacao.md
echo 📖 Leia o plano detalhado: plano_pdf.md
echo.
echo ✅ Backup disponível em: %BACKUP_DIR%

pause
