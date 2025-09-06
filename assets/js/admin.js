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
            
            // Simular envio para FTP
            // Em implementação real, substituir por chamada real ao FTP
            await new Promise(resolve => setTimeout(resolve, 3000));
            
            // Simular sucesso ou erro aleatório
            const success = Math.random() > 0.2; // 80% de chance de sucesso
            
            if (success) {
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
                throw new Error('Falha ao enviar arquivo para o servidor FTP.');
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
document.addEventListener('DOMContentLoaded', () => {
    new AdminDashboard();
});