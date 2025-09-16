# Documento de Design de Arquitetura

## 1. Introdução

### 1.1 Finalidade
Este documento descreve a arquitetura do sistema do Centro de Comércio de Café do Rio de Janeiro (CCCRJ), incluindo decisões arquiteturais, diagramas e padrões utilizados.

### 1.2 Escopo
O documento abrange a arquitetura frontend do sistema, descrevendo a estrutura de componentes, padrões de design, fluxo de dados e integrações com serviços externos.

### 1.3 Definições, Acrônimos e Abreviações
- CCCRJ: Centro de Comércio de Café do Rio de Janeiro
- MVC: Model-View-Controller
- API: Application Programming Interface
- FTP: File Transfer Protocol
- CDN: Content Delivery Network

### 1.4 Referências
- Análise do Sistema Atual
- Especificação de Requisitos de Software
- Documento de Requisitos do Produto

## 2. Representação Arquitetural

### 2.1 Estilo Arquitetural
O sistema adota uma arquitetura modular baseada em componentes, com separação clara entre apresentação, lógica de negócio e integração com serviços externos.

### 2.2 Padrões de Design Utilizados
- **Módulo**: Para encapsulamento de funcionalidades
- **Observer/Pub-Sub**: Para comunicação entre componentes
- **Singleton**: Para gerenciamento de configurações
- **Factory**: Para criação de componentes dinâmicos

### 2.3 Tecnologias e Frameworks
- **HTML5**: Estrutura semântica das páginas
- **Tailwind CSS**: Framework CSS utilitário
- **JavaScript (ES6+)**: Lógica de negócio e interatividade
- **Google Fonts**: Tipografia personalizada
- **Material Icons**: Biblioteca de ícones
- **PHP 8+**: Linguagem backend
- **Laravel 10+**: Framework backend

## 3. Metas e Restrições Arquiteturais

### 3.1 Metas de Qualidade
- **Performance**: Tempo de carregamento < 3 segundos
- **Escalabilidade**: Suporte a expansão de funcionalidades
- **Manutenibilidade**: Código modular e bem documentado
- **Segurança**: Proteção contra vulnerabilidades comuns
- **Acessibilidade**: Conformidade com WCAG 2.1 AA

### 3.2 Restrições
- Arquitetura frontend separada do backend
- Frontend hospedado em serviço de hospedagem estática
- Backend desenvolvido com PHP e Laravel
- Compatibilidade com navegadores modernos

## 4. Visão Lógica

### 4.1 Diagrama de Componentes

```
┌─────────────────────────────────────────────────────────────────┐
│                         Camada de Apresentação                  │
├─────────────────────────────────────────────────────────────────┤
│  ┌───────────────┐  ┌───────────────┐  ┌─────────────────────┐ │
│  │   index.html  │  │  login.html   │  │    admin.html       │ │
│  └───────────────┘  └───────────────┘  └─────────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
                                  │
                                  ▼
┌─────────────────────────────────────────────────────────────────┐
│                         Camada de Lógica                        │
├─────────────────────────────────────────────────────────────────┤
│  ┌───────────────┐  ┌───────────────┐  ┌─────────────────────┐ │
│  │   main.js     │  │   auth.js     │  │     admin.js        │ │
│  └───────────────┘  └───────────────┘  └─────────────────────┘ │
│  ┌───────────────┐  ┌───────────────┐  ┌─────────────────────┐ │
│  │ components/   │  │ components/   │  │   components/       │ │
│  │ ┌───────────┐ │  │ ┌───────────┐ │  │   ┌─────────────┐   │ │
│  │ │quotes.js  │ │  │ │ news.js   │ │  │   │reports.js   │   │ │
│  │ └───────────┘ │  │ └───────────┘ │  │   └─────────────┘   │ │
│  │ ┌───────────┐ │  │ ┌───────────┐ │  │   ┌─────────────┐   │ │
│  │ │calculator.│ │  │ │           │ │  │   │             │   │ │
│  │ │   js      │ │  │ │           │ │  │   │             │   │ │
│  │ └───────────┘ │  │ └───────────┘ │  │   └─────────────┘   │ │
│  └───────────────┘  └───────────────┘  └─────────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
                                  │
                                  ▼
┌─────────────────────────────────────────────────────────────────┐
│                      Camada de Integração                       │
├─────────────────────────────────────────────────────────────────┤
│  ┌───────────────┐  ┌───────────────┐  ┌─────────────────────┐ │
│  │  API Laravel  │  │ Servidor FTP  │  │  Serviços Externos  │ │
│  │   (REST)      │  │               │  │                     │ │
│  └───────────────┘  └───────────────┘  └─────────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
```

### 4.2 Componentes Principais

#### 4.2.1 Módulo de Cotações (quotes.js)
- **Responsabilidade**: Gerenciar a exibição e atualização de cotações
- **Dependências**: Nenhuma
- **Interface**: 
  - `init()`: Inicializa o módulo
  - `updatePrices()`: Atualiza os preços exibidos
  - `destroy()`: Limpa recursos e timers

#### 4.2.2 Módulo de Notícias (news.js)
- **Responsabilidade**: Gerenciar a exibição de notícias
- **Dependências**: Nenhuma
- **Interface**:
  - `init()`: Inicializa o módulo
  - `loadNews()`: Carrega notícias do backend
  - `createNewsCard()`: Cria elemento de notícia
  - `destroy()`: Limpa recursos

#### 4.2.3 Módulo de Relatórios (reports.js)
- **Responsabilidade**: Gerenciar a exibição e download de relatórios
- **Dependências**: Nenhuma
- **Interface**:
  - `init()`: Inicializa o módulo
  - `loadReports()`: Carrega relatórios do servidor
  - `downloadReport()`: Inicia download de relatório
  - `destroy()`: Limpa recursos

#### 4.2.4 Sistema de Autenticação (auth.js)
- **Responsabilidade**: Gerenciar autenticação e sessão de usuários
- **Dependências**: Nenhuma
- **Interface**:
  - `init()`: Inicializa o sistema
  - `handleLogin()`: Processa login
  - `handleLogout()`: Processa logout
  - `isAuthenticated()`: Verifica status de autenticação
  - `getCurrentUser()`: Retorna usuário logado

### 4.3 Fluxo de Dados

#### 4.3.1 Fluxo de Cotações
```
1. CoffeeQuotes.init()
2. CoffeeQuotes.updatePrices() (imediato)
3. setInterval -> CoffeeQuotes.updatePrices() (a cada 10s)
4. updatePrices() -> atualiza elementos DOM
```

#### 4.3.2 Fluxo de Notícias
```
1. NewsManager.init()
2. NewsManager.loadNews() (inicial)
3. Usuário clica "Carregar Mais"
4. NewsManager.loadNews() (adicional)
5. loadNews() -> createNewsCard() -> DOM
```

#### 4.3.3 Fluxo de Relatórios
```
1. ReportsManager.init()
2. ReportsManager.loadReports()
3. loadReports() -> cria cards -> DOM
4. Usuário clica "Baixar PDF"
5. downloadReport() -> inicia download
```

#### 4.3.4 Fluxo de Autenticação
```
1. AuthManager.init()
2. Verifica sessão existente
3. Usuário submete login
4. handleLogin() -> valida credenciais
5. Se válido -> salva sessão -> redireciona
6. Usuário faz logout
7. handleLogout() -> limpa sessão -> redireciona
```

## 5. Visão de Processo

### 5.1 Inicialização do Sistema
1. Carregamento de HTML, CSS e JavaScript
2. Execução de `DOMContentLoaded`
3. Inicialização de módulos através de suas classes
4. Verificação de autenticação (quando aplicável)
5. Renderização inicial de conteúdo

### 5.2 Ciclo de Vida de Componentes
```
Criação -> Inicialização -> Uso -> Destruição
    │          │           │        │
    ▼          ▼           ▼        ▼
  new X()   X.init()    X.metodo()  X.destroy()
```

### 5.3 Tratamento de Eventos
- Eventos de clique em elementos DOM
- Eventos de formulário (submit, input)
- Eventos de navegação (scroll, resize)
- Eventos de teclado (quando necessário)

## 6. Visão de Implementação

### 6.1 Estrutura de Diretórios
```
cccrj/
├── index.html
├── login.html
├── admin.html
├── assets/
│   ├── css/
│   │   ├── styles.css
│   │   └── admin.css
│   └── js/
│       ├── main.js
│       ├── auth.js
│       ├── admin.js
│       └── components/
│           ├── quotes.js
│           ├── news.js
│           ├── reports.js
│           └── calculator.js
└── docs/
    ├── especificacao-requisitos-software.md
    ├── documento-requisitos-produto.md
    ├── backlog.md
    └── documento-design-arquitetura.md
```

### 6.2 Padrões de Codificação
- Uso de classes ES6 para componentes
- Funções puras quando possível
- Tratamento de erros com try/catch
- Comentários JSDoc para funções públicas
- Nomes de variáveis e funções descritivos

### 6.3 Gerenciamento de Dependências
- CDN para bibliotecas externas (Tailwind CSS, Google Fonts)
- Arquivos locais para código específico do projeto
- Nenhuma dependência de gerenciadores de pacotes (npm, yarn)

## 7. Visão de Dados

### 7.1 Estrutura de Dados
O sistema não possui armazenamento local de dados complexos, mas trabalha com as seguintes estruturas:

#### 7.1.1 Estrutura de Cotações
```javascript
{
  id: string,
  tipo: string,
  preco: number,
  variacao: number,
  ultimaAtualizacao: Date
}
```

#### 7.1.2 Estrutura de Notícias
```javascript
{
  id: number,
  categoria: string,
  data: string,
  titulo: string,
  resumo: string,
  conteudo: string,
  icone: string
}
```

#### 7.1.3 Estrutura de Relatórios
```javascript
{
  id: number,
  titulo: string,
  data: string,
  descricao: string,
  fileName: string
}
```

#### 7.1.4 Estrutura de Usuário
```javascript
{
  username: string,
  role: string
}
```

### 7.2 Armazenamento
- **LocalStorage**: Para sessão de usuário
- **Memória**: Para dados temporários durante a execução
- **Servidor Externo**: Para dados persistentes (através de API/FTP)

## 8. Tamanho e Performance

### 8.1 Métricas de Performance
- Tempo de carregamento total < 3 segundos
- First Contentful Paint < 1.8 segundos
- Largest Contentful Paint < 2.5 segundos
- First Input Delay < 100ms

### 8.2 Otimizações
- Minificação de CSS e JavaScript
- Compressão de imagens
- Lazy loading de seções não críticas
- Cache de dados quando apropriado
- Uso eficiente de animações

## 9. Qualidade

### 9.1 Padrões de Qualidade
- Código revisado por pares
- Testes unitários para componentes críticos
- Cobertura de testes > 80%
- Análise estática de código
- Revisão de segurança

### 9.2 Métricas de Qualidade
- Taxa de bugs em produção < 1%
- Tempo médio para resolução de bugs < 24h
- Cobertura de testes > 80%
- Conformidade com padrões de acessibilidade

## 10. Considerações Finais

### 10.1 Escalabilidade
A arquitetura modular permite fácil expansão com novos componentes e funcionalidades sem impacto no sistema existente.

### 10.2 Manutenibilidade
A separação clara de responsabilidades e a documentação completa facilitam a manutenção e evolução do sistema.

### 10.3 Segurança
A arquitetura prevê pontos de integração seguros com serviços externos e práticas recomendadas para proteção contra vulnerabilidades comuns.