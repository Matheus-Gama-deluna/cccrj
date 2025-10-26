class PDFPreviewManager {
    constructor() {
        this.modal = document.getElementById('pdf-preview-modal');
        this.frame = document.getElementById('pdf-preview-frame');
        this.title = document.getElementById('pdf-preview-title');
        this.info = document.getElementById('pdf-preview-info');
        this.loading = document.getElementById('pdf-preview-loading');
        this.error = document.getElementById('pdf-preview-error');
        this.status = document.getElementById('pdf-preview-status');
        this.downloadBtn = document.getElementById('btn-download-preview');

        this.currentFile = null;
        this.currentType = 'boletins';
        this.currentPath = '';

        this.init();
    }

    init() {
        if (!this.modal) {
            console.error('PDFPreviewManager: Modal element not found!');
            return;
        }

        // Configurar eventos do modal
        this.modal?.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.close();
            }
        });

        // Fechar com ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !this.modal?.classList.contains('hidden')) {
                this.close();
            }
        });
    }

    show(fileName, title, type = 'boletins', relativePath = '') {
        this.currentFile = fileName;
        this.currentType = type;
        this.currentPath = relativePath || '';

        if (!this.modal) {
            console.error('PDFPreviewManager: Modal not available!');
            return;
        }

        // Configurar modal
        this.title.textContent = title || this.formatFileName(fileName);
        this.info.textContent = `Arquivo: ${fileName}`;
        this.downloadBtn.onclick = () => this.download();

        // Mostrar loading
        this.showLoading();

        // URL direta para o PDF (preview inline)
        const pathQuery = this.currentPath
            ? `&path=${encodeURIComponent(this.currentPath)}`
            : '';
        const pdfUrl = `api/pdf/preview/index.php?file=${encodeURIComponent(fileName)}&type=${encodeURIComponent(type)}${pathQuery}`;

        // Tentar carregar no iframe
        this.frame.onload = () => {
            this.hideLoading();
            this.status.textContent = 'PDF carregado com sucesso';
        };

        this.frame.onerror = () => {
            this.showError();
        };

        // Definir URL do iframe
        this.frame.src = pdfUrl;

        // Mostrar iframe
        this.frame.classList.remove('hidden');
        this.error.classList.add('hidden');

        // Mostrar modal
        this.modal.classList.remove('hidden');

        // Timeout para verificar se carregou
        setTimeout(() => {
            if (this.loading.classList.contains('hidden') === false) {
                this.hideLoading();
                this.status.textContent = 'PDF carregado';
            }
        }, 2000);
    }

    download() {
        if (this.currentFile) {
            const pathQuery = this.currentPath
                ? `&path=${encodeURIComponent(this.currentPath)}`
                : '';
            const downloadUrl = `api/reports/download.php?file=${encodeURIComponent(this.currentFile)}&type=${encodeURIComponent(this.currentType)}${pathQuery}`;
            window.open(downloadUrl, '_blank');
        }
    }

    retry() {
        if (this.currentFile) {
            this.show(this.currentFile, this.title.textContent, this.currentType, this.currentPath);
        }
    }

    showLoading() {
        this.loading.classList.remove('hidden');
        this.error.classList.add('hidden');
    }

    hideLoading() {
        this.loading.classList.add('hidden');
    }

    showError() {
        this.loading.classList.add('hidden');
        this.error.classList.remove('hidden');
        this.frame.classList.add('hidden');
        this.status.textContent = 'Erro ao carregar PDF';
    }

    close() {
        this.modal.classList.add('hidden');
        this.frame.src = '';
        this.currentFile = null;
        this.currentPath = '';
        this.frame.classList.remove('hidden');
        this.error.classList.add('hidden');
    }

    formatFileName(fileName) {
        return fileName.replace('.pdf', '')
                      .replace(/[-_]/g, ' ')
                      .replace(/\b\w/g, l => l.toUpperCase());
    }
}

// Inicializar quando DOM estiver pronto
let pdfPreview;
document.addEventListener('DOMContentLoaded', () => {
    pdfPreview = new PDFPreviewManager();
});
