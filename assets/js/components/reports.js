// Lógica da seção de boletins com múltiplas visualizações
let reportsInitialized = false;

class ReportsManager {
    constructor() {
        this.reportsContainer = document.getElementById('reports-container');
        this.archiveControls = document.getElementById('reports-archive-controls');
        this.feedbackElement = document.getElementById('reports-feedback');
        this.loadMoreContainer = document.getElementById('reports-load-more-container');
        this.updateInfoElement = document.getElementById('reports-update-info');

        this.viewButtons = {
            latest: document.getElementById('reports-latest-btn'),
            list: document.getElementById('reports-list-btn'),
            archive: document.getElementById('reports-archive-btn')
        };

        this.apiUrl = 'api/reports/list.php?type=boletins';
        this.archiveApiUrl = 'api/boletins/';
        this.downloadEndpoint = 'api/reports/download.php';

        this.currentView = 'latest';
        this.isLoading = false;

        this.latestLimit = 10;
        this.latestReports = [];
        this.latestMeta = null;

        this.listPerPage = 12;
        this.listReports = [];
        this.listMeta = { page: 1, total_pages: 1, total: 0 };

        this.archiveYears = [];
        this.archiveMonths = {};
        this.archiveReports = [];
        this.activeYear = null;
        this.activeMonthDir = null;

        this.init();
    }

    init() {
        if (reportsInitialized || !this.reportsContainer) {
            return;
        }

        reportsInitialized = true;
        this.bindControls();
        this.switchView('latest');
    }

    bindControls() {
        Object.entries(this.viewButtons).forEach(([view, button]) => {
            if (!button) return;
            button.addEventListener('click', () => this.switchView(view));
        });
    }

    async switchView(view) {
        if (this.isLoading && this.currentView === view) {
            return;
        }

        this.currentView = view;
        this.highlightActiveButton(view);
        this.clearFeedback();
        this.clearLoadMore();

        if (view === 'archive') {
            this.toggleArchiveControls(true);
            await this.handleArchiveFlow();
            return;
        }

        this.toggleArchiveControls(false);

        if (view === 'latest') {
            if (this.latestReports.length === 0) {
                await this.loadLatestReports();
            } else {
                this.renderCardGrid(this.latestReports, {
                    title: 'Últimos boletins publicados',
                    subtitle: 'Seleção automática dos 10 arquivos mais recentes'
                });
                this.renderOlderButton();
                this.updateUpdateInfo(this.latestMeta, { prefix: 'Atualizado em' });
            }
            return;
        }

        if (view === 'list') {
            if (this.listReports.length === 0) {
                await this.loadListReports({ append: false });
            } else {
                this.renderListView(this.listReports, { append: false });
                this.renderListLoadMore();
                this.updateUpdateInfo(this.listMeta, {
                    prefix: 'Dados atualizados em',
                    suffix: `• Página ${this.listMeta.page} de ${this.listMeta.total_pages}`
                });
            }
        }
    }

    highlightActiveButton(activeView) {
        Object.entries(this.viewButtons).forEach(([view, button]) => {
            if (!button) return;
            const isActive = view === activeView;
            button.classList.toggle('active-view', isActive);
            button.classList.toggle('bg-[#8B2635]', isActive);
            button.classList.toggle('text-white', isActive);
            button.classList.toggle('bg-white', !isActive);
            button.classList.toggle('border', !isActive);
            button.classList.toggle('border-[#8B2635]', !isActive);
        });
    }

    async loadLatestReports() {
        try {
            this.setLoading(true);
            const response = await fetch(`${this.apiUrl}&per_page=${this.latestLimit}&page=1`, {
                headers: {
                    'Cache-Control': 'no-cache',
                    'Pragma': 'no-cache'
                }
            });

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Não foi possível carregar os boletins mais recentes.');
            }

            this.latestReports = this.prepareReports(data.data);
            this.latestMeta = data.meta || null;

            this.renderCardGrid(this.latestReports, {
                title: 'Últimos boletins publicados',
                subtitle: 'Seleção automática dos 10 arquivos mais recentes'
            });
            this.renderOlderButton();
            this.updateUpdateInfo(this.latestMeta, { prefix: 'Atualizado em' });

        } catch (error) {
            this.showError(error.message || 'Erro ao carregar os boletins mais recentes.');
        } finally {
            this.setLoading(false);
        }
    }

    async loadListReports({ append = false } = {}) {
        try {
            this.setLoading(!append);

            const nextPage = append ? this.listMeta.page + 1 : 1;
            const response = await fetch(`${this.apiUrl}&per_page=${this.listPerPage}&page=${nextPage}`, {
                headers: {
                    'Cache-Control': 'no-cache',
                    'Pragma': 'no-cache'
                }
            });

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Erro ao carregar boletins.');
            }

            const reports = data.data || [];

            const combinedReports = append
                ? [...this.listReports, ...reports]
                : reports;

            this.listReports = this.prepareReports(combinedReports);

            this.listMeta = {
                page: data.meta?.page || nextPage,
                total_pages: data.meta?.total_pages || nextPage,
                total: data.meta?.total || this.listReports.length,
                timestamp: data.meta?.timestamp || new Date().toISOString()
            };

            this.renderListView(this.listReports, { append });
            this.renderListLoadMore();
            this.updateUpdateInfo(this.listMeta, {
                prefix: 'Dados atualizados em',
                suffix: `• Página ${this.listMeta.page} de ${this.listMeta.total_pages}`
            });

        } catch (error) {
            this.showError(error.message || 'Erro ao carregar boletins.');
        } finally {
            this.setLoading(false);
        }
    }

    async handleArchiveFlow() {
        if (this.archiveYears.length === 0) {
            try {
                this.setLoading(true);
                const response = await fetch(`${this.archiveApiUrl}list_by_year.php?type=boletins`);
                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || 'Não foi possível carregar a lista de anos.');
                }

                this.archiveYears = data.data || [];
            } catch (error) {
                this.showError(error.message || 'Erro ao carregar o arquivo de boletins.');
                return;
            } finally {
                this.setLoading(false);
            }
        }

        this.renderArchiveControls();

        if (this.activeYear && this.activeMonthDir) {
            await this.loadArchiveReports(this.activeYear, this.activeMonthDir);
        } else {
            this.reportsContainer.innerHTML = '';
            this.setFeedback('Selecione um ano para consultar os boletins arquivados.');
            this.updateUpdateInfo();
        }
    }

    async ensureArchiveMonths(year) {
        if (this.archiveMonths[year]) {
            return;
        }

        this.setFeedback('Carregando meses disponíveis...');
        const response = await fetch(`${this.archiveApiUrl}list_by_month.php?type=boletins&year=${year}`);
        const data = await response.json();

        if (!data.success) {
            throw new Error(data.message || 'Erro ao carregar meses disponíveis.');
        }

        this.archiveMonths[year] = data.data || [];
    }

    async loadArchiveReports(year, monthDir) {
        try {
            this.setLoading(true);
            const response = await fetch(`${this.archiveApiUrl}list_by_date.php?type=boletins&year=${year}&month=${monthDir}`);
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Erro ao carregar boletins do período selecionado.');
            }

            this.archiveReports = this.prepareReports(data.data);
            this.renderCardGrid(this.archiveReports, {
                title: `Boletins de ${monthDir.replace('_', ' ')} de ${year}`,
                subtitle: `${data.total || 0} arquivos disponíveis`
            });

            this.updateUpdateInfo(null, {
                prefix: `Arquivo: ${monthDir.replace('_', ' ')} / ${year}`
            });
            this.clearFeedback();
        } catch (error) {
            this.showError(error.message || 'Erro ao carregar os boletins arquivados.');
        } finally {
            this.setLoading(false);
        }
    }

    renderCardGrid(reports, { title = '', subtitle = '' } = {}) {
        this.applyContainerLayout('grid');

        if (!reports || reports.length === 0) {
            this.showEmptyState();
            return;
        }

        this.reportsContainer.innerHTML = '';

        if (title || subtitle) {
            const header = document.createElement('div');
            header.className = 'col-span-full mb-4 text-left';
            header.innerHTML = `
                ${title ? `<h3 class="text-2xl font-semibold text-[#6B4423]">${title}</h3>` : ''}
                ${subtitle ? `<p class="text-[#8B2635] mt-1">${subtitle}</p>` : ''}
            `;
            this.reportsContainer.appendChild(header);
        }

        reports.forEach((report) => {
            const card = this.createReportCard(report);
            this.reportsContainer.appendChild(card);
        });
    }

    renderListView(reports, { append = false } = {}) {
        this.applyContainerLayout('list');

        if (!append) {
            this.reportsContainer.innerHTML = `
                <div id="reports-list-wrapper" class="bg-white/80 rounded-2xl shadow-lg border border-[#F5F0E8] overflow-hidden">
                    <div class="bg-[#8B2635] text-white px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                        <div>
                            <h3 class="text-xl font-semibold">Todos os boletins</h3>
                            <p class="text-sm text-white/80">${this.listMeta.total} arquivos cadastrados</p>
                        </div>
                        <div class="text-sm text-white/80">Página ${this.listMeta.page} de ${this.listMeta.total_pages}</div>
                    </div>
                    <div class="divide-y divide-[#F5F0E8]" id="reports-list-items"></div>
                </div>
            `;
        }

        const listWrapper = document.getElementById('reports-list-items');
        if (!listWrapper) return;

        const startIndex = append ? (this.listMeta.page - 1) * this.listPerPage : 0;
        const newItems = reports.slice(startIndex);

        const fragment = document.createDocumentFragment();

        newItems.forEach((report) => {
            const row = document.createElement('div');
            row.className = 'flex flex-col md:flex-row md:items-center md:justify-between px-6 py-4 hover:bg-[#FFF9F5] transition-colors duration-300';
            row.innerHTML = `
                <div class="flex items-start gap-4">
                    <span class="material-icons text-[#8B2635] mt-1">picture_as_pdf</span>
                    <div>
                        <h4 class="text-[#6B4423] font-semibold">${report.title}</h4>
                        <p class="text-sm text-[#8B2635]">${report.formattedDate} • ${report.formattedSize}</p>
                    </div>
                </div>
                <div class="mt-4 md:mt-0 flex gap-2">
                    <button class="preview-btn bg-white text-[#8B2635] border border-[#8B2635] rounded-lg px-4 py-2 flex items-center gap-2 hover:bg-[#F5F0E8] transition-colors duration-300">
                        <span class="material-icons text-sm">visibility</span>
                        Ver
                    </button>
                    <button class="download-report bg-[#8B2635] text-white rounded-lg px-4 py-2 flex items-center gap-2 hover:bg-[#992D3D] transition-colors duração-300">
                        <span class="material-icons text-sm">download</span>
                        Baixar
                    </button>
                </div>
            `;

            const previewBtn = row.querySelector('.preview-btn');
            const downloadBtn = row.querySelector('.download-report');

            if (previewBtn) {
                previewBtn.addEventListener('click', (event) => {
                    event.preventDefault();
                    if (typeof pdfPreview?.show === 'function') {
                        pdfPreview.show(report.name, report.title, 'boletins');
                    }
                });
            }

            if (downloadBtn) {
                downloadBtn.addEventListener('click', (event) => {
                    event.preventDefault();
                    this.downloadReport(report.name);
                });
            }

            fragment.appendChild(row);
        });

        listWrapper.appendChild(fragment);
    }

    renderOlderButton() {
        if (!this.loadMoreContainer) return;

        this.loadMoreContainer.innerHTML = `
            <button id="reports-see-older" class="inline-flex items-center gap-2 bg-white text-[#8B2635] border border-[#8B2635] px-6 py-3 rounded-lg hover:bg-[#F5F0E8] transition-colors duração-300">
                <span class="material-icons text-sm">history</span>
                Ver boletins mais antigos
            </button>
        `;

        const button = document.getElementById('reports-see-older');
        if (button) {
            button.addEventListener('click', () => this.switchView('list'));
        }
    }

    renderListLoadMore() {
        if (!this.loadMoreContainer) return;

        const hasMore = this.listMeta.page < this.listMeta.total_pages;
        if (!hasMore) {
            this.loadMoreContainer.innerHTML = '';
            return;
        }

        this.loadMoreContainer.innerHTML = `
            <button id="reports-load-more" class="inline-flex items-center gap-2 bg-[#8B2635] text-white px-6 py-3 rounded-lg hover:bg-[#992D3D] transition-colors duração-300">
                <span class="material-icons text-sm">unfold_more</span>
                Carregar mais boletins
            </button>
        `;

        const button = document.getElementById('reports-load-more');
        if (button) {
            button.addEventListener('click', () => this.loadListReports({ append: true }));
        }
    }

    renderArchiveControls() {
        if (!this.archiveControls) return;

        const yearOptions = this.archiveYears.map((year) => `
            <option value="${year}" ${this.activeYear === year ? 'selected' : ''}>${year}</option>
        `).join('');

        const months = this.activeYear ? (this.archiveMonths[this.activeYear] || []) : [];
        const monthOptions = months.map((month) => `
            <option value="${month.month_dir}" ${this.activeMonthDir === month.month_dir ? 'selected' : ''}>${month.month_name}</option>
        `).join('');

        this.archiveControls.innerHTML = `
            <label class="sr-only" for="reports-year-select">Ano</label>
            <select id="reports-year-select" class="border border-[#8B2635] text-[#6B4423] rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#8B2635]">
                <option value="">Selecione um ano...</option>
                ${yearOptions}
            </select>
            <label class="sr-only" for="reports-month-select">Mês</label>
            <select id="reports-month-select" class="border border-[#8B2635] text-[#6B4423] rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#8B2635]" ${months.length ? '' : 'disabled'}>
                <option value="">${months.length ? 'Selecione um mês...' : 'Selecione um ano primeiro'}</option>
                ${monthOptions}
            </select>
            <button id="reports-clear-archive" class="bg-gray-100 text-[#8B2635] px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors duração-300 ${this.activeYear || this.activeMonthDir ? '' : 'hidden'}">
                Limpar seleção
            </button>
        `;

        const yearSelect = document.getElementById('reports-year-select');
        const monthSelect = document.getElementById('reports-month-select');
        const clearButton = document.getElementById('reports-clear-archive');

        if (yearSelect) {
            yearSelect.addEventListener('change', async (event) => {
                const year = event.target.value || null;
                this.activeYear = year;
                this.activeMonthDir = null;
                this.archiveReports = [];

                if (year) {
                    await this.ensureArchiveMonths(year);
                    this.renderArchiveControls();
                    if (this.archiveMonths[year].length === 0) {
                        this.reportsContainer.innerHTML = '';
                        this.setFeedback(`Ainda não há boletins cadastrados para ${year}.`);
                    } else {
                        this.reportsContainer.innerHTML = '';
                        this.setFeedback('Selecione um mês para visualizar os boletins.');
                    }
                } else {
                    this.renderArchiveControls();
                    this.reportsContainer.innerHTML = '';
                    this.setFeedback('Selecione um ano para explorar os boletins arquivados.');
                    this.updateUpdateInfo();
                }
            });
        }

        if (monthSelect) {
            monthSelect.addEventListener('change', async (event) => {
                const month = event.target.value || null;
                this.activeMonthDir = month;
                if (this.activeYear && month) {
                    await this.loadArchiveReports(this.activeYear, month);
                } else {
                    this.reportsContainer.innerHTML = '';
                    this.setFeedback('Selecione um mês para visualizar os boletins.');
                }
            });
        }

        if (clearButton) {
            clearButton.addEventListener('click', () => {
                this.activeYear = null;
                this.activeMonthDir = null;
                this.archiveReports = [];
                this.renderArchiveControls();
                this.reportsContainer.innerHTML = '';
                this.setFeedback('Selecione um ano para explorar os boletins arquivados.');
                this.updateUpdateInfo();
            });
        }
    }

    createReportCard(report) {
        const article = document.createElement('article');
        article.className = 'bg-gradient-to-br from-white to-[#F5F0E8] rounded-2xl shadow-lg card-hover border border-[#F5F0E8] overflow-hidden flex flex-col';

        article.innerHTML = `
            <div class="h-44 bg-gradient-to-br from-[#8B2635] to-[#992D3D] flex items-center justify-center">
                <div class="text-center text-white">
                    <span class="material-icons text-6xl block">picture_as_pdf</span>
                    <span class="uppercase tracking-wide text-sm">Boletim</span>
                </div>
            </div>
            <div class="p-6 flex flex-col flex-1">
                <div class="text-sm text-[#6B4423] mb-2">${report.formattedDate}</div>
                <h3 class="text-xl font-bold text-[#6B4423] mb-3">${report.title}</h3>
                <p class="text-[#8B2635] text-sm mb-4">Arquivo em PDF • ${report.formattedSize}</p>
                <div class="mt-auto flex gap-2">
                    <button class="preview-btn flex-1 bg-white text-[#8B2635] border border-[#8B2635] rounded-lg py-2 px-4 flex items-center justify-center gap-2 hover:bg-[#F5F0E8] transition-colors duração-300">
                        <span class="material-icons text-sm">visibility</span>
                        Ver PDF
                    </button>
                    <button class="download-report flex-1 bg-[#8B2635] text-white rounded-lg py-2 px-4 flex items-center justify-center gap-2 hover:bg-[#992D3D] transition-colors duração-300">
                        <span class="material-icons text-sm">download</span>
                        Baixar
                    </button>
                </div>
            </div>
        `;

        const previewBtn = article.querySelector('.preview-btn');
        const downloadBtn = article.querySelector('.download-report');

        if (previewBtn) {
            previewBtn.addEventListener('click', (event) => {
                event.preventDefault();
                if (typeof pdfPreview?.show === 'function') {
                    pdfPreview.show(report.name, report.title, 'boletins');
                }
            });
        }

        if (downloadBtn) {
            downloadBtn.addEventListener('click', (event) => {
                event.preventDefault();
                this.downloadReport(report.name);
            });
        }

        return article;
    }

    downloadReport(fileName) {
        window.open(`${this.downloadEndpoint}?file=${encodeURIComponent(fileName)}&type=boletins`, '_blank');
    }

    applyContainerLayout(layout) {
        if (!this.reportsContainer) return;

        if (layout === 'grid') {
            this.reportsContainer.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6';
        } else {
            this.reportsContainer.className = 'space-y-0';
        }
    }

    setLoading(state) {
        this.isLoading = state;
        if (state) {
            this.showLoading();
        }
    }

    clearLoadMore() {
        if (this.loadMoreContainer) {
            this.loadMoreContainer.innerHTML = '';
        }
    }

    clearFeedback() {
        if (this.feedbackElement) {
            this.feedbackElement.classList.add('hidden');
            this.feedbackElement.textContent = '';
        }
    }

    setFeedback(message) {
        if (!this.feedbackElement) return;

        if (!message) {
            this.clearFeedback();
            return;
        }

        this.feedbackElement.textContent = message;
        this.feedbackElement.classList.remove('hidden');
    }

    toggleArchiveControls(visible) {
        if (!this.archiveControls) return;
        this.archiveControls.classList.toggle('hidden', !visible);
    }

    updateUpdateInfo(meta = null, { prefix = '', suffix = '' } = {}) {
        if (!this.updateInfoElement) return;

        if (!meta && !prefix && !suffix) {
            this.updateInfoElement.textContent = '';
            return;
        }

        const parts = [];

        if (prefix) {
            parts.push(prefix);
        }

        const timestamp = meta?.timestamp || meta?.updated_at;
        if (timestamp) {
            const date = new Date(timestamp);
            if (!Number.isNaN(date.getTime())) {
                parts.push(date.toLocaleString('pt-BR'));
            }
        }

        if (suffix) {
            parts.push(suffix);
        }

        this.updateInfoElement.textContent = parts.join(' ');
    }
    prepareReports(reports) {
        if (!Array.isArray(reports)) {
            return [];
        }

        return reports
            .map(report => this.normalizeReport(report))
            .filter(report => report.date) // Filtrar apenas boletins com data válida
            .sort((a, b) => {
                // Ordenar por data decrescente (mais recentes primeiro)
                const dateA = new Date(a.date);
                const dateB = new Date(b.date);
                return dateB.getTime() - dateA.getTime();
            });
    }

    normalizeReport(report) {
        if (!report || typeof report !== 'object') {
            return null;
        }

        const name = report.name || 'Boletim sem nome.pdf';
        const title = this.formatFileName(name);
        const dateValue = report.date || report.modified || new Date().toISOString();
        const sizeValue = report.size || 0;

        return {
            name,
            title,
            date: dateValue,
            modified: report.modified || new Date().toISOString(),
            size: sizeValue,
            year: report.year || null,
            month: report.month || null,
            month_number: report.month_number || null,
            relative_path: report.relative_path || null,
            full_path: report.full_path || null,
            url: report.url || null,
            formattedDate: this.formatDate(dateValue),
            formattedSize: this.formatSize(sizeValue)
        };
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        if (Number.isNaN(date.getTime())) {
            return 'Data não disponível';
        }
        return date.toLocaleDateString('pt-BR', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
    }

    formatFileName(fileName) {
        return fileName
            .replace(/\.pdf$/i, '')
            .replace(/[-_]/g, ' ')
            .replace(/\s+/g, ' ')
            .trim()
            .replace(/\b\w/g, (letter) => letter.toUpperCase());
    }

    formatSize(size) {
        if (!size || Number.isNaN(size)) {
            return 'tamanho desconhecido';
        }
        const kb = size / 1024;
        if (kb < 1024) {
            return `${Math.round(kb)} KB`;
        }
        return `${(kb / 1024).toFixed(2)} MB`;
    }

    showLoading() {
        if (!this.reportsContainer) return;

        this.reportsContainer.innerHTML = `
            <div class="col-span-full text-center py-12">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-[#8B2635] mb-4"></div>
                <p class="text-[#6B4423] text-xl">Carregando boletins...</p>
            </div>
        `;
        this.clearLoadMore();
    }

    showEmptyState() {
        if (!this.reportsContainer) return;

        this.reportsContainer.innerHTML = `
            <div class="col-span-full text-center py-12">
                <span class="material-icons text-5xl text-[#8B2635] mb-4">folder_open</span>
                <p class="text-[#6B4423] text-xl">Nenhum boletim encontrado</p>
                <p class="text-[#8B2635] mt-2">Ainda não há boletins disponíveis para download.</p>
            </div>
        `;
        this.clearLoadMore();
    }

    showError(message) {
        if (!this.reportsContainer) return;

        this.reportsContainer.innerHTML = `
            <div class="col-span-full text-center py-12">
                <span class="material-icons text-5xl text-[#8B2635] mb-4">error</span>
                <p class="text-[#6B4423] text-xl">${message}</p>
                <button class="mt-4 bg-[#8B2635] text-white py-2 px-6 rounded-lg hover:bg-[#992D3D] transition-colors duração-300" id="reports-retry">
                    Tentar novamente
                </button>
            </div>
        `;
        this.clearLoadMore();

        const retry = document.getElementById('reports-retry');
        if (retry) {
            retry.addEventListener('click', () => this.switchView(this.currentView));
        }
    }
}

// Inicializar quando o DOM estiver pronto
let reportsManager;
document.addEventListener('DOMContentLoaded', () => {
    reportsManager = new ReportsManager();
});
