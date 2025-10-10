<?php
/**
 * Classe para gerenciar conexões FTP de forma segura
 */
class FtpService {
    private $connection = null;
    private $host;
    private $username;
    private $password;
    private $port;
    private $isConnected = false;
    private $lastError = '';
    
    public function __construct($host, $username, $password, $port = 21) {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->port = $port;
    }
    
    /**
     * Estabelece uma conexão com o servidor FTP
     * 
     * @return bool Retorna true em caso de sucesso
     * @throws Exception Se não for possível conectar ou autenticar
     */
    public function connect() {
        // Fecha a conexão atual se existir
        $this->close();
        
        // Tenta conectar ao servidor FTP
        $this->connection = @ftp_connect($this->host, $this->port, 30);
        
        if ($this->connection === false) {
            $this->lastError = error_get_last()['message'] ?? 'Erro desconhecido';
            throw new Exception("Não foi possível conectar ao servidor FTP: " . $this->lastError);
        }
        
        // Tenta fazer login
        $login = @ftp_login($this->connection, $this->username, $this->password);
        if ($login === false) {
            $this->lastError = 'Credenciais inválidas';
            $this->close();
            throw new Exception("Não foi possível autenticar no servidor FTP: " . $this->lastError);
        }
        
        // Configurações adicionais
        @ftp_pasv($this->connection, true); // Modo passivo
        @ftp_set_option($this->connection, FTP_TIMEOUT_SEC, 30);
        
        $this->isConnected = true;
        return true;
    }
    
    /**
     * Lista os arquivos em um diretório remoto
     * 
     * @param string $directory Diretório remoto
     * @return array Lista de arquivos
     * @throws Exception Se ocorrer um erro ao listar os arquivos
     */
    public function listFiles($directory = '/') {
        if (!$this->isConnected) {
            $this->connect();
        }
        
        $files = @ftp_nlist($this->connection, $directory);
        
        if ($files === false) {
            $this->lastError = 'Erro ao listar diretório: ' . $directory;
            throw new Exception($this->lastError);
        }
        
        // Remove os diretórios . e .. da lista
        return array_filter($files, function($file) {
            return !in_array($file, ['.', '..']);
        });
    }
    
    /**
     * Faz o download de um arquivo remoto
     * 
     * @param string $remoteFile Caminho do arquivo remoto
     * @param string $localFile Caminho local para salvar o arquivo
     * @return bool True em caso de sucesso
     * @throws Exception Se ocorrer um erro ao baixar o arquivo
     */
    public function downloadFile($remoteFile, $localFile) {
        if (!$this->isConnected) {
            $this->connect();
        }
        
        // Cria o diretório de destino se não existir
        $dir = dirname($localFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        $result = @ftp_get($this->connection, $localFile, $remoteFile, FTP_BINARY);
        
        if ($result === false) {
            $this->lastError = 'Erro ao baixar o arquivo: ' . $remoteFile;
            throw new Exception($this->lastError);
        }
        
        return true;
    }
    
    /**
     * Fecha a conexão FTP
     */
    public function close() {
        if ($this->connection) {
            @ftp_close($this->connection);
            $this->connection = null;
            $this->isConnected = false;
        }
    }
    
    /**
     * Verifica se a conexão está ativa
     * 
     * @return bool True se estiver conectado, false caso contrário
     */
    public function isConnected() {
        return $this->isConnected;
    }
    
    /**
     * Obtém a última mensagem de erro
     * 
     * @return string Última mensagem de erro
     */
    public function getLastError() {
        return $this->lastError;
    }
    
    /**
     * Destrutor - garante que a conexão seja fechada
     */
    public function __destruct() {
        $this->close();
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