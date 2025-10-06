<?php
/**
 * Script principal para executar o scraping do site CCCRJ
 * 
 * Este script coordena o processo de scraping de http://www.cccrj.com.br/
 * seguindo as melhores práticas para respeitar o servidor e coletar
 * todos os conteúdos do site para futura replicação.
 */

// Aumentar o limite de tempo de execução
ini_set('max_execution_time', 0); // Sem limite (ou pode definir um valor alto como 300 para 5 minutos)
ini_set('memory_limit', '256M');

// Incluir o arquivo com as classes do scraper
include_once 'cccrj_scraper.php';

echo "<h1>Iniciando processo de scraping do site CCCRJ</h1>" . PHP_EOL;
echo "<p>Este processo coletará todos os conteúdos do site http://www.cccrj.com.br/</p>" . PHP_EOL;
echo "<p>O processo respeitará os limites de requisições e os termos de uso do site.</p>" . PHP_EOL;

echo "<h2>Etapa 1: Executando scraper principal...</h2>" . PHP_EOL;
$start = microtime(true);

// Executar o scraper principal
$scraper = new CCCRJScraper();
$scraper->run();

$end = microtime(true);
echo "<h3>Scraper principal concluído em " . round($end - $start, 2) . " segundos</h3>" . PHP_EOL;

echo "<h2>Etapa 2: Procurando e baixando imagens adicionais...</h2>" . PHP_EOL;
$start = microtime(true);

// Executar o finder de imagens
$imageFinder = new ImageFinder();
$imageFinder->findAndDownloadImages();

$end = microtime(true);
echo "<h3>Download de imagens concluído em " . round($end - $start, 2) . " segundos</h3>" . PHP_EOL;

echo "<h1>Processo de scraping concluído com sucesso!</h1>" . PHP_EOL;
echo "<p>Conteúdos salvos na pasta: cccrj_content</p>" . PHP_EOL;

// Mostrar resumo do scraping
$outputDir = 'cccrj_content';
if (file_exists($outputDir . '/resumo_scraping.txt')) {
    echo "<h2>Resumo do scraping:</h2>" . PHP_EOL;
    echo "<pre>" . htmlspecialchars(file_get_contents($outputDir . '/resumo_scraping.txt')) . "</pre>" . PHP_EOL;
}

echo "<h2>Instruções para replicação futura:</h2>" . PHP_EOL;
echo "<p>Para replicar os conteúdos salvos:</p>" . PHP_EOL;
echo "<ol>" . PHP_EOL;
echo "<li>Os arquivos HTML podem ser servidos diretamente por qualquer servidor web</li>" . PHP_EOL;
echo "<li>As imagens estão armazenadas na pasta 'images' dentro de 'cccrj_content'</li>" . PHP_EOL;
echo "<li>Os arquivos PDF e CSS foram salvos com seus respectivos caminhos originais</li>" . PHP_EOL;
echo "<li>Para manter a estrutura original, mantenha a mesma hierarquia de diretórios</li>" . PHP_EOL;
echo "</ol>" . PHP_EOL;

echo "<p>Os conteúdos do site CCCRJ agora estão disponíveis localmente e podem ser utilizados para fins de preservação histórica.</p>" . PHP_EOL;
?>