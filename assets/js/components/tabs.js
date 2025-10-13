// Variável global para controlar inicialização das abas
let tabsInitialized = false;

// Função para mostrar uma aba específica
function showTab(tabId) {
    // Seleciona todos os elementos necessários
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    // Esconde todos os conteúdos
    tabContents.forEach(tab => {
        tab.style.display = 'none';
        tab.classList.remove('active');
    });

    // Remove a classe 'active' de todos os botões
    tabButtons.forEach(btn => {
        btn.classList.remove('active');
        btn.setAttribute('aria-selected', 'false');
    });

    // Mostra o conteúdo da aba selecionada
    const activeTab = document.getElementById(`tab-${tabId}`);
    const activeButton = document.querySelector(`.tab-button[data-tab="${tabId}"]`);

    if (activeTab && activeButton) {
        activeTab.style.display = 'block';
        activeTab.classList.add('active');
        activeButton.classList.add('active');
        activeButton.setAttribute('aria-selected', 'true');

        // Atualiza a URL sem recarregar a página
        const newUrl = `${window.location.pathname}?tab=${tabId}`;
        window.history.pushState({ tab: tabId }, '', newUrl);
    }
}

// Função para inicializar o sistema de abas
function initTabs() {
    // Previne múltiplas inicializações
    if (tabsInitialized) {
        return;
    }

    tabsInitialized = true;

    // Seleciona todos os botões de abas
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    // Adiciona evento de clique para cada botão de aba
    tabButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const tabId = this.getAttribute('data-tab');
            if (tabId) {
                showTab(tabId);
            }
        });

        // Suporte para teclado (acessibilidade)
        button.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const tabId = this.getAttribute('data-tab');
                if (tabId) {
                    showTab(tabId);
                }
            }
        });
    });

    // Verifica se há uma aba na URL
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');

    // Se houver uma aba na URL, mostra ela, senão mostra a primeira
    if (tabParam) {
        showTab(tabParam);
    } else if (tabButtons.length > 0) {
        const firstTab = tabButtons[0].getAttribute('data-tab');
        if (firstTab) {
            showTab(firstTab);
        }
    }
}

// Inicializa as abas quando o DOM estiver pronto
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        initTabs();
    });
} else {
    initTabs();
}

// Exporta a função para ser usada globalmente
window.switchTab = function(tabId) {
    const tabToOpen = document.querySelector(`.tab-button[data-tab="${tabId}"]`);
    if (tabToOpen) {
        tabToOpen.click();
    }
};
