# DOCUMENTAÇÃO TÉCNICA DO SISTEMA CCCRJ
====================================

## 1. VISÃO GERAL DO SISTEMA
--------------------------

**Nome do Projeto**: Centro de Comércio do Café do Rio de Janeiro (CCCRJ)
**Versão**: 1.0.0
**Data de Lançamento**: 2025-10-06
**Arquitetura**: Frontend/Backend Separated with REST API

### Tecnologias Utilizadas

- **Frontend**: HTML5, Tailwind CSS, JavaScript (ES6+)
- **Backend**: PHP 8+ (Pure PHP, no frameworks)
- **Database**: MySQL
- **Server**: Apache (XAMPP)
- **API**: RESTful API
- **Authentication**: Session-based Authentication

### Equipe de Desenvolvimento

- **Lead Developer**: Equipe de Desenvolvimento CCCRJ
- **Frontend Developer**: Especialista em UI/UX
- **Backend Developer**: Especialista em PHP
- **Database Administrator**: Especialista em MySQL
- **Content Specialist**: Especialista em Conteúdo Histórico

## 2. ESTRUTURA DO PROJETO
------------------------

### root
**Descrição**: Diretório raiz do projeto

**Conteúdo**:
- `api/`: API em PHP puro
- `assets/`: Recursos frontend
- `scraping_cccrj/`: Conteúdo raspado do site original
- `uploads/`: Arquivos enviados
- `reports/`: Relatórios gerados
- `exports/`: Exportações de dados
- `index.html`: Página principal
- `main.js`: Script principal
- `style.css`: Folha de estilo principal
- `README.md`: Documentação principal

### api
**Descrição**: Diretório da API em PHP puro

**Conteúdo**:
- `config/`: Configurações do sistema
- `models/`: Modelos de dados
- `utils/`: Funções utilitárias
- `clipping/`: Endpoints para clipping
- `publications/`: Endpoints para publicações
- `history/`: Endpoints para história
- `about/`: Endpoints para seção sobre
- `crmc/`: Endpoints para CRMC
- `archive/`: Endpoints para acervo

### assets
**Descrição**: Diretório de recursos frontend

**Conteúdo**:
- `css/`: Folhas de estilo
- `js/`: Scripts JavaScript
- `js/components/`: Componentes JavaScript modulares
- `images/`: Imagens e recursos visuais

## 3. COMPONENTES DO SISTEMA
---------------------------

### Frontend Components

#### quotes.js
**Descrição**: Componente de Cotações de Café

**Responsabilidades**:
- Exibir cotações em tempo real
- Atualizar preços automaticamente
- Calcular variações de preços
- Mostrar gráficos de tendência

**Dependências**:
- Tailwind CSS
- Material Icons
- API de cotações (/api/quotes/)

**Eventos**:
- `DOMContentLoaded`: Inicialização do componente
- `priceUpdate`: Atualização de preços
- `error`: Tratamento de erros

#### news.js
**Descrição**: Componente de Notícias

**Responsabilidades**:
- Exibir notícias do setor cafeeiro
- Paginação de notícias
- Filtragem por categoria
- Busca de notícias

**Dependências**:
- Tailwind CSS
- Material Icons
- API de notícias (/api/news/)

**Eventos**:
- `DOMContentLoaded`: Inicialização do componente
- `newsLoad`: Carregamento de notícias
- `error`: Tratamento de erros

#### reports.js
**Descrição**: Componente de Relatórios

**Responsabilidades**:
- Exibir relatórios técnicos em PDF
- Download de relatórios
- Visualização de metadados
- Filtragem por tipo

**Dependências**:
- Tailwind CSS
- Material Icons
- API de relatórios (/api/reports/)

**Eventos**:
- `DOMContentLoaded`: Inicialização do componente
- `reportLoad`: Carregamento de relatórios
- `downloadReport`: Download de relatórios
- `error`: Tratamento de erros

#### calculator.js
**Descrição**: Componente de Calculadora

**Responsabilidades**:
- Converter unidades de medida
- Calcular valores de café
- Exibir resultados
- Validar entradas

**Dependências**:
- Tailwind CSS
- Material Icons

**Eventos**:
- `DOMContentLoaded`: Inicialização do componente
- `calculate`: Realização de cálculos
- `input`: Captura de entradas

#### clipping.js
**Descrição**: Componente de Clipping Histórico

**Responsabilidades**:
- Exibir clipping histórico
- Filtragem por categoria
- Busca de clipping
- Paginação de conteúdo

**Dependências**:
- Tailwind CSS
- Material Icons
- API de clipping (/api/clipping/)

**Eventos**:
- `DOMContentLoaded`: Inicialização do componente
- `clippingLoad`: Carregamento de clipping
- `error`: Tratamento de erros

#### publications.js
**Descrição**: Componente de Publicações

**Responsabilidades**:
- Exibir revistas e boletins
- Download de publicações
- Visualização de metadados
- Filtragem por tipo

**Dependências**:
- Tailwind CSS
- Material Icons
- API de publicações (/api/publications/)

**Eventos**:
- `DOMContentLoaded`: Inicialização do componente
- `publicationLoad`: Carregamento de publicações
- `downloadPublication`: Download de publicações
- `error`: Tratamento de erros

#### history.js
**Descrição**: Componente de História

**Responsabilidades**:
- Exibir timeline histórica
- Mostrar eventos históricos
- Filtragem por período
- Detalhes de eventos

**Dependências**:
- Tailwind CSS
- Material Icons
- API de história (/api/history/)

**Eventos**:
- `DOMContentLoaded`: Inicialização do componente
- `historyLoad`: Carregamento de eventos históricos
- `error`: Tratamento de erros

#### about.js
**Descrição**: Componente Sobre Nós

**Responsabilidades**:
- Exibir informações institucionais
- Mostrar história do CCCRJ
- Apresentar diretoria
- Exibir estatutos

**Dependências**:
- Tailwind CSS
- Material Icons
- API de seção sobre (/api/about/)

**Eventos**:
- `DOMContentLoaded`: Inicialização do componente
- `aboutLoad`: Carregamento de informações institucionais
- `error`: Tratamento de erros

#### crmc.js
**Descrição**: Componente CRMC

**Responsabilidades**:
- Exibir conteúdo do CRMC
- Mostrar biblioteca
- Apresentar cafeteria temática
- Exibir programação cultural

**Dependências**:
- Tailwind CSS
- Material Icons
- API do CRMC (/api/crmc/)

**Eventos**:
- `DOMContentLoaded`: Inicialização do componente
- `crmcLoad`: Carregamento de conteúdo do CRMC
- `error`: Tratamento de erros

#### archive.js
**Descrição**: Componente de Acervo

**Responsabilidades**:
- Exibir itens do acervo
- Mostrar documentos históricos
- Apresentar galeria de fotos
- Exibir metadados

**Dependências**:
- Tailwind CSS
- Material Icons
- API de acervo (/api/archive/)

**Eventos**:
- `DOMContentLoaded`: Inicialização do componente
- `archiveLoad`: Carregamento de itens do acervo
- `error`: Tratamento de erros

### Backend Components

#### Clipping.php
**Descrição**: Modelo de Clipping

**Responsabilidades**:
- Gerenciar dados de clipping
- Buscar clipping do banco de dados
- Criar e atualizar clipping
- Excluir clipping

**Dependências**:
- PDO (PHP Data Objects)
- Database Configuration

**Métodos**:
- `getAll()`: Buscar todos os clippings
- `getById()`: Buscar clipping por ID
- `create()`: Criar novo clipping
- `update()`: Atualizar clipping existente
- `delete()`: Excluir clipping

#### Publication.php
**Descrição**: Modelo de Publicação

**Responsabilidades**:
- Gerenciar dados de publicações
- Buscar publicações do banco de dados
- Criar e atualizar publicações
- Excluir publicações

**Dependências**:
- PDO (PHP Data Objects)
- Database Configuration

**Métodos**:
- `getAll()`: Buscar todas as publicações
- `getById()`: Buscar publicação por ID
- `create()`: Criar nova publicação
- `update()`: Atualizar publicação existente
- `delete()`: Excluir publicação

#### HistoricalEvent.php
**Descrição**: Modelo de Evento Histórico

**Responsabilidades**:
- Gerenciar dados de eventos históricos
- Buscar eventos do banco de dados
- Criar e atualizar eventos
- Excluir eventos

**Dependências**:
- PDO (PHP Data Objects)
- Database Configuration

**Métodos**:
- `getAll()`: Buscar todos os eventos históricos
- `getById()`: Buscar evento por ID
- `create()`: Criar novo evento
- `update()`: Atualizar evento existente
- `delete()`: Excluir evento

#### AboutSection.php
**Descrição**: Modelo de Seção Sobre

**Responsabilidades**:
- Gerenciar dados de seções sobre
- Buscar seções do banco de dados
- Criar e atualizar seções
- Excluir seções

**Dependências**:
- PDO (PHP Data Objects)
- Database Configuration

**Métodos**:
- `getAll()`: Buscar todas as seções sobre
- `getById()`: Buscar seção por ID
- `create()`: Criar nova seção
- `update()`: Atualizar seção existente
- `delete()`: Excluir seção

#### CrmcItem.php
**Descrição**: Modelo de Item do CRMC

**Responsabilidades**:
- Gerenciar dados de itens do CRMC
- Buscar itens do banco de dados
- Criar e atualizar itens
- Excluir itens

**Dependências**:
- PDO (PHP Data Objects)
- Database Configuration

**Métodos**:
- `getAll()`: Buscar todos os itens do CRMC
- `getById()`: Buscar item por ID
- `create()`: Criar novo item
- `update()`: Atualizar item existente
- `delete()`: Excluir item

#### ArchiveItem.php
**Descrição**: Modelo de Item do Acervo

**Responsabilidades**:
- Gerenciar dados de itens do acervo
- Buscar itens do banco de dados
- Criar e atualizar itens
- Excluir itens

**Dependências**:
- PDO (PHP Data Objects)
- Database Configuration

**Métodos**:
- `getAll()`: Buscar todos os itens do acervo
- `getById()`: Buscar item por ID
- `create()`: Criar novo item
- `update()`: Atualizar item existente
- `delete()`: Excluir item

## 4. ENDPOINTS DA API
---------------------

### Clipping

#### `GET` /api/clipping/list.php
**Descrição**: Listar clippings

**Parâmetros**:
- `limit`: (opcional) Número de registros por página
- `offset`: (opcional) Deslocamento dos registros

**Resposta**:
- `success`: boolean
- `data`: array
- `limit`: integer
- `offset`: integer

#### `GET` /api/clipping/details.php
**Descrição**: Detalhes de clipping específico

**Parâmetros**:
- `id`: (requerido) ID do clipping

**Resposta**:
- `success`: boolean
- `data`: object

### Publications

#### `GET` /api/publications/list.php
**Descrição**: Listar publicações

**Parâmetros**:
- `limit`: (opcional) Número de registros por página
- `offset`: (opcional) Deslocamento dos registros

**Resposta**:
- `success`: boolean
- `data`: array
- `limit`: integer
- `offset`: integer

#### `GET` /api/publications/details.php
**Descrição**: Detalhes de publicação específica

**Parâmetros**:
- `id`: (requerido) ID da publicação

**Resposta**:
- `success`: boolean
- `data`: object

### History

#### `GET` /api/history/list.php
**Descrição**: Listar eventos históricos

**Parâmetros**:
- `limit`: (opcional) Número de registros por página
- `offset`: (opcional) Deslocamento dos registros

**Resposta**:
- `success`: boolean
- `data`: array
- `limit`: integer
- `offset`: integer

#### `GET` /api/history/details.php
**Descrição**: Detalhes de evento histórico específico

**Parâmetros**:
- `id`: (requerido) ID do evento

**Resposta**:
- `success`: boolean
- `data`: object

### About

#### `GET` /api/about/list.php
**Descrição**: Listar seções sobre

**Parâmetros**:
- `limit`: (opcional) Número de registros por página
- `offset`: (opcional) Deslocamento dos registros

**Resposta**:
- `success`: boolean
- `data`: array
- `limit`: integer
- `offset`: integer

#### `GET` /api/about/details.php
**Descrição**: Detalhes de seção sobre específica

**Parâmetros**:
- `id`: (requerido) ID da seção

**Resposta**:
- `success`: boolean
- `data`: object

### CRMC

#### `GET` /api/crmc/list.php
**Descrição**: Listar itens do CRMC

**Parâmetros**:
- `limit`: (opcional) Número de registros por página
- `offset`: (opcional) Deslocamento dos registros

**Resposta**:
- `success`: boolean
- `data`: array
- `limit`: integer
- `offset`: integer

#### `GET` /api/crmc/details.php
**Descrição**: Detalhes de item do CRMC específico

**Parâmetros**:
- `id`: (requerido) ID do item

**Resposta**:
- `success`: boolean
- `data`: object

### Archive

#### `GET` /api/archive/list.php
**Descrição**: Listar itens do acervo

**Parâmetros**:
- `limit`: (opcional) Número de registros por página
- `offset`: (opcional) Deslocamento dos registros

**Resposta**:
- `success`: boolean
- `data`: array
- `limit`: integer
- `offset`: integer

#### `GET` /api/archive/details.php
**Descrição**: Detalhes de item do acervo específico

**Parâmetros**:
- `id`: (requerido) ID do item

**Resposta**:
- `success`: boolean
- `data`: object

## 5. BANCO DE DADOS
-------------------

### `clippings`
**Descrição**: Tabela para clipping histórico

**Colunas**:
- `id`: INT AUTO_INCREMENT PRIMARY KEY
- `title`: VARCHAR(255) NOT NULL
- `summary`: TEXT
- `content`: TEXT
- `source_url`: VARCHAR(500)
- `date`: DATE
- `category`: VARCHAR(100)
- `is_active`: TINYINT(1) DEFAULT 1
- `created_at`: TIMESTAMP DEFAULT CURRENT_TIMESTAMP
- `updated_at`: TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

**Índices**:
- `idx_date`: INDEX (date)
- `idx_category`: INDEX (category)
- `idx_is_active`: INDEX (is_active)

### `publications`
**Descrição**: Tabela para revistas e boletins

**Colunas**:
- `id`: INT AUTO_INCREMENT PRIMARY KEY
- `title`: VARCHAR(255) NOT NULL
- `description`: TEXT
- `file_path`: VARCHAR(500)
- `date`: DATE
- `number`: VARCHAR(50)
- `type`: VARCHAR(50)
- `is_active`: TINYINT(1) DEFAULT 1
- `created_at`: TIMESTAMP DEFAULT CURRENT_TIMESTAMP
- `updated_at`: TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

**Índices**:
- `idx_date`: INDEX (date)
- `idx_type`: INDEX (type)
- `idx_is_active`: INDEX (is_active)

### `historical_events`
**Descrição**: Tabela para eventos históricos

**Colunas**:
- `id`: INT AUTO_INCREMENT PRIMARY KEY
- `title`: VARCHAR(255) NOT NULL
- `description`: TEXT
- `content`: TEXT
- `date`: DATE
- `event_type`: VARCHAR(100)
- `image_url`: VARCHAR(500)
- `is_featured`: TINYINT(1) DEFAULT 0
- `is_active`: TINYINT(1) DEFAULT 1
- `created_at`: TIMESTAMP DEFAULT CURRENT_TIMESTAMP
- `updated_at`: TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

**Índices**:
- `idx_date`: INDEX (date)
- `idx_event_type`: INDEX (event_type)
- `idx_is_featured`: INDEX (is_featured)
- `idx_is_active`: INDEX (is_active)

### `about_sections`
**Descrição**: Tabela para seções sobre

**Colunas**:
- `id`: INT AUTO_INCREMENT PRIMARY KEY
- `title`: VARCHAR(255) NOT NULL
- `content`: TEXT
- `section_type`: VARCHAR(100)
- `order`: INT DEFAULT 0
- `is_active`: TINYINT(1) DEFAULT 1
- `image_url`: VARCHAR(500)
- `created_at`: TIMESTAMP DEFAULT CURRENT_TIMESTAMP
- `updated_at`: TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

**Índices**:
- `idx_section_type`: INDEX (section_type)
- `idx_order`: INDEX (order)
- `idx_is_active`: INDEX (is_active)

### `crmc_items`
**Descrição**: Tabela para itens do CRMC

**Colunas**:
- `id`: INT AUTO_INCREMENT PRIMARY KEY
- `title`: VARCHAR(255) NOT NULL
- `description`: TEXT
- `content`: TEXT
- `category`: VARCHAR(100)
- `image_url`: VARCHAR(500)
- `file_path`: VARCHAR(500)
- `is_active`: TINYINT(1) DEFAULT 1
- `created_at`: TIMESTAMP DEFAULT CURRENT_TIMESTAMP
- `updated_at`: TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

**Índices**:
- `idx_category`: INDEX (category)
- `idx_is_active`: INDEX (is_active)

### `archive_items`
**Descrição**: Tabela para itens do acervo

**Colunas**:
- `id`: INT AUTO_INCREMENT PRIMARY KEY
- `title`: VARCHAR(255) NOT NULL
- `description`: TEXT
- `file_path`: VARCHAR(500)
- `date`: DATE
- `item_type`: VARCHAR(100)
- `category`: VARCHAR(100)
- `metadata`: TEXT
- `is_active`: TINYINT(1) DEFAULT 1
- `created_at`: TIMESTAMP DEFAULT CURRENT_TIMESTAMP
- `updated_at`: TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

**Índices**:
- `idx_date`: INDEX (date)
- `idx_item_type`: INDEX (item_type)
- `idx_category`: INDEX (category)
- `idx_is_active`: INDEX (is_active)

