<?php
// Teste simples da API de download de PDF
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/html; charset=UTF-8');

echo "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Teste PDF Preview - Versão Simplificada</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ccc; border-radius: 8px; }
        iframe { width: 100%; height: 600px; border: 1px solid #ccc; }
        .success { background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; }
        .error { background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; }
        .info { background-color: #d1ecf1; color: #0c5460; padding: 10px; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Teste PDF Preview - Nova Versão Simplificada</h1>";

echo "<div class='info'>
    <strong>Como funciona agora:</strong><br>
    - O modal usa a URL direta de download: /api/reports/download.php<br>
    - O navegador exibe o PDF diretamente no iframe<br>
    - Sem APIs complexas de preview, apenas download direto<br>
    - Mais compatível e simples de manter
</div>";

// Listar arquivos disponíveis
$dataDir = __DIR__ . '/../data/boletins/';
$reportsDir = __DIR__ . '/../data/reports/';

echo "<div class='test-section'>
    <h2>Arquivos Disponíveis para Teste</h2>";

// Verificar boletins
if (is_dir($dataDir)) {
    $boletins = glob($dataDir . '*.pdf');
    if ($boletins) {
        echo "<h3>Boletins:</h3><ul>";
        foreach ($boletins as $file) {
            $fileName = basename($file);
            echo "<li><a href='/api/reports/download.php?file=" . urlencode($fileName) . "&type=boletins' target='_blank'>$fileName</a></li>";
        }
        echo "</ul>";
    }
}

// Verificar reports
if (is_dir($reportsDir)) {
    $reports = glob($reportsDir . '*.pdf');
    if ($reports) {
        echo "<h3>Reports:</h3><ul>";
        foreach ($reports as $file) {
            $fileName = basename($file);
            echo "<li><a href='/api/reports/download.php?file=" . urlencode($fileName) . "&type=reports' target='_blank'>$fileName</a></li>";
        }
        echo "</ul>";
    }
}

echo "</div>";

// Teste com iframe
echo "<div class='test-section'>
    <h2>Teste com Iframe</h2>
    <p>Se um arquivo estiver disponível, ele será exibido abaixo:</p>";

// Tentar encontrar um arquivo para teste
$testFile = 'boletim (1).pdf'; // Usar o arquivo real disponível
$testType = 'boletins';

$testUrl = '/api/reports/download.php?file=' . urlencode($testFile) . '&type=' . $testType;
echo "<p class='success'>Arquivo de teste: <strong>$testFile</strong></p>";
echo "<p>URL de teste: <a href='$testUrl' target='_blank'>$testUrl</a></p>";
echo "<iframe src='$testUrl' style='width: 100%; height: 600px; border: 1px solid #ccc; margin-top: 10px;'></iframe>";

echo "</div>";

echo "<div class='test-section'>
    <h2>Como usar o novo sistema:</h2>
    <ol>
        <li>O modal agora usa a URL: <code>/api/reports/download.php?file=NOME_ARQUIVO&type=TIPO</code></li>
        <li>O navegador exibe o PDF diretamente no iframe</li>
        <li>O botão 'Baixar PDF' abre o arquivo em uma nova aba</li>
        <li>Muito mais simples e confiável que o sistema anterior</li>
    </ol>
</div>";

echo "</body>
</html>";
?>
