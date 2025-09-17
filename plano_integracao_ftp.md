# Plano de Integração com Servidor FTP

## 1. Visão Geral

Este plano detalha como integrar a estrutura frontend atual com acesso a arquivos de um servidor FTP para exibição de relatórios técnicos no sistema CCCRJ. A implementação seguirá a abordagem definida nos documentos de arquitetura e plano de desenvolvimento.

## 2. Arquitetura Atual

### 2.1 Estrutura Frontend
- `index.html`: Página principal com seção de relatórios
- `admin.html`: Área administrativa para upload de arquivos
- `assets/js/components/reports.js`: Componente responsável pela exibição de relatórios
- `assets/js/admin.js`: Lógica da área administrativa
- `assets/js/auth.js`: Sistema de autenticação

### 2.2 Componentes de Relatórios
O componente `ReportsManager` em `reports.js` atualmente:
- Simula o carregamento de relatórios
- Cria cards de exibição para cada relatório
- Simula o download de arquivos

### 2.3 Área Administrativa
O componente `AdminDashboard` em `admin.js` atualmente:
- Gerencia o upload de arquivos
- Simula o envio para um servidor FTP
- Realiza validações básicas de arquivos

## 3. Plano de Implementação

### 3.1 Fase 1: Configuração do Acesso FTP

#### 3.1.1 Backend Laravel
Criar um serviço no backend Laravel para gerenciar a conexão com o servidor FTP:

1. Criar `FtpService.php`:
   ```php
   <?php
   
   namespace App\Services;
   
   class FtpService
   {
       private $connection;
       private $host;
       private $username;
       private $password;
       private $port;
       
       public function __construct($host, $username, $password, $port = 21)
       {
           $this->host = $host;
           $this->username = $username;
           $this->password = $password;
           $this->port = $port;
       }
       
       public function connect()
       {
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
       
       public function listFiles($directory = '/')
       {
           if (!$this->connection) {
               $this->connect();
           }
           
           $files = ftp_nlist($this->connection, $directory);
           return $files ? $files : [];
       }
       
       public function downloadFile($remoteFile, $localFile)
       {
           if (!$this->connection) {
               $this->connect();
           }
           
           return ftp_get($this->connection, $localFile, $remoteFile, FTP_BINARY);
       }
       
       public function uploadFile($localFile, $remoteFile)
       {
           if (!$this->connection) {
               $this->connect();
           }
           
           return ftp_put($this->connection, $remoteFile, $localFile, FTP_BINARY);
       }
       
       public function close()
       {
           if ($this->connection) {
               ftp_close($this->connection);
           }
       }
   }
   ```

#### 3.1.2 Configuração de Credenciais
Adicionar credenciais do FTP no arquivo `.env` do Laravel:
```env
FTP_HOST=seu.servidor.ftp.com
FTP_USERNAME=usuario_ftp
FTP_PASSWORD=senha_segura
FTP_PORT=21
FTP_REPORTS_PATH=/relatorios/
```

### 3.2 Fase 2: API REST para Relatórios

#### 3.2.1 Controller de Relatórios
Criar `ReportController.php`:
```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    private $ftpService;
    
    public function __construct()
    {
        $this->ftpService = new FtpService(
            env('FTP_HOST'),
            env('FTP_USERNAME'),
            env('FTP_PASSWORD'),
            env('FTP_PORT', 21)
        );
    }
    
    public function index()
    {
        try {
            $this->ftpService->connect();
            $files = $this->ftpService->listFiles(env('FTP_REPORTS_PATH'));
            
            $reports = [];
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'pdf') {
                    $reports[] = [
                        'name' => basename($file),
                        'path' => $file,
                        'size' => $this->getFileSize($file),
                        'modified' => $this->getFileModifiedTime($file)
                    ];
                }
            }
            
            $this->ftpService->close();
            
            return response()->json($reports);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function download($filename)
    {
        try {
            $this->ftpService->connect();
            
            // Caminho completo do arquivo no FTP
            $remoteFile = env('FTP_REPORTS_PATH') . $filename;
            
            // Caminho temporário local para armazenar o arquivo
            $localFile = storage_path('app/temp/' . $filename);
            
            // Certificar-se de que o diretório temporário existe
            if (!file_exists(storage_path('app/temp'))) {
                mkdir(storage_path('app/temp'), 0755, true);
            }
            
            // Baixar o arquivo do FTP
            if ($this->ftpService->downloadFile($remoteFile, $localFile)) {
                $this->ftpService->close();
                
                // Retornar o arquivo para download
                return response()->download($localFile)->deleteFileAfterSend(true);
            } else {
                $this->ftpService->close();
                return response()->json(['error' => 'Não foi possível baixar o arquivo'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    private function getFileSize($filePath)
    {
        // Implementar obtenção do tamanho do arquivo via FTP
        // Esta é uma simplificação - em produção, seria necessário usar ftp_size()
        return 0;
    }
    
    private function getFileModifiedTime($filePath)
    {
        // Implementar obtenção da data de modificação via FTP
        // Esta é uma simplificação - em produção, seria necessário usar ftp_mdtm()
        return date('Y-m-d H:i:s');
    }
}
```

#### 3.2.2 Rotas da API
Adicionar rotas em `routes/api.php`:
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReportController;

Route::middleware('api')->prefix('reports')->group(function () {
    Route::get('/', [ReportController::class, 'index']);
    Route::get('/{filename}/download', [ReportController::class, 'download']);
});
```

### 3.3 Fase 3: Integração Frontend

#### 3.3.1 Atualização do Componente de Relatórios
Modificar `assets/js/components/reports.js` para consumir a API:

```javascript
// Lógica para os relatórios em PDF
class ReportsManager {
    constructor() {
        this.reportsContainer = document.getElementById('reports-container');
        this.apiUrl = '/api/reports'; // URL da API Laravel
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

    async downloadReport(fileName) {
        try {
            // Abrir o arquivo em uma nova aba para download
            window.open(`/api/reports/${fileName}/download`, '_blank');
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

#### 3.3.2 Atualização da Área Administrativa
Modificar `assets/js/admin.js` para enviar arquivos para o FTP via API:

```javascript
// Lógica para a área administrativa
class AdminDashboard {
    constructor() {
        this.apiUrl = '/api/reports'; // URL da API Laravel
        this.init();
    }

    init() {
        // Verificar autenticação
        if (!authManager.isAuthenticated()) {
            window.location.href = 'login.html';
            return;
        }
        
        // Atualizar interface com informações do usuário
        this.updateUserInfo();
        
        // Adicionar eventos
        const uploadForm = document.getElementById('uploadForm');
        if (uploadForm) {
            uploadForm.addEventListener('submit', (e) => this.handleFileUpload(e));
        }
        
        // Adicionar evento para seleção de arquivo
        const fileInput = document.getElementById('fileInput');
        if (fileInput) {
            fileInput.addEventListener('change', (e) => this.handleFileSelect(e));
        }
    }

    updateUserInfo() {
        const user = authManager.getCurrentUser();
        if (user) {
            // Atualizar nome do usuário na interface
            const userNameElement = document.getElementById('userName');
            if (userNameElement) {
                userNameElement.textContent = user.username;
            }
        }
    }

    handleFileSelect(event) {
        const file = event.target.files[0];
        const fileInfo = document.getElementById('fileInfo');
        
        if (file && fileInfo) {
            fileInfo.innerHTML = `
                <strong>Arquivo selecionado:</strong> ${file.name}<br>
                <strong>Tamanho:</strong> ${(file.size / 1024 / 1024).toFixed(2)} MB<br>
                <strong>Tipo:</strong> ${file.type}
            `;
            fileInfo.classList.remove('hidden');
        }
    }

    async handleFileUpload(event) {
        event.preventDefault();
        
        const fileInput = document.getElementById('fileInput');
        const reportTitle = document.getElementById('reportTitle').value;
        const reportDescription = document.getElementById('reportDescription').value;
        
        if (!fileInput.files.length) {
            this.showUploadStatus('Por favor, selecione um arquivo para enviar.', 'error');
            return;
        }
        
        const file = fileInput.files[0];
        
        // Validar tipo de arquivo
        if (file.type !== 'application/pdf') {
            this.showUploadStatus('Por favor, selecione um arquivo PDF.', 'error');
            return;
        }
        
        // Validar tamanho do arquivo (máximo 10MB)
        if (file.size > 10 * 1024 * 1024) {
            this.showUploadStatus('O arquivo deve ter no máximo 10MB.', 'error');
            return;
        }
        
        // Validar campos obrigatórios
        if (!reportTitle) {
            this.showUploadStatus('Por favor, informe o título do relatório.', 'error');
            return;
        }
        
        try {
            // Mostrar indicador de carregamento
            this.showUploadStatus('Enviando arquivo...', 'processing');
            
            // Preparar dados para envio
            const formData = new FormData();
            formData.append('file', file);
            formData.append('title', reportTitle);
            formData.append('description', reportDescription);
            
            // Enviar arquivo para a API
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                body: formData,
                // Não definir Content-Type, deixar o navegador definir com boundary
            });
            
            if (response.ok) {
                this.showUploadStatus('Arquivo enviado com sucesso!', 'success');
                
                // Limpar formulário
                document.getElementById('uploadForm').reset();
                const fileInfo = document.getElementById('fileInfo');
                if (fileInfo) {
                    fileInfo.classList.add('hidden');
                }
                
                // Atualizar lista de relatórios (se houver uma função para isso)
                if (typeof reportsManager !== 'undefined' && reportsManager.loadReports) {
                    reportsManager.loadReports();
                }
            } else {
                const errorData = await response.json();
                throw new Error(errorData.error || 'Falha ao enviar arquivo para o servidor.');
            }
        } catch (error) {
            console.error('Erro ao enviar arquivo:', error);
            this.showUploadStatus(`Erro: ${error.message}`, 'error');
        }
    }

    showUploadStatus(message, status) {
        // Criar ou atualizar elemento de status
        let statusElement = document.getElementById('uploadStatus');
        if (!statusElement) {
            statusElement = document.createElement('div');
            statusElement.id = 'uploadStatus';
            statusElement.className = 'mt-4 p-4 rounded-lg';
            const form = document.getElementById('uploadForm');
            if (form) {
                form.appendChild(statusElement);
            }
        }
        
        // Definir classe e mensagem com base no status
        statusElement.textContent = message;
        
        switch (status) {
            case 'processing':
                statusElement.className = 'mt-4 p-4 rounded-lg bg-yellow-100 text-yellow-800';
                break;
            case 'success':
                statusElement.className = 'mt-4 p-4 rounded-lg bg-green-100 text-green-800';
                break;
            case 'error':
                statusElement.className = 'mt-4 p-4 rounded-lg bg-red-100 text-red-800';
                break;
            default:
                statusElement.className = 'mt-4 p-4 rounded-lg bg-gray-100 text-gray-800';
        }
    }

    async refreshReportsList() {
        // Em implementação real, isso atualizaria a lista de relatórios
        console.log('Lista de relatórios atualizada');
    }
}

// Inicializar quando o DOM estiver pronto
let adminDashboard;
document.addEventListener('DOMContentLoaded', () => {
    adminDashboard = new AdminDashboard();
});
```

### 3.4 Fase 4: Backend para Upload de Arquivos

#### 3.4.1 Atualização do Controller de Relatórios
Adicionar método para upload no `ReportController.php`:

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    private $ftpService;
    
    public function __construct()
    {
        $this->ftpService = new FtpService(
            env('FTP_HOST'),
            env('FTP_USERNAME'),
            env('FTP_PASSWORD'),
            env('FTP_PORT', 21)
        );
    }
    
    // ... métodos anteriores ...

    public function store(Request $request)
    {
        try {
            // Validar requisição
            $validator = Validator::make($request->all(), [
                'file' => 'required|file|mimes:pdf|max:10240', // máximo 10MB
                'title' => 'required|string|max:255',
                'description' => 'nullable|string'
            ]);
            
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], 400);
            }
            
            $file = $request->file('file');
            $title = $request->input('title');
            $description = $request->input('description');
            
            // Conectar ao FTP
            $this->ftpService->connect();
            
            // Gerar nome único para o arquivo
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $fileName = $originalName . '_' . time() . '.' . $extension;
            
            // Caminho temporário local
            $localFile = $file->getPathname();
            
            // Caminho remoto no FTP
            $remoteFile = env('FTP_REPORTS_PATH') . $fileName;
            
            // Enviar arquivo para o FTP
            if ($this->ftpService->uploadFile($localFile, $remoteFile)) {
                $this->ftpService->close();
                
                // Retornar sucesso
                return response()->json([
                    'message' => 'Arquivo enviado com sucesso',
                    'file' => $fileName
                ], 201);
            } else {
                $this->ftpService->close();
                return response()->json(['error' => 'Não foi possível enviar o arquivo para o servidor FTP'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    // ... métodos anteriores ...
}
```

#### 3.4.2 Atualização das Rotas
Atualizar `routes/api.php`:

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReportController;

Route::middleware('api')->prefix('reports')->group(function () {
    Route::get('/', [ReportController::class, 'index']);
    Route::post('/', [ReportController::class, 'store']);
    Route::get('/{filename}/download', [ReportController::class, 'download']);
});
```

## 4. Considerações de Segurança

1. **Autenticação**: Garantir que apenas usuários autenticados possam acessar a API de relatórios
2. **Validação**: Validar todos os inputs no backend para evitar uploads maliciosos
3. **Permissões**: Configurar permissões adequadas no servidor FTP
4. **HTTPS**: Usar HTTPS para todas as comunicações
5. **Rate Limiting**: Implementar limites de requisições para prevenir abuso

## 5. Considerações de Performance

1. **Caching**: Implementar caching para a listagem de arquivos do FTP
2. **Paginação**: Para muitos arquivos, implementar paginação na API
3. **Otimização de Conexão**: Reutilizar conexões FTP quando possível
4. **Tratamento de Erros**: Implementar retries com backoff exponencial para operações FTP

## 6. Próximos Passos

1. Implementar autenticação JWT para proteger as rotas da API
2. Adicionar funcionalidade de deleção de relatórios
3. Implementar busca e filtragem de relatórios
4. Adicionar metadados aos relatórios (categorias, descrições, etc.)
5. Implementar histórico de uploads
6. Adicionar notificações de sucesso/erro mais robustas