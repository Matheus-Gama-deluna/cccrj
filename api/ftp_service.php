<?php
class FtpService {
    private $connection;
    private $host;
    private $username;
    private $password;
    private $port;
    
    public function __construct($host, $username, $password, $port = 21) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->port = $port;
    }
    
    public function connect() {
        $this->connection = ftp_connect($this->host, $this->port);
        if (!$this->connection) {
            throw new Exception("Não foi possível conectar ao servidor FTP");
        }
        
        $login = ftp_login($this->connection, $this->username, $this->password);
        if (!$login) {
            throw new Exception("Não foi possível autenticar no servidor FTP");
        }
        
        // Modo passivo para evitar problemas com firewalls
        ftp_pasv($this->connection, true);
        
        return true;
    }
    
    public function listFiles($directory = '/') {
        if (!$this->connection) {
            $this->connect();
        }
        
        $files = ftp_nlist($this->connection, $directory);
        return $files ? $files : [];
    }
    
    public function downloadFile($remoteFile, $localFile) {
        if (!$this->connection) {
            $this->connect();
        }
        
        return ftp_get($this->connection, $localFile, $remoteFile, FTP_BINARY);
    }
    
    public function close() {
        if ($this->connection) {
            ftp_close($this->connection);
        }
    }
    
    public function getFileSize($filePath) {
        if (!$this->connection) {
            $this->connect();
        }
        return ftp_size($this->connection, $filePath);
    }
    
    public function getFileModifiedTime($filePath) {
        if (!$this->connection) {
            $this->connect();
        }
        return ftp_mdtm($this->connection, $filePath);
    }
}
?>