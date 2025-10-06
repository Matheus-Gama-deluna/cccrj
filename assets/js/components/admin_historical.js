// Componente AdminHistoricalContent em assets/js/components/admin_historical.js
class AdminHistoricalContent {
    constructor() {
        this.apiUrl = '';
        this.contentType = '';
        this.currentItemId = null;
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadContentList();
    }

    bindEvents() {
        // Eventos para os botões de adicionar conteúdo
        const addClippingBtn = document.getElementById('add-clipping-btn');
        if (addClippingBtn) {
            addClippingBtn.addEventListener('click', () => this.openAddModal('clipping'));
        }
        
        const addPublicationBtn = document.getElementById('add-publication-btn');
        if (addPublicationBtn) {
            addPublicationBtn.addEventListener('click', () => this.openAddModal('publication'));
        }
        
        const addArchiveBtn = document.getElementById('add-archive-btn');
        if (addArchiveBtn) {
            addArchiveBtn.addEventListener('click', () => this.openAddModal('archive'));
        }
        
        const addHistoryBtn = document.getElementById('add-history-btn');
        if (addHistoryBtn) {
            addHistoryBtn.addEventListener('click', () => this.openAddModal('history'));
        }
        
        // Eventos para o formulário modal
        const saveBtn = document.getElementById('save-content-btn');
        if (saveBtn) {
            saveBtn.addEventListener('click', () => this.saveContent());
        }
        
        const cancelBtn = document.getElementById('cancel-content-btn');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => this.closeModal());
        }
    }

    openAddModal(contentType) {
        this.contentType = contentType;
        this.currentItemId = null;
        
        // Limpar formulário
        this.clearForm();
        
        // Atualizar título do modal
        const modalTitle = document.getElementById('modal-title');
        if (modalTitle) {
            modalTitle.textContent = `Adicionar ${this.getContentTypeName(contentType)}`;
        }
        
        // Mostrar modal
        const modal = document.getElementById('content-modal');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    openEditModal(contentType, itemId) {
        this.contentType = contentType;
        this.currentItemId = itemId;
        
        // Carregar dados do item
        this.loadItemData(itemId);
        
        // Atualizar título do modal
        const modalTitle = document.getElementById('modal-title');
        if (modalTitle) {
            modalTitle.textContent = `Editar ${this.getContentTypeName(contentType)}`;
        }
        
        // Mostrar modal
        const modal = document.getElementById('content-modal');
        if (modal) {
            modal.classList.remove('hidden');
        }
    }

    closeModal() {
        const modal = document.getElementById('content-modal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    async loadContentList() {
        try {
            // Carregar lista de conteúdo de cada tipo
            await this.loadClippings();
            await this.loadPublications();
            await this.loadArchiveItems();
            await this.loadHistoricalEvents();
        } catch (error) {
            console.error('Erro ao carregar lista de conteúdo:', error);
            this.showError('Não foi possível carregar o conteúdo. Tente novamente mais tarde.');
        }
    }

    async loadClippings() {
        try {
            const response = await fetch('api/clipping/list.php');
            
            if (!response.ok) {
                throw new Error(`Erro na requisição: ${response.status}`);
            }
            
            const data = await response.json();
            this.renderClippingsList(data.data);
            
        } catch (error) {
            console.error('Erro ao carregar clippings:', error);
            this.showError('Não foi possível carregar os clippings.');
        }
    }

    renderClippingsList(clippings) {
        const container = document.getElementById('clippings-list');
        if (!container) return;
        
        container.innerHTML = '';
        
        if (clippings.length > 0) {
            clippings.forEach(clipping => {
                const clippingElement = this.createClippingElement(clipping);
                container.appendChild(clippingElement);
            });
        } else {
            container.innerHTML = '<p class="text-center text-[#8B2635] py-4">Nenhum clipping encontrado.</p>';
        }
    }

    createClippingElement(clipping) {
        const div = document.createElement('div');
        div.className = 'bg-white p-4 rounded-lg shadow mb-2 flex justify-between items-center';
        div.innerHTML = `
            <div>
                <h4 class="font-bold text-[#6B4423]">${clipping.title}</h4>
                <p class="text-sm text-[#8B2635]">${this.formatDate(clipping.date)} • ${clipping.category}</p>
            </div>
            <div class="flex space-x-2">
                <button class="edit-clipping-btn text-[#8B2635] hover:text-[#992D3D]" data-id="${clipping.id}">
                    <span class="material-icons">edit</span>
                </button>
                <button class="delete-clipping-btn text-[#8B2635] hover:text-[#992D3D]" data-id="${clipping.id}">
                    <span class="material-icons">delete</span>
                </button>
            </div>
        `;
        
        // Adicionar eventos aos botões
        const editBtn = div.querySelector('.edit-clipping-btn');
        if (editBtn) {
            editBtn.addEventListener('click', () => this.openEditModal('clipping', clipping.id));
        }
        
        const deleteBtn = div.querySelector('.delete-clipping-btn');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', () => this.deleteClipping(clipping.id));
        }
        
        return div;
    }

    async loadPublications() {
        try {
            const response = await fetch('api/publications/list.php');
            
            if (!response.ok) {
                throw new Error(`Erro na requisição: ${response.status}`);
            }
            
            const data = await response.json();
            this.renderPublicationsList(data.data);
            
        } catch (error) {
            console.error('Erro ao carregar publicações:', error);
            this.showError('Não foi possível carregar as publicações.');
        }
    }

    renderPublicationsList(publications) {
        const container = document.getElementById('publications-list');
        if (!container) return;
        
        container.innerHTML = '';
        
        if (publications.length > 0) {
            publications.forEach(publication => {
                const publicationElement = this.createPublicationElement(publication);
                container.appendChild(publicationElement);
            });
        } else {
            container.innerHTML = '<p class="text-center text-[#8B2635] py-4">Nenhuma publicação encontrada.</p>';
        }
    }

    createPublicationElement(publication) {
        const div = document.createElement('div');
        div.className = 'bg-white p-4 rounded-lg shadow mb-2 flex justify-between items-center';
        div.innerHTML = `
            <div>
                <h4 class="font-bold text-[#6B4423]">${publication.title}</h4>
                <p class="text-sm text-[#8B2635]">${this.formatDate(publication.date)} • ${publication.type} • Edição ${publication.number}</p>
            </div>
            <div class="flex space-x-2">
                <button class="edit-publication-btn text-[#8B2635] hover:text-[#992D3D]" data-id="${publication.id}">
                    <span class="material-icons">edit</span>
                </button>
                <button class="delete-publication-btn text-[#8B2635] hover:text-[#992D3D]" data-id="${publication.id}">
                    <span class="material-icons">delete</span>
                </button>
            </div>
        `;
        
        // Adicionar eventos aos botões
        const editBtn = div.querySelector('.edit-publication-btn');
        if (editBtn) {
            editBtn.addEventListener('click', () => this.openEditModal('publication', publication.id));
        }
        
        const deleteBtn = div.querySelector('.delete-publication-btn');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', () => this.deletePublication(publication.id));
        }
        
        return div;
    }

    async loadItemData(itemId) {
        try {
            const response = await fetch(`api/${this.contentType}/details.php?id=${itemId}`);
            
            if (!response.ok) {
                throw new Error(`Erro na requisição: ${response.status}`);
            }
            
            const data = await response.json();
            this.fillForm(data.data);
            
        } catch (error) {
            console.error('Erro ao carregar dados do item:', error);
            this.showError('Não foi possível carregar os dados do item.');
        }
    }

    fillForm(data) {
        // Preencher campos do formulário com base no tipo de conteúdo
        switch (this.contentType) {
            case 'clipping':
                document.getElementById('clipping-title').value = data.title || '';
                document.getElementById('clipping-summary').value = data.summary || '';
                document.getElementById('clipping-content').value = data.content || '';
                document.getElementById('clipping-source-url').value = data.source_url || '';
                document.getElementById('clipping-date').value = data.date || '';
                document.getElementById('clipping-category').value = data.category || '';
                document.getElementById('clipping-is-active').checked = data.is_active == 1;
                break;
                
            case 'publication':
                document.getElementById('publication-title').value = data.title || '';
                document.getElementById('publication-description').value = data.description || '';
                document.getElementById('publication-file-path').value = data.file_path || '';
                document.getElementById('publication-date').value = data.date || '';
                document.getElementById('publication-number').value = data.number || '';
                document.getElementById('publication-type').value = data.type || '';
                document.getElementById('publication-is-active').checked = data.is_active == 1;
                break;
                
            // Adicionar casos para outros tipos de conteúdo conforme necessário
        }
    }

    clearForm() {
        // Limpar todos os campos do formulário
        const form = document.getElementById('content-form');
        if (form) {
            form.reset();
        }
    }

    async saveContent() {
        try {
            // Obter dados do formulário
            const formData = this.getFormData();
            
            // Determinar URL da API
            const apiUrl = this.currentItemId 
                ? `api/${this.contentType}/update.php?id=${this.currentItemId}`
                : `api/${this.contentType}/create.php`;
            
            // Enviar requisição
            const response = await fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });
            
            if (!response.ok) {
                throw new Error(`Erro na requisição: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success) {
                // Fechar modal
                this.closeModal();
                
                // Recarregar lista de conteúdo
                await this.loadContentList();
                
                // Mostrar mensagem de sucesso
                this.showSuccess('Conteúdo salvo com sucesso!');
            } else {
                throw new Error(result.message || 'Erro ao salvar conteúdo');
            }
            
        } catch (error) {
            console.error('Erro ao salvar conteúdo:', error);
            this.showError('Não foi possível salvar o conteúdo: ' + error.message);
        }
    }

    getFormData() {
        // Obter dados do formulário com base no tipo de conteúdo
        switch (this.contentType) {
            case 'clipping':
                return {
                    title: document.getElementById('clipping-title').value,
                    summary: document.getElementById('clipping-summary').value,
                    content: document.getElementById('clipping-content').value,
                    source_url: document.getElementById('clipping-source-url').value,
                    date: document.getElementById('clipping-date').value,
                    category: document.getElementById('clipping-category').value,
                    is_active: document.getElementById('clipping-is-active').checked ? 1 : 0
                };
                
            case 'publication':
                return {
                    title: document.getElementById('publication-title').value,
                    description: document.getElementById('publication-description').value,
                    file_path: document.getElementById('publication-file-path').value,
                    date: document.getElementById('publication-date').value,
                    number: document.getElementById('publication-number').value,
                    type: document.getElementById('publication-type').value,
                    is_active: document.getElementById('publication-is-active').checked ? 1 : 0
                };
                
            // Adicionar casos para outros tipos de conteúdo conforme necessário
            
            default:
                return {};
        }
    }

    async deleteClipping(id) {
        if (!confirm('Tem certeza que deseja excluir este clipping?')) return;
        
        try {
            const response = await fetch(`api/clipping/delete.php?id=${id}`, {
                method: 'DELETE'
            });
            
            if (!response.ok) {
                throw new Error(`Erro na requisição: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success) {
                // Recarregar lista de clippings
                await this.loadClippings();
                
                // Mostrar mensagem de sucesso
                this.showSuccess('Clipping excluído com sucesso!');
            } else {
                throw new Error(result.message || 'Erro ao excluir clipping');
            }
            
        } catch (error) {
            console.error('Erro ao excluir clipping:', error);
            this.showError('Não foi possível excluir o clipping: ' + error.message);
        }
    }

    async deletePublication(id) {
        if (!confirm('Tem certeza que deseja excluir esta publicação?')) return;
        
        try {
            const response = await fetch(`api/publications/delete.php?id=${id}`, {
                method: 'DELETE'
            });
            
            if (!response.ok) {
                throw new Error(`Erro na requisição: ${response.status}`);
            }
            
            const result = await response.json();
            
            if (result.success) {
                // Recarregar lista de publicações
                await this.loadPublications();
                
                // Mostrar mensagem de sucesso
                this.showSuccess('Publicação excluída com sucesso!');
            } else {
                throw new Error(result.message || 'Erro ao excluir publicação');
            }
            
        } catch (error) {
            console.error('Erro ao excluir publicação:', error);
            this.showError('Não foi possível excluir a publicação: ' + error.message);
        }
    }

    getContentTypeName(contentType) {
        const names = {
            'clipping': 'Clipping',
            'publication': 'Publicação',
            'archive': 'Item do Acervo',
            'history': 'Evento Histórico'
        };
        
        return names[contentType] || 'Conteúdo';
    }

    formatDate(dateString) {
        const options = { day: 'numeric', month: 'short', year: 'numeric' };
        const date = new Date(dateString);
        return isNaN(date.getTime()) ? 'Data não disponível' : date.toLocaleDateString('pt-BR', options);
    }

    showSuccess(message) {
        // Implementar exibição de mensagem de sucesso
        alert('Sucesso: ' + message);
    }

    showError(message) {
        // Implementar exibição de mensagem de erro
        alert('Erro: ' + message);
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    // Apenas inicializar se estivermos na página de administração
    if (document.getElementById('admin-historical-content')) {
        new AdminHistoricalContent();
    }
});