<?php
// utils/JsonCache.php

class JsonCache {
    private $cacheDir;
    private $cacheExpiry; // Tempo de expiração em segundos
    
    public function __construct($cacheDir = null, $cacheExpiry = 3600) { // 1 hora padrão
        $this->cacheDir = $cacheDir ?: __DIR__ . '/../cache';
        $this->cacheExpiry = $cacheExpiry;
        
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    public function getCachedData($jsonFile) {
        $fileName = basename($jsonFile);
        $cacheFile = $this->cacheDir . '/' . $fileName . '.cache';
        
        // Verificar se o cache existe e não está expirado
        if (file_exists($cacheFile)) {
            $cacheTime = filemtime($cacheFile);
            $jsonTime = file_exists($jsonFile) ? filemtime($jsonFile) : 0;
            
            // Se o arquivo JSON foi modificado após o cache ou o cache expirou
            if ($jsonTime > $cacheTime || (time() - $cacheTime) > $this->cacheExpiry) {
                // Cache expirado ou arquivo original foi atualizado
                return null;
            }
            
            // Retornar dados do cache
            return json_decode(file_get_contents($cacheFile), true);
        }
        
        return null;
    }
    
    public function cacheData($jsonFile, $data) {
        $fileName = basename($jsonFile);
        $cacheFile = $this->cacheDir . '/' . $fileName . '.cache';
        
        file_put_contents($cacheFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    
    public function clearCache($jsonFile = null) {
        if ($jsonFile) {
            $fileName = basename($jsonFile);
            $cacheFile = $this->cacheDir . '/' . $fileName . '.cache';
            
            if (file_exists($cacheFile)) {
                unlink($cacheFile);
            }
        } else {
            // Limpar todos os arquivos de cache
            $files = glob($this->cacheDir . '/*.cache');
            foreach ($files as $file) {
                unlink($file);
            }
        }
    }
    
    public function isCacheValid($jsonFile) {
        $cachedData = $this->getCachedData($jsonFile);
        return $cachedData !== null;
    }
}
?>