# Plano de Implementação: Sistema de Busca e Organização de Boletins CCCRJ

## Análise da Estrutura Atual

### Boletins Existentes
- **Localização**: `data/boletins_site/`
- **Estrutura**: Organizados por anos (2017, 2018) e meses (01_Janeiro, 02_Fevereiro, etc.)
- **Nomenclatura**:
  - Arquivos diários: `DD-MM-YY.pdf` (ex: 02-01-17.pdf)
  - Boletins mensais: `Boletim_Mensal_[MÊS].pdf`
- **Volume**: 365+ boletins distribuídos entre 2017 e 2018

### APIs Existentes
- **LocalFileService.php**: Gerenciamento básico de arquivos
- **`/api/reports/list.php`**: Listagem com paginação simples
- **`/api/reports/download.php`**: Download de arquivos
- **Componente Frontend**: `reports.js` com modal de preview

### Limitações Atuais
- Sem sistema de busca por conteúdo
- Sem metadados estruturados
- Sem categorização ou tags
- Busca limitada aos nomes dos arquivos
- Sem filtros avançados (data, tipo, etc.)

## Requisitos Funcionais

### Funcionalidades de Busca
1. **Busca por texto**: Título, conteúdo do PDF, metadados
2. **Filtros por data**: Ano, mês, período customizado
3. **Filtros por tipo**: Diário, mensal, boletim especial
4. **Busca por palavras-chave**: Tags e categorias
5. **Busca avançada**: Operadores booleanos (AND, OR, NOT)

### Organização e Visualização
1. **Hierarquia temporal**: Ano → Mês → Dia
2. **Categorização**: Por tipo, relevância, tema
3. **Visualização em grid**: Cards com preview
4. **Visualização em lista**: Tabela detalhada
5. **Timeline interativa**: Linha do tempo dos boletins

### Funcionalidades Administrativas
1. **Upload com metadados**: Título, descrição, tags
2. **Categorização automática**: Baseada em padrões de nomenclatura
3. **Indexação de conteúdo**: Extração automática de texto dos PDFs
4. **Gestão de tags**: CRUD para categorias e etiquetas

## Arquitetura Proposta

### Backend
```
API de Busca e Indexação
├── /api/boletins/search.php (busca principal)
├── /api/boletins/index.php (indexação de PDFs)
├── /api/boletins/metadata.php (metadados)
├── /api/boletins/categories.php (categorias)
└── /api/boletins/batch.php (operações em lote)
```

### Sistema de Metadados
```json
{
  "id": "unique_id",
  "filename": "02-01-17.pdf",
  "title": "Boletim Diário - 02 de Janeiro de 2017",
  "date": "2017-01-02",
  "type": "diario|mensal|especial",
  "category": ["mercado", "preços", "exportação"],
  "tags": ["café robusta", "cotações"],
  "description": "Texto extraído do PDF",
  "file_path": "data/boletins_site/2017/01_Janeiro/02-01-17.pdf",
  "file_size": 111170,
  "indexed_at": "2024-10-24T10:00:00Z",
  "is_active": true
}
```

### Frontend
```
Interface de Busca
├── Componente de busca principal
├── Filtros e facetas
├── Visualização em grid/lista
├── Timeline interativa
├── Modal de preview avançado
└── Paginação inteligente
```

## Tecnologias Propostas

### Backend
- **PHP 8+**: Para APIs REST
- **SQLite/MySQL**: Metadados estruturados
- **PDF Parser**: Extração de texto (ex: PDFParser, TCPDF)
- **Full-text Search**: SQLite FTS5 ou Elasticsearch
- **Cache**: Redis para resultados de busca

### Frontend
- **JavaScript ES6+**: Vanilla JS (consistente com projeto)
- **Search Components**: Autocomplete, filtros dinâmicos
- **Virtual Scrolling**: Para listas grandes
- **Progressive Loading**: Carregamento sob demanda

### Indexação
- **OCR**: Para PDFs escaneados (Tesseract)
- **Text Extraction**: PDFBox, PDF.js
- **Metadata Parsing**: ExifTool para PDFs
- **Batch Processing**: Processamento em background

## Interface e UX

### Página de Busca
```
┌─────────────────────────────────────────────────┐
│ Busca: [____________________] [Buscar] [Limpar] │
├─────────────────────────────────────────────────┤
│ Filtros:                                        │
│ □ Por Data: [De: ___] [Até: ___]                │
│ □ Por Tipo: ☑ Diário ☑ Mensal ☑ Especial        │
│ □ Categorias: ☑ Mercado ☑ Preços ☑ Exportação    │
│ □ Tags: [____________]                          │
├─────────────────────────────────────────────────┤
│ Visualização: [Grid] [Lista] [Timeline]         │
│ Ordenação: [Data ↓] [Relevância] [Título]       │
├─────────────────────────────────────────────────┤
│ [25 resultados]                                 │
│ ┌─────────────────────────────────────────────┐ │
│ │ [Card 1] [Card 2] [Card 3] ... [Card 25]    │ │
│ │ [Pág 1] [2] [3] ... [15] [Próxima]          │ │
│ └─────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────┘
```

### Card de Boletim
```
┌─────────────────────────────────────────┐
│ 📄 Boletim Diário - 02 Jan 2017         │
│ 📅 02/01/2017 • 📁 Diário • 🏷️ mercado  │
│ ─────────────────────────────────────── │
│ Cotações do dia: Café robusta em alta   │
│ com aumento de 2% nos preços...         │
│ ─────────────────────────────────────── │
│ [👁️ Preview] [⬇️ Download] [⭐ Favoritar] │
└─────────────────────────────────────────┘
```

## Cronograma de Implementação

### Fase 1: Fundamentos (2 semanas)
1. **Sistema de Metadados**
   - Criar estrutura de banco/metadados JSON
   - API básica de CRUD para boletins
   - Indexação inicial dos boletins existentes

2. **API de Busca Básica**
   - Busca por título e filename
   - Filtros por data e tipo
   - Paginação simples

3. **Interface Básica**
   - Formulário de busca simples
   - Lista básica de resultados
   - Integração com modal de preview

### Fase 2: Funcionalidades Avançadas (2 semanas)
1. **Extração de Texto**
   - Implementar parser de PDF
   - Busca full-text no conteúdo
   - Indexação automática de novos uploads

2. **Sistema de Categorização**
   - Interface de tags/categorias
   - Categorização automática por padrões
   - Busca por tags

3. **Visualizações Avançadas**
   - Timeline interativa
   - Grid responsivo
   - Filtros faceted

### Fase 3: Otimização e Performance (1 semana)
1. **Performance**
   - Cache de resultados
   - Indexação otimizada
   - Lazy loading

2. **Administração**
   - Interface de upload com metadados
   - Gestão de categorias
   - Batch operations

3. **UX Final**
   - Responsividade completa
   - Acessibilidade
   - Testes e refinamentos

## Considerações Técnicas

### Performance
- **Indexação**: Processamento em background para não impactar UI
- **Cache**: Resultados de busca em cache por 1 hora
- **Paginação**: Cursor-based para grandes volumes
- **Virtual Scrolling**: Para listas > 1000 itens

### Segurança
- **Validação**: Sanitização de queries de busca
- **Rate Limiting**: Controle de uso da API de busca
- **Access Control**: Permissões para admin vs público
- **XSS Protection**: Escape de conteúdo dinâmico

### Escalabilidade
- **Banco de Dados**: Estrutura preparada para crescimento
- **Microserviços**: APIs independentes e stateless
- **CDN**: Assets estáticos em cache
- **Monitoring**: Logs de performance e uso

### Integração
- **API Existente**: Compatibilidade com LocalFileService
- **Frontend Atual**: Extensão do componente reports.js
- **Admin Panel**: Integração com área administrativa
- **SEO**: URLs amigáveis e meta tags

## Testes e Validação

### Critérios de Aceitação
1. **Funcionalidade**: Busca retorna resultados corretos
2. **Performance**: < 2s para queries simples, < 5s para complexas
3. **Usabilidade**: Interface intuitiva e responsiva
4. **Compatibilidade**: Funciona em browsers modernos
5. **Acessibilidade**: WCAG 2.1 AA compliance

### Cenários de Teste
1. **Busca por data específica**
2. **Busca por palavra-chave no conteúdo**
3. **Filtros combinados**
4. **Paginação e ordenação**
5. **Upload e indexação automática**

## Riscos e Mitigações

### Riscos Técnicos
- **Performance**: PDFs grandes podem impactar indexação
  - *Mitigação*: Processamento assíncrono e lazy loading
- **Memória**: Extração de texto de PDFs grandes
  - *Mitigação*: Streaming e processamento em chunks
- **Compatibilidade**: Diferentes formatos de PDF
  - *Mitigação*: Suporte gradual e fallbacks

### Riscos de Projeto
- **Volume de dados**: 1000+ boletins para indexar
  - *Mitigação*: Indexação incremental e progressiva
- **Mudanças de escopo**: Novos requisitos durante desenvolvimento
  - *Mitigação*: Módulos independentes e documentação clara
- **Integração**: Conflitos com sistema existente
  - *Mitigação*: APIs backward-compatible

## Métricas de Sucesso

### Quantitativas
- **Performance**: Tempo de resposta < 2s
- **Cobertura**: 100% dos boletins indexados
- **Precisão**: > 95% de relevância nos resultados
- **Uptime**: > 99% disponibilidade

### Qualitativas
- **Usabilidade**: Interface intuitiva e fácil navegação
- **Satisfação**: Feedback positivo dos usuários
- **Acessibilidade**: Cumprimento das diretrizes WCAG
- **Manutenibilidade**: Código bem documentado e modular

## Implementação Gradual

### MVP (4 semanas)
1. Busca básica por título e data
2. Filtros simples
3. Listagem paginada
4. Integração com preview existente

### Versão Completa (6 semanas)
1. Busca full-text no conteúdo
2. Sistema de tags e categorias
3. Timeline interativa
4. Admin panel completo

### Melhorias Futuras (8+ semanas)
1. Machine learning para categorização automática
2. Análise de sentimentos nos boletins
3. Recomendações personalizadas
4. API pública para desenvolvedores

---

*Este plano será implementado mantendo a consistência com as tecnologias existentes do projeto (HTML5, CSS3, JavaScript vanilla, PHP) e seguindo os padrões visuais já estabelecidos do CCCRJ.*
