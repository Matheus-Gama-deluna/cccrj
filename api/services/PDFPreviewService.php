<?php
class PDFPreviewService {
    private $cacheDir;
    private $thumbnailDir;

    public function __construct() {
        $this->cacheDir = __DIR__ . '/../../cache/';
        $this->thumbnailDir = __DIR__ . '/../../cache/thumbnails/';
        $this->ensureDirectories();
    }

    private function ensureDirectories() {
        $dirs = [$this->cacheDir, $this->thumbnailDir];
        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }

    public function generatePreview($pdfPath, $outputPath, $page = 1, $width = 200) {
        try {
            // Verificar se o PDF existe
            if (!file_exists($pdfPath)) {
                throw new Exception('Arquivo PDF não encontrado: ' . $pdfPath);
            }

            // Para Windows/XAMPP, usamos uma abordagem diferente
            // Como GD e ImageMagick podem não estar disponíveis, criamos uma miniatura simples
            if ($this->isWindows()) {
                return $this->generateWindowsPreview($pdfPath, $outputPath, $width);
            }

            // Para Linux com ImageMagick
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

    private function isWindows() {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    private function generateWindowsPreview($pdfPath, $outputPath, $width) {
        // Em Windows, sem ImageMagick, criamos um arquivo de placeholder
        // ou tentamos usar ferramentas externas se disponíveis

        // Verificar se o pdf2jpg ou similar está disponível
        $tools = ['pdf2jpg', 'convert', 'magick'];

        foreach ($tools as $tool) {
            $result = $this->executeCommand($tool, $pdfPath, $outputPath, $width);
            if ($result) {
                return true;
            }
        }

        // Se nenhuma ferramenta estiver disponível, criar uma imagem placeholder
        return $this->createPlaceholderImage($outputPath, $width);
    }

    private function executeCommand($tool, $input, $output, $width) {
        // Tentar executar comando externo
        $command = sprintf('%s "%s[0]" -resize %dx -quality 85 "%s"', $tool, $input, $width, $output);

        try {
            exec($command, $output, $returnVar);
            return $returnVar === 0 && file_exists($output);
        } catch (Exception $e) {
            return false;
        }
    }

    private function createPlaceholderImage($outputPath, $width) {
        // Criar uma imagem placeholder simples usando GD se disponível
        if (extension_loaded('gd')) {
            $height = 280; // Proporção 5:7 para PDFs

            $image = imagecreate($width, $height);
            $white = imagecolorallocate($image, 255, 255, 255);
            $gray = imagecolorallocate($image, 128, 128, 128);
            $black = imagecolorallocate($image, 0, 0, 0);

            // Fundo branco
            imagefill($image, 0, 0, $white);

            // Borda cinza
            imagerectangle($image, 0, 0, $width-1, $height-1, $gray);

            // Texto "PDF"
            $fontSize = min($width / 10, 20);
            imagestring($image, 5, $width/2 - 20, $height/2 - 10, 'PDF', $black);

            // Salvar como JPEG
            imagejpeg($image, $outputPath, 85);
            imagedestroy($image);

            return true;
        }

        // Se GD não estiver disponível, copiar uma imagem padrão
        $defaultPreview = __DIR__ . '/../../assets/images/pdf-placeholder.jpg';
        if (file_exists($defaultPreview)) {
            copy($defaultPreview, $outputPath);
            return true;
        }

        return false;
    }

    public function generateThumbnail($pdfPath, $fileName, $width = 300) {
        $cacheKey = md5($fileName . $width . filemtime($pdfPath));
        $thumbnailPath = $this->thumbnailDir . $cacheKey . '.jpg';

        // Verificar cache primeiro
        if (file_exists($thumbnailPath)) {
            return $thumbnailPath;
        }

        // Gerar nova miniatura
        if ($this->generatePdfThumbnail($pdfPath, $thumbnailPath, $width)) {
            // Cache válido por 24 horas
            return $thumbnailPath;
        }

        return false;
    }

    private function generatePdfThumbnail($pdfPath, $thumbnailPath, $width) {
        try {
            if (!file_exists($pdfPath)) {
                throw new Exception('Arquivo PDF não encontrado: ' . $pdfPath);
            }

            if ($this->isWindows()) {
                return $this->generateWindowsPreview($pdfPath, $thumbnailPath, $width);
            }

            // Para sistemas Linux com ImageMagick
            if (extension_loaded('imagick')) {
                $imagick = new Imagick();
                $imagick->setResolution(150, 150);
                $imagick->readImage($pdfPath . '[0]'); // Primeira página

                $imagick->setImageFormat('jpg');
                $imagick->scaleImage($width, 0);
                $imagick->writeImage($thumbnailPath);

                $imagick->clear();
                $imagick->destroy();

                return true;
            }

            // Fallback para GD
            return $this->createPlaceholderImage($thumbnailPath, $width);

        } catch (Exception $e) {
            error_log("Erro ao gerar miniatura: " . $e->getMessage());
            return $this->createPlaceholderImage($thumbnailPath, $width);
        }
    }

    private function cleanupOldThumbnails() {
        $files = glob($this->thumbnailDir . '*.jpg');
        $now = time();
        $maxAge = 24 * 60 * 60; // 24 horas

        foreach ($files as $file) {
            if ($now - filemtime($file) > $maxAge) {
                unlink($file);
            }
        }
    }

    public function getThumbnailUrl($fileName, $type = 'boletins', $width = 300) {
        return "/api/pdf/preview/index.php?file=" . urlencode($fileName) .
               "&type=" . urlencode($type) .
               "&thumbnail=1&width=" . $width;
    }

    public function getPreviewUrl($fileName, $type = 'boletins') {
        return "/api/pdf/preview/index.php?file=" . urlencode($fileName) .
               "&type=" . urlencode($type);
    }
}
?>
