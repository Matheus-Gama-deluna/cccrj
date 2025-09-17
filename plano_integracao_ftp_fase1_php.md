# Plano de Integração com Servidor FTP - Fase 1: Visualização de Relatórios (PHP Puro)

## 1. Visão Geral

Este plano detalha como integrar a estrutura frontend atual com acesso a arquivos de um servidor FTP para exibição de relatórios técnicos na página inicial do sistema CCCRJ, utilizando PHP puro em vez de Laravel.

## 1.1 Status da Implementação

✅ Estrutura de diretórios criada:
- `api/` - Diretório para os endpoints da API
- `api/reports/` - Diretório para endpoints específicos de relatórios
- `temp/` - Diretório temporário para armazenamento de arquivos durante o download

✅ Arquivos de backend criados:
- `api/config.php` - Configurações do servidor FTP
- `api/ftp_service.php` - Classe para gerenciamento de conexão FTP
- `api/reports/list.php` - Endpoint para listagem de relatórios
- `api/reports/download.php` - Endpoint para download de relatórios

✅ Frontend atualizado:
- `assets/js/components/reports.js` - Componente atualizado para consumir a API

## 2. Arquitetura Atual

### 2.1 Estrutura Frontend
- `index.html`: Página principal com seção de relatórios
- `assets/js/components/reports.js`: Componente responsável pela exibição de relatórios

### 2.2 Componentes de Relatórios
O componente `ReportsManager` em `reports.js` atualmente:
- Simula o carregamento de relatórios
- Cria cards de exibição para cada relatório
- Simula o download de arquivos

## 3. Plano de Implementação

### 3.1 Fase 1: Configuração do Acesso FTP

#### 3.1.1 Serviço FTP em PHP
Criar arquivo `api/ftp_service.php`:

```php
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
```

#### 3.1.2 Configuração de Credenciais
Criar arquivo `api/config.php`:

```php
<?php
// Configurações do servidor FTP
define('FTP_HOST', 'seu.servidor.ftp.com');
define('FTP_USERNAME', 'usuario_ftp');
define('FTP_PASSWORD', 'senha_segura');
define('FTP_PORT', 21);
define('FTP_REPORTS_PATH', '/relatorios/');

// Configurações do sistema
define('TEMP_DIR', __DIR__ . '/../temp/');
?>
```

### 3.2 Fase 2: API REST para Relatórios

#### 3.2.1 Endpoint de Listagem de Relatórios
Criar arquivo `api/reports/list.php`:

```php
<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config.php';
require_once '../ftp_service.php';

try {
    $ftp = new FtpService(FTP_HOST, FTP_USERNAME, FTP_PASSWORD, FTP_PORT);
    $ftp->connect();
    
    $files = $ftp->listFiles(FTP_REPORTS_PATH);
    
    $reports = [];
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'pdf') {
            $reports[] = [
                'name' => basename($file),
                'path' => $file,
                'size' => $ftp->getFileSize($file),
                'modified' => $ftp->getFileModifiedTime($file)
            ];
        }
    }
    
    $ftp->close();
    
    echo json_encode($reports);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
```

#### 3.2.2 Endpoint de Download de Relatórios
Criar arquivo `api/reports/download.php`:

```php
<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config.php';
require_once '../ftp_service.php';

if (!isset($_GET['file'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Nome do arquivo não especificado']);
    exit;
}

$filename = basename($_GET['file']); // Sanitizar o nome do arquivo
$remoteFile = FTP_REPORTS_PATH . $filename;

try {
    // Verificar se o arquivo existe
    if (pathinfo($filename, PATHINFO_EXTENSION) !== 'pdf') {
        http_response_code(400);
        echo json_encode(['error' => 'Somente arquivos PDF são permitidos']);
        exit;
    }
    
    $ftp = new FtpService(FTP_HOST, FTP_USERNAME, FTP_PASSWORD, FTP_PORT);
    $ftp->connect();
    
    // Certificar-se de que o diretório temporário existe
    if (!file_exists(TEMP_DIR)) {
        mkdir(TEMP_DIR, 0755, true);
    }
    
    // Caminho temporário local para armazenar o arquivo
    $localFile = TEMP_DIR . $filename;
    
    // Baixar o arquivo do FTP
    if ($ftp->downloadFile($remoteFile, $localFile)) {
        $ftp->close();
        
        // Definir headers para download
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($localFile));
        
        // Ler e enviar o arquivo
        readfile($localFile);
        
        // Remover o arquivo temporário
        unlink($localFile);
        exit;
    } else {
        $ftp->close();
        http_response_code(500);
        echo json_encode(['error' => 'Não foi possível baixar o arquivo']);
        exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
```

### 3.3 Fase 3: Estrutura de Diretórios

```
cccrj/
├── index.html
├── api/
│   ├── config.php
│   ├── ftp_service.php
│   └── reports/
│       ├── list.php
│       └── download.php
├── temp/ (diretório temporário para downloads)
└── assets/
    └── js/
        └── components/
            └── reports.js
```

### 3.4 Fase 4: Integração Frontend

#### 3.4.1 Atualização do Componente de Relatórios
Modificar `assets/js/components/reports.js` para consumir a API:

```javascript
// Lógica para os relatórios em PDF
class ReportsManager {
    constructor() {
        this.reportsContainer = document.getElementById('reports-container');
        this.apiUrl = 'api/reports/list.php'; // URL da API PHP
        this.downloadUrl = 'api/reports/download.php?file='; // URL para download
        this.init();
    }

    init() {
        // Carregar relatórios iniciais
        this.loadReports();
    }

    async loadReports() {
        try {
            // Mostrar indicador de carregamento
            this.showLoading();
            
            // Chamar API para obter relatórios do FTP
            const response = await fetch(this.apiUrl);
            
            if (!response.ok) {
                throw new Error(`Erro na requisição: ${response.status}`);
            }
            
            const reports = await response.json();
            
            // Limpar container
            if (this.reportsContainer) {
                this.reportsContainer.innerHTML = '';
                
                // Adicionar relatórios ao container
                if (reports.length > 0) {
                    reports.forEach(report => {
                        const card = this.createReportCard(report);
                        this.reportsContainer.appendChild(card);
                    });
                } else {
                    this.showEmptyState();
                }
            }
        } catch (error) {
            console.error('Erro ao carregar relatórios:', error);
            this.showError('Não foi possível carregar os relatórios. Tente novamente mais tarde.');
        }
    }

    createReportCard(report) {
        // Criar elemento para o card do relatório
        const article = document.createElement('div');
        article.className = 'bg-gradient-to-br from-white to-[#F5F0E8] rounded-2xl shadow-lg card-hover border border-[#F5F0E8]';
        
        // Gradientes para diferentes tipos de relatórios
        const gradients = [
            'from-[#8B2635] to-[#992D3D]',
            'from-[#4A6B8A] to-[#8B2635]',
            'from-[#D4A574] to-[#8B2635]'
        ];
        
        const randomGradient = gradients[Math.floor(Math.random() * gradients.length)];
        
        // Extrair data do nome do arquivo ou usar data de modificação
        const fileName = report.name;
        const fileDate = this.extractDateFromFilename(fileName) || new Date().toISOString().split('T')[0];
        
        article.innerHTML = `
            <div class="h-48 bg-gradient-to-br ${randomGradient} relative flex items-center justify-center">
                <span class="material-icons text-white text-6xl">picture_as_pdf</span>
            </div>
            <div class="p-6">
                <div class="text-sm text-[#6B4423] mb-2">${this.formatDate(fileDate)}</div>
                <h3 class="text-xl font-bold text-[#6B4423] mb-3">${this.formatFileName(fileName)}</h3>
                <p class="text-[#8B2635] mb-4">Relatório técnico em formato PDF</p>
                <button class="w-full bg-[#8B2635] text-white py-2 px-4 rounded-lg hover:bg-[#992D3D] transition-colors duration-300 font-medium flex items-center justify-center download-report" data-file="${fileName}">
                    <span class="material-icons mr-2">download</span> Baixar PDF
                </button>
            </div>
        `;
        
        // Adicionar evento de clique para download
        const downloadButton = article.querySelector('.download-report');
        if (downloadButton) {
            downloadButton.addEventListener('click', (e) => {
                e.preventDefault();
                this.downloadReport(fileName);
            });
        }
        
        return article;
    }

    downloadReport(fileName) {
        try {
            // Abrir o arquivo em uma nova aba para download
            window.open(this.downloadUrl + encodeURIComponent(fileName), '_blank');
        } catch (error) {
            console.error('Erro ao baixar relatório:', error);
            alert('Não foi possível baixar o relatório. Tente novamente mais tarde.');
        }
    }

    formatDate(dateString) {
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        const date = new Date(dateString);
        return isNaN(date.getTime()) ? 'Data não disponível' : date.toLocaleDateString('pt-BR', options);
    }

    formatFileName(fileName) {
        // Remover extensão .pdf e substituir hífens/underscores por espaços
        return fileName.replace('.pdf', '')
                      .replace(/[-_]/g, ' ')
                      .replace(/\b\w/g, l => l.toUpperCase());
    }

    extractDateFromFilename(filename) {
        // Tentar extrair data do nome do arquivo (formato YYYY-MM-DD ou DD-MM-YYYY)
        const datePatterns = [
            /(\d{4})-(\d{2})-(\d{2})/,
            /(\d{2})-(\d{2})-(\d{4})/
        ];
        
        for (const pattern of datePatterns) {
            const match = filename.match(pattern);
            if (match) {
                if (match[1].length === 4) {
                    // Formato YYYY-MM-DD
                    return `${match[1]}-${match[2]}-${match[3]}`;
                } else {
                    // Formato DD-MM-YYYY
                    return `${match[3]}-${match[2]}-${match[1]}`;
                }
            }
        }
        
        return null;
    }

    showLoading() {
        if (this.reportsContainer) {
            this.reportsContainer.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-[#8B2635] mb-4"></div>
                    <p class="text-[#6B4423] text-xl">Carregando relatórios...</p>
                </div>
            `;
        }
    }

    showEmptyState() {
        if (this.reportsContainer) {
            this.reportsContainer.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <span class="material-icons text-5xl text-[#8B2635] mb-4">folder_open</span>
                    <p class="text-[#6B4423] text-xl">Nenhum relatório encontrado</p>
                    <p class="text-[#8B2635] mt-2">Ainda não há relatórios disponíveis para download.</p>
                </div>
            `;
        }
    }

    showError(message) {
        if (this.reportsContainer) {
            this.reportsContainer.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <span class="material-icons text-5xl text-[#8B2635] mb-4">error</span>
                    <p class="text-[#6B4423] text-xl">${message}</p>
                    <button class="mt-4 bg-[#8B2635] text-white py-2 px-6 rounded-lg hover:bg-[#992D3D] transition-colors duration-300" onclick="reportsManager.loadReports()">
                        Tentar novamente
                    </button>
                </div>
            `;
        }
    }
}

// Inicializar quando o DOM estiver pronto
let reportsManager;
document.addEventListener('DOMContentLoaded', () => {
    reportsManager = new ReportsManager();
});
```

## 4. Configuração do Servidor

### 4.1 Permissões de Diretório
Certifique-se de que o diretório `temp/` tenha permissões de escrita:
```bash
chmod 755 temp/
```

### 4.2 Configuração do Servidor Web
Adicione estas regras ao `.htaccess` na raiz do projeto:

```apache
# Permitir acesso à API
<Files "api/*">
    Order allow,deny
    Allow from all
</Files>

# Configuração para CORS
Header always set Access-Control-Allow-Origin "*"
Header always set Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
Header always set Access-Control-Allow-Headers "Content-Type, Authorization"
```

## 5. Considerações de Segurança

1. **Sanitização de Inputs**: Sempre sanitizar nomes de arquivos recebidos via URL
2. **Validação de Extensão**: Permitir apenas arquivos PDF
3. **Permissões FTP**: Usar usuário com permissões mínimas necessárias
4. **HTTPS**: Configurar SSL para todas as comunicações
5. **Rate Limiting**: Implementar limites de requisições para prevenir abuso
6. **Logs**: Registrar acessos e erros para monitoramento

## 6. Considerações de Performance

1. **Caching**: Implementar cache da listagem de arquivos (arquivo temporário)
2. **Limpeza de Temp**: Criar script para limpar arquivos temporários antigos
3. **Tratamento de Erros**: Implementar retries com backoff exponencial para operações FTP
4. **Timeouts**: Configurar timeouts adequados para conexões FTP

## 7. Próximos Passos (Fase 2)

1. Implementar área administrativa para upload de relatórios
2. Adicionar autenticação para funcionalidades administrativas
3. Implementar deleção de relatórios
4. Adicionar busca e filtragem de relatórios
5. Implementar histórico de uploads

## 8. Testes Necessários

1. **Conectividade FTP**: Testar conexão com credenciais fornecidas
2. **Listagem de Arquivos**: Verificar se arquivos PDF são listados corretamente
3. **Download de Arquivos**: Testar download de diferentes tamanhos de arquivos
4. **Tratamento de Erros**: Verificar comportamento em caso de falhas de conexão
5. **Segurança**: Testar tentativas de acesso a arquivos não autorizados