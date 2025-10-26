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

            // Remove todas as classes de estado
            button.className = button.className.replace(/\breports-toggle\b/g, '').trim();
            button.className = button.className.replace(/\bactive-view\b/g, '').trim();
            button.className = button.className.replace(/\btransform\b/g, '').trim();
            button.className = button.className.replace(/hover:-translate-y-1/g, '').trim();

            if (isActive) {
                button.className += ' reports-toggle active-view bg-[#8B2635] text-white py-3 px-6 rounded-xl transition-all duration-300 shadow-lg transform -translate-y-1';
                button.className = button.className.replace(/bg-white/g, '');
                button.className = button.className.replace(/text-\[#8B2635\]/g, '');
                button.className = button.className.replace(/border-2/g, '');
                button.className = button.className.replace(/border-\[#8B2635\]/g, '');
                button.className = button.className.replace(/hover:bg-\[#F5F0E8\]/g, '');
            } else {
                button.className += ' reports-toggle bg-white text-[#8B2635] py-3 px-6 rounded-xl border-2 border-[#8B2635] hover:bg-[#F5F0E8] transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-1';
                button.className = button.className.replace(/bg-\[#8B2635\]/g, '');
                button.className = button.className.replace(/text-white/g, '');
            }
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
                <div id="reports-list-wrapper" class="bg-white rounded-2xl shadow-lg border border-[#F5F0E8] overflow-hidden">
                    <div class="bg-gradient-to-r from-[#8B2635] to-[#992D3D] text-white px-6 py-4">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="bg-white/20 p-2 rounded-lg">
                                    <span class="material-icons text-xl">list</span>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold">Todos os boletins</h3>
                                    <p class="text-white/90 text-sm">${this.listMeta.total} arquivos cadastrados</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="text-right text-sm text-white/90">
                                    <div class="flex items-center gap-1">
                                        <span class="material-icons text-sm">info</span>
                                        Página ${this.listMeta.page} de ${this.listMeta.total_pages}
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button class="bg-white/20 hover:bg-white/30 p-2 rounded-lg transition-colors" title="Atualizar lista">
                                        <span class="material-icons text-sm">refresh</span>
                                    </button>
                                    <button class="bg-white/20 hover:bg-white/30 p-2 rounded-lg transition-colors" title="Filtros">
                                        <span class="material-icons text-sm">filter_list</span>
                                    </button>
                                </div>
                            </div>
                        </div>
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
            row.className = 'flex flex-col md:flex-row md:items-center md:justify-between px-6 py-4 hover:bg-[#FFF9F5] transition-colors duration-300 border-b border-gray-100';

            // Determinar badge baseado no título ou data
            const getBadge = () => {
                const title = report.title.toLowerCase();
                const daysSincePublished = report.hasDateInName ?
                    Math.floor((new Date() - new Date(report.date)) / (1000 * 60 * 60 * 24)) : 999;

                if (daysSincePublished <= 7) return { text: 'Novo', color: 'bg-green-500', icon: 'fiber_new' };
                if (title.includes('análise') || title.includes('mercado') || title.includes('relatório')) return { text: 'Análise', color: 'bg-blue-500', icon: 'trending_up' };
                if (title.includes('especial') || title.includes('destaque')) return { text: 'Destaque', color: 'bg-purple-500', icon: 'star' };
                return { text: 'Boletim', color: 'bg-gray-500', icon: 'description' };
            };

            const badge = getBadge();

            row.innerHTML = `
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 mt-1">
                        <span class="material-icons text-[#8B2635] text-xl">picture_as_pdf</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <h4 class="text-[#6B4423] font-semibold text-lg">${report.title}</h4>
                            <span class="${badge.color} text-white text-xs px-2 py-1 rounded-full flex items-center gap-1">
                                <span class="material-icons text-xs">${badge.icon}</span>
                                ${badge.text}
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-4 text-sm text-[#8B2635]">
                            <span class="flex items-center gap-1">
                                <span class="material-icons text-sm">event</span>
                                ${report.formattedDate}
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="material-icons text-sm">data_usage</span>
                                ${report.formattedSize}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 md:mt-0 flex gap-2">
                    <button class="preview-btn bg-white text-[#8B2635] border border-[#8B2635] rounded-lg px-4 py-2 flex items-center gap-2 hover:bg-[#F5F0E8] transition-all duration-300 hover:shadow-md">
                        <span class="material-icons text-sm">visibility</span>
                        Ver
                    </button>
                    <button class="download-report bg-[#8B2635] text-white rounded-lg px-4 py-2 flex items-center gap-2 hover:bg-[#992D3D] transition-all duration-300 hover:shadow-md">
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
            <div class="text-center">
                <button id="reports-see-older" class="inline-flex items-center gap-3 bg-gradient-to-r from-[#6B4423] to-[#8B2635] text-white px-8 py-4 rounded-2xl hover:from-[#5A3720] hover:to-[#6B1E2A] transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-2">
                    <span class="material-icons">history</span>
                    <span class="font-semibold">Ver boletins mais antigos</span>
                    <span class="material-icons ml-2">arrow_forward</span>
                </button>
                <p class="text-sm text-[#6B4423] mt-3 opacity-75">Explore nosso arquivo completo de publicações</p>
            </div>
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
            this.loadMoreContainer.innerHTML = `
                <div class="text-center py-8">
                    <div class="bg-gradient-to-r from-[#F5F0E8] to-white rounded-2xl p-6 border border-[#F5F0E8]">
                        <span class="material-icons text-4xl text-[#8B2635] mb-3">check_circle</span>
                        <p class="text-[#6B4423] font-semibold mb-1">Todos os boletins carregados!</p>
                        <p class="text-[#8B2635] text-sm">Você visualizou todos os ${this.listMeta.total} boletins disponíveis.</p>
                    </div>
                </div>
            `;
            return;
        }

        this.loadMoreContainer.innerHTML = `
            <div class="text-center">
                <button id="reports-load-more" class="inline-flex items-center gap-3 bg-gradient-to-r from-[#8B2635] to-[#992D3D] text-white px-8 py-4 rounded-2xl hover:from-[#6B1E2A] hover:to-[#7A252C] transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-2">
                    <span class="material-icons">unfold_more</span>
                    <span class="font-semibold">Carregar mais boletins</span>
                    <span class="bg-white/20 px-2 py-1 rounded-full text-xs">${this.listMeta.total - (this.listMeta.page * this.listPerPage)} restantes</span>
                </button>
                <p class="text-sm text-[#6B4423] mt-3 opacity-75">Página ${this.listMeta.page} de ${this.listMeta.total_pages}</p>
            </div>
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
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-[#F5F0E8]">
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="flex-1">
                        <label class="block text-[#6B4423] font-semibold mb-2" for="reports-year-select">
                            <span class="material-icons mr-1 align-middle text-[#8B2635]">calendar_today</span>
                            Ano
                        </label>
                        <select id="reports-year-select" class="w-full p-3 border-2 border-[#8B2635] rounded-xl text-[#6B4423] focus:outline-none focus:ring-2 focus:ring-[#8B2635] bg-white">
                            <option value="">Selecione um ano...</option>
                            ${yearOptions}
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-[#6B4423] font-semibold mb-2" for="reports-month-select">
                            <span class="material-icons mr-1 align-middle text-[#8B2635]">date_range</span>
                            Mês
                        </label>
                        <select id="reports-month-select" class="w-full p-3 border-2 border-[#8B2635] rounded-xl text-[#6B4423] focus:outline-none focus:ring-2 focus:ring-[#8B2635] bg-white ${months.length ? '' : 'bg-gray-100'}" ${months.length ? '' : 'disabled'}>
                            <option value="">${months.length ? 'Selecione um mês...' : 'Selecione um ano primeiro'}</option>
                            ${monthOptions}
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button id="reports-clear-archive" class="bg-gray-100 text-[#8B2635] px-4 py-3 rounded-xl hover:bg-gray-200 transition-all duration-300 shadow-md ${this.activeYear || this.activeMonthDir ? '' : 'hidden'}">
                            <span class="material-icons">clear</span>
                        </button>
                        <button class="bg-[#8B2635] text-white px-4 py-3 rounded-xl hover:bg-[#992D3D] transition-all duration-300 shadow-md" title="Atualizar">
                            <span class="material-icons">refresh</span>
                        </button>
                    </div>
                </div>
            </div>
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
        article.className = 'bg-gradient-to-br from-white to-[#F5F0E8] rounded-2xl shadow-lg reports-card-enhanced border border-[#F5F0E8] overflow-hidden flex flex-col relative reports-fade-in';

        // Determinar badge baseado no título ou data
        const getBadge = () => {
            const title = report.title.toLowerCase();
            const daysSincePublished = report.hasDateInName ?
                Math.floor((new Date() - new Date(report.date)) / (1000 * 60 * 60 * 24)) : 999;

            if (daysSincePublished <= 7) return {
                text: 'Novo',
                color: 'reports-badge-new',
                icon: 'fiber_new'
            };
            if (title.includes('análise') || title.includes('mercado') || title.includes('relatório')) return {
                text: 'Análise',
                color: 'reports-badge-analysis',
                icon: 'trending_up'
            };
            if (title.includes('especial') || title.includes('destaque')) return {
                text: 'Destaque',
                color: 'reports-badge-highlight',
                icon: 'star'
            };
            return {
                text: 'Boletim',
                color: 'reports-badge-default',
                icon: 'description'
            };
        };

        const badge = getBadge();

        article.innerHTML = `
            <div class="absolute top-4 right-4 z-10">
                <span class="${badge.color} text-white text-xs px-3 py-1.5 rounded-full flex items-center gap-1 shadow-lg font-medium">
                    <span class="material-icons text-sm">${badge.icon}</span>
                    ${badge.text}
                </span>
            </div>
            <div class="h-44 bg-gradient-to-br from-[#8B2635] to-[#992D3D] flex items-center justify-center relative overflow-hidden">
                <div class="text-center text-white">
                    <span class="material-icons text-6xl block mb-2 drop-shadow-lg">picture_as_pdf</span>
                    <span class="uppercase tracking-wide text-sm font-bold drop-shadow-md">PDF</span>
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-transparent via-transparent to-black/10"></div>
            </div>
            <div class="p-6 flex flex-col flex-1">
                <h3 class="text-xl font-bold text-[#6B4423] mb-3 leading-tight line-clamp-2">${report.title}</h3>
                <div class="flex items-center gap-2 mb-4">
                    <div class="flex items-center gap-1 text-[#8B2635]">
                        <span class="material-icons text-sm">event</span>
                        <span class="text-sm font-medium">${report.formattedDate}</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="flex items-center gap-1 text-[#8B2635]">
                        <span class="material-icons text-sm">data_usage</span>
                        <span class="text-sm">${report.formattedSize}</span>
                    </div>
                </div>
                <div class="mt-auto flex gap-2">
                    <button class="preview-btn flex-1 bg-white text-[#8B2635] border-2 border-[#8B2635] rounded-xl py-3 px-4 flex items-center justify-center gap-2 hover:bg-[#F5F0E8] transition-all duration-300 hover:shadow-lg font-semibold">
                        <span class="material-icons text-sm">visibility</span>
                        Ver PDF
                    </button>
                    <button class="download-report flex-1 bg-gradient-to-r from-[#8B2635] to-[#992D3D] text-white rounded-xl py-3 px-4 flex items-center justify-center gap-2 hover:from-[#6B1E2A] hover:to-[#7A252C] transition-all duration-300 hover:shadow-lg font-semibold">
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

        this.feedbackElement.innerHTML = `
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-center">
                <div class="flex items-center justify-center gap-2 text-blue-800">
                    <span class="material-icons text-sm">info</span>
                    <span class="font-medium">${message}</span>
                </div>
            </div>
        `;
        this.feedbackElement.classList.remove('hidden');
    }

    toggleArchiveControls(visible) {
        if (!this.archiveControls) return;
        this.archiveControls.classList.toggle('hidden', !visible);
    }

    updateUpdateInfo(meta = null, { prefix = '', suffix = '' } = {}) {
        if (!this.updateInfoElement) return;

        if (!meta && !prefix && !suffix) {
            this.updateInfoElement.innerHTML = '';
            return;
        }

        const parts = [];

        if (prefix) {
            parts.push(`<span class="text-[#6B4423] font-medium">${prefix}</span>`);
        }

        const timestamp = meta?.timestamp || meta?.updated_at;
        if (timestamp) {
            const date = new Date(timestamp);
            if (!Number.isNaN(date.getTime())) {
                const formattedDate = date.toLocaleString('pt-BR');
                parts.push(`<span class="text-[#8B2635]">${formattedDate}</span>`);
            }
        }

        if (suffix) {
            parts.push(`<span class="text-[#6B4423] opacity-75">${suffix}</span>`);
        }

        this.updateInfoElement.innerHTML = `
            <div class="flex items-center justify-center gap-2 text-sm bg-white px-4 py-2 rounded-xl border border-[#F5F0E8] shadow-sm">
                <span class="material-icons text-[#8B2635] text-sm">info</span>
                ${parts.join(' • ')}
            </div>
        `;
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
        const parsedName = this.formatFileNameWithDate(name);
        const dateValue = report.date || report.modified || new Date().toISOString();
        const sizeValue = report.size || 0;

        // Se não há data no nome do arquivo, tenta usar a data do report ou modified
        let finalDate = parsedName.date;
        if (parsedName.date === 'Data não disponível' && report.date) {
            const date = new Date(report.date);
            if (!isNaN(date.getTime())) {
                finalDate = date.toLocaleDateString('pt-BR', {
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                });
            }
        }

        return {
            name,
            title: parsedName.title,
            date: dateValue,
            modified: report.modified || new Date().toISOString(),
            size: sizeValue,
            year: report.year || null,
            month: report.month || null,
            month_number: report.month_number || null,
            relative_path: report.relative_path || null,
            full_path: report.full_path || null,
            url: report.url || null,
            formattedDate: finalDate,
            formattedSize: this.formatSize(sizeValue),
            hasDateInName: parsedName.hasDateInName
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

    formatFileNameWithDate(fileName) {
        // Remove extensão
        const nameWithoutExt = fileName.replace(/\.pdf$/i, '');

        // Padrões de data para identificar no nome do arquivo
        const datePatterns = [
            /(\d{2})[-_](\d{2})[-_](\d{2,4})/,  // DD-MM-YY ou DD-MM-YYYY
            /(\d{2})(\d{2})(\d{2,4})/,           // DDMMYY ou DDMMYYYY
            /(\d{1,2})[-_\/](\d{1,2})[-_\/](\d{2,4})/ // Variações com diferentes separadores
        ];

        for (let pattern of datePatterns) {
            const match = nameWithoutExt.match(pattern);
            if (match) {
                const [, day, month, year] = match;
                const fullYear = year.length === 2 ? '20' + year : year;
                const date = new Date(fullYear, month - 1, day);

                if (!isNaN(date.getTime())) {
                    const formattedDate = date.toLocaleDateString('pt-BR', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });

                    // Remove a parte da data do nome original
                    const cleanName = nameWithoutExt.replace(pattern, '').trim();
                    const finalName = cleanName.replace(/[-_\s]+/g, ' ').trim();

                    return {
                        title: finalName || 'Boletim',
                        date: formattedDate,
                        hasDateInName: true
                    };
                }
            }
        }

        // Fallback: se não encontrou data no nome, retorna o nome formatado e data de modificação
        return {
            title: nameWithoutExt.replace(/[-_]/g, ' ').trim(),
            date: 'Data não disponível',
            hasDateInName: false
        };
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
            <div class="col-span-full text-center py-16">
                <div class="relative">
                    <div class="inline-block animate-spin rounded-full h-16 w-16 border-4 border-[#F5F0E8] border-t-[#8B2635] mb-6"></div>
                    <div class="absolute inset-0 animate-pulse">
                        <div class="inline-block rounded-full h-16 w-16 bg-[#8B2635]/10"></div>
                    </div>
                </div>
                <div class="space-y-2">
                    <p class="text-[#6B4423] text-2xl font-semibold">Carregando boletins...</p>
                    <p class="text-[#8B2635] text-sm">Aguarde enquanto preparamos o conteúdo para você</p>
                </div>
                <div class="mt-6 flex justify-center">
                    <div class="flex space-x-1">
                        <div class="w-2 h-2 bg-[#8B2635] rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-[#8B2635] rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                        <div class="w-2 h-2 bg-[#8B2635] rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                    </div>
                </div>
            </div>
        `;
        this.clearLoadMore();
    }

    showEmptyState() {
        if (!this.reportsContainer) return;

        this.reportsContainer.innerHTML = `
            <div class="col-span-full text-center py-16">
                <div class="bg-gradient-to-br from-[#F5F0E8] to-white rounded-3xl p-8 border border-[#F5F0E8] shadow-lg max-w-md mx-auto">
                    <div class="bg-[#8B2635]/10 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6">
                        <span class="material-icons text-4xl text-[#8B2635]">folder_open</span>
                    </div>
                    <h3 class="text-[#6B4423] text-2xl font-bold mb-3">Nenhum boletim encontrado</h3>
                    <p class="text-[#8B2635] mb-6 leading-relaxed">
                        ${this.currentView === 'archive' ?
                            'Selecione um ano e mês para explorar os boletins arquivados.' :
                            'Ainda não há boletins disponíveis para download nesta seção.'
                        }
                    </p>
                    ${this.currentView === 'latest' ?
                        '<button class="bg-[#8B2635] text-white px-6 py-3 rounded-xl hover:bg-[#992D3D] transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-1" onclick="reportsManager.switchView(\'list\')">Ver todos os boletins</button>' :
                        ''
                    }
                </div>
            </div>
        `;
        this.clearLoadMore();
    }

    showError(message) {
        if (!this.reportsContainer) return;

        this.reportsContainer.innerHTML = `
            <div class="col-span-full text-center py-16">
                <div class="bg-gradient-to-br from-red-50 to-white rounded-3xl p-8 border border-red-100 shadow-lg max-w-md mx-auto">
                    <div class="bg-red-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6">
                        <span class="material-icons text-4xl text-red-600">error_outline</span>
                    </div>
                    <h3 class="text-[#6B4423] text-2xl font-bold mb-3">Erro ao carregar boletins</h3>
                    <p class="text-red-600 mb-6 leading-relaxed">${message}</p>
                    <div class="space-y-3">
                        <button class="w-full bg-[#8B2635] text-white px-6 py-3 rounded-xl hover:bg-[#992D3D] transition-all duration-300 shadow-md hover:shadow-lg transform hover:-translate-y-1" id="reports-retry">
                            <span class="material-icons mr-2">refresh</span>
                            Tentar novamente
                        </button>
                        <button class="w-full bg-white text-[#8B2635] border-2 border-[#8B2635] px-6 py-3 rounded-xl hover:bg-[#F5F0E8] transition-all duration-300" onclick="reportsManager.switchView('list')">
                            <span class="material-icons mr-2">list</span>
                            Ver todos os boletins
                        </button>
                    </div>
                </div>
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
