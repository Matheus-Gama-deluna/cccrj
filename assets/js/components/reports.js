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