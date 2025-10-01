<?php
/**
 * Endpoint para processamento de PDF com API de IA
 * Recebe um arquivo PDF e envia para análise com IA via OpenRouter
 */

require_once 'config_openrouter.php';
require_once 'config.php'; // Configurações gerais do sistema

// Função para validar upload de PDF
function validarPdf($file) {
    $allowedTypes = ['application/pdf'];
    $maxSize = 10 * 1024 * 1024; // 10MB
    
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'error' => 'Tipo de arquivo não suportado. Apenas PDFs são permitidos.'];
    }
    
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'error' => 'Arquivo muito grande. O tamanho máximo é de 10MB.'];
    }
    
    return ['success' => true];
}

// Função para converter PDF para formato que a API aceita
function converterPdfParaBase64($pdfPath) {
    $content = file_get_contents($pdfPath);
    return base64_encode($content);
}

// Função para chamar a API do OpenRouter
function chamarApiOpenRouter($pdfBase64) {
    $curl = curl_init();
    
    // Montar o prompt para extração de cotações
    $prompt = "Você é um assistente especializado em extrair informações financeiras de documentos PDF do mercado cafeeiro brasileiro.
Extraia as cotações de café do PDF fornecido, identificando os seguintes tipos:

GRUPO I (BASE VARGINHA/MG):
- Tipo C. Interno \$600 DEF. 11+\$
- Tipo 6/7, BC Duro
- Tipo 6, BC Fino

GRUPO II (BASE VITÓRIA):
- Tipo C. Interno 600 DEF.
- Tipo 7, Bica
- Tipo 5/6 15/16 (Pronto Embarque)
- Tipo 2/3 17/18 (Pronto Embarque)

CONILLON (BASE VITÓRIA):
- Tipo 7 Bica Corrida
- Tipo 5/6 13 UP Pronto Embarque

Forneça as cotações em um formato JSON estruturado, incluindo:
- Valores em reais (R\$)
- Unidade de medida (geralmente por saca de 60kg - R\$/saca 60kg)
- Data da cotação se estiver disponível
- Base de cotação (Varginha/MG ou Vitória)

Responda estritamente no seguinte formato JSON:
{
  \"base\": \"Varginha/MG ou Vitória\",
  \"date\": \"AAAA-MM-DD\",
  \"quotes\": {
    \"grupo_i\": {
      \"tipo_c_interno_600_def_11\": {\"name\": \"Tipo C. Interno \$600 DEF. 11+\$\", \"price\": valor, \"unit\": \"R\$/saca 60kg\"},
      \"tipo_6_7_bc_duro\": {\"name\": \"Tipo 6/7, BC Duro\", \"price\": valor, \"unit\": \"R\$/saca 60kg\"},
      \"tipo_6_bc_fino\": {\"name\": \"Tipo 6, BC Fino\", \"price\": valor, \"unit\": \"R\$/saca 60kg\"}
    },
    \"grupo_ii\": {
      \"tipo_c_interno_600_def\": {\"name\": \"Tipo C. Interno 600 DEF.\", \"price\": valor, \"unit\": \"R\$/saca 60kg\"},
      \"tipo_7_bica\": {\"name\": \"Tipo 7, Bica\", \"price\": valor, \"unit\": \"R\$/saca 60kg\"},
      \"tipo_5_6_15_16_pronto_embarque\": {\"name\": \"Tipo 5/6 15/16 (Pronto Embarque)\", \"price\": valor, \"unit\": \"R\$/saca 60kg\"},
      \"tipo_2_3_17_18_pronto_embarque\": {\"name\": \"Tipo 2/3 17/18 (Pronto Embarque)\", \"price\": valor, \"unit\": \"R\$/saca 60kg\"}
    },
    \"conillon\": {
      \"tipo_7_bica_corrida\": {\"name\": \"Tipo 7 Bica Corrida\", \"price\": valor, \"unit\": \"R\$/saca 60kg\"},
      \"tipo_5_6_13_up_pronto_embarque\": {\"name\": \"Tipo 5/6 13 UP Pronto Embarque\", \"price\": valor, \"unit\": \"R\$/saca 60kg\"}
    }
  },
  \"raw_data\": \"texto_bruto_extraído\"
}";

    $data = [
        'model' => OPENROUTER_MODEL,
        'messages' => [
            [
                'role' => 'user',
                'content' => [
                    ['type' => 'text', 'text' => $prompt],
                    [
                        'type' => 'image_url',
                        'image_url' => [
                            'url' => 'data:application/pdf;base64,' . $pdfBase64
                        ]
                    ]
                ]
            ]
        ],
        'temperature' => 0.1
    ];

    curl_setopt_array($curl, [
        CURLOPT_URL => OPENROUTER_API_URL,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . OPENROUTER_API_KEY,
            'HTTP-Referer: ' . $_SERVER['HTTP_HOST'],
            'X-Title: CCCRJ Cotações IA'
        ]
    ]);

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($httpCode !== 200) {
        return ['success' => false, 'error' => 'Erro na API: ' . $response];
    }

    $result = json_decode($response, true);
    
    if (!$result || !isset($result['choices'][0]['message']['content'])) {
        return ['success' => false, 'error' => 'Resposta inválida da API'];
    }

    return ['success' => true, 'data' => $result['choices'][0]['message']['content']];
}

// Função para validar e processar dados extraídos
function validarDadosExtraidos($rawData) {
    try {
        // Tentar converter a resposta da IA em JSON
        $json = json_decode($rawData, true);
        
        if (!$json) {
            return ['success' => false, 'error' => 'Não foi possível converter a resposta da IA em JSON'];
        }
        
        // Verificar se a estrutura do JSON está correta
        if (!isset($json['quotes'])) {
            return ['success' => false, 'error' => 'Estrutura de dados inválida na resposta da IA'];
        }
        
        // Validação adicional dos dados
        $groups = ['grupo_i', 'grupo_ii', 'conillon'];
        
        foreach ($groups as $group) {
            if (!isset($json['quotes'][$group])) {
                continue; // Alguns grupos podem estar vazios
            }
            
            foreach ($json['quotes'][$group] as $quoteId => $quote) {
                if (!isset($quote['price']) || !is_numeric($quote['price']) || $quote['price'] < 0) {
                    return ['success' => false, 'error' => "Valor inválido para $quoteId: " . $quote['price'] ?? 'null'];
                }
                
                if (!isset($quote['name']) || !isset($quote['unit'])) {
                    return ['success' => false, 'error' => "Dados incompletos para $quoteId"];
                }
            }
        }
        
        return ['success' => true, 'data' => $json];
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Erro ao processar dados extraídos: ' . $e->getMessage()];
    }
}

// Processar upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf_file'])) {
    // Validar o upload
    $validacao = validarPdf($_FILES['pdf_file']);
    if (!$validacao['success']) {
        http_response_code(400);
        echo json_encode($validacao);
        exit;
    }
    
    // Converter PDF para base64
    $pdfBase64 = converterPdfParaBase64($_FILES['pdf_file']['tmp_name']);
    if (!$pdfBase64) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erro ao converter PDF']);
        exit;
    }
    
    // Chamar API do OpenRouter
    $apiResponse = chamarApiOpenRouter($pdfBase64);
    if (!$apiResponse['success']) {
        http_response_code(500);
        echo json_encode($apiResponse);
        exit;
    }
    
    // Validar dados extraídos
    $dadosValidados = validarDadosExtraidos($apiResponse['data']);
    if (!$dadosValidados['success']) {
        http_response_code(500);
        echo json_encode($dadosValidados);
        exit;
    }
    
    // Salvar dados extraídos em arquivo
    $data = $dadosValidados['data'];
    $data['timestamp'] = time();
    $jsonContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    if (file_put_contents('../data/quotes_openrouter.json', $jsonContent) === false) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erro ao salvar dados']);
        exit;
    }
    
    // Resposta de sucesso
    echo json_encode([
        'success' => true,
        'message' => 'Cotações extraídas com sucesso',
        'data' => $data
    ]);
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método não permitido']);
}