// Lógica para os relatórios em PDF
class ReportsManager {
    constructor() {
        this.reportsContainer = document.getElementById('reports-container');
        this.init();
    }

    init() {
        // Carregar relatórios iniciais
        this.loadReports();
    }

    async loadReports() {
        try {
            // Simular chamada à API para obter relatórios do FTP
            // Em implementação real, substituir por fetch real
            await new Promise(resolve => setTimeout(resolve, 800));
            
            // Dados mockados para teste
            const reports = [
                {
                    id: 1,
                    title: 'Relatório de Produção - Semestre 1',
                    date: '2024-07-15',
                    description: 'Análise completa da produção cafeeira no Rio de Janeiro no primeiro semestre de 2024.',
                    fileName: 'relatorio-producao-semestre1-2024.pdf'
                },
                {
                    id: 2,
                    title: 'Análise de Mercado - Trimestral',
                    date: '2024-07-10',
                    description: 'Relatório detalhado sobre as tendências do mercado cafeeiro nacional e internacional.',
                    fileName: 'analise-mercado-trimestral-Q2-2024.pdf'
                },
                {
                    id: 3,
                    title: 'Sustentabilidade na Cafeicultura',
                    date: '2024-07-05',
                    description: 'Estudo sobre práticas sustentáveis adotadas pelos produtores do Rio de Janeiro.',
                    fileName: 'sustentabilidade-cafeicultura-RJ-2024.pdf'
                }
            ];
            
            // Limpar container
            if (this.reportsContainer) {
                // Remover os exemplos estáticos, mantendo apenas os carregados dinamicamente
                this.reportsContainer.innerHTML = '';
                
                // Adicionar relatórios ao container
                reports.forEach(report => {
                    const card = this.createReportCard(report);
                    this.reportsContainer.appendChild(card);
                });
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
        
        article.innerHTML = `
            <div class="h-48 bg-gradient-to-br ${randomGradient} relative flex items-center justify-center">
                <span class="material-icons text-white text-6xl">picture_as_pdf</span>
            </div>
            <div class="p-6">
                <div class="text-sm text-[#6B4423] mb-2">${this.formatDate(report.date)}</div>
                <h3 class="text-xl font-bold text-[#6B4423] mb-3">${report.title}</h3>
                <p class="text-[#8B2635] mb-4">${report.description}</p>
                <button class="w-full bg-[#8B2635] text-white py-2 px-4 rounded-lg hover:bg-[#992D3D] transition-colors duration-300 font-medium flex items-center justify-center download-report" data-file="${report.fileName}">
                    <span class="material-icons mr-2">download</span> Baixar PDF
                </button>
            </div>
        `;
        
        // Adicionar evento de clique para download
        const downloadButton = article.querySelector('.download-report');
        if (downloadButton) {
            downloadButton.addEventListener('click', (e) => {
                e.preventDefault();
                this.downloadReport(report.fileName);
            });
        }
        
        return article;
    }

    formatDate(dateString) {
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        return new Date(dateString).toLocaleDateString('pt-BR', options);
    }

    downloadReport(fileName) {
        // Simular download do arquivo
        // Em implementação real, isso faria o download do arquivo do FTP
        alert(`Em uma implementação real, o arquivo ${fileName} seria baixado do servidor FTP.`);
        
        // Exemplo de como seria feito o download real:
        // const link = document.createElement('a');
        // link.href = `ftp://servidor.com/caminho/para/arquivos/${fileName}`;
        // link.download = fileName;
        // link.click();
    }

    showError(message) {
        if (this.reportsContainer) {
            this.reportsContainer.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <span class="material-icons text-5xl text-[#8B2635] mb-4">error</span>
                    <p class="text-[#6B4423] text-xl">${message}</p>
                </div>
            `;
        }
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    new ReportsManager();
});