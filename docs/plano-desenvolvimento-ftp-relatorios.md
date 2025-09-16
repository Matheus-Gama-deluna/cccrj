# Plano de Desenvolvimento Focado - Funcionalidade de Relatórios via FTP

## 1. Visão Geral

Este plano detalha o desenvolvimento rápido e focado para implementar apenas a funcionalidade essencial de relatórios via FTP do sistema CCCRJ. O objetivo é permitir que os usuários visualizem e enviem PDFs de relatórios diretamente do servidor FTP, minimizando o escopo para entregar valor rapidamente.

## 2. Escopo Reduzido

### 2.1 Funcionalidades Inclusas (MVP)
- **Autenticação de administradores**: Sistema mínimo de login/logout
- **Área administrativa básica**: Interface para gerenciamento de relatórios
- **Listagem de relatórios**: Exibição de relatórios disponíveis no FTP
- **Download de relatórios**: Visualização e download de PDFs
- **Upload de relatórios**: Envio de novos PDFs para o servidor FTP
- **Metadados básicos**: Título, descrição e data dos relatórios

### 2.2 Funcionalidades Excluídas (Fase 1)
- Módulo de cotações em tempo real
- Módulo de notícias
- Calculadora de conversão
- Sistema de busca avançada
- Histórico de cotações
- Sistema de alertas/notificações
- Perfis de usuário avançados
- Categorização complexa de relatórios

## 3. Estrutura Simplificada do Projeto

```
cccrj/
├── frontend/                 # Aplicação frontend simplificada
│   ├── index.html            # Página pública simples (informações básicas)
│   ├── login.html            # Página de autenticação
│   ├── admin.html            # Área administrativa para relatórios
│   ├── assets/
│   │   ├── css/
│   │   │   ├── styles.css    # Estilos públicos
│   │   │   └── admin.css     # Estilos administrativos
│   │   └── js/
│   │       ├── main.js       # Scripts da página principal
│   │       ├── auth.js       # Autenticação
│   │       └── admin.js      # Scripts da área administrativa
│   └── reports/              # Diretório temporário para uploads
├── backend/                  # Aplicação Laravel simplificada
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── Api/
│   │   │   │   │   ├── AuthController.php
│   │   │   │   │   └── ReportController.php
│   │   │   │   └── Web/
│   │   │   │       └── AdminController.php
│   │   │   ├── Requests/
│   │   │   │   └── ReportRequest.php
│   │   │   └── Resources/
│   │   │       └── ReportResource.php
│   │   ├── Models/
│   │   │   └── User.php
│   │   └── Services/
│   │       └── FtpService.php
│   ├── config/
│   ├── database/
│   │   └── migrations/
│   │       └── users_table.php
│   ├── routes/
│   │   └── api.php
│   ├── resources/
│   │   └── views/
│   │       └── admin/
│   │           └── reports.blade.php
│   └── storage/
├── docs/                     # Documentação essencial
└── README.md
```

## 4. Tecnologias Utilizadas (Mantidas)

- **Frontend**: HTML5, Tailwind CSS, JavaScript (ES6+)
- **Backend**: PHP 8+, Laravel 10+, MySQL/PostgreSQL
- **Autenticação**: Laravel Sanctum (mínimo necessário)
- **FTP**: Extensão FTP do PHP ou biblioteca Flysystem
- **Deploy**: Frontend em hospedagem estática, Backend em servidor PHP

## 5. Plano de Desenvolvimento Acelerado (2 Semanas)

### Semana 1: Setup e Funcionalidade Básica

#### Dia 1-2: Configuração Inicial
1. **Ambiente de desenvolvimento**
   - Instalar PHP 8+, Composer
   - Configurar Laravel Valet ou XAMPP
   - Criar projeto Laravel básico

2. **Configuração de banco de dados**
   - Criar migration para usuários
   - Configurar SQLite para desenvolvimento rápido

3. **Setup mínimo do frontend**
   - Copiar estrutura básica do projeto atual
   - Limpar componentes desnecessários
   - Manter apenas páginas essenciais

#### Dia 3-4: Autenticação Mínima
1. **Implementar autenticação básica**
   - Configurar Laravel Sanctum para API tokens
   - Criar endpoints de login/logout
   - Implementar proteção de rotas administrativas

2. **Área administrativa inicial**
   - Criar layout básico da área admin
   - Implementar verificação de autenticação
   - Criar menu de navegação mínimo

#### Dia 5: Integração com FTP
1. **Implementar serviço FTP**
   - Criar FtpService para conexão com servidor FTP
   - Implementar listagem de arquivos PDF
   - Implementar download de arquivos

2. **Testes de conexão**
   - Validar credenciais FTP
   - Testar listagem de arquivos
   - Testar download de arquivos

### Semana 2: Funcionalidade Completa de Relatórios

#### Dia 6-7: Backend de Relatórios
1. **Endpoints da API**
   - GET /api/reports - Listar relatórios do FTP
   - GET /api/reports/{filename}/download - Download de relatório
   - POST /api/reports - Upload de novo relatório
   - DELETE /api/reports/{filename} - Exclusão de relatório

2. **Modelos e validação**
   - Criar ReportRequest para validação de uploads
   - Implementar lógica de extração de metadados
   - Criar recursos API para transformação de dados

#### Dia 8-9: Frontend da Área Administrativa
1. **Interface de relatórios**
   - Criar página administrativa para gerenciamento de relatórios
   - Implementar listagem com cards de relatórios
   - Adicionar botões de download para cada relatório

2. **Funcionalidade de upload**
   - Criar formulário para upload de PDFs
   - Implementar preview de arquivos
   - Adicionar validação de tipo e tamanho

#### Dia 10: Integração Completa e Testes

1. **Conexão frontend-backend**
   - Conectar chamadas API para listagem de relatórios
   - Implementar download de arquivos via frontend
   - Conectar formulário de upload com backend

2. **Testes e validação**
   - Testar fluxo completo de upload/download
   - Validar proteção de rotas
   - Testar tratamento de erros

#### Dia 11-12: Refinamentos e Deploy

1. **Melhorias de UX**
   - Adicionar indicadores de carregamento
   - Implementar feedback visual para ações
   - Melhorar responsividade

2. **Segurança e validações**
   - Validar tipos de arquivos no upload
   - Adicionar proteções contra uploads maliciosos
   - Validar nomes de arquivos

3. **Preparação para deploy**
   - Configurar ambiente de produção
   - Otimizar assets para produção
   - Criar documentação mínima de uso

4. **Deploy inicial**
   - Deploy do backend
   - Deploy do frontend
   - Testes finais em ambiente de produção

## 6. Cronograma Acelerado

| Dia | Atividade | Entregáveis |
|-----|-----------|-------------|
| 1-2 | Setup e configuração | Ambiente de desenvolvimento funcional |
| 3-4 | Autenticação | Sistema de login/logout funcional |
| 5 | Integração FTP | Conexão com servidor FTP estabelecida |
| 6-7 | Backend de relatórios | API REST para relatórios completa |
| 8-9 | Frontend administrativo | Interface administrativa funcional |
| 10 | Integração completa | Sistema integrado e testado |
| 11-12 | Refinamentos e deploy | Sistema em produção |

## 7. Recursos Necessários (Mínimos)

### 7.1 Equipe
- 1 Desenvolvedor Full Stack (PHP/Laravel + JavaScript)
- 1 Analista de Testes (opcional, pode ser o mesmo desenvolvedor)

### 7.2 Infraestrutura
- Servidor compartilhado ou VPS (para backend Laravel)
- Hospedagem estática (para frontend)
- Acesso FTP ao servidor de relatórios
- Banco de dados MySQL/PostgreSQL ou SQLite para desenvolvimento

### 7.3 Ferramentas
- IDE (Visual Studio Code)
- Git para controle de versão
- Postman para testes de API
- FTP client para testes manuais

## 8. Critérios de Sucesso (MVP)

### 8.1 Funcionais
- Administradores conseguem fazer login no sistema
- Administradores conseguem visualizar lista de relatórios do FTP
- Administradores conseguem fazer download de relatórios
- Administradores conseguem fazer upload de novos relatórios
- Usuários conseguem acessar informações básicas do CCCRJ

### 8.2 Técnicos
- Tempo de carregamento < 3 segundos
- Upload/download de arquivos funcionando corretamente
- Validação de arquivos (somente PDFs)
- Autenticação segura
- Tratamento adequado de erros

### 8.3 Qualidade
- Código seguindo padrões PSR
- Documentação mínima de uso e deploy
- Zero bugs críticos em produção
- Interface responsiva funcional

## 9. Limitações Conhecidas

### 9.1 Escopo Deliberadamente Limitado
- Sem histórico de cotações
- Sem notícias automatizadas
- Sem busca avançada
- Sem categorização complexa
- Sem sistema de alertas

### 9.2 Compromissos Técnicos
- Banco de dados simplificado (somente usuários)
- Sem testes automatizados extensivos
- Interface administrativa básica
- Sem cache avançado

## 10. Próximos Passos Pós-MVP

### 10.1 Curto Prazo (Semanas 3-4)
- Adicionar categorização básica de relatórios
- Implementar busca simples
- Melhorar interface administrativa

### 10.2 Médio Prazo (Meses 2-3)
- Implementar módulo de cotações
- Adicionar módulo de notícias
- Implementar sistema de busca avançada

### 10.3 Longo Prazo (Meses 4+)
- Desenvolver aplicativo mobile
- Implementar sistema de alertas em tempo real
- Adicionar inteligência artificial para análise preditiva

## 11. Conclusão

Este plano acelerado permite entregar funcionalidade de valor imediato aos usuários do CCCRJ em apenas duas semanas, focando exclusivamente na funcionalidade crítica de gerenciamento de relatórios via FTP. Ao manter o escopo restrito e priorizar as funcionalidades essenciais, é possível ter um sistema funcional em produção rapidamente, que pode ser expandido posteriormente com funcionalidades adicionais.