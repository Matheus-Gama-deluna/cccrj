#!/usr/bin/env php
<?php
/**
 * Script para executar o scraping do site CCCRJ via linha de comando
 */
 
// Incluir o arquivo com as classes do scraper
include_once 'cccrj_scraper.php';

// Executar o scraping
runScraper();
?>