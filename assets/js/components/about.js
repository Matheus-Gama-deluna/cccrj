// Componente AboutManager em assets/js/components/about.js
class AboutManager {
    constructor() {
        this.sections = [];
        this.apiUrl = 'api/json/about/list.php'; // Novo endpoint JSON
        this.init();
    }

    async init() {
        await this.loadSections();
        this.bindEvents();
    }

    async loadSections() {
        try {
            const response = await fetch(this.apiUrl);
            
            if (!response.ok) {
                throw new Error(`Erro na requisição: ${response.status}`);
            }
            
            const data = await response.json();
            this.sections = data.data;
            
            this.renderSections();
            
        } catch (error) {
            console.error('Erro ao carregar seções sobre:', error);
            this.showError(true, 'Não foi possível carregar as seções sobre. Tente novamente mais tarde.');
        }
    }

    renderSections() {
        const aboutContainer = document.getElementById('about-container');
        if (!aboutContainer) return;

        this.sections.forEach(section => {
            const sectionElement = this.createSectionElement(section);
            aboutContainer.appendChild(sectionElement);
        });
    }

    createSectionElement(section) {
        const div = document.createElement('div');
        div.className = 'about-section bg-white p-6 rounded-lg shadow mb-6 border border-[#F5F0E8] opacity-0 translate-y-4 transition-all duration-500';
        
        div.innerHTML = \`
            <h3 class="text-2xl font-bold text-[#6B4423] mb-4">${section.title}</h3>
            <div class="text-[#8B2635] mb-4">
                ${section.content}
            </div>
            ${section.image_url ? \`<div class="mt-4"><img src="${section.image_url}" alt="${section.title}" class="rounded-lg max-w-full h-auto"></div>\` : ''}
        \`;
        
        // Adicionar animação de entrada
        setTimeout(() => {
            div.classList.remove('opacity-0', 'translate-y-4');
        }, 100);

        return div;
    }

    bindEvents() {
        // Eventos específicos para a seção sobre
    }

    showError(show, message = null) {
        const error = document.getElementById('about-error');
        if (error) {
            if (message) {
                const messageElement = error.querySelector('.error-message');
                if (messageElement) {
                    messageElement.textContent = message;
                }
            }
            error.classList.toggle('hidden', !show);
        }
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    // Apenas inicializar se o elemento about-container existir na página
    if (document.getElementById('about-container')) {
        new AboutManager();
    }
});