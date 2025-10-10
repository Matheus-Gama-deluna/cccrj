// Função para mostrar uma aba específica
function showTab(tabId) {
    console.log('Mostrando aba:', tabId);
    
    // Seleciona todos os elementos necessários
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    // Esconde todos os conteúdos
    tabContents.forEach(tab => {
        console.log('Ocultando aba:', tab.id);
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
    
    console.log('Aba ativa encontrada:', activeTab);
    console.log('Botão ativo encontrado:', activeButton);
    
    if (activeTab && activeButton) {
        activeTab.style.display = 'block';
        activeTab.classList.add('active');
        activeButton.classList.add('active');
        activeButton.setAttribute('aria-selected', 'true');
        
        // Atualiza a URL sem recarregar a página
        const newUrl = `${window.location.pathname}?tab=${tabId}`;
        window.history.pushState({ tab: tabId }, '', newUrl);
    } else {
        console.error('Aba ou botão não encontrado para o ID:', tabId);
    }
}

// Função para inicializar o sistema de abas
function initTabs() {
    console.log('Inicializando abas...');
    
    // Seleciona todos os botões de abas
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    console.log('Total de botões de abas encontrados:', tabButtons.length);
    console.log('Total de conteúdos de abas encontrados:', tabContents.length);
    
    // Adiciona evento de clique para cada botão de aba
    tabButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const tabId = this.getAttribute('data-tab');
            if (tabId) {
                showTab(tabId);
            } else {
                console.error('Botão de aba sem atributo data-tab:', this);
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
        } else {
            console.error('Primeiro botão de aba sem atributo data-tab válido');
        }
    }
}

// Inicializa as abas quando o DOM estiver pronto
console.log('Estado do documento:', document.readyState); // Log de depuração

if (document.readyState === 'loading') {
    console.log('Aguardando carregamento do DOM...'); // Log de depuração
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM completamente carregado, inicializando abas...'); // Log de depuração
        initTabs();
    });
} else {
    console.log('DOM já está pronto, inicializando abas imediatamente'); // Log de depuração
    initTabs();
}

// Exporta a função para ser usada globalmente
window.switchTab = function(tabId) {
    const tabToOpen = document.querySelector(`.tab-button[data-tab="${tabId}"]`);
    if (tabToOpen) {
        tabToOpen.click();
    }
};
