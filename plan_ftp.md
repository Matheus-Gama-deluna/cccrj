# Plano de Desenvolvimento com Qwen Code - Funcionalidade de Relatórios via FTP (Sistema Robusto)

## 1. Visão Geral

Este plano detalha o desenvolvimento da funcionalidade de relatórios via FTP utilizando exclusivamente o Qwen Code, seguindo o protocolo PRAR (Perceber, Raciocinar, Agir, Refinar). O objetivo é transformar o sistema atual MVP em um sistema robusto que implemente funcionalidades reais de FTP para visualização e envio de PDFs diretamente do servidor FTP.

## 2. Protocolo PRAR Aplicado

### Fase 1: Perceber & Entender

#### 2.1 Análise do Sistema Atual
1. **Mapeamento de Dependências Existentes**
   - Tailwind CSS (via CDN)
   - Google Fonts (Inter)
   - Material Icons
   - Estrutura de arquivos JavaScript modular
   - Componentes existentes (auth.js, admin.js, reports.js)

2. **Identificação de Pontos Críticos**
   - Sistema de autenticação simulado
   - Integração com FTP simulada
   - Estrutura de componentes pronta para adaptação
   - Arquitetura frontend-only atual

3. **Documentação da Arquitetura Existente**
   - Componentização lógica dos recursos
   - Padrões de codificação ES6
   - Estrutura de diretórios atual

#### 2.2 Definição de Requisitos de Qualidade
1. **Métricas de Performance**
   - Tempo de carregamento < 3 segundos
   - Primeira renderização de conteúdo < 1.8 segundos

2. **Critérios de Segurança**
   - Proteção contra XSS
   - Validação de entrada de dados
   - Armazenamento seguro de credenciais
   - Autenticação JWT para acesso à API

3. **Padrões de Acessibilidade**
   - Conformidade com WCAG 2.1 AA
   - Navegação por teclado
   - Contraste adequado

4. **Requisitos de Compatibilidade**
   - Funcionamento em Chrome, Firefox, Safari, Edge
   - Responsividade em dispositivos móveis

### Fase 2: Raciocinar & Planejar

#### 2.3 Arquitetura de Solução Robusta
1. **Estratégia de Implementação**
   - Substituir arquitetura frontend-only por integração com backend Laravel
   - Substituir integração simulada por chamadas reais à API Laravel e servidor FTP
   - Utilizar bibliotecas JavaScript para comunicação com backend
   - Manter componentização existente com melhorias
   - Implementar sistema de autenticação baseado em JWT

2. **Definição de Componentes Necessários**
   - `auth.js`: Sistema de autenticação seguro com JWT
   - `reports.js`: Integração real com API Laravel e servidor FTP
   - `admin.js`: Funcionalidades administrativas com integração real
   - Novo componente: `ftp-service.js` para comunicação com servidor FTP via backend
   - Novo componente: `api-client.js` para comunicação com API Laravel

3. **Fluxo de Dados**
   ```
   Usuário -> Login (JWT) -> Área Admin -> Listar Relatórios (API) -> Download/Upload (FTP via API)
   ```

#### 2.4 Planejamento de Funcionalidades
1. **Funcionalidades Inclusas (Sistema Robusto)**
   - Autenticação segura de administradores com JWT
   - Listagem de relatórios do servidor FTP via API Laravel
   - Download de relatórios PDF via API Laravel
   - Upload de novos relatórios via API Laravel
   - Exclusão de relatórios existentes via API Laravel
   - Metadados de relatórios (título, descrição, data)
   - Visualizador PDF embutido
   - Busca e filtros avançados
   - Histórico de uploads

2. **Funcionalidades Excluídas**
   - Módulo de cotações em tempo real (será implementado em release futura)
   - Módulo de notícias (será implementado em release futura)
   - Calculadora de conversão (será implementado em release futura)
   - Sistema de busca avançada em outros módulos (será implementado em releases futuras)

#### 2.5 Estratégia de Testes
1. **Testes Unitários**
   - Validação de componentes JavaScript
   - Testes de integração com API Laravel
   - Verificação de autenticação JWT
   - Testes de manipulação de arquivos

2. **Testes Manuais**
   - Fluxo completo de upload/download
   - Tratamento de erros
   - Compatibilidade entre navegadores
   - Segurança de autenticação

### Fase 3: Agir & Implementar

#### 3.1 Estruturação e Organização (Dias 1-3)

##### Dia 1: Setup Inicial
1. **Organização de Arquivos**
   ```bash
   # Verificar estrutura atual
   ls -la
   
   # Criar diretório para novos componentes
   mkdir -p assets/js/services
   mkdir -p assets/js/api
   
   # Backup de arquivos críticos
   cp assets/js/components/reports.js assets/js/components/reports.js.bak
   cp assets/js/auth.js assets/js/auth.js.bak
   cp assets/js/admin.js assets/js/admin.js.bak
   ```

2. **Configuração de Dependências**
   ```bash
   # Verificar dependências existentes
   # Adicionar bibliotecas necessárias para comunicação com API
   ```

##### Dia 2: Componentização e Refatoração
1. **Criar Componente API Client**
   ```javascript
   // assets/js/api/client.js
   class ApiClient {
     constructor(baseURL) {
       this.baseURL = baseURL;
       this.token = localStorage.getItem('authToken');
     }
     
     setToken(token) {
       this.token = token;
       localStorage.setItem('authToken', token);
     }
     
     clearToken() {
       this.token = null;
       localStorage.removeItem('authToken');
     }
     
     async request(endpoint, options = {}) {
       const url = `${this.baseURL}${endpoint}`;
       const headers = {
         'Content-Type': 'application/json',
         ...options.headers
       };
       
       if (this.token) {
         headers['Authorization'] = `Bearer ${this.token}`;
       }
       
       const config = {
         ...options,
         headers
       };
       
       try {
         const response = await fetch(url, config);
         
         if (!response.ok) {
           throw new Error(`HTTP error! status: ${response.status}`);
         }
         
         return await response.json();
       } catch (error) {
         console.error('API request failed:', error);
         throw error;
       }
     }
     
     async get(endpoint) {
       return this.request(endpoint, { method: 'GET' });
     }
     
     async post(endpoint, data) {
       return this.request(endpoint, {
         method: 'POST',
         body: JSON.stringify(data)
       });
     }
     
     async put(endpoint, data) {
       return this.request(endpoint, {
         method: 'PUT',
         body: JSON.stringify(data)
       });
     }
     
     async delete(endpoint) {
       return this.request(endpoint, { method: 'DELETE' });
     }
   }
   ```

2. **Criar Componente FTP Service**
   ```javascript
   // assets/js/services/ftp-service.js
   class FtpService {
     constructor(apiClient) {
       this.apiClient = apiClient;
     }
     
     async listReports() {
       try {
         const response = await this.apiClient.get('/api/reports');
         return response.data;
       } catch (error) {
         console.error('Error listing reports:', error);
         throw error;
       }
     }
     
     async downloadReport(reportId) {
       try {
         // Para download, podemos usar direct link ou fetch com blob
         const response = await this.apiClient.get(`/api/reports/${reportId}/download`);
         // O endpoint deve retornar uma URL para download direto
         return response.downloadUrl;
       } catch (error) {
         console.error('Error downloading report:', error);
         throw error;
       }
     }
     
     async uploadReport(file, metadata) {
       try {
         const formData = new FormData();
         formData.append('file', file);
         formData.append('title', metadata.title);
         formData.append('description', metadata.description);
         
         // Para upload de arquivos, precisamos ajustar o cliente API
         const response = await this.apiClient.request('/api/reports', {
           method: 'POST',
           body: formData,
           headers: {
             // Remover Content-Type para deixar o navegador definir com boundary
           }
         });
         
         return response.data;
       } catch (error) {
         console.error('Error uploading report:', error);
         throw error;
       }
     }
     
     async deleteReport(reportId) {
       try {
         const response = await this.apiClient.delete(`/api/reports/${reportId}`);
         return response.data;
       } catch (error) {
         console.error('Error deleting report:', error);
         throw error;
       }
     }
   }
   ```

##### Dia 3: Refatorar Componente de Autenticação
1. **Atualizar auth.js para autenticação JWT**
   ```javascript
   // assets/js/auth.js
   class AuthManager {
     constructor() {
       this.apiClient = new ApiClient('http://localhost:8000'); // URL do backend Laravel
       this.currentUser = null;
       this.init();
     }
     
     init() {
       // Verificar token existente
       this.checkLoginStatus();
       
       // Adicionar evento de login
       const loginForm = document.getElementById('loginForm');
       if (loginForm) {
         loginForm.addEventListener('submit', (e) => this.handleLogin(e));
       }
     }
     
     async handleLogin(event) {
       event.preventDefault();
       
       const username = document.getElementById('username').value;
       const password = document.getElementById('password').value;
       
       // Validar credenciais através da API
       try {
         const response = await this.apiClient.post('/api/auth/login', {
           username,
           password
         });
         
         if (response.token) {
           // Salvar token
           this.apiClient.setToken(response.token);
           this.currentUser = response.user;
           this.saveUserSession();
           window.location.href = 'admin.html';
         } else {
           throw new Error('Credenciais inválidas');
         }
       } catch (error) {
         console.error('Erro durante o login:', error);
         alert('Credenciais inválidas. Tente novamente.');
       }
     }
     
     async handleLogout() {
       try {
         await this.apiClient.post('/api/auth/logout');
       } catch (error) {
         console.error('Erro durante o logout:', error);
       } finally {
         this.apiClient.clearToken();
         this.currentUser = null;
         this.clearUserSession();
         window.location.href = 'login.html';
       }
     }
     
     isAuthenticated() {
       return !!this.apiClient.token && !!this.currentUser;
     }
     
     getCurrentUser() {
       return this.currentUser;
     }
     
     checkLoginStatus() {
       const token = localStorage.getItem('authToken');
       if (token) {
         this.apiClient.setToken(token);
         // Verificar se o token é válido
         // Em uma implementação real, faríamos uma chamada à API para validar
       }
     }
     
     saveUserSession() {
       if (this.currentUser) {
         localStorage.setItem('currentUser', JSON.stringify(this.currentUser));
       }
     }
     
     clearUserSession() {
       localStorage.removeItem('currentUser');
     }
   }
   ```

#### 3.2 Implementação da Área Administrativa (Dias 4-6)

##### Dia 4: Sistema de Autenticação Seguro
1. **Atualizar admin.js para integração real**
   ```javascript
   // assets/js/admin.js
   class AdminDashboard {
     constructor() {
       this.apiClient = new ApiClient('http://localhost:8000');
       this.ftpService = new FtpService(this.apiClient);
       this.init();
     }
     
     init() {
       // Verificar autenticação
       if (!authManager.isAuthenticated()) {
         window.location.href = 'login.html';
         return;
       }
       
       // Atualizar interface com informações do usuário
       this.updateUserInfo();
       
       // Carregar relatórios
       this.loadReports();
       
       // Adicionar eventos
       const uploadForm = document.getElementById('uploadForm');
       if (uploadForm) {
         uploadForm.addEventListener('submit', (e) => this.handleFileUpload(e));
       }
       
       // Adicionar evento para seleção de arquivo
       const fileInput = document.getElementById('fileInput');
       if (fileInput) {
         fileInput.addEventListener('change', (e) => this.handleFileSelect(e));
       }
       
       // Adicionar evento de logout
       const logoutButton = document.getElementById('logoutButton');
       if (logoutButton) {
         logoutButton.addEventListener('click', () => authManager.handleLogout());
       }
     }
     
     updateUserInfo() {
       const user = authManager.getCurrentUser();
       if (user) {
         // Atualizar nome do usuário na interface
         const userNameElement = document.getElementById('userName');
         if (userNameElement) {
           userNameElement.textContent = user.username;
         }
       }
     }
     
     async loadReports() {
       try {
         this.toggleLoading(true);
         const reports = await this.ftpService.listReports();
         this.renderReports(reports);
       } catch (error) {
         console.error('Erro ao carregar relatórios:', error);
         this.showError('Não foi possível carregar os relatórios. Tente novamente mais tarde.');
       } finally {
         this.toggleLoading(false);
       }
     }
     
     renderReports(reports) {
       const container = document.getElementById('reports-container');
       if (!container) return;
       
       // Limpar container
       container.innerHTML = '';
       
       if (!reports || reports.length === 0) {
         container.innerHTML = `
           <div class="col-span-full text-center py-12">
             <span class="material-icons text-5xl text-[#8B2635] mb-4">description</span>
             <p class="text-[#6B4423] text-xl">Nenhum relatório encontrado</p>
           </div>
         `;
         return;
       }
       
       // Adicionar relatórios ao container
       reports.forEach(report => {
         const card = this.createReportCard(report);
         container.appendChild(card);
       });
     }
     
     createReportCard(report) {
       // Criar elemento para o card do relatório
       const article = document.createElement('div');
       article.className = 'bg-gradient-to-br from-white to-[#F5F0E8] rounded-2xl shadow-lg card-hover border border-[#F5F0E8]';
       
       // Gradientes para diferentes tipos de relatórios
       const gradients = [
         'from-[#8B2635] to-[#992D3D]',
         'from-[#4A6B8A] to-[#8B2635]',
         'from-[#D4A574] to-[#8B2635]'
       ];
       
       const randomGradient = gradients[Math.floor(Math.random() * gradients.length)];
       
       article.innerHTML = `
         <div class="h-48 bg-gradient-to-br ${randomGradient} relative flex items-center justify-center">
           <span class="material-icons text-white text-6xl">picture_as_pdf</span>
         </div>
         <div class="p-6">
           <div class="text-sm text-[#6B4423] mb-2">${this.formatDate(report.date)}</div>
           <h3 class="text-xl font-bold text-[#6B4423] mb-3">${report.title}</h3>
           <p class="text-[#8B2635] mb-4">${report.description}</p>
           <div class="flex gap-2">
             <button class="flex-1 bg-[#8B2635] text-white py-2 px-4 rounded-lg hover:bg-[#992D3D] transition-colors duration-300 font-medium flex items-center justify-center download-report" data-id="${report.id}">
               <span class="material-icons mr-2">download</span> Baixar
             </button>
             <button class="delete-report p-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors duration-300" data-id="${report.id}">
               <span class="material-icons">delete</span>
             </button>
           </div>
         </div>
       `;
       
       // Adicionar evento de clique para download
       const downloadButton = article.querySelector('.download-report');
       if (downloadButton) {
         downloadButton.addEventListener('click', (e) => {
           e.preventDefault();
           this.downloadReport(report.id);
         });
       }
       
       // Adicionar evento de clique para exclusão
       const deleteButton = article.querySelector('.delete-report');
       if (deleteButton) {
         deleteButton.addEventListener('click', (e) => {
           e.preventDefault();
           if (confirm('Tem certeza que deseja excluir este relatório?')) {
             this.deleteReport(report.id);
           }
         });
       }
       
       return article;
     }
     
     formatDate(dateString) {
       const options = { day: 'numeric', month: 'long', year: 'numeric' };
       return new Date(dateString).toLocaleDateString('pt-BR', options);
     }
     
     handleFileSelect(event) {
       const file = event.target.files[0];
       const fileInfo = document.getElementById('fileInfo');
       
       if (file && fileInfo) {
         fileInfo.innerHTML = `
           <strong>Arquivo selecionado:</strong> ${file.name}<br>
           <strong>Tamanho:</strong> ${(file.size / 1024 / 1024).toFixed(2)} MB<br>
           <strong>Tipo:</strong> ${file.type}
         `;
         fileInfo.classList.remove('hidden');
       }
     }
     
     async handleFileUpload(event) {
       event.preventDefault();
       
       const fileInput = document.getElementById('fileInput');
       const reportTitle = document.getElementById('reportTitle').value;
       const reportDescription = document.getElementById('reportDescription').value;
       
       if (!fileInput.files.length) {
         alert('Por favor, selecione um arquivo para enviar.');
         return;
       }
       
       const file = fileInput.files[0];
       
       // Validar tipo de arquivo
       if (file.type !== 'application/pdf') {
         alert('Por favor, selecione um arquivo PDF.');
         return;
       }
       
       // Validar tamanho do arquivo (máximo 10MB)
       if (file.size > 10 * 1024 * 1024) {
         alert('O arquivo deve ter no máximo 10MB.');
         return;
       }
       
       // Validar campos obrigatórios
       if (!reportTitle) {
         alert('Por favor, informe o título do relatório.');
         return;
       }
       
       try {
         // Mostrar indicador de carregamento
         this.showUploadStatus('Enviando arquivo...', 'processing');
         
         // Enviar arquivo via FTP Service
         const metadata = {
           title: reportTitle,
           description: reportDescription
         };
         
         await this.ftpService.uploadReport(file, metadata);
         
         this.showUploadStatus('Arquivo enviado com sucesso!', 'success');
         
         // Limpar formulário
         document.getElementById('uploadForm').reset();
         const fileInfo = document.getElementById('fileInfo');
         if (fileInfo) {
           fileInfo.classList.add('hidden');
         }
         
         // Atualizar lista de relatórios
         await this.loadReports();
         
         // Limpar mensagem após 3 segundos
         setTimeout(() => {
           this.showUploadStatus('', 'hidden');
         }, 3000);
       } catch (error) {
         console.error('Erro ao enviar arquivo:', error);
         this.showUploadStatus(`Erro: ${error.message}`, 'error');
       }
     }
     
     async downloadReport(reportId) {
       try {
         const downloadUrl = await this.ftpService.downloadReport(reportId);
         
         // Criar elemento de link e clicar para download
         const link = document.createElement('a');
         link.href = downloadUrl;
         link.target = '_blank';
         link.download = ''; // O nome do arquivo será determinado pelo servidor
         document.body.appendChild(link);
         link.click();
         document.body.removeChild(link);
       } catch (error) {
         console.error('Erro ao baixar relatório:', error);
         alert('Erro ao baixar relatório. Tente novamente.');
       }
     }
     
     async deleteReport(reportId) {
       try {
         await this.ftpService.deleteReport(reportId);
         
         // Atualizar lista de relatórios
         await this.loadReports();
       } catch (error) {
         console.error('Erro ao excluir relatório:', error);
         alert('Erro ao excluir relatório. Tente novamente.');
       }
     }
     
     showUploadStatus(message, status) {
       // Criar ou atualizar elemento de status
       let statusElement = document.getElementById('uploadStatus');
       if (!statusElement) {
         statusElement = document.createElement('div');
         statusElement.id = 'uploadStatus';
         statusElement.className = 'mt-4 p-4 rounded-lg';
         const form = document.getElementById('uploadForm');
         if (form) {
           form.parentNode.insertBefore(statusElement, form.nextSibling);
         }
       }
       
       // Definir classe e mensagem com base no status
       statusElement.textContent = message;
       
       switch (status) {
         case 'processing':
           statusElement.className = 'mt-4 p-4 rounded-lg bg-yellow-100 text-yellow-800';
           break;
         case 'success':
           statusElement.className = 'mt-4 p-4 rounded-lg bg-green-100 text-green-800';
           break;
         case 'error':
           statusElement.className = 'mt-4 p-4 rounded-lg bg-red-100 text-red-800';
           break;
         case 'hidden':
           statusElement.className = 'hidden';
           break;
         default:
           statusElement.className = 'mt-4 p-4 rounded-lg bg-gray-100 text-gray-800';
       }
     }
     
     toggleLoading(show) {
       const loading = document.getElementById('reports-loading');
       if (loading) {
         loading.classList.toggle('hidden', !show);
       }
       const container = document.getElementById('reports-container');
       if (container) {
         container.classList.toggle('opacity-50', show);
       }
     }
     
     showError(message) {
       const container = document.getElementById('reports-container');
       if (container) {
         container.innerHTML = `
           <div class="col-span-full text-center py-12">
             <span class="material-icons text-5xl text-[#8B2635] mb-4">error</span>
             <p class="text-[#6B4423] text-xl">${message}</p>
           </div>
         `;
       }
     }
   }
   
   // Inicializar quando o DOM estiver pronto
   document.addEventListener('DOMContentLoaded', () => {
     new AdminDashboard();
   });
   ```

##### Dia 5: Atualização da Área Administrativa
1. **Atualizar admin.html para funcionalidades reais**
   ```html
   <!-- admin.html - Seção de relatórios -->
   <section class="py-20 bg-white">
     <div class="container mx-auto px-6">
       <div class="text-center mb-16">
         <h2 class="text-4xl font-bold text-[#6B4423] mb-4">Gerenciamento de Relatórios</h2>
         <p class="text-xl text-[#8B2635]">Upload e gerenciamento de relatórios PDF</p>
       </div>
       
       <!-- Formulário de upload -->
       <div class="bg-gradient-to-r from-emerald-50 to-teal-50 p-8 rounded-2xl shadow-lg mb-12">
         <h3 class="text-2xl font-bold text-gray-800 mb-6">Enviar Novo Relatório</h3>
         <form id="uploadForm">
           <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
             <div>
               <label class="block text-gray-700 font-medium mb-2">Título do Relatório</label>
               <input type="text" id="reportTitle" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500" placeholder="Ex: Relatório Mensal de Produção" required>
             </div>
             <div>
               <label class="block text-gray-700 font-medium mb-2">Arquivo PDF</label>
               <input type="file" id="fileInput" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500" accept=".pdf" required>
             </div>
           </div>
           <div class="mt-6">
             <label class="block text-gray-700 font-medium mb-2">Descrição</label>
             <textarea id="reportDescription" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500" placeholder="Descreva brevemente o conteúdo do relatório"></textarea>
           </div>
           <div id="fileInfo" class="mt-4 text-sm text-gray-600 hidden"></div>
           <div class="mt-6">
             <button type="submit" class="bg-[#8B2635] text-white font-semibold py-3 px-8 rounded-lg hover:bg-[#992D3D] transition-colors duration-300">
               Enviar Relatório
             </button>
           </div>
         </form>
       </div>
       
       <!-- Lista de relatórios -->
       <div id="reports-loading" class="text-center py-8 hidden">
         <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-[#8B2635]"></div>
         <p class="mt-4 text-[#6B4423]">Carregando relatórios...</p>
       </div>
       <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="reports-container">
         <!-- Relatórios serão carregados dinamicamente aqui -->
       </div>
     </div>
   </section>
   ```

##### Dia 6: Integração com Página Principal
1. **Atualizar reports.js para integração real**
   ```javascript
   // assets/js/components/reports.js
   class ReportsManager {
     constructor() {
       this.apiClient = new ApiClient('http://localhost:8000');
       this.ftpService = new FtpService(this.apiClient);
       this.reportsContainer = document.getElementById('reports-container');
       this.init();
     }
   
     init() {
       // Carregar relatórios iniciais
       this.loadReports();
     }
   
     async loadReports() {
       try {
         // Carregar relatórios reais via API
         const reports = await this.ftpService.listReports();
         
         // Limpar container
         if (this.reportsContainer) {
           // Remover os exemplos estáticos, mantendo apenas os carregados dinamicamente
           this.reportsContainer.innerHTML = '';
           
           // Limitar a 3 relatórios para a página principal
           const limitedReports = reports.slice(0, 3);
           
           // Adicionar relatórios ao container
           limitedReports.forEach(report => {
             const card = this.createReportCard(report);
             this.reportsContainer.appendChild(card);
           });
           
           // Se não houver relatórios, mostrar mensagem
           if (limitedReports.length === 0) {
             this.reportsContainer.innerHTML = `
               <div class="col-span-full text-center py-8">
                 <p class="text-[#6B4423]">Nenhum relatório disponível no momento.</p>
               </div>
             `;
           }
         }
       } catch (error) {
         console.error('Erro ao carregar relatórios:', error);
         this.showError('Não foi possível carregar os relatórios. Tente novamente mais tarde.');
       }
     }
   
     createReportCard(report) {
       // Criar elemento para o card do relatório
       const article = document.createElement('article');
       article.className = 'bg-gradient-to-br from-white to-[#F5F0E8] rounded-2xl shadow-lg card-hover border border-[#F5F0E8]';
       
       // Gradientes para diferentes tipos de relatórios
       const gradients = [
         'from-[#8B2635] to-[#992D3D]',
         'from-[#4A6B8A] to-[#8B2635]',
         'from-[#D4A574] to-[#8B2635]'
       ];
       
       const randomGradient = gradients[Math.floor(Math.random() * gradients.length)];
       
       article.innerHTML = `
         <div class="h-48 bg-gradient-to-br ${randomGradient} relative flex items-center justify-center">
           <span class="material-icons text-white text-6xl">picture_as_pdf</span>
         </div>
         <div class="p-6">
           <div class="text-sm text-[#6B4423] mb-2">${this.formatDate(report.date)}</div>
           <h3 class="text-xl font-bold text-[#6B4423] mb-3">${report.title}</h3>
           <p class="text-[#8B2635] mb-4">${report.description}</p>
           <button class="w-full bg-[#8B2635] text-white py-2 px-4 rounded-lg hover:bg-[#992D3D] transition-colors duration-300 font-medium flex items-center justify-center download-report" data-id="${report.id}">
             <span class="material-icons mr-2">download</span> Baixar PDF
           </button>
         </div>
       `;
       
       // Adicionar evento de clique para download
       const downloadButton = article.querySelector('.download-report');
       if (downloadButton) {
         downloadButton.addEventListener('click', (e) => {
           e.preventDefault();
           this.downloadReport(report.id);
         });
       }
       
       return article;
     }
   
     formatDate(dateString) {
       const options = { day: 'numeric', month: 'long', year: 'numeric' };
       return new Date(dateString).toLocaleDateString('pt-BR', options);
     }
   
     async downloadReport(reportId) {
       try {
         const downloadUrl = await this.ftpService.downloadReport(reportId);
         
         // Criar elemento de link e clicar para download
         const link = document.createElement('a');
         link.href = downloadUrl;
         link.target = '_blank';
         link.download = ''; // O nome do arquivo será determinado pelo servidor
         document.body.appendChild(link);
         link.click();
         document.body.removeChild(link);
       } catch (error) {
         console.error('Erro ao baixar relatório:', error);
         alert('Erro ao baixar relatório. Tente novamente.');
       }
     }
   
     showError(message) {
       if (this.reportsContainer) {
         this.reportsContainer.innerHTML = `
           <div class="col-span-full text-center py-12">
             <span class="material-icons text-5xl text-[#8B2635] mb-4">error</span>
             <p class="text-[#6B4423] text-xl">${message}</p>
           </div>
         `;
       }
     }
   }
   
   // Inicializar quando o DOM estiver pronto
   document.addEventListener('DOMContentLoaded', () => {
     new ReportsManager();
   });
   ```

#### 3.3 Integração com Backend e FTP (Dias 7-10)

##### Dia 7: Configuração do Backend Laravel
1. **Configurar ambiente Laravel**
   - Instalar Laravel
   - Configurar banco de dados
   - Configurar autenticação com Sanctum
   - Configurar integração com FTP

2. **Criar modelos e migrations**
   ```php
   // app/Models/Report.php
   <?php
   
   namespace App\Models;
   
   use Illuminate\Database\Eloquent\Factories\HasFactory;
   use Illuminate\Database\Eloquent\Model;
   
   class Report extends Model
   {
       use HasFactory;
   
       protected $fillable = [
           'title',
           'description',
           'file_name',
           'file_path',
           'uploaded_by',
           'upload_date'
       ];
   
       protected $casts = [
           'upload_date' => 'datetime',
       ];
   }
   ```

##### Dia 8: Implementação da API Laravel
1. **Criar endpoints para relatórios**
   ```php
   // routes/api.php
   <?php
   
   use Illuminate\Support\Facades\Route;
   use App\Http\Controllers\ReportController;
   use App\Http\Controllers\AuthController;
   
   Route::post('/auth/login', [AuthController::class, 'login']);
   Route::post('/auth/logout', [AuthController::class, 'logout']);
   
   Route::middleware('auth:sanctum')->group(function () {
       Route::get('/reports', [ReportController::class, 'index']);
       Route::post('/reports', [ReportController::class, 'store']);
       Route::get('/reports/{id}', [ReportController::class, 'show']);
       Route::get('/reports/{id}/download', [ReportController::class, 'download']);
       Route::delete('/reports/{id}', [ReportController::class, 'destroy']);
   });
   ```

2. **Implementar controller de relatórios**
   ```php
   // app/Http/Controllers/ReportController.php
   <?php
   
   namespace App\Http\Controllers;
   
   use App\Models\Report;
   use Illuminate\Http\Request;
   use Illuminate\Support\Facades\Storage;
   use Illuminate\Support\Facades\Auth;
   
   class ReportController extends Controller
   {
       public function index()
       {
           $reports = Report::orderBy('upload_date', 'desc')->get();
           return response()->json(['data' => $reports]);
       }
   
       public function store(Request $request)
       {
           $request->validate([
               'file' => 'required|file|mimes:pdf|max:10240', // 10MB max
               'title' => 'required|string|max:255',
               'description' => 'nullable|string',
           ]);
   
           $file = $request->file('file');
           $fileName = time() . '_' . $file->getClientOriginalName();
           
           // Salvar arquivo no FTP (ou storage configurado)
           $filePath = $file->storeAs('reports', $fileName, 'ftp');
   
           // Criar registro no banco de dados
           $report = Report::create([
               'title' => $request->title,
               'description' => $request->description,
               'file_name' => $fileName,
               'file_path' => $filePath,
               'uploaded_by' => Auth::id(),
               'upload_date' => now(),
           ]);
   
           return response()->json(['data' => $report]);
       }
   
       public function show($id)
       {
           $report = Report::findOrFail($id);
           return response()->json(['data' => $report]);
       }
   
       public function download($id)
       {
           $report = Report::findOrFail($id);
           
           // Gerar URL temporária para download
           $downloadUrl = Storage::disk('ftp')->url($report->file_path);
           
           return response()->json(['downloadUrl' => $downloadUrl]);
       }
   
       public function destroy($id)
       {
           $report = Report::findOrFail($id);
           
           // Excluir arquivo do FTP
           Storage::disk('ftp')->delete($report->file_path);
           
           // Excluir registro do banco de dados
           $report->delete();
           
           return response()->json(['message' => 'Relatório excluído com sucesso']);
       }
   }
   ```

##### Dia 9: Configuração de FTP no Laravel
1. **Configurar disco FTP no filesystems.php**
   ```php
   // config/filesystems.php
   <?php
   
   return [
       'default' => env('FILESYSTEM_DRIVER', 'local'),
   
       'disks' => [
           'local' => [
               'driver' => 'local',
               'root' => storage_path('app'),
           ],
   
           'public' => [
               'driver' => 'local',
               'root' => storage_path('app/public'),
               'url' => env('APP_URL').'/storage',
               'visibility' => 'public',
           ],
   
           'ftp' => [
               'driver' => 'ftp',
               'host' => env('FTP_HOST'),
               'username' => env('FTP_USERNAME'),
               'password' => env('FTP_PASSWORD'),
   
               // Optional FTP Settings
               'port' => env('FTP_PORT', 21),
               'root' => env('FTP_ROOT', '/'),
               'passive' => true,
               'ssl' => true,
               'timeout' => 30,
           ],
       ],
   
       'links' => [
           public_path('storage') => storage_path('app/public'),
       ],
   ];
   ```

2. **Adicionar variáveis de ambiente**
   ```env
   FTP_HOST=seu.servidor.ftp.com
   FTP_USERNAME=seu_usuario
   FTP_PASSWORD=sua_senha
   FTP_ROOT=/relatorios
   FTP_PORT=21
   ```

##### Dia 10: Testes e Validação
1. **Testar fluxo completo**
   - Login/logout de administrador
   - Listagem de relatórios
   - Upload de novos relatórios
   - Download de relatórios existentes
   - Exclusão de relatórios

2. **Validar tratamento de erros**
   - Credenciais inválidas
   - Falhas na conexão com FTP
   - Uploads de arquivos inválidos
   - Tamanhos de arquivo excedidos

#### 3.4 Refinamento e Otimização (Dias 11-14)

##### Dia 11: Melhorias de Interface
1. **Adicionar feedback visual**
   ```javascript
   // Melhorar experiência do usuário com indicadores visuais
   class AdminDashboard {
     // ... métodos existentes ...
     
     showNotification(message, type = 'info') {
       // Criar notificação toast
       const notification = document.createElement('div');
       notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 ${
         type === 'success' ? 'bg-green-500 text-white' :
         type === 'error' ? 'bg-red-500 text-white' :
         'bg-blue-500 text-white'
       }`;
       notification.textContent = message;
       notification.style.transform = 'translateX(100%)';
       
       document.body.appendChild(notification);
       
       // Animação de entrada
       setTimeout(() => {
         notification.style.transform = 'translateX(0)';
       }, 100);
       
       // Remover após 3 segundos
       setTimeout(() => {
         notification.style.transform = 'translateX(100%)';
         setTimeout(() => {
           if (notification.parentNode) {
             notification.parentNode.removeChild(notification);
           }
         }, 300);
       }, 3000);
     }
   }
   ```

##### Dia 12: Otimizações de Performance
1. **Implementar caching**
   ```javascript
   class ReportsManager {
     constructor() {
       // ... código existente ...
       this.cache = {
         reports: null,
         timestamp: 0
       };
       this.CACHE_DURATION = 5 * 60 * 1000; // 5 minutos
     }
     
     async loadReports(useCache = true) {
       const now = Date.now();
       
       // Verificar se podemos usar o cache
       if (useCache && this.cache.reports && (now - this.cache.timestamp) < this.CACHE_DURATION) {
         this.renderReports(this.cache.reports);
         return;
       }
       
       try {
         // ... carregar relatórios do FTP ...
         const reports = await this.ftpService.listReports();
         
         // Armazenar em cache
         this.cache.reports = reports;
         this.cache.timestamp = now;
         
         this.renderReports(reports);
       } catch (error) {
         // ... tratamento de erro ...
       }
     }
   }
   ```

##### Dia 13: Implementar Busca e Filtros
1. **Adicionar funcionalidade de busca**
   ```javascript
   class ReportsManager {
     constructor() {
       // ... código existente ...
       this.initSearch();
     }
     
     initSearch() {
       const searchInput = document.getElementById('searchReports');
       if (searchInput) {
         searchInput.addEventListener('input', (e) => {
           this.filterReports(e.target.value);
         });
       }
     }
     
     filterReports(searchTerm) {
       const reports = this.cache.reports || [];
       const filtered = reports.filter(report => 
         report.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
         report.description.toLowerCase().includes(searchTerm.toLowerCase())
       );
       this.renderReports(filtered);
     }
   }
   ```

##### Dia 14: Testes Finais e Documentação
1. **Realizar testes completos**
   - Testes em diferentes navegadores
   - Testes de responsividade
   - Testes de usabilidade
   - Testes de segurança

2. **Documentar processo**
   ```markdown
   ## Documentação da Funcionalidade de Relatórios FTP
   
   ### Como usar:
   1. Faça login na área administrativa
   2. Na seção de relatórios, você verá os arquivos PDF disponíveis no servidor FTP
   3. Clique em "Baixar" para fazer download de um relatório
   4. Use o formulário para enviar novos relatórios
   
   ### Configuração:
   - As credenciais do FTP estão configuradas no backend Laravel
   - As credenciais são armazenadas de forma segura nas variáveis de ambiente
   
   ### Funcionalidades:
   - Listagem de relatórios com metadados
   - Upload de novos relatórios
   - Download de relatórios existentes
   - Exclusão de relatórios
   - Busca e filtros
   ```
   
## 4. Considerações Finais

### 4.1 Próximos Passos
1. **Expansão de funcionalidades**
   - Adicionar categorização de relatórios
   - Implementar busca e filtros avançados
   - Adicionar visualizador PDF embutido
   - Implementar histórico de alterações

2. **Integração com outros módulos**
   - Conectar módulo de cotações com API real
   - Conectar módulo de notícias com API real
   - Implementar sistema de permissões granular

### 4.2 Manutenção Contínua
1. **Monitoramento**
   - Logs de acesso e erros
   - Monitoramento de desempenho
   - Alertas para falhas de conexão

2. **Atualizações**
   - Manter bibliotecas atualizadas
   - Aplicar patches de segurança
   - Melhorias baseadas em feedback

## 5. Conclusão

Este plano utiliza o Qwen Code seguindo rigorosamente o protocolo PRAR para desenvolver um sistema robusto de gerenciamento de relatórios via FTP. A abordagem transforma o MVP atual em uma solução completa com integração real com backend Laravel e servidor FTP.

A implementação foi dividida em fases claras:
1. **Perceber**: Análise do sistema atual e requisitos
2. **Raciocinar**: Planejamento da arquitetura robusta necessária
3. **Agir**: Implementação prática dos componentes
4. **Refinar**: Otimizações e melhorias pós-implementação

O resultado é um sistema funcional que atende às necessidades reais de gerenciamento de relatórios com segurança, escalabilidade e uma base sólida para evoluções futuras.