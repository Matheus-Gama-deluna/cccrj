// Controle do menu móvel
document.addEventListener('DOMContentLoaded', function() {
    const menuButton = document.getElementById('mobile-menu-button');
    const menuContainer = document.getElementById('mobile-menu');
    const menuCloseButton = menuContainer?.querySelector('.mobile-menu-close');
    const menuOverlay = menuContainer?.querySelector('.mobile-menu-overlay');
    
    // Função para abrir o menu
    function openMenu() {
        if (!menuContainer) return;
        
        // Adiciona classe para mostrar o menu
        menuContainer.classList.add('show');
        document.body.classList.add('menu-open');
        
        // Força o navegador a renderizar o estado inicial
        void menuContainer.offsetWidth;
        
        // Atualiza o estado do botão para acessibilidade
        menuButton.setAttribute('aria-expanded', 'true');
        
        // Foca no primeiro item do menu para navegação por teclado
        setTimeout(() => {
            const firstMenuItem = menuContainer.querySelector('a');
            if (firstMenuItem) firstMenuItem.focus();
        }, 300);
    }
    
    // Função para fechar o menu
    function closeMenu() {
        if (!menuContainer) return;
        
        // Remove a classe para esconder o menu
        menuContainer.classList.remove('show');
        document.body.classList.remove('menu-open');
        
        // Atualiza o estado do botão para acessibilidade
        menuButton.setAttribute('aria-expanded', 'false');
        
        // Retorna o foco para o botão do menu
        menuButton.focus();
    }
    
    // Event Listeners
    if (menuButton) {
        menuButton.addEventListener('click', function(e) {
            e.stopPropagation();
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            
            if (isExpanded) {
                closeMenu();
            } else {
                openMenu();
            }
        });
    }
    
    // Fechar menu ao clicar no botão de fechar
    if (menuCloseButton) {
        menuCloseButton.addEventListener('click', closeMenu);
    }
    
    // Fechar menu ao clicar no overlay
    if (menuOverlay) {
        menuOverlay.addEventListener('click', closeMenu);
    }
    
    // Fechar menu ao pressionar a tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && menuContainer && menuContainer.classList.contains('show')) {
            closeMenu();
        }
    });
    
    // Fechar menu ao redimensionar para desktop
    function handleResize() {
        if (window.innerWidth >= 768) {
            closeMenu();
        }
    }
    
    window.addEventListener('resize', handleResize);
    
    // Fechar menu ao clicar em um link do menu
    const menuLinks = menuContainer?.querySelectorAll('a');
    if (menuLinks) {
        menuLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Se for um link âncora, rola suavemente
                if (this.getAttribute('href').startsWith('#')) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);
                    
                    if (targetElement) {
                        closeMenu();
                        
                        // Pequeno atraso para permitir que o menu feche antes de rolar
                        setTimeout(() => {
                            const headerOffset = 80;
                            const elementPosition = targetElement.getBoundingClientRect().top;
                            const offsetPosition = elementPosition + window.pageYOffset - headerOffset;
                            
                            window.scrollTo({
                                top: offsetPosition,
                                behavior: 'smooth'
                            });
                            
                            // Atualiza a URL sem recarregar a página
                            if (history.pushState) {
                                history.pushState(null, null, targetId);
                            } else {
                                window.location.hash = targetId;
                            }
                        }, 300);
                    }
                }
            });
        });
    }
});
