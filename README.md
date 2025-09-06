# Centro de Comércio de Café do Rio de Janeiro (CCCRJ)

## Visão Geral

Este projeto implementa o site oficial do Centro de Comércio de Café do Rio de Janeiro, seguindo o plano definido no documento `plan.md`. O sistema foi desenvolvido como um MVP frontend-only com as seguintes características:

1. **Página Principal Responsiva** - Apresentando informações sobre o mercado cafeeiro do Rio de Janeiro
2. **Seção de Cotações** - Com valores atualizados automaticamente para diferentes tipos de café
3. **Seção de Notícias** - Com as últimas notícias do mercado
4. **Seção de Relatórios** - Para visualização e download de relatórios em PDF armazenados em um servidor FTP
5. **Área Administrativa** - Para envio de novos relatórios via FTP

## Estrutura do Projeto

```
cccrj/
├── index.html (página principal)
├── admin.html (área administrativa)
├── login.html (página de login)
├── assets/
│   ├── css/
│   │   ├── styles.css (estilos personalizados)
│   │   └── admin.css (estilos da área administrativa)
│   ├── js/
│   │   ├── main.js (lógica principal)
│   │   ├── auth.js (autenticação e controle de sessão)
│   │   ├── admin.js (lógica da área administrativa)
│   │   └── components/
│   │       ├── quotes.js (lógica das cotações)
│   │       ├── news.js (lógica das notícias)
│   │       ├── reports.js (lógica dos relatórios)
│   │       └── calculator.js (calculadora de conversão)
│   └── images/
├── docs/
│   ├── especificacao-requisitos-software.md
│   ├── documento-requisitos-produto.md
│   └── backlog.md
├── .gitignore
├── README.md
└── plan.md
```

## Tecnologias Utilizadas

- **HTML5** - Estrutura semântica da página
- **Tailwind CSS** - Framework CSS para estilização
- **JavaScript (ES6+)** - Interatividade e lógica
- **Google Fonts** - Tipografia personalizada (Fonte Inter)
- **Material Icons** - Ícones vetoriais

## Funcionalidades Implementadas

### Página Principal (`index.html`)

- Design responsivo com animações e efeitos visuais
- Seção hero com call-to-action
- Cotações em tempo real para diferentes tipos de café (Arábica, Conilon, Especial)
- Seção de notícias do mercado
- Seção de relatórios PDF com carregamento do FTP
- Footer com informações de contato

### Área Administrativa

- **Página de Login** (`login.html`) - Autenticação de usuários administradores
- **Dashboard** (`admin.html`) - Interface para envio de relatórios em PDF via FTP

## Como Executar

1. Clone o repositório
2. Abra o arquivo `index.html` em qualquer navegador moderno
3. Para acessar a área administrativa, clique no link "Admin" no menu ou acesse diretamente `login.html`
4. Utilize as credenciais de teste:
   - Usuário: `admin`
   - Senha: `admin123`

## Considerações Técnicas

### Economia de Requisições

- Implementação de lazy loading para seções que não estão imediatamente visíveis
- Cache de dados para reduzir requisições repetidas
- Otimização de assets para melhor tempo de carregamento
- Pooling de requisições quando possível

### Integração com FTP

A funcionalidade de envio e recebimento de arquivos via FTP foi implementada com as seguintes considerações:

1. **Frontend-only**: A implementação atual simula a integração com FTP
2. **Extensibilidade**: O código foi estruturado para facilitar a integração real com um servidor FTP
3. **Segurança**: A autenticação é tratada de forma segura com sessões

## Próximos Passos

Conforme definido no plano, os próximos passos incluem:

- Integração com backend e API real
- Sistema de gerenciamento de conteúdo
- Autenticação de usuários mais robusta
- Sistema de alertas em tempo real
- Histórico de envios de arquivos
- Gerenciamento de múltiplos usuários administradores
- Filtros e busca avançada nos relatórios
- Visualizador de PDF com ferramentas avançadas

## Manutenção

Para manter o projeto:

1. Atualize as dependências conforme necessário
2. Verifique a compatibilidade com novas versões dos navegadores
3. Otimize o desempenho regularmente
4. Mantenha a documentação atualizada

## Contribuição

Para contribuir com o projeto:

1. Faça um fork do repositório
2. Crie uma branch para sua feature (`git checkout -b feature/NovaFeature`)
3. Commit suas mudanças (`git commit -am 'Adiciona nova feature'`)
4. Push para a branch (`git push origin feature/NovaFeature`)
5. Crie um novo Pull Request