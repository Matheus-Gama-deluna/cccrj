<?php
// Lista de arquivos das diretorias
$arquivos = [
    'diret_2_1.htm' => 'Diretoria 2',
    'diret_3_1.htm' => 'Diretoria 3',
    'diret_4_1.htm' => 'Diretoria 4',
    'diret_5_1.htm' => 'Diretoria 5',
    'diretorias.htm' => 'Visão Geral das Diretorias'
];

// Diretório base
$diretorio = 'scraping_cccrj/cccrj_content/cccrj/';

$dados = [];

foreach ($arquivos as $arquivo => $nome) {
    $caminho = $diretorio . $arquivo;
    if (file_exists($caminho)) {
        // Lê o arquivo com codificação adequada (teste com 'Windows-1252' ou 'ISO-8859-1')
        $conteudo = file_get_contents($caminho);
        $conteudo = iconv('Windows-1252', 'UTF-8', $conteudo); // Usar iconv em vez de mb_convert_encoding
        
        // Regex para extrair dados (exemplos: nomes, cargos, períodos)
        preg_match_all('/Presidente[:\s]*([^\n\r]*)/i', $conteudo, $presidentes);
        preg_match_all('/Vice-Presidente[:\s]*([^\n\r]*)/i', $conteudo, $vices);
        preg_match_all('/Diretor[:\s]*([^\n\r]*)/i', $conteudo, $diretores);
        preg_match_all('/Período|Mandato[:\s]*([^\n\r]*)/i', $conteudo, $periodos);
        
        $dados[$nome] = [
            'presidentes' => array_unique($presidentes[1]),
            'vices' => array_unique($vices[1]),
            'diretores' => array_unique($diretores[1]),
            'periodos' => array_unique($periodos[1]),
            'conteudo_resumo' => substr(strip_tags($conteudo), 0, 500) // Resumo do texto
        ];
    } else {
        $dados[$nome] = ['erro' => 'Arquivo não encontrado'];
    }
}

// Saída em JSON
header('Content-Type: application/json');
echo json_encode($dados, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
