<?php
// Script para processar as diretorias do arquivo textos_sistema.md e gerar o diretorias.json

// Caminho dos arquivos
$caminhoTexto = __DIR__ . '/../scraping_cccrj/textos_sistema.md';
$caminhoSaida = __DIR__ . '/../data/institucional/diretorias.json';

// Função para extrair as diretorias do texto
function extrairDiretorias($conteudo) {
    $diretorias = [];
    $blocos = preg_split('/### Diretoria (\d{4}-\d{2,4})/', $conteudo, -1, PREG_SPLIT_DELIM_CAPTURE);
    
    // Remover o primeiro elemento que é o conteúdo antes da primeira diretoria
    array_shift($blocos);
    
    // Processar cada bloco de diretoria
    for ($i = 0; $i < count($blocos); $i += 2) {
        if (!isset($blocos[$i]) || !isset($blocos[$i+1])) continue;
        
        $periodo = trim($blocos[$i]);
        $conteudo = $blocos[$i+1];
        
        // Extrair presidente
        preg_match('/- \*\*Presidente\*\*: ([^\n]+)/', $conteudo, $matchesPresidente);
        $presidente = $matchesPresidente[1] ?? '';
        
        // Extrair diretores
        preg_match_all('/- \*\*([^:]+)\*\*: ([^\n]+)/', $conteudo, $matchesDiretores, PREG_SET_ORDER);
        
        $diretores = [];
        foreach ($matchesDiretores as $diretor) {
            if (stripos($diretor[1], 'Presidente') !== false) continue;
            
            $diretores[] = [
                'cargo' => trim($diretor[1]),
                'nome' => trim($diretor[2])
            ];
        }
        
        // Adicionar diretoria ao array
        $diretorias[] = [
            'periodo' => $periodo,
            'presidente' => $presidente,
            'diretores' => $diretores
        ];
    }
    
    return $diretorias;
}

// Ler o conteúdo do arquivo
$conteudo = file_get_contents($caminhoTexto);
if ($conteudo === false) {
    die("Erro ao ler o arquivo de texto.");
}

// Extrair as diretorias
$diretorias = extrairDiretorias($conteudo);

// Ordenar as diretorias por período (mais recente primeiro)
usort($diretorias, function($a, $b) {
    $anoA = (int)substr($a['periodo'], 0, 4);
    $anoB = (int)substr($b['periodo'], 0, 4);
    return $anoB - $anoA;
});

// Separar a diretoria atual (a mais recente)
$diretoriaAtual = array_shift($diretorias);

// Montar a estrutura final
$dados = [
    'diretoria_atual' => $diretoriaAtual,
    'diretorias_historicas' => $diretorias,
    'atualizacao' => date('Y-m-d H:i:s'),
    'total_diretorias' => count($diretorias) + 1
];

// Converter para JSON formatado
$json = json_encode($dados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

// Salvar no arquivo
if (file_put_contents($caminhoSaida, $json)) {
    echo "Arquivo gerado com sucesso em: " . $caminhoSaida . "\n";
    echo "Total de diretorias processadas: " . (count($diretorias) + 1) . "\n";
} else {
    echo "Erro ao salvar o arquivo JSON.\n";
}

// Mostrar prévia
if (php_sapi_name() === 'cli') {
    echo "\nPrévia da Estrutura Gerada:\n";
    print_r($dados);
}
