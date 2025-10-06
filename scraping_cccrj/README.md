# Projeto de Scraping do Site CCCRJ

Este projeto contém os scripts e documentação necessários para realizar o scraping completo do site do Centro de Comércio do Café do Rio de Janeiro (CCCRJ), localizado em http://www.cccrj.com.br/.

## Objetivo

Realizar o scraping de todos os conteúdos e páginas secundárias do site CCCRJ para fins de preservação e futura replicação.

## Conteúdo do Projeto

- `scraper.py/php` - Script principal para scraping de páginas HTML e arquivos
- `image_scraper.py/php` - Script para encontrar e baixar imagens
- `run_scraper.py/php` - Script principal que orquestra o processo
- `executar_scraping.php` - Script para execução via navegador web (corrigido)
- `requirements.txt` - Dependências necessárias (para a versão Python)
- `DOCUMENTACAO.md` - Documentação detalhada do processo
- `cccrj_content/` - Diretório onde o conteúdo é salvo (será criado após execução)

## Como Usar

### Para a versão Python:
1. Instale as dependências: `pip install -r requirements.txt`
2. Execute: `python run_scraper.py`

### Para a versão PHP via linha de comando:
1. Execute: `php run_scraper.php`

### Para a versão PHP via navegador web (recomendado para ambiente XAMPP):
1. Coloque todos os arquivos do diretório scraping_cccrj no diretório htdocs do XAMPP
2. Inicie o Apache pelo painel de controle do XAMPP
3. Acesse via navegador: http://localhost/executar_scraping.php

## Estrutura de Saída

Após a execução, todos os conteúdos serão organizados em uma estrutura de diretórios semelhante à do site original, dentro do diretório `cccrj_content`.

## Considerações Importantes

- O scraping é realizado de forma ética e respeitosa ao servidor
- Um delay de 1 segundo é aplicado entre requisições
- O conteúdo coletado é destinado à preservação e replicaçāo, respeitando os direitos autorais

## Execução via Navegador Web

O método recomendado para executar este scraping no ambiente XAMPP é através do navegador web, usando o script `executar_scraping.php`. Isso contorna as limitações do ambiente Windows e permite a execução de scripts PHP de forma mais confiável.

Ao executar via navegador, você verá o progresso em tempo real e um resumo final do scraping realizado. O script `executar_scraping.php` foi corrigido para resolver problemas de inclusão de classes e execução adequada no contexto web.

## Autores

Este projeto foi desenvolvido como parte de uma solicitação para realizar o scraping do site CCCRJ com objetivo de replicaçāo futura.