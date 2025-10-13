// main-compat.js - Versão compatível do script principal do sistema

// Função para rolar até a seção de notícias
function scrollToNews() {
    document.getElementById('noticias').scrollIntoView({ 
        behavior: 'smooth',
        block: 'start'
    });
}

// Animação de entrada dos elementos
window.addEventListener('load', function() {
    const elements = document.querySelectorAll('.card-hover');
    elements.forEach((el, index) => {
        setTimeout(() => {
            el.style.transform = 'translateY(0)';
            el.style.opacity = '1';
        }, index * 200);
    });
});

// Inicializar elementos com animação
document.querySelectorAll('.card-hover').forEach(el => {
    el.style.transform = 'translateY(50px)';
    el.style.opacity = '0';
    el.style.transition = 'all 0.6s ease';
});

// O gerenciamento de abas foi movido para tabs.js
// Utilize window.tabManager para acessar as funções de abas

// Função para gerenciar busca e filtragem do acervo
function setupArchiveFilters() {
    const searchInput = document.getElementById('search-acervo');
    const filterType = document.getElementById('filter-type');
    const filterYear = document.getElementById('filter-year');
    const applyButton = document.getElementById('apply-filters');
    
    if (searchInput && filterType && filterYear && applyButton) {
        // Eventos para cada filtro individualmente
        [searchInput, filterType, filterYear].forEach(element => {
            if (element) {
                element.addEventListener('change', applyArchiveFilters);
            }
        });
        
        // Evento para o botão de aplicar filtros
        applyButton.addEventListener('click', applyArchiveFilters);
        
        // Evento para Enter no campo de busca
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyArchiveFilters();
            }
        });
    }
}

// Função para aplicar os filtros do acervo
function applyArchiveFilters() {
    console.log('Aplicando filtros do acervo...');
    // Esta função pode ser expandida para filtrar os itens do acervo localmente
    // ou chamar uma API com os parâmetros de busca e filtro
    const searchValue = document.getElementById('search-acervo')?.value || '';
    const typeValue = document.getElementById('filter-type')?.value || '';
    const yearValue = document.getElementById('filter-year')?.value || '';
    
    // Aqui você pode adicionar lógica para filtragem real,
    // seja localmente nos dados já carregados ou 
    // fazendo uma nova requisição à API com os filtros
    console.log('Filtros aplicados:', { search: searchValue, type: typeValue, year: yearValue });
    
    // Se os managers estiverem disponíveis, podemos reiniciar a busca
    if (typeof ArchiveManager !== 'undefined') {
        // Reiniciar a busca com os filtros aplicados
        // Isto exigiria modificar o ArchiveManager para aceitar parâmetros de filtro
        console.log('ArchiveManager encontrado, possível implementação futura de filtros dinâmicos');
    }
}

// Inicializar os componentes ao carregar a página
document.addEventListener('DOMContentLoaded', () => {
    // Inicializar outros componentes se os elementos existirem na página
    if (typeof NewsManager !== 'undefined' && document.getElementById('news-container')) {
        new NewsManager();
    }
    
    if (typeof ReportsManager !== 'undefined' && document.getElementById('reports-container')) {
        new ReportsManager();
    }
    
    // Inicializar componentes adicionais se os elementos existirem
    if (typeof AboutManager !== 'undefined' && document.getElementById('about-container')) {
        new AboutManager();
    }
    
    if (typeof HistoryManager !== 'undefined' && document.getElementById('history-container')) {
        new HistoryManager();
    }
    
    if (typeof PublicationsManager !== 'undefined' && document.getElementById('publications-container')) {
        new PublicationsManager();
    }
    
    if (typeof ClippingManager !== 'undefined' && document.getElementById('clipping-container')) {
        new ClippingManager();
    }
    
    if (typeof CrmcManager !== 'undefined' && document.getElementById('crmc-container')) {
        new CrmcManager();
    }
    
    if (typeof ArchiveManager !== 'undefined' && document.getElementById('archive-container')) {
        new ArchiveManager();
    }
    
    // O gerenciamento de abas agora é feito pelo TabManager em tabs.js
    
    // Inicializar filtros do acervo se os elementos existirem
    if (document.getElementById('search-acervo') && 
        document.getElementById('filter-type') && 
        document.getElementById('filter-year') && 
        document.getElementById('apply-filters')) {
        setupArchiveFilters();
    }
});