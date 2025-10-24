<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$fileName = $_GET['file'] ?? '';
$type = $_GET['type'] ?? 'boletins';
$thumbnail = $_GET['thumbnail'] ?? false;

echo "<h1>Teste PDF Preview API</h1>";
echo "<p><strong>Arquivo:</strong> " . htmlspecialchars($fileName) . "</p>";
echo "<p><strong>Tipo:</strong> " . htmlspecialchars($type) . "</p>";
echo "<p><strong>Miniatura:</strong> " . ($thumbnail ? 'Sim' : 'Não') . "</p>";

$dataPath = __DIR__ . '/../../../data/' . $type . '/' . basename($fileName);
$reportsPath = __DIR__ . '/../../../data/reports/' . basename($fileName);

echo "<p><strong>Caminho dados:</strong> " . htmlspecialchars($dataPath) . "</p>";
echo "<p><strong>Arquivo existe em dados:</strong> " . (file_exists($dataPath) ? 'SIM' : 'NÃO') . "</p>";
echo "<p><strong>Caminho reports:</strong> " . htmlspecialchars($reportsPath) . "</p>";
echo "<p><strong>Arquivo existe em reports:</strong> " . (file_exists($reportsPath) ? 'SIM' : 'NÃO') . "</p>";

if (file_exists($dataPath)) {
    echo "<p><strong>Tamanho do arquivo:</strong> " . filesize($dataPath) . " bytes</p>";
    echo "<p><strong>Última modificação:</strong> " . date('Y-m-d H:i:s', filemtime($dataPath)) . "</p>";
}
?>
