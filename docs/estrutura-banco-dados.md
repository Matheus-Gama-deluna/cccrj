# Definição da Estrutura de Banco de Dados (Laravel)

## 1. Introdução

### 1.1 Objetivo
Definir a estrutura do banco de dados para o sistema do Centro de Comércio de Café do Rio de Janeiro (CCCRJ) utilizando migrations do Laravel, incluindo tabelas, relacionamentos e restrições necessárias para suportar as funcionalidades do sistema.

### 1.2 Escopo
Este documento abrange:
- Modelo de dados conceitual
- Modelo de dados lógico
- Esquema de tabelas
- Relacionamentos entre entidades
- Índices e otimizações
- Considerações de segurança

## 2. Modelo de Dados Conceitual

### 2.1 Entidades Principais
- **Usuários**: Administradores do sistema
- **Cotações**: Valores de diferentes tipos de café
- **Notícias**: Artigos e informações do setor cafeeiro
- **Relatórios**: Documentos técnicos em PDF
- **Categorias**: Classificações para notícias e relatórios
- **Histórico de Cotações**: Registro de variações de preços

### 2.2 Relacionamentos
- Usuários podem criar e gerenciar notícias
- Usuários podem criar e gerenciar relatórios
- Notícias pertencem a categorias
- Relatórios pertencem a categorias
- Cotações têm histórico de variações

## 3. Modelo de Dados Lógico

### 3.1 Tabela de Usuários (users)
| Coluna | Tipo | Restrições | Descrição |
|--------|------|------------|-----------|
| id | BIGINT | PK, AUTO_INCREMENT | Identificador único |
| name | VARCHAR(255) | NOT NULL | Nome do usuário |
| email | VARCHAR(255) | UNIQUE, NOT NULL | Endereço de e-mail |
| email_verified_at | TIMESTAMP | NULL | Data de verificação do e-mail |
| password | VARCHAR(255) | NOT NULL | Hash da senha |
| role | ENUM('admin', 'user') | NOT NULL, DEFAULT 'user' | Papel do usuário |
| remember_token | VARCHAR(100) | NULL | Token para "Lembrar-me" |
| created_at | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP | Data de criação |
| updated_at | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Data de atualização |
| last_login | TIMESTAMP | NULL | Último login |

### 3.2 Tabela de Cotações (quotes)
| Coluna | Tipo | Restrições | Descrição |
|--------|------|------------|-----------|
| id | VARCHAR(20) | PK | Identificador único (ex: 'arabica', 'conilon') |
| name | VARCHAR(100) | NOT NULL | Nome da cotação |
| current_price | DECIMAL(10,2) | NOT NULL | Preço atual |
| unit | VARCHAR(20) | NOT NULL, DEFAULT 'saca 60kg' | Unidade de medida |
| last_update | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Última atualização |
| is_active | BOOLEAN | NOT NULL, DEFAULT TRUE | Status da cotação |

### 3.3 Tabela de Histórico de Cotações (quote_history)
| Coluna | Tipo | Restrições | Descrição |
|--------|------|------------|-----------|
| id | BIGINT | PK, AUTO_INCREMENT | Identificador único |
| quote_id | VARCHAR(20) | FK to quotes.id | Referência à cotação |
| price | DECIMAL(10,2) | NOT NULL | Preço no momento |
| recorded_at | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP | Data e hora do registro |

### 3.4 Tabela de Categorias (categories)
| Coluna | Tipo | Restrições | Descrição |
|--------|------|------------|-----------|
| id | BIGINT | PK, AUTO_INCREMENT | Identificador único |
| name | VARCHAR(50) | UNIQUE, NOT NULL | Nome da categoria |
| slug | VARCHAR(50) | UNIQUE, NOT NULL | Slug para URLs |
| description | TEXT | NULL | Descrição da categoria |
| type | ENUM('news', 'report') | NOT NULL | Tipo de categoria |
| created_at | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP | Data de criação |

### 3.5 Tabela de Notícias (news)
| Coluna | Tipo | Restrições | Descrição |
|--------|------|------------|-----------|
| id | BIGINT | PK, AUTO_INCREMENT | Identificador único |
| title | VARCHAR(200) | NOT NULL | Título da notícia |
| slug | VARCHAR(200) | UNIQUE, NOT NULL | Slug para URLs |
| summary | TEXT | NOT NULL | Resumo da notícia |
| content | LONGTEXT | NOT NULL | Conteúdo completo |
| category_id | BIGINT | FK to categories.id | Categoria da notícia |
| author_id | BIGINT | FK to users.id | Autor da notícia |
| published_at | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP | Data de publicação |
| is_published | BOOLEAN | NOT NULL, DEFAULT TRUE | Status de publicação |
| views | INT | NOT NULL, DEFAULT 0 | Número de visualizações |
| created_at | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP | Data de criação |
| updated_at | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Data de atualização |

### 3.6 Tabela de Relatórios (reports)
| Coluna | Tipo | Restrições | Descrição |
|--------|------|------------|-----------|
| id | BIGINT | PK, AUTO_INCREMENT | Identificador único |
| title | VARCHAR(200) | NOT NULL | Título do relatório |
| slug | VARCHAR(200) | UNIQUE, NOT NULL | Slug para URLs |
| description | TEXT | NOT NULL | Descrição do relatório |
| filename | VARCHAR(255) | NOT NULL | Nome do arquivo PDF |
| file_path | VARCHAR(500) | NOT NULL | Caminho do arquivo |
| file_size | INT | NOT NULL | Tamanho do arquivo em bytes |
| category_id | BIGINT | FK to categories.id | Categoria do relatório |
| uploaded_by | BIGINT | FK to users.id | Usuário que fez upload |
| upload_date | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP | Data de upload |
| is_active | BOOLEAN | NOT NULL, DEFAULT TRUE | Status do relatório |
| downloads | INT | NOT NULL, DEFAULT 0 | Número de downloads |
| created_at | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP | Data de criação |
| updated_at | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Data de atualização |

### 3.7 Tabela de Configurações (settings)
| Coluna | Tipo | Restrições | Descrição |
|--------|------|------------|-----------|
| id | VARCHAR(50) | PK | Identificador da configuração |
| value | TEXT | NOT NULL | Valor da configuração |
| description | TEXT | NULL | Descrição da configuração |
| type | VARCHAR(20) | NOT NULL, DEFAULT 'string' | Tipo do valor |
| updated_at | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP | Data de atualização |

## 4. Diagrama de Relacionamento de Entidades (DER)

```
users (1) ────────────────< news (N)
  │                          │
  │                          │
  └─────────────────────────< reports (N)
                             │
quotes (1) ────────────────< quote_history (N)
  │
  │
categories (1) ────────────< news (N)
  │
  │
  └─────────────────────────< reports (N)
```

## 5. Índices

### 5.1 Índices para Performance
```sql
-- Índices para usuários
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_email ON users(email);

-- Índices para cotações
CREATE INDEX idx_quotes_active ON quotes(is_active);

-- Índices para histórico de cotações
CREATE INDEX idx_quote_history_quote_id ON quote_history(quote_id);
CREATE INDEX idx_quote_history_recorded_at ON quote_history(recorded_at);

-- Índices para categorias
CREATE INDEX idx_categories_slug ON categories(slug);
CREATE INDEX idx_categories_type ON categories(type);

-- Índices para notícias
CREATE INDEX idx_news_slug ON news(slug);
CREATE INDEX idx_news_category ON news(category_id);
CREATE INDEX idx_news_author ON news(author_id);
CREATE INDEX idx_news_published ON news(is_published);
CREATE INDEX idx_news_published_at ON news(published_at);

-- Índices para relatórios
CREATE INDEX idx_reports_slug ON reports(slug);
CREATE INDEX idx_reports_category ON reports(category_id);
CREATE INDEX idx_reports_uploaded_by ON reports(uploaded_by);
CREATE INDEX idx_reports_active ON reports(is_active);
CREATE INDEX idx_reports_upload_date ON reports(upload_date);
```

## 6. Restrições e Chaves Estrangeiras

### 6.1 Chaves Estrangeiras
```sql
-- Notícias
ALTER TABLE news 
ADD CONSTRAINT fk_news_category 
FOREIGN KEY (category_id) REFERENCES categories(id) 
ON DELETE SET NULL;

ALTER TABLE news 
ADD CONSTRAINT fk_news_author 
FOREIGN KEY (author_id) REFERENCES users(id) 
ON DELETE SET NULL;

-- Relatórios
ALTER TABLE reports 
ADD CONSTRAINT fk_reports_category 
FOREIGN KEY (category_id) REFERENCES categories(id) 
ON DELETE SET NULL;

ALTER TABLE reports 
ADD CONSTRAINT fk_reports_uploaded_by 
FOREIGN KEY (uploaded_by) REFERENCES users(id) 
ON DELETE SET NULL;

-- Histórico de Cotações
ALTER TABLE quote_history 
ADD CONSTRAINT fk_quote_history_quote 
FOREIGN KEY (quote_id) REFERENCES quotes(id) 
ON DELETE CASCADE;
```

## 7. Considerações de Segurança

### 7.1 Proteção de Dados
- Senhas armazenadas como hash (bcrypt)
- Dados sensíveis criptografados
- Validação de entrada em todas as operações

### 7.2 Controle de Acesso
- Princípio do menor privilégio
- Separação de permissões por papel
- Logs de auditoria para operações críticas

### 7.3 Backup e Recuperação
- Backups diários do banco de dados
- Réplicas para desastres
- Testes regulares de recuperação

## 8. Otimizações

### 8.1 Particionamento
- Histórico de cotações particionado por data
- Logs e auditorias particionados por período

### 8.2 Caching
- Cache de cotações atuais
- Cache de notícias recentes
- Cache de metadados de relatórios

### 8.3 Compressão
- Compressão de textos longos (conteúdo de notícias)
- Compressão de dados históricos

## 9. Scripts de Criação

### 9.1 Criação das Tabelas
```sql
-- Tabela de usuários (Laravel padrão + campos personalizados)
CREATE TABLE users (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  email_verified_at TIMESTAMP NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
  remember_token VARCHAR(100) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  last_login TIMESTAMP NULL
);

-- Tabela de cotações
CREATE TABLE quotes (
  id VARCHAR(20) PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  current_price DECIMAL(10,2) NOT NULL,
  unit VARCHAR(20) NOT NULL DEFAULT 'saca 60kg',
  last_update TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  is_active BOOLEAN NOT NULL DEFAULT TRUE
);

-- Tabela de histórico de cotações
CREATE TABLE quote_history (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  quote_id VARCHAR(20) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  recorded_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (quote_id) REFERENCES quotes(id) ON DELETE CASCADE
);

-- Tabela de categorias
CREATE TABLE categories (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL UNIQUE,
  slug VARCHAR(50) NOT NULL UNIQUE,
  description TEXT NULL,
  type ENUM('news', 'report') NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de notícias
CREATE TABLE news (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(200) NOT NULL UNIQUE,
  summary TEXT NOT NULL,
  content LONGTEXT NOT NULL,
  category_id BIGINT NULL,
  author_id BIGINT NULL,
  published_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  is_published BOOLEAN NOT NULL DEFAULT TRUE,
  views INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
  FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Tabela de relatórios
CREATE TABLE reports (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(200) NOT NULL UNIQUE,
  description TEXT NOT NULL,
  filename VARCHAR(255) NOT NULL,
  file_path VARCHAR(500) NOT NULL,
  file_size INT NOT NULL,
  category_id BIGINT NULL,
  uploaded_by BIGINT NULL,
  upload_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  is_active BOOLEAN NOT NULL DEFAULT TRUE,
  downloads INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
  FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Tabela de configurações
CREATE TABLE settings (
  id VARCHAR(50) PRIMARY KEY,
  value TEXT NOT NULL,
  description TEXT NULL,
  type VARCHAR(20) NOT NULL DEFAULT 'string',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## 10. Dados Iniciais

### 10.1 Cotações Padrão
```sql
INSERT INTO quotes (id, name, current_price, unit) VALUES
('arabica', 'Café Arábica', 1250.75, 'saca 60kg'),
('conilon', 'Café Conilon', 980.50, 'saca 60kg'),
('especial', 'Café Especial', 1800.00, 'saca 60kg');
```

### 10.2 Categorias Padrão
```sql
INSERT INTO categories (name, slug, description, type) VALUES
('Produção', 'producao', 'Notícias relacionadas à produção de café', 'news'),
('Exportação', 'exportacao', 'Notícias sobre exportação de café', 'news'),
('Evento', 'evento', 'Eventos do setor cafeeiro', 'news'),
('Produção', 'producao', 'Relatórios sobre produção cafeeira', 'report'),
('Mercado', 'mercado', 'Análises de mercado', 'report'),
('Sustentabilidade', 'sustentabilidade', 'Relatórios sobre práticas sustentáveis', 'report');
```

### 10.3 Usuário Administrador Padrão
```sql
INSERT INTO users (name, email, password, role) VALUES
('Administrador', 'admin@cccrj.com.br', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
```

## 11. Considerações Finais

### 11.1 Escalabilidade
A estrutura definida permite fácil escalabilidade horizontal e vertical, com possibilidade de particionamento e replicação.

### 11.2 Manutenção
A estrutura modular facilita a manutenção e evolução do sistema, com migrações controladas e versionamento.

### 11.3 Performance
Os índices e otimizações definidos garantem boa performance mesmo com grande volume de dados.