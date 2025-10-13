// Função para trocar entre abas
function showTab(tabId) {
    // Esconder todos os conteúdos de abas
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.setAttribute('hidden', 'hidden');
    });
    
    // Remover a classe ativa de todos os botões
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
        button.setAttribute('aria-selected', 'false');
    });
    
    // Mostrar a aba selecionada
    const selectedTab = document.getElementById(`tab-${tabId}`);
    if (selectedTab) {
        selectedTab.removeAttribute('hidden');
    }
    
    // Ativar o botão clicado
    const activeButton = document.querySelector(`[data-tab="${tabId}"]`);
    if (activeButton) {
        activeButton.classList.add('active');
        activeButton.setAttribute('aria-selected', 'true');
    }
}

// Inicialização quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', function() {
    // Configurar navegação por abas
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            showTab(tabId);
        });
    });
    
    // Mostrar a primeira aba por padrão
    if (tabButtons.length > 0) {
        showTab('instituicao');
    }
});
