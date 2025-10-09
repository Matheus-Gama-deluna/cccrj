<?php
// utils/html_parser.php

class HtmlParser {
    public static function extractTitle($html) {
        $title = '';
        
        // Extrair título da tag title
        preg_match('/<title[^>]*>(.*?)<\/title>/i', $html, $matches);
        if (!empty($matches[1])) {
            $title = trim(strip_tags($matches[1]));
        }
        
        return $title;
    }
    
    public static function extractContent($html) {
        // Criar DOMDocument para parser mais robusto
        $dom = new DOMDocument();
        
        // Suprimir erros de HTML inválido
        libxml_use_internal_errors(true);
        
        // Converter encoding para UTF-8
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'ISO-8859-1');
        
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        
        // Remover tags de script e estilo
        $scripts = $dom->getElementsByTagName('script');
        while ($scripts->length > 0) {
            $scripts->item(0)->parentNode->removeChild($scripts->item(0));
        }
        
        $styles = $dom->getElementsByTagName('style');
        while ($styles->length > 0) {
            $styles->item(0)->parentNode->removeChild($styles->item(0));
        }
        
        // Pegar o conteúdo do body
        $body = $dom->getElementsByTagName('body')->item(0);
        if ($body) {
            $content = $dom->saveHTML($body);
            // Remover a tag body, mantendo apenas seu conteúdo
            $content = str_replace(['<body>', '</body>'], '', $content);
        } else {
            $content = $dom->saveHTML();
        }
        
        return trim($content);
    }
    
    public static function extractTextSummary($html, $length = 200) {
        $dom = new DOMDocument();
        
        // Suprimir erros de HTML inválido
        libxml_use_internal_errors(true);
        
        // Converter encoding para UTF-8
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'ISO-8859-1');
        
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        
        $textContent = $dom->textContent;
        
        // Remover quebras de linha e espaços extras
        $textContent = preg_replace('/\s+/', ' ', $textContent);
        $textContent = trim($textContent);
        
        // Cortar para o tamanho desejado
        if (strlen($textContent) > $length) {
            $textContent = substr($textContent, 0, $length) . '...';
        }
        
        return $textContent;
    }
    
    public static function extractImages($html) {
        $dom = new DOMDocument();
        
        // Suprimir erros de HTML inválido
        libxml_use_internal_errors(true);
        
        // Converter encoding para UTF-8
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'ISO-8859-1');
        
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        
        $images = [];
        $imgTags = $dom->getElementsByTagName('img');
        
        foreach ($imgTags as $img) {
            $src = $img->getAttribute('src');
            if (!empty($src)) {
                $images[] = $src;
            }
        }
        
        return $images;
    }
}
?>