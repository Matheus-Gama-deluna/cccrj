# Documentação do Processo de Scraping do Site CCCRJ

## Descrição Geral

Este projeto contém os scripts e documentação necessários para realizar o scraping completo do site do Centro de Comércio do Café do Rio de Janeiro (CCCRJ), localizado em http://www.cccrj.com.br/. O objetivo é coletar todos os conteúdos e páginas secundárias para futura replicação.

## Estrutura do Site Identificada

O site CCCRJ possui a seguinte estrutura:

- Página inicial e páginas secundárias em HTML
- Seções organizadas em diretórios:
  - `/cccrj/` - Sobre o CCCRJ
  - `/crmc/` - Centro de Referência e Memória do Café
  - `/revista/` - Revista do Café
  - `/terminal/` - Terminal Rio-Café
  - `/rio/` - Café no Rio
  - `/Boletim/` - Boletins do Café
  - `/clipping/` - Clipping
  - `/noticias/` - Notícias
  - `/links/` - Links úteis

## Scripts Criados

### 1. scraper.py
Script principal em Python para fazer o scraping das páginas HTML, arquivos CSS, JavaScript e PDF.

### 2. image_scraper.py
Script auxiliar para encontrar e baixar todas as imagens mencionadas nas páginas do site.

### 3. run_scraper.py
Script principal que orquestra a execução de todos os componentes do scraping.

### 4. scraper.php (alternativo)
Versão em PHP do script de scraping para ambientes onde Python não está disponível.

### 5. image_finder.php (alternativo)
Versão em PHP do script de busca de imagens.

## Como Executar o Scraping

### Requisitos
- Python 3.x ou PHP 7.x+
- Bibliotecas: requests, beautifulsoup4 (para Python)

### Para Python:
1. Instale as dependências: `pip install -r requirements.txt`
2. Execute: `python run_scraper.py`

### Para PHP:
1. Execute: `php run_scraper.php`

### Resultado
Todos os conteúdos são salvos no diretório `cccrj_content` com a mesma estrutura de diretórios do site original.

## Considerações Éticas e Técnicas

- O scraping respeita as diretrizes do robots.txt do site
- Um delay de 1 segundo entre requisições foi implementado para evitar sobrecarga no servidor
- O User-Agent foi configurado de forma identificável e respeitosa
- O script tenta respeitar os limites de requisições e a carga no servidor alvo

## Limitações Conhecidas

- Alguns links podem estar quebrados ou desatualizados
- O site usa tecnologias antigas (frames, Dreamweaver templates) que podem afetar o scraping
- Alguns recursos dinâmicos podem não ser capturados corretamente

## Estrutura de Arquivos Resultante

Após a execução, a pasta `cccrj_content` conterá:

```
cccrj_content/
├── index.htm
├── mapa.htm
├── fale.htm
├── Style.css
├── javascript/
├── boletim.pdf
├── Manual_PERT_Simples_Nacional.pdf
├── cccrj/
├── crmc/
├── revista/
├── terminal/
├── rio/
├── Boletim/
├── clipping/
├── noticias/
├── links/
├── images/
└── resumo_scraping.txt
```

## Uso Futuro e Replicação

Os arquivos salvos no diretório `cccrj_content` podem ser usados para:

1. Hospedar uma cópia local do site CCCRJ
2. Analisar o conteúdo para fins de pesquisa ou histórico
3. Referência para migração do conteúdo para novas tecnologias
4. Backup do conteúdo do site original

Para replicar o site, basta hospedar a estrutura de arquivos gerada em qualquer servidor web, mantendo a mesma estrutura de diretórios.

## Notas Adicionais

- O site CCCRJ parece ter sido criado com Dreamweaver e usa tecnologias antigas
- O conteúdo é principalmente informativo sobre a história do café no Rio de Janeiro
- O site contém documentos históricos, boletins e informações institucionais

## Licença e Direitos Autorais

Este script de scraping foi desenvolvido para fins de preservação de conteúdo e backup. Qualquer uso do conteúdo coletado deve respeitar os direitos autorais e termos de uso do site original.