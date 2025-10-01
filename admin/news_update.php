<?php
// Interface de administração para atualização de notícias
require_once '../api/config.php';
require_once '../api/news_scraper.php';

if (isset($_POST['force_update'])) {
    $scraper = new NewsScraper();
    $result = $scraper->forceUpdate();
    $updateResult = $result;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Administração de Notícias</title>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .card { background: #f5f5f5; padding: 20px; margin: 20px 0; border-radius: 5px; }
        .btn { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; }
        .btn:hover { background: #005a87; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Administração de Notícias</h1>
        
        <div class="card">
            <h2>Forçar Atualização de Notícias</h2>
            <p>Esta ação irá buscar as notícias mais recentes do site do CECAFÉ e atualizar o cache.</p>
            
            <form method="post">
                <button type="submit" name="force_update" class="btn">Atualizar Notícias Agora</button>
            </form>
        </div>
        
        <?php if (isset($updateResult)): ?>
        <div class="card <?php echo $updateResult['updated'] ? 'success' : 'error'; ?>">
            <h3>Resultado da Atualização</h3>
            <p><?php echo $updateResult['message'] ?? ''; ?></p>
            <?php if (isset($updateResult['items'])): ?>
                <p>Notícias encontradas: <?php echo $updateResult['items']; ?></p>
            <?php endif; ?>
            <?php if (isset($updateResult['error'])): ?>
                <p>Erro: <?php echo $updateResult['error']; ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>