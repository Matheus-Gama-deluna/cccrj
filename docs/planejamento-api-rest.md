# Planejamento de Integração com Backend (API REST - Laravel)

## 1. Introdução

### 1.1 Objetivo
Definir a estratégia e implementação para integração do frontend com um backend baseado em API REST desenvolvido com PHP e Laravel, substituindo as funcionalidades simuladas atualmente no sistema.

### 1.2 Escopo
Este planejamento abrange:
- Estrutura da API REST com Laravel
- Endpoints necessários
- Tratamento de autenticação
- Manipulação de dados
- Tratamento de erros
- Padrões de comunicação

## 2. Arquitetura da API

### 2.1 Estrutura Base
```
https://api.cccrj.com.br/api/v1/
```

### 2.2 Tecnologias Backend
- **Linguagem**: PHP 8+
- **Framework**: Laravel 10+
- **Banco de Dados**: MySQL 8+ / PostgreSQL
- **Autenticação**: Laravel Sanctum (API Tokens) ou Passport (OAuth2)

### 2.2 Formatos de Dados
- **Requisições**: JSON
- **Respostas**: JSON
- **Codificação**: UTF-8

### 2.3 Cabeçalhos Padrão
```
Content-Type: application/json
Accept: application/json
```

## 3. Endpoints da API

### 3.1 Autenticação
| Método | Endpoint | Descrição |
|--------|----------|-----------|
| POST | `/auth/login` | Autenticação de usuário |
| POST | `/auth/logout` | Encerramento de sessão |
| GET | `/auth/me` | Dados do usuário autenticado |

### 3.2 Cotações
| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/quotes` | Listagem de cotações atuais |
| GET | `/quotes/{type}` | Cotação específica por tipo |
| GET | `/quotes/{type}/history` | Histórico de cotações |
| POST | `/quotes` | (Admin) Criação de nova cotação |
| PUT | `/quotes/{id}` | (Admin) Atualização de cotação |
| DELETE | `/quotes/{id}` | (Admin) Exclusão de cotação |

### 3.3 Notícias
| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/news` | Listagem de notícias |
| GET | `/news/{id}` | Detalhes de notícia específica |
| POST | `/news` | (Admin) Criação de nova notícia |
| PUT | `/news/{id}` | (Admin) Atualização de notícia |
| DELETE | `/news/{id}` | (Admin) Exclusão de notícia |

### 3.4 Relatórios
| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/reports` | Listagem de relatórios |
| GET | `/reports/{id}` | Detalhes de relatório específico |
| GET | `/reports/{id}/download` | Download de relatório |
| POST | `/reports` | (Admin) Upload de novo relatório |
| PUT | `/reports/{id}` | (Admin) Atualização de relatório |
| DELETE | `/reports/{id}` | (Admin) Exclusão de relatório |

### 3.5 Usuários (Admin)
| Método | Endpoint | Descrição |
|--------|----------|-----------|
| GET | `/users` | (Admin) Listagem de usuários |
| GET | `/users/{id}` | (Admin) Detalhes de usuário |
| POST | `/users` | (Admin) Criação de novo usuário |
| PUT | `/users/{id}` | (Admin) Atualização de usuário |
| DELETE | `/users/{id}` | (Admin) Exclusão de usuário |

## 4. Autenticação e Autorização

### 4.1 Autenticação com Laravel Sanctum
- API Tokens para autenticação stateless
- Expiração configurável
- Suporte a múltiplos tokens por usuário
- Proteção CSRF integrada

### 4.2 Autenticação com Laravel Passport (OAuth2)
- Implementação completa do OAuth2
- Suporte a diferentes grants
- Refresh tokens para sessões prolongadas
- Escopos de permissão

### 4.3 Permissões
- **Usuário comum**: Acesso somente leitura
- **Administrador**: Acesso completo a todas as funcionalidades
- **Gates e Policies**: Controle de acesso baseado em papéis com Laravel

### 4.4 Cabeçalho de Autenticação
```
Authorization: Bearer <token>
Accept: application/json
```

## 5. Estrutura de Dados

### 5.1 Cotações
```json
{
  "id": "string",
  "type": "arabica|conilon|especial",
  "currentPrice": "number",
  "variation": "number",
  "lastUpdate": "ISO 8601 date",
  "history": [
    {
      "date": "ISO 8601 date",
      "price": "number"
    }
  ]
}
```

### 5.2 Notícias
```json
{
  "id": "number",
  "category": "producao|exportacao|evento",
  "title": "string",
  "summary": "string",
  "content": "string",
  "publishDate": "ISO 8601 date",
  "author": "string",
  "tags": ["string"]
}
```

### 5.3 Relatórios
```json
{
  "id": "number",
  "title": "string",
  "description": "string",
  "filename": "string",
  "uploadDate": "ISO 8601 date",
  "size": "number",
  "category": "string"
}
```

### 5.4 Usuários
```json
{
  "id": "number",
  "username": "string",
  "email": "string",
  "role": "admin|user",
  "createdAt": "ISO 8601 date",
  "lastLogin": "ISO 8601 date"
}
```

## 6. Tratamento de Erros

### 6.1 Códigos de Status HTTP
- `200`: Sucesso
- `201`: Criado com sucesso
- `400`: Requisição inválida
- `401`: Não autorizado
- `403`: Acesso proibido
- `404`: Não encontrado
- `500`: Erro interno do servidor

### 6.2 Estrutura de Respostas de Erro
```json
{
  "error": {
    "code": "string",
    "message": "string",
    "details": "object"
  }
}
```

## 7. Implementação no Frontend

### 7.1 Cliente HTTP
Criar um cliente HTTP centralizado para todas as requisições:

```javascript
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
    const config = {
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        ...options.headers
      },
      ...options
    };
    
    if (this.token) {
      config.headers.Authorization = `Bearer ${this.token}`;
    }
    
    const response = await fetch(url, config);
    const data = await response.json();
    
    if (!response.ok) {
      if (response.status === 401) {
        this.clearToken();
        window.location.href = '/login.html';
      }
      throw new ApiError(data.error.message, response.status, data.error);
    }
    
    return data;
  }
}
```

### 7.2 Integração com Módulos

#### 7.2.1 Módulo de Cotações
```javascript
class CoffeeQuotes {
  constructor(apiClient) {
    this.api = apiClient;
  }
  
  async loadQuotes() {
    try {
      const data = await this.api.request('/quotes');
      this.renderQuotes(data);
    } catch (error) {
      this.handleError(error);
    }
  }
}
```

#### 7.2.2 Módulo de Notícias
```javascript
class NewsManager {
  constructor(apiClient) {
    this.api = apiClient;
  }
  
  async loadNews(page = 1) {
    try {
      const data = await this.api.request(`/news?page=${page}`);
      this.renderNews(data);
    } catch (error) {
      this.handleError(error);
    }
  }
}
```

#### 7.2.3 Módulo de Relatórios
```javascript
class ReportsManager {
  constructor(apiClient) {
    this.api = apiClient;
  }
  
  async loadReports() {
    try {
      const data = await this.api.request('/reports');
      this.renderReports(data);
    } catch (error) {
      this.handleError(error);
    }
  }
  
  async downloadReport(reportId) {
    try {
      const response = await this.api.request(`/reports/${reportId}/download`);
      // Tratar download
    } catch (error) {
      this.handleError(error);
    }
  }
}
```

#### 7.2.4 Sistema de Autenticação
```javascript
class AuthManager {
  constructor(apiClient) {
    this.api = apiClient;
  }
  
  async login(username, password) {
    try {
      const data = await this.api.request('/auth/login', {
        method: 'POST',
        body: JSON.stringify({ username, password })
      });
      
      this.api.setToken(data.token);
      this.setUser(data.user);
      return data;
    } catch (error) {
      throw error;
    }
  }
  
  async logout() {
    try {
      await this.api.request('/auth/logout', {
        method: 'POST'
      });
    } finally {
      this.api.setToken(null);
      this.clearUser();
    }
  }
}
```

## 8. Considerações de Segurança

### 8.1 CORS
Configurar CORS adequadamente no backend para permitir requisições do frontend.

### 8.2 HTTPS
Todas as comunicações devem ser feitas via HTTPS.

### 8.3 Tratamento de Dados Sensíveis
- Nunca armazenar senhas em texto plano
- Usar tokens JWT com expiração
- Validar e sanitizar todas as entradas

## 9. Plano de Migração

### 9.1 Fase 1: Infraestrutura
- Configurar servidor backend
- Implementar endpoints básicos
- Configurar autenticação

### 9.2 Fase 2: Integração Gradual
- Substituir módulo de cotações
- Substituir módulo de notícias
- Substituir módulo de relatórios
- Substituir sistema de autenticação

### 9.3 Fase 3: Testes e Validação
- Testes de integração completos
- Validação de performance
- Revisão de segurança

## 10. Monitoramento e Logging

### 10.1 Logging no Frontend
- Registrar requisições e respostas
- Registrar erros de API
- Monitorar tempo de resposta

### 10.2 Métricas
- Taxa de sucesso de requisições
- Tempo médio de resposta
- Taxa de erros por endpoint