// assets/js/services/NotificationService.js
class NotificationService {
    static show(message, type = 'info', duration = 5000) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-message">${message}</span>
                <button class="notification-close">&times;</button>
            </div>
        `;

        // Adicionar ao DOM
        document.body.appendChild(notification);

        // Auto-remover após duração
        setTimeout(() => {
            this.remove(notification);
        }, duration);

        // Botão de fechar
        notification.querySelector('.notification-close').addEventListener('click', () => {
            this.remove(notification);
        });
    }

    static remove(notification) {
        notification.style.opacity = '0';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }
}
