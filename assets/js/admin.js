// Lógica para a área administrativa
class AdminDashboard {
    constructor() {
        this.uploadEndpoint = 'api/upload_report.php';
        this.reportsEndpoint = 'api/reports/list.php';
        this.dashboardState = {
            reports: [],
            boletins: []
        };
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

        const documentTypeInputs = document.querySelectorAll('input[name="documentType"]');
        if (documentTypeInputs && documentTypeInputs.length) {
            documentTypeInputs.forEach((input) => {
                input.addEventListener('change', () => this.updateUploadHelper());
            });
        }

        // Garantir mensagem inicial coerente com o tipo pré-selecionado
        this.updateUploadHelper();

        const refreshButton = document.getElementById('refreshDocuments');
        if (refreshButton) {
            refreshButton.addEventListener('click', (e) => {
                e.preventDefault();
                this.loadDashboardData();
                this.loadRecentDocuments();
            });
        }

        this.loadDashboardData();
        this.loadRecentDocuments();
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
        const documentType = document.querySelector('input[name="documentType"]:checked')?.value || 'relatorio';

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
            formData.append('is_boletim', documentType === 'boletim');

            // Enviar arquivo para o servidor
            const response = await fetch(this.uploadEndpoint, {
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
                this.loadDashboardData();
                this.loadRecentDocuments();
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

    async loadDashboardData() {
        try {
            const [reportsData, boletinsData] = await Promise.all([
                this.fetchDocuments('reports'),
                this.fetchDocuments('boletins')
            ]);

            this.dashboardState.reports = reportsData;
            this.dashboardState.boletins = boletinsData;

            const totalDocumentsCount = reportsData.length + boletinsData.length;
            const uploadsTodayCount = this.countUploadsToday([...reportsData, ...boletinsData]);
            const storageUsage = this.calculateStorageUsage([...reportsData, ...boletinsData]);

            const totalElement = document.getElementById('totalDocumentsCount');
            const todayElement = document.getElementById('uploadsTodayCount');
            const storageElement = document.getElementById('storageUsage');

            if (totalElement) totalElement.textContent = totalDocumentsCount.toString().padStart(2, '0');
            if (todayElement) todayElement.textContent = uploadsTodayCount.toString().padStart(2, '0');
            if (storageElement) storageElement.textContent = storageUsage;

            this.updateUploadHelper();
        } catch (error) {
            console.error('Erro ao carregar métricas do dashboard:', error);
            this.showUploadStatus('Erro ao atualizar métricas. Tente novamente.', 'error');
        }
    }

    async loadRecentDocuments() {
        const tableBody = document.getElementById('recentReportsBody');
        if (!tableBody) return;

        try {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">Carregando documentos...</td>
                </tr>
            `;

            const allDocuments = [...this.dashboardState.reports, ...this.dashboardState.boletins]
                .map((doc) => this.normalizeDocument(doc))
                .sort((a, b) => new Date(b.modified) - new Date(a.modified));

            if (!allDocuments.length) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">Nenhum documento enviado até o momento.</td>
                    </tr>
                `;
                return;
            }

            const latestDocuments = allDocuments.slice(0, 6);
            tableBody.innerHTML = latestDocuments.map((doc) => `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-[#6B4423]">${doc.title}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-[#8B2635]">${doc.typeLabel}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${doc.formattedDate}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-[#8B2635] flex gap-3">
                        <a href="${doc.downloadUrl}" target="_blank" rel="noopener noreferrer" class="hover:text-[#992D3D] inline-flex items-center gap-1">
                            <span class="material-icons text-base">download</span>
                            Baixar
                        </a>
                    </td>
                </tr>
            `).join('');
        } catch (error) {
            console.error('Erro ao carregar documentos recentes:', error);
            tableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-sm text-red-600">Erro ao carregar documentos. Tente novamente.</td>
                </tr>
            `;
        }
    }

    async fetchDocuments(type) {
        try {
            const response = await fetch(`${this.reportsEndpoint}?type=${type}&per_page=50`);
            if (!response.ok) {
                throw new Error(`Erro ${response.status}`);
            }

            const contentType = response.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) {
                throw new Error('Resposta inválida do servidor');
            }

            const data = await response.json();
            if (!data.success || !Array.isArray(data.data)) {
                throw new Error('Estrutura de dados inesperada na resposta');
            }

            return data.data.map((doc) => ({ ...doc, _type: type }));
        } catch (error) {
            console.error(`Erro ao buscar documentos do tipo ${type}:`, error);
            return [];
        }
    }

    countUploadsToday(documents) {
        const now = new Date();
        const dayAgo = new Date(now.getTime() - 24 * 60 * 60 * 1000);

        return documents.filter((doc) => {
            const modifiedDate = new Date(doc.modified || doc.uploaded_at || 0);
            return modifiedDate >= dayAgo && modifiedDate <= now;
        }).length;
    }

    calculateStorageUsage(documents) {
        const totalBytes = documents.reduce((acc, doc) => acc + (Number(doc.size) || 0), 0);
        return this.formatBytes(totalBytes);
    }

    normalizeDocument(doc) {
        const title = this.formatFileName(doc.name || 'Documento sem nome');
        const typeLabel = doc._type === 'boletins' ? 'Boletim informativo' : 'Relatório técnico';
        const formattedDate = this.formatDate(doc.modified);
        const downloadUrl = doc.url || `api/reports/download.php?file=${encodeURIComponent(doc.name || '')}&type=${doc._type || 'reports'}`;

        return {
            ...doc,
            title,
            typeLabel,
            formattedDate,
            downloadUrl
        };
    }

    updateUploadHelper() {
        const helper = document.getElementById('uploadHelper');
        const selectedType = document.querySelector('input[name="documentType"]:checked')?.value || 'relatorio';

        if (!helper) return;

        const helperMessages = {
            relatorio: 'O relatório ficará disponível na seção "Publicações Técnicas" do site.',
            boletim: 'O boletim será exibido automaticamente na vitrine de boletins para download público.'
        };

        helper.textContent = helperMessages[selectedType] || 'O arquivo será disponibilizado imediatamente na área pública correspondente.';
    }

    formatFileName(name) {
        return name
            .replace(/^.*[\\/]/, '')
            .replace(/\.pdf$/i, '')
            .replace(/[-_]+/g, ' ')
            .replace(/\b\w/g, (char) => char.toUpperCase());
    }

    formatDate(dateString) {
        const date = new Date(dateString);

        if (Number.isNaN(date.getTime())) {
            return 'Data indisponível';
        }

        const day = date.getDate().toString().padStart(2, '0');
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const year = date.getFullYear();

        return `${day}/${month}/${year}`;
    }

    formatBytes(bytes) {
        if (bytes < 1024) return `${bytes} bytes`;
        if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(2)} KB`;
        if (bytes < 1024 * 1024 * 1024) return `${(bytes / (1024 * 1024)).toFixed(2)} MB`;
        return `${(bytes / (1024 * 1024 * 1024)).toFixed(2)} GB`;
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
            method: 'GET'
        });
        
        if (!response.ok) {
            throw new Error(`Erro na requisição: ${response.status}`);
        }
        
        // Check if response is HTML/PHP instead of JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('O servidor não está retornando dados no formato JSON esperado. Verifique se o servidor está configurado para processar arquivos PHP.');
        }
        
        const responseText = await response.text();
        
        // Validate JSON before parsing
        if (responseText.trim().startsWith('<?php') || responseText.trim().startsWith('<')) {
            throw new Error('O servidor está retornando código PHP em vez de dados JSON. Verifique a configuração do servidor.');
        }
        
        const result = JSON.parse(responseText);
        
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
        
        // Check if this is likely a PHP processing issue
        let errorMessage = 'Verifique o console para mais detalhes';
        if (error.message.includes('PHP') || error.message.includes('JSON')) {
            errorMessage = 'Erro de configuração do servidor: O arquivo PHP não está sendo processado corretamente.';
        }
        
        statusDiv.innerHTML = `
            <div class="text-center">
                <span class="material-icons text-5xl text-red-500">error</span>
                <p class="mt-2 text-[#6B4423] font-medium">Erro ao atualizar notícias</p>
                <p class="mt-1 text-gray-600">${errorMessage}</p>
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