// main.js - Script principal do sistema

// Importar componentes
import './components/news.js';
import './components/reports.js';
import './components/calculator.js';
import './components/clipping.js';
import './components/publications.js';
import './components/history.js';
import './components/about.js';
import './components/crmc.js';
import './components/archive.js';

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

// Função para gerenciar abas
function setupTabNavigation() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    // Adiciona evento de clique para cada botão de aba
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tabId = button.getAttribute('data-tab');
            
            // Remove a classe 'active-tab' de todos os botões
            tabButtons.forEach(btn => btn.classList.remove('active-tab', 'bg-[#8B2635]', 'text-white'));
            // Adiciona a classe 'active-tab' ao botão clicado
            button.classList.add('active-tab', 'bg-[#8B2635]', 'text-white');
            
            // Esconde todos os conteúdos das abas
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });
            
            // Mostra o conteúdo da aba selecionada
            const selectedContent = document.getElementById(`tab-${tabId}`);
            if (selectedContent) {
                selectedContent.classList.remove('hidden');
                // Adiciona animação de fade-in
                selectedContent.style.opacity = '0';
                selectedContent.style.transition = 'opacity 0.3s ease-in-out';
                setTimeout(() => {
                    selectedContent.style.opacity = '1';
                }, 10);
            }
        });
    });
}

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
    const searchValue = document.getElementById('search-acervo')?.value || '';
    const typeValue = document.getElementById('filter-type')?.value || '';
    const yearValue = document.getElementById('filter-year')?.value || '';
    
    if (window.archiveManager) {
        window.archiveManager.applyFilters({
            search: searchValue,
            type: typeValue,
            year: yearValue
        });
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
        window.archiveManager = new ArchiveManager();
    }
    
    // Inicializar sistema de abas se os elementos existirem
    if (document.querySelectorAll('.tab-button').length > 0) {
        setupTabNavigation();
    }
    
    // Inicializar filtros do acervo se os elementos existirem
    if (document.getElementById('search-acervo') && 
        document.getElementById('filter-type') && 
        document.getElementById('filter-year') && 
        document.getElementById('apply-filters')) {
        setupArchiveFilters();
    }
});