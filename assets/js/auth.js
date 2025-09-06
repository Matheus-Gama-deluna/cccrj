// Sistema de autenticação
class AuthManager {
    constructor() {
        this.currentUser = null;
        this.init();
    }

    init() {
        // Verificar se usuário já está logado
        this.checkLoginStatus();
        
        // Adicionar evento de login
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', (e) => this.handleLogin(e));
        }
        
        // Adicionar evento de logout
        const logoutButton = document.getElementById('logoutButton');
        if (logoutButton) {
            logoutButton.addEventListener('click', () => this.handleLogout());
        }
    }

    async handleLogin(event) {
        event.preventDefault();
        
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        
        // Simular autenticação
        // Em implementação real, substituir por chamada à API
        try {
            // Simular delay de rede
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            // Verificar credenciais (em produção, isso seria feito no backend)
            if (username === 'admin' && password === 'admin123') {
                this.currentUser = { username: 'admin', role: 'administrator' };
                this.saveUserSession();
                window.location.href = 'admin.html';
            } else {
                alert('Credenciais inválidas. Tente novamente.');
            }
        } catch (error) {
            console.error('Erro durante o login:', error);
            alert('Erro ao realizar login. Tente novamente.');
        }
    }

    handleLogout() {
        // Remover sessão do usuário
        this.clearUserSession();
        // Redirecionar para página de login
        window.location.href = 'login.html';
    }

    checkLoginStatus() {
        // Verificar se há uma sessão salva
        const userData = localStorage.getItem('cccrj_user');
        if (userData) {
            this.currentUser = JSON.parse(userData);
            return true;
        }
        return false;
    }

    saveUserSession() {
        // Salvar dados do usuário na sessão
        if (this.currentUser) {
            localStorage.setItem('cccrj_user', JSON.stringify(this.currentUser));
        }
    }

    clearUserSession() {
        // Remover dados da sessão
        localStorage.removeItem('cccrj_user');
        this.currentUser = null;
    }

    isAuthenticated() {
        return this.currentUser !== null;
    }

    getCurrentUser() {
        return this.currentUser;
    }
}

// Instância global do gerenciador de autenticação
const authManager = new AuthManager();

// Verificar autenticação em páginas protegidas
document.addEventListener('DOMContentLoaded', () => {
    // Se estiver na página de admin e usuário não estiver autenticado, redirecionar
    if (window.location.pathname.includes('admin.html') && !authManager.isAuthenticated()) {
        window.location.href = 'login.html';
    }
    
    // Se estiver na página de login e usuário já estiver autenticado, redirecionar
    if (window.location.pathname.includes('login.html') && authManager.isAuthenticated()) {
        window.location.href = 'admin.html';
    }
});