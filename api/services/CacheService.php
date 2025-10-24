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
