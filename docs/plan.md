# Planejamento do Sistema CCCRJ - MVP Frontend

## Estado Atual

Após analisar os arquivos existentes, identifiquei que o projeto possui:

1. Dois arquivos HTML idênticos (`index.html` e `CCCRJ-PROTOTIPO.html`) que implementam:
   - Página principal com informações sobre o mercado cafeeiro do Rio de Janeiro
   - Seção de cotações em tempo real para diferentes tipos de café (Arábica, Conilon e Especial)
   - Seção de notícias sobre o mercado
   - Design responsivo com Tailwind CSS
   - Cores temáticas relacionadas ao café (tons de marrom, bege e dourado)

2. Um documento de contexto (`qwen.md`) contendo diretrizes para o desenvolvimento orientado a agentes.

A implementação atual é uma página única estática com elementos de interatividade simulada:
- Preços que variam automaticamente (simulação)
- Navegação por seções
- Design moderno com animações e efeitos visuais

## Objetivo do MVP

Transformar o protótipo existente em um MVP frontend-only funcional que possa ser facilmente hospedado em qualquer serviço de hospedagem estática. O foco será em:

1. Manter toda a funcionalidade visual do protótipo atual, seguindo rigorosamente o padrão visual definido no `CCCRJ-PROTOTIPO.html`
2. Melhorar a organização do código
3. Adicionar interatividade real onde atualmente é simulada
4. Implementar uma área administrativa com funcionalidades de envio de arquivos
5. Adicionar seção para visualização e download de relatórios em PDF do FTP
6. Preparar a estrutura para futuras expansões

## Funcionalidades do MVP

1. **Página Principal Responsiva**
   - Hero section com call-to-action (mantendo as animações de café, planta e gráfico)
   - Seção de cotações com valores atualizáveis (mantendo o estilo dos cards com gradientes e animações)
   - Seção de notícias do mercado (mantendo o design dos cards com diferentes categorias)
   - Seção de relatórios PDF do FTP (seguindo o mesmo padrão visual dos cards de notícias)
   - Footer com informações de contato (mantendo o design com ícones e links)
   - Link para área administrativa no header
   - Indicador de status de login

2. **Área Administrativa**
   - Página de login seguro
   - Dashboard administrativo
   - Interface para envio de arquivos via FTP
   - Visualização de status do usuário logado

3. **Interatividade**
   - Navegação suave entre seções
   - Atualização simulada de preços em tempo real
   - Carregamento dinâmico de notícias (simulado)
   - Carregamento dinâmico de relatórios PDF do FTP
   - Calculadora de conversão de valores

4. **Design e UX**
   - Manter o design visual atrativo com tema cafeeiro (paleta de cores: #8B2635, #992D3D, #A63545, #6B4423, #F5F0E8, #D4A574)
   - Garantir responsividade em todos os dispositivos
   - Animações e transições suaves (efeitos glassmorphism, hover e floating)
   - Ícones e elementos visuais consistentes (Material Icons)
   - Tipografia personalizada (Fonte Inter)

## Estrutura de Pastas Proposta

```
cccrj/
├── index.html (página principal)
├── admin.html (área administrativa)
├── login.html (página de login)
├── assets/
│   ├── css/
│   │   ├── styles.css (estilos personalizados, mantendo as classes do Tailwind e estilos customizados)
│   │   ├── admin.css (estilos da área administrativa)
│   │   └── tailwind.css (se necessário compilar)
│   ├── js/
│   │   ├── main.js (lógica principal da página index.html)
│   │   ├── auth.js (autenticação e controle de sessão)
│   │   ├── admin.js (lógica da área administrativa)
│   │   ├── components/
│   │   │   ├── quotes.js (lógica das cotações)
│   │   │   ├── news.js (lógica das notícias)
│   │   │   ├── reports.js (lógica dos relatórios do FTP)
│   │   │   └── calculator.js (calculadora de conversão)
│   │   └── utils/
│   │       └── helpers.js (funções auxiliares)
│   └── images/
│       ├── favicon.ico
│       └── (outras imagens do projeto)
├── docs/
│   ├── especificacao-requisitos-software.md
│   ├── documento-requisitos-produto.md
│   └── backlog.md
├── .gitignore
├── README.md
└── qwen.md
```

## Passos para Implementação

1. **Organização de Arquivos**
   - Consolidar os dois arquivos HTML idênticos em um único index.html
   - Criar páginas separadas para login e área administrativa
   - Criar a estrutura de pastas definida acima
   - Mover CSS inline para arquivos externos
   - Separar o JavaScript em módulos organizados

2. **Implementação da Área Administrativa**
   - Criar página de login com validação de credenciais
   - Desenvolver dashboard administrativo
   - Implementar interface para envio de arquivos via FTP
   - Adicionar mecanismo de controle de sessão

3. **Implementação da Seção de Relatórios**
   - Criar seção moderna para exibição de relatórios PDF, seguindo o mesmo padrão visual das notícias
   - Implementar carregamento dinâmico de arquivos do FTP
   - Adicionar funcionalidade de visualização e download de PDFs
   - Criar interface intuitiva para navegação entre relatórios

4. **Integração com Página Principal**
   - Adicionar link para área administrativa no header
   - Implementar indicador de status de login (usuário logado/deslogado)
   - Criar mecanismo de redirecionamento baseado no status de autenticação
   - Integrar seção de relatórios na página principal

5. **Refatoração do Código**
   - Extrair estilos inline para arquivos CSS separados
   - Modularizar o JavaScript em componentes reutilizáveis
   - Otimizar o código existente para melhor manutenção
   - Adicionar comentários explicativos onde necessário

6. **Implementação de Funcionalidades**
   - Aprimorar a simulação de atualização de preços
   - Implementar carregamento dinâmico de notícias (simulado)
   - Implementar carregamento dinâmico de relatórios do FTP
   - Finalizar a calculadora de conversão de valores
   - Adicionar animações e transições aprimoradas

7. **Segurança e Validação**
   - Implementar validação de formulários
   - Adicionar proteções básicas contra ataques comuns
   - Garantir que áreas restritas não sejam acessadas sem autenticação
   - Validar arquivos PDF antes da exibição

8. **Testes e Otimização**
   - Verificar responsividade em diferentes dispositivos
   - Otimizar performance (carregamento, animações)
   - Validar compatibilidade entre navegadores
   - Realizar testes de usabilidade

9. **Documentação**
   - Atualizar README.md com instruções de uso
   - Documentar a arquitetura frontend
   - Criar guia para futuras expansões
   - Manter o backlog de melhorias

## Tecnologias Utilizadas

- **HTML5** - Estrutura semântica da página
- **Tailwind CSS** - Framework CSS para estilização (mantendo as classes e estilos do protótipo)
- **JavaScript (ES6+)** - Interatividade e lógica
- **Google Fonts** - Tipografia personalizada (Fonte Inter)
- **Material Icons** - Ícones vetoriais
- **FTP Client Library** - Para funcionalidade de envio/recebimento de arquivos
- **PDF.js** - Para visualização de arquivos PDF no navegador

## Entregáveis do MVP

1. Página única funcional e responsiva, seguindo rigorosamente o padrão visual do `CCCRJ-PROTOTIPO.html`
2. Seção de cotações com atualização simulada
3. Seção de notícias com carregamento dinâmico
4. Seção de relatórios PDF com carregamento do FTP
5. Calculadora de conversão de valores
6. Página de login segura
7. Área administrativa com dashboard
8. Funcionalidade de envio de arquivos via FTP
9. Código organizado e bem documentado
10. Documentação completa do projeto

## Considerações Técnicas para Economia de Requisições

1. **Carregamento sob demanda**: Implementar lazy loading para seções que não estão imediatamente visíveis
2. **Cache de dados**: Armazenar em cache os dados de relatórios e notícias para reduzir requisições repetidas
3. **Otimização de assets**: Comprimir imagens e minificar CSS/JS para melhor tempo de carregamento
4. **Pooling de requisições**: Agrupar requisições quando possível para reduzir o número total de chamadas
5. **Fallbacks apropriados**: Implementar fallbacks para quando o FTP não estiver disponível

## Próximos Passos Pós-MVP

Após a conclusão do MVP frontend-only, as possíveis expansões incluem:
- Integração com backend e API real
- Sistema de gerenciamento de conteúdo
- Autenticação de usuários mais robusta
- Sistema de alertas em tempo real
- Histórico de envios de arquivos
- Gerenciamento de múltiplos usuários administradores
- Filtros e busca avançada nos relatórios
- Visualizador de PDF com ferramentas avançadas