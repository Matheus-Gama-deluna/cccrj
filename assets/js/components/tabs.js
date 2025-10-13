// Gerenciador de abas
class TabManager {
    constructor() {
        this.tabsContainer = document.querySelector('.tab-navigation');
        this.tabButtons = [];
        this.tabContents = [];
        this.initialized = false;
        this.init();
    }

    init() {
        if (this.initialized || !this.tabsContainer) return;
        
        this.tabButtons = Array.from(document.querySelectorAll('.tab-button'));
        this.tabContents = Array.from(document.querySelectorAll('.tab-content'));
        
        if (this.tabButtons.length === 0 || this.tabContents.length === 0) return;
        
        this.setupEventListeners();
        this.initialized = true;
        
        // Ativar a aba inicial baseada na URL ou na primeira aba
        this.activateInitialTab();
    }
    
    setupEventListeners() {
        this.tabButtons.forEach(button => {
            // Clique do mouse
            button.addEventListener('click', (e) => this.handleTabClick(e, button));
            
            // Navegação por teclado
            button.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.handleTabClick(e, button);
                }
            });
        });
        
        // Lidar com o botão voltar/avançar do navegador
        window.addEventListener('popstate', () => this.activateInitialTab());
    }
    
    handleTabClick(e, button) {
        e.preventDefault();
        const tabId = button.getAttribute('data-tab');
        if (tabId) {
            this.activateTab(tabId);
        }
    }
    
    activateTab(tabId) {
        // Desativa todas as abas
        this.tabButtons.forEach(btn => {
            btn.classList.remove('active', 'bg-[#8B2635]', 'text-white');
            btn.classList.add('text-[#6B4423]', 'hover:bg-[#F5F0E8]');
            btn.setAttribute('aria-selected', 'false');
        });
        
        this.tabContents.forEach(content => {
            content.classList.add('hidden');
            content.classList.remove('active');
        });
        
        // Ativa a aba selecionada
        const activeButton = document.querySelector(`.tab-button[data-tab="${tabId}"]`);
        const activeContent = document.getElementById(`tab-${tabId}`);
        
        if (activeButton && activeContent) {
            activeButton.classList.add('active', 'bg-[#8B2635]', 'text-white');
            activeButton.classList.remove('text-[#6B4423]', 'hover:bg-[#F5F0E8]');
            activeButton.setAttribute('aria-selected', 'true');
            
            activeContent.classList.remove('hidden');
            activeContent.classList.add('active');
            
            // Rolar suavemente para a seção de abas se não estiver visível
            if (!this.isElementInViewport(this.tabsContainer)) {
                this.tabsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
            
            // Atualizar a URL sem recarregar a página
            const newUrl = `${window.location.pathname}?tab=${tabId}`;
            window.history.pushState({ tab: tabId }, '', newUrl);
            
            // Disparar evento personalizado para notificar outras partes do sistema
            document.dispatchEvent(new CustomEvent('tabChanged', { detail: { tabId } }));
        }
    }
    
    activateInitialTab() {
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab');
        
        if (tabParam) {
            this.activateTab(tabParam);
        } else {
            // Ativar a primeira aba por padrão
            const firstTab = this.tabButtons[0]?.getAttribute('data-tab');
            if (firstTab) {
                this.activateTab(firstTab);
            }
        }
    }
    
    isElementInViewport(el) {
        if (!el) return false;
        const rect = el.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }
}

// Inicializar o gerenciador de abas quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    window.tabManager = new TabManager();
});

// Função global para mudar de aba
window.switchTab = function(tabId) {
    if (window.tabManager) {
        window.tabManager.activateTab(tabId);
    }
};
