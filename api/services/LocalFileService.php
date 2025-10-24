<?php
class LocalFileService {
    private $basePath;
    private $reportsPath;
    private $boletinsPath;
    private $tempPath;
    private $allowedExtensions;
    private $maxFileSize;

    public function __construct() {
        $this->basePath = __DIR__ . '/../../data/';
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
