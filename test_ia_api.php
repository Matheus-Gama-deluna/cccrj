<?php
/**
 * Script de teste para verificar a funcionalidade de extração de cotações via API de IA
 */

echo "Testando funcionalidade de extração de cotações via IA...\n";

// Verificar se o arquivo boletim.pdf existe
$pdfFile = 'boletim.pdf';
if (!file_exists($pdfFile)) {
    echo "ERRO: Arquivo boletim.pdf não encontrado!\n";
    exit(1);
}

echo "Arquivo boletim.pdf encontrado. Tamanho: " . filesize($pdfFile) . " bytes\n";

// Testar o endpoint de upload
echo "\nTestando endpoint de upload...\n";

$uploadUrl = 'http://localhost/cccrj/api/upload_report_openrouter.php'; // Ajustar conforme necessário

// Preparar o upload
$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => $uploadUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => [
        'pdf_file' => new CURLFile(realpath($pdfFile))
    ],
    CURLOPT_TIMEOUT => 60, // Aumentar timeout para processamento com IA
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

if ($response === false) {
    echo "ERRO: Não foi possível conectar ao endpoint de upload\n";
    exit(1);
}

echo "Código HTTP: $httpCode\n";
echo "Resposta do endpoint:\n";
echo $response . "\n";

// Decodificar a resposta
$result = json_decode($response, true);
if ($result) {
    if ($result['success']) {
        echo "\nSUCCESS: Cotações extraídas com sucesso!\n";
        
        if (isset($result['data'])) {
            echo "Data: " . ($result['data']['date'] ?? 'Desconhecida') . "\n";
            echo "Base: " . ($result['data']['base'] ?? 'Desconhecida') . "\n";
            
            if (isset($result['data']['quotes'])) {
                echo "\nCotações extraídas:\n";
                foreach ($result['data']['quotes'] as $group => $quotes) {
                    echo "  $group:\n";
                    foreach ($quotes as $id => $quote) {
                        echo "    - {$quote['name']}: R$ {$quote['price']}\n";
                    }
                }
            }
        }
    } else {
        echo "\nERRO: " . ($result['error'] ?? 'Erro desconhecido') . "\n";
    }
} else {
    echo "\nERRO: Não foi possível decodificar a resposta JSON\n";
    echo "Resposta recebida: $response\n";
}

// Testar endpoint de obtenção de cotações
echo "\nTestando endpoint de obtenção de cotações...\n";

$getCotacoesUrl = 'http://localhost/cccrj/api/get_quotes_openrouter.php'; // Ajustar conforme necessário

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => $getCotacoesUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPGET => true,
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

if ($response === false) {
    echo "ERRO: Não foi possível conectar ao endpoint de obtenção de cotações\n";
    exit(1);
}

echo "Código HTTP: $httpCode\n";
echo "Resposta do endpoint:\n";
echo $response . "\n";

// Decodificar a resposta
$result = json_decode($response, true);
if ($result) {
    if ($result['success']) {
        echo "\nSUCCESS: Cotações obtidas com sucesso!\n";
        
        if (isset($result['data'])) {
            echo "Data: " . ($result['data']['date'] ?? 'Desconhecida') . "\n";
            echo "Base: " . ($result['data']['base'] ?? 'Desconhecida') . "\n";
            
            if (isset($result['data']['quotes'])) {
                echo "\nNúmero de cotações: " . count($result['data']['quotes']) . "\n";
            }
        }
    } else {
        echo "\nERRO: " . ($result['error'] ?? 'Erro desconhecido') . "\n";
    }
} else {
    echo "\nERRO: Não foi possível decodificar a resposta JSON\n";
    echo "Resposta recebida: $response\n";
}

echo "\nTeste concluído.\n";