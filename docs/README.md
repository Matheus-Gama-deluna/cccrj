# Sistema do Centro de Comércio do Café do Rio de Janeiro (CCCRJ)

## Visão Geral

Este sistema é uma plataforma web moderna desenvolvida para o Centro de Comércio do Café do Rio de Janeiro (CCCRJ), com o objetivo de preservar e apresentar o rico conteúdo histórico do CCCRJ em uma interface contemporânea e funcional.

## Arquitetura

O sistema utiliza uma arquitetura frontend/backend separada:

- **Frontend**: HTML5, Tailwind CSS, JavaScript (ES6+)
- **Backend**: PHP puro (sem frameworks)
- **Banco de Dados**: MySQL
- **Servidor Web**: Apache (XAMPP)

## Estrutura de Diretórios

```
cccrj/
├── api/                    # API em PHP puro
│   ├── config/            # Configurações
│   ├── utils/             # Funções utilitárias
│   ├── models/            # Modelos de dados
│   ├── clipping/          # Endpoints para clipping
│   ├── publications/     # Endpoints para publicações
│   ├── archive/          # Endpoints para acervo
│   ├── history/          # Endpoints para história
│   ├── about/            # Endpoints para seção sobre
│   └── crmc/             # Endpoints para CRMC
├── assets/                # Recursos frontend
│   ├── css/              # Folhas de estilo
│   ├── js/               # Scripts JavaScript
│   │   └── components/    # Componentes modulares
│   └── images/           # Imagens e recursos visuais
├── scraping_cccrj/        # Conteúdo raspado do site original
│   └── cccrj_content/     # Conteúdo HTML raspado
├── uploads/              # Arquivos enviados
├── index.html             # Página principal
└── ...
```

## Componentes Principais

### Frontend

1. **Componente de Cotações** (`assets/js/components/quotes.js`)
   - Exibe cotações de café em tempo real
   - Atualização automática a cada 10 segundos

2. **Componente de Notícias** (`assets/js/components/news.js`)
   - Sistema de notícias do setor cafeeiro
   - Paginação e carregamento progressivo

3. **Componente de Relatórios** (`assets/js/components/reports.js`)
   - Exibição de relatórios técnicos em PDF
   - Integração com servidor FTP

4. **Componente de Clipping Histórico** (`assets/js/components/clipping.js`)
   - Conteúdo de clipping raspado do site original
   - Categorização e filtragem

5. **Componente de Publicações** (`assets/js/components/publications.js`)
   - Exibição de revistas e boletins do CCCRJ
   - Sistema de download de publicações

6. **Componente de História** (`assets/js/components/history.js`)
   - Timeline interativa da história do CCCRJ
   - Eventos históricos e marcos institucionais

### Backend (API em PHP Puro)

1. **Modelos de Dados**
   - `Clipping` - Para conteúdo de clipping
   - `Publication` - Para revistas e boletins
   - `ArchiveItem` - Para itens do acervo
   - `HistoricalEvent` - Para eventos históricos
   - `AboutSection` - Para seções sobre
   - `CrmcItem` - Para conteúdo do CRMC

2. **Endpoints da API**
   - `/api/clipping/` - Gerenciamento de clipping
   - `/api/publications/` - Gerenciamento de publicações
   - `/api/archive/` - Gerenciamento do acervo
   - `/api/history/` - Gerenciamento da história
   - `/api/about/` - Gerenciamento da seção sobre
   - `/api/crmc/` - Gerenciamento do CRMC

## Instalação e Configuração

### Requisitos

- XAMPP (Apache, MySQL, PHP)
- Navegador moderno (Chrome, Firefox, Edge, Safari)

### Passos de Instalação

1. **Instalar XAMPP**
   - Baixe e instale o XAMPP de https://www.apachefriends.org/
   - Inicie os serviços Apache e MySQL

2. **Configurar o Projeto**
   ```bash
   # Clone ou copie o projeto para o diretório htdocs do XAMPP
   cp -r cccrj/ C:/xampp/htdocs/
   ```

3. **Criar o Banco de Dados**
   ```bash
   # Acesse http://localhost/cccrj/db_setup.php no navegador
   # Ou execute via linha de comando:
   php db_setup.php
   ```

4. **Popular o Banco de Dados (Opcional)**
   ```bash
   # Acesse http://localhost/cccrj/populate_db.php no navegador
   # Ou execute via linha de comando:
   php populate_db.php
   ```

5. **Migrar Conteúdo Raspado (Opcional)**
   ```bash
   # Execute o script de migração:
   php migrate_scraped_content.php
   ```

6. **Verificar Configuração**
   ```bash
   # Acesse http://localhost/cccrj/check_database.php no navegador
   # Ou execute via linha de comando:
   php check_database.php
   ```

## Uso

### Acessar o Sistema

Abra o navegador e acesse:
```
http://localhost/cccrj/
```

### Estrutura de Navegação

- **Página Inicial** - Visão geral do CCCRJ
- **Cotações** - Cotações de café em tempo real
- **Notícias** - Notícias do setor cafeeiro
- **Relatórios** - Relatórios técnicos e publicações
- **Sobre** - História e informações institucionais
- **Contato** - Informações de contato

### Área Administrativa

Para acessar a área administrativa:
```
http://localhost/cccrj/login.html
```

Credenciais padrão:
- Usuário: `admin`
- Senha: `admin123`

## Desenvolvimento

### Estrutura de Componentes

Cada componente segue o padrão:

```javascript
class ComponentName {
    constructor() {
        this.apiUrl = 'api/component/endpoint.php';
        this.init();
    }
    
    async init() {
        // Inicialização do componente
    }
    
    async loadData() {
        // Carregar dados da API
    }
    
    render() {
        // Renderizar interface
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('component-container')) {
        new ComponentName();
    }
});
```

### Adicionar Novo Componente

1. Criar arquivo em `assets/js/components/nome_componente.js`
2. Registrar o componente no `main.js`
3. Adicionar script ao `index.html`
4. Criar endpoints da API correspondentes

## Manutenção

### Backup do Banco de Dados

```bash
mysqldump -u root cccrj_db > backup_cccrj.sql
```

### Restaurar Backup

```bash
mysql -u root cccrj_db < backup_cccrj.sql
```

## Solução de Problemas

### Problemas Comuns

1. **Página em branco**
   - Verifique se o Apache está rodando
   - Verifique se os caminhos dos arquivos estão corretos

2. **Erro de conexão com o banco de dados**
   - Verifique as credenciais em `api/config/database.php`
   - Certifique-se de que o MySQL está rodando

3. **Dados não carregando**
   - Verifique se as tabelas foram criadas corretamente
   - Verifique os endpoints da API

### Logs de Erro

Os erros do PHP são registrados no arquivo de log do Apache:
```
C:/xampp/apache/logs/error.log
```

## Contribuição

1. Faça um fork do repositório
2. Crie uma branch para sua feature (`git checkout -b feature/nova-feature`)
3. Commit suas mudanças (`git commit -am 'Adiciona nova feature'`)
4. Faça push para a branch (`git push origin feature/nova-feature`)
5. Crie um Pull Request

## Licença

Este projeto é de propriedade do Centro de Comércio do Café do Rio de Janeiro (CCCRJ) e é destinado exclusivamente para uso institucional.

## Contato

Centro de Comércio do Café do Rio de Janeiro
- Website: http://www.cccrj.com.br
- Email: riocafe@cccrj.com.br
- Telefone: (21) 2516-3399