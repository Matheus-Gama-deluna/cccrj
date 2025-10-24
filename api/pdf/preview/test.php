<?php
echo "API funcionando!<br>";
echo "Arquivo: " . ($_GET['file'] ?? 'não especificado') . "<br>";
echo "Tipo: " . ($_GET['type'] ?? 'não especificado') . "<br>";
echo "Data/Hora: " . date('Y-m-d H:i:s');
?>
