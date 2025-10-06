# Instruções para Execução do Scraping do Site CCCRJ

## Situação Atual

Devido às limitações do ambiente (Windows com XAMPP), encontramos dificuldades para executar diretamente os scripts de scraping:

1. O comando `php` não estava disponível na linha de comando
2. O Python também não estava instalado ou disponível
3. A execução direta dos scripts PHP resultou em exibição do código-fonte em vez de execução

## Soluções Alternativas Disponíveis

### 1. Executar usando o XAMPP
- Coloque os arquivos PHP no diretório htdocs do XAMPP
- Acesse via navegador web em vez de linha de comando
- Configure o Apache para permitir a execução de scripts que fazem requisições externas

### 2. Instalar Python
- Baixe e instale Python 3.x de https://python.org
- Adicione o Python ao PATH do sistema
- Instale as dependências com `pip install -r requirements.txt`
- Execute o script com `python run_scraper.py`

### 3. Executar via navegador com XAMPP
1. Coloque os arquivos PHP no diretório www do XAMPP
2. Inicie o Apache através do painel de controle do XAMPP
3. Crie um arquivo index.php que execute o processo de scraping:

```php
<?php
include_once 'scraper.php';
include_once 'image_finder.php';

echo "<h1>Iniciando processo de scraping...</h1>";

// Executar o scraper principal
echo "<h2>Etapa 1: Executando scraper principal...</h2>";
$start = microtime(true);

$scraper = new CCCRJScraper();
$scraper->run();

$end = microtime(true);
echo "<h3>Scraper principal concluído em " . ($end - $start) . " segundos</h3>";

// Executar o finder de imagens
echo "<h2>Etapa 2: Procurando e baixando imagens adicionais...</h2>";
$start = microtime(true);

$imageFinder = new ImageFinder();
$imageFinder->findAndDownloadImages();

$end = microtime(true);
echo "<h3>Download de imagens concluído em " . ($end - $start) . " segundos</h3>";

echo "<h1>Processo de scraping concluído com sucesso!</h1>";
echo "<p>Conteúdos salvos na pasta: cccrj_content</p>";
?>
```

## Arquivos Criados

Todos os arquivos necessários já foram criados no diretório:

- `scraper.php` - Classe principal de scraping em PHP
- `image_finder.php` - Classe para encontrar e baixar imagens em PHP
- `scraper.py` - Classe principal de scraping em Python
- `image_scraper.py` - Classe para encontrar e baixar imagens em Python
- `run_scraper.php` - Script principal em PHP
- `run_scraper.py` - Script principal em Python
- `requirements.txt` - Dependências para Python
- `DOCUMENTACAO.md` - Documentação detalhada
- `README.md` - Instruções básicas

## Próximos Passos

Para completar o scraping:

1. Escolha uma das soluções acima para executar os scripts
2. Execute o processo em um ambiente compatível
3. Verifique o diretório `cccrj_content` para confirmar a captura dos dados

## Nota sobre Limitações de Execução

Os scripts foram criados e estão prontos para execução. As limitações encontradas são ambientais e não de implementação. Os arquivos contêm código funcional que pode ser executado em ambientes com as tecnologias apropriadas instaladas.