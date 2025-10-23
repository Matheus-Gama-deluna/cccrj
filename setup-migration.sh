#!/bin/bash
# Script de Automação - Migração FTP para Sistema Local
# Data: $(date)
# Uso: bash setup-migration.sh

set -e

echo "🚀 Iniciando Migração FTP para Sistema Local - CCCRJ"
echo "================================================="

# Verificar se está no diretório correto
if [ ! -f "plano_pdf.md" ]; then
    echo "❌ Erro: Execute este script a partir do diretório raiz do projeto CCCRJ"
    exit 1
fi

echo "📍 Diretório atual: $(pwd)"

# Fase 1: Preparação do Ambiente
echo ""
echo "📋 FASE 1: Preparação do Ambiente"
echo "================================="

# Backup
echo "💾 Fazendo backup do sistema..."
BACKUP_DIR="../cccrj_backup_$(date +%Y%m%d_%H%M%S)"
cp -r . $BACKUP_DIR
echo "✅ Backup criado em: $BACKUP_DIR"

# Criar estrutura de diretórios
echo "📁 Criando estrutura de diretórios..."
mkdir -p pdf/reports
mkdir -p pdf/boletins
mkdir -p api/services
mkdir -p temp
mkdir -p cache

# Configurar permissões
echo "🔐 Configurando permissões..."
chmod 755 pdf reports boletins temp cache
chmod 755 api/services

# Configurar .gitignore
echo "📝 Atualizando .gitignore..."
if [ -f ".gitignore" ]; then
    if ! grep -q "/pdf/" .gitignore; then
        echo "/pdf/" >> .gitignore
    fi
    if ! grep -q "/temp/" .gitignore; then
        echo "/temp/" >> .gitignore
    fi
    if ! grep -q ".env" .gitignore; then
        echo ".env" >> .gitignore
    fi
fi

echo "✅ Fase 1 concluída!"

# Fase 2: Criar arquivos base
echo ""
echo "📋 FASE 2: Criando Arquivos Base"
echo "==============================="

# Criar LocalFileService
echo "🔧 Criando LocalFileService..."
cat > api/services/LocalFileService.php << 'EOF'
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
        if (!$this->validateFile($file)) {
            throw new Exception('Arquivo inválido');
        }

        $fileName = $this->generateUniqueFileName($title, $file['name']);
        $destinationDir = $isBoletim ? $this->boletinsPath : $this->reportsPath;
        $destinationPath = $destinationDir . $fileName;

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
        if (!is_dir($directory)) return [];

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

        usort($fileInfo, function($a, $b) {
            return strtotime($b['modified']) - strtotime($a['modified']);
        });

        return $fileInfo;
    }

    private function validateFile($file) {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return false;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if ($mimeType !== 'application/pdf') return false;
        if ($file['size'] > $this->maxFileSize) return false;

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
EOF

# Criar arquivo .env
echo "🔐 Criando arquivo .env..."
cat > .env << EOF
DB_HOST=localhost
DB_NAME=cccrj
DB_USER=usuario
DB_PASS=senha_segura
UPLOAD_MAX_SIZE=10485760
ALLOWED_TYPES=application/pdf
JWT_SECRET=$(openssl rand -hex 32)
SESSION_TIMEOUT=3600
LOG_LEVEL=INFO
EOF

chmod 600 .env

echo "✅ Fase 2 concluída!"

# Fase 3: Verificar dependências
echo ""
echo "📋 FASE 3: Verificação de Dependências"
echo "===================================="

echo "🐘 Verificando PHP..."
php --version | head -1

echo "📦 Verificando extensões..."
php -m | grep -E "(fileinfo|openssl|gd)" || echo "⚠️  Algumas extensões podem estar faltando"

echo "🔍 Verificando se todas as pastas foram criadas..."
ls -la pdf/
ls -la api/services/

echo "✅ Fase 3 concluída!"

echo ""
echo "🎉 SETUP INICIAL CONCLUÍDO!"
echo "=========================="
echo ""
echo "📝 PRÓXIMOS PASSOS:"
echo "1. Configure as credenciais no arquivo .env"
echo "2. Execute o script de migração: php api/migrate_from_ftp.php"
echo "3. Teste as APIs atualizadas"
echo "4. Implemente as funcionalidades extras (cache, preview, etc.)"
echo ""
echo "📋 Consulte o checklist: docs/checklist-implementacao.md"
echo "📖 Leia o plano detalhado: plano_pdf.md"
echo ""
echo "✅ Backup disponível em: $BACKUP_DIR"
