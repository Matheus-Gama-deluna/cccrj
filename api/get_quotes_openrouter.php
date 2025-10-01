<?php
/**
 * Endpoint para obtenção de cotações extraídas via IA
 * Retorna as cotações extraídas do último PDF processado
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config_openrouter.php';

// Caminho para o arquivo de cotações extraídas
$quotesFile = '../data/quotes_openrouter.json';

// Função para obter as cotações
function obterCotacoes() {
    global $quotesFile;
    
    // Verificar se o arquivo existe
    if (!file_exists($quotesFile)) {
        return [
            'success' => false,
            'error' => 'Nenhuma cotação extraída encontrada. Faça o upload de um PDF primeiro.',
            'fallback' => obterCotacoesSimuladas()
        ];
    }
    
    // Ler o conteúdo do arquivo
    $content = file_get_contents($quotesFile);
    if ($content === false) {
        return [
            'success' => false,
            'error' => 'Erro ao ler o arquivo de cotações',
            'fallback' => obterCotacoesSimuladas()
        ];
    }
    
    // Decodificar o JSON
    $data = json_decode($content, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'success' => false,
            'error' => 'Erro ao decodificar JSON: ' . json_last_error_msg(),
            'fallback' => obterCotacoesSimuladas()
        ];
    }
    
    // Retornar os dados
    return [
        'success' => true,
        'data' => $data
    ];
}

// Função para obter cotações simuladas como fallback
function obterCotacoesSimuladas() {
    $tiposCotacoes = [
        'grupo_i' => [
            'tipo_c_interno_600_def_11' => [
                'name' => 'Tipo C. Interno $600 DEF. 11+$',
                'price' => rand(120000, 130000) / 100, // Valor entre 1200 e 1300
                'unit' => 'R$/saca 60kg'
            ],
            'tipo_6_7_bc_duro' => [
                'name' => 'Tipo 6/7, BC Duro',
                'price' => rand(110000, 120000) / 100, // Valor entre 1100 e 1200
                'unit' => 'R$/saca 60kg'
            ],
            'tipo_6_bc_fino' => [
                'name' => 'Tipo 6, BC Fino',
                'price' => rand(115000, 125000) / 100, // Valor entre 1150 e 1250
                'unit' => 'R$/saca 60kg'
            ]
        ],
        'grupo_ii' => [
            'tipo_c_interno_600_def' => [
                'name' => 'Tipo C. Interno 600 DEF.',
                'price' => rand(118000, 128000) / 100, // Valor entre 1180 e 1280
                'unit' => 'R$/saca 60kg'
            ],
            'tipo_7_bica' => [
                'name' => 'Tipo 7, Bica',
                'price' => rand(116000, 126000) / 100, // Valor entre 1160 e 1260
                'unit' => 'R$/saca 60kg'
            ],
            'tipo_5_6_15_16_pronto_embarque' => [
                'name' => 'Tipo 5/6 15/16 (Pronto Embarque)',
                'price' => rand(114000, 124000) / 100, // Valor entre 1140 e 1240
                'unit' => 'R$/saca 60kg'
            ],
            'tipo_2_3_17_18_pronto_embarque' => [
                'name' => 'Tipo 2/3 17/18 (Pronto Embarque)',
                'price' => rand(112000, 122000) / 100, // Valor entre 1120 e 1220
                'unit' => 'R$/saca 60kg'
            ]
        ],
        'conillon' => [
            'tipo_7_bica_corrida' => [
                'name' => 'Tipo 7 Bica Corrida',
                'price' => rand(95000, 105000) / 100, // Valor entre 950 e 1050
                'unit' => 'R$/saca 60kg'
            ],
            'tipo_5_6_13_up_pronto_embarque' => [
                'name' => 'Tipo 5/6 13 UP Pronto Embarque',
                'price' => rand(93000, 103000) / 100, // Valor entre 930 e 1030
                'unit' => 'R$/saca 60kg'
            ]
        ]
    ];
    
    return [
        'date' => date('Y-m-d'),
        'base' => 'Varginha/MG ou Vitória',
        'quotes' => $tiposCotacoes,
        'timestamp' => time()
    ];
}

// Processar requisição GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = obterCotacoes();
    
    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'data' => $result['data']
        ]);
    } else {
        // Em caso de erro, retornar dados simulados como fallback
        http_response_code(200); // Manter status 200 mesmo com fallback
        echo json_encode([
            'success' => false,
            'error' => $result['error'],
            'data' => $result['fallback']
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Método não permitido'
    ]);
}