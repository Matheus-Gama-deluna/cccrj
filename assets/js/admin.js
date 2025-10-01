// Lógica para a área administrativa
class AdminDashboard {
    constructor() {
        this.ftpServer = 'ftp.example.com';
        this.ftpUsername = 'reports_user';
        this.ftpPassword = 'secure_password';
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
            alert('Por favor, selecione um arquivo para enviar.');
            return;
        }
        
        const file = fileInput.files[0];
        
        // Validar tipo de arquivo
        if (file.type !== 'application/pdf') {
            alert('Por favor, selecione um arquivo PDF.');
            return;
        }
        
        // Validar tamanho do arquivo (máximo 10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert('O arquivo deve ter no máximo 10MB.');
            return;
        }
        
        // Validar campos obrigatórios
        if (!reportTitle) {
            alert('Por favor, informe o título do relatório.');
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
            
            // Enviar arquivo para o servidor
            const response = await fetch('api/upload_report.php', {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) {
                throw new Error(`Erro no upload: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success) {
                this.showUploadStatus('Arquivo enviado com sucesso!', 'success');
                
                // Limpar formulário
                document.getElementById('uploadForm').reset();
                const fileInfo = document.getElementById('fileInfo');
                if (fileInfo) {
                    fileInfo.classList.add('hidden');
                }
                
                // Atualizar lista de relatórios
                this.refreshReportsList();
            } else {
                throw new Error(result.message || 'Falha no upload do arquivo');
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

// Função para atualizar notícias
async function updateNews() {
    const updateBtn = document.getElementById('updateNewsBtn');
    const statusDiv = document.getElementById('updateNewsStatus');
    
    if (!updateBtn || !statusDiv) return;
    
    updateBtn.disabled = true;
    updateBtn.textContent = 'Atualizando...';
    statusDiv.innerHTML = '<div class="text-center"><div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-[#8B2635]"></div><p class="mt-2 text-[#6B4423]">Buscando notícias...</p></div>';
    statusDiv.classList.remove('hidden');
    
    try {
        const response = await fetch('api/news_scraper.php?force_update=1', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        });
        
        if (!response.ok) {
            throw new Error(`Erro na requisição: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.updated) {
            statusDiv.innerHTML = `
                <div class="text-center">
                    <span class="material-icons text-5xl text-green-500">check_circle</span>
                    <p class="mt-2 text-[#6B4423] font-medium">Notícias atualizadas com sucesso!</p>
                    <p class="mt-1 text-gray-600">${result.items} notícias encontradas</p>
                </div>
            `;
            statusDiv.classList.remove('bg-red-100', 'text-red-800');
            statusDiv.classList.add('bg-green-100', 'text-green-800');
        } else {
            statusDiv.innerHTML = `
                <div class="text-center">
                    <span class="material-icons text-5xl text-red-500">error</span>
                    <p class="mt-2 text-[#6B4423] font-medium">Erro ao atualizar notícias</p>
                    <p class="mt-1 text-gray-600">${result.error || 'Erro desconhecido'}</p>
                </div>
            `;
            statusDiv.classList.remove('bg-green-100', 'text-green-800');
            statusDiv.classList.add('bg-red-100', 'text-red-800');
        }
    } catch (error) {
        console.error('Erro ao atualizar notícias:', error);
        statusDiv.innerHTML = `
            <div class="text-center">
                <span class="material-icons text-5xl text-red-500">error</span>
                <p class="mt-2 text-[#6B4423] font-medium">Erro ao atualizar notícias</p>
                <p class="mt-1 text-gray-600">Verifique o console para mais detalhes</p>
            </div>
        `;
        statusDiv.classList.remove('bg-green-100', 'text-green-800');
        statusDiv.classList.add('bg-red-100', 'text-red-800');
    } finally {
        updateBtn.disabled = false;
        updateBtn.textContent = 'Atualizar Notícias Agora';
    }
}

// Adicionar evento ao botão de atualização de notícias
document.addEventListener('DOMContentLoaded', function() {
    const updateNewsBtn = document.getElementById('updateNewsBtn');
    if (updateNewsBtn) {
        updateNewsBtn.addEventListener('click', updateNews);
    }
    
    new AdminDashboard();
});