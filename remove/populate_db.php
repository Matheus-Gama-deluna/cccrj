<?php
// populate_db.php

// Incluir configuração do banco de dados
require_once 'api/config/database.php';

try {
    $pdo = connectDatabase();
    
    // Popular tabela de clippings com dados de exemplo
    $clippings = [
        [
            'title' => 'Novo acordo comercial fortalece o setor cafeeiro',
            'summary' => 'Acordo entre Brasil e União Europeia abre novas oportunidades para exportações',
            'content' => 'O novo acordo comercial entre o Brasil e a União Europeia representa um marco importante para o setor cafeeiro brasileiro. Com a redução de tarifas e a eliminação de barreiras comerciais, as exportações de café brasileiro para o mercado europeu devem aumentar significativamente nos próximos anos.',
            'source_url' => 'https://exemplo.com/noticia1',
            'date' => '2023-05-15',
            'category' => 'Notícia',
            'is_active' => 1
        ],
        [
            'title' => 'Tecnologia revoluciona a colheita de café',
            'summary' => 'Novas máquinas colheitadeiras aumentam a eficiência da produção cafeeira',
            'content' => 'A introdução de novas tecnologias no processo de colheita do café está transformando a forma como os produtores trabalham. Máquinas colheitadeiras automatizadas estão aumentando a eficiência e reduzindo custos de produção, beneficiando todo o setor cafeeiro.',
            'source_url' => 'https://exemplo.com/noticia2',
            'date' => '2023-06-20',
            'category' => 'Notícia',
            'is_active' => 1
        ]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO clippings (title, summary, content, source_url, date, category, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($clippings as $clipping) {
        $stmt->execute([
            $clipping['title'],
            $clipping['summary'],
            $clipping['content'],
            $clipping['source_url'],
            $clipping['date'],
            $clipping['category'],
            $clipping['is_active']
        ]);
    }
    
    // Popular tabela de publicações com dados de exemplo
    $publications = [
        [
            'title' => 'Relatório Anual de Produção - 2023',
            'description' => 'Análise completa da produção cafeeira no Rio de Janeiro no ano de 2023',
            'file_path' => 'relatorios/relatorio_2023.pdf',
            'date' => '2023-12-31',
            'number' => '001/2023',
            'type' => 'relatorio',
            'is_active' => 1
        ],
        [
            'title' => 'Revista do Café - Edição Especial',
            'description' => 'Edição especial comemorando o centenário do CCCRJ',
            'file_path' => 'revista/edicao_especial_centenario.pdf',
            'date' => '2023-12-19',
            'number' => '100 anos',
            'type' => 'revista',
            'is_active' => 1
        ]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO publications (title, description, file_path, date, number, type, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($publications as $publication) {
        $stmt->execute([
            $publication['title'],
            $publication['description'],
            $publication['file_path'],
            $publication['date'],
            $publication['number'],
            $publication['type'],
            $publication['is_active']
        ]);
    }
    
    // Popular tabela de eventos históricos com dados de exemplo
    $historicalEvents = [
        [
            'title' => 'Fundação do CCCRJ',
            'description' => 'O Centro do Comércio do Café do Rio de Janeiro é fundado',
            'content' => 'Em 19 de dezembro de 1901, o Centro do Comércio do Café do Rio de Janeiro é oficialmente fundado com o objetivo de defender os direitos e interesses do comércio de café.',
            'date' => '1901-12-19',
            'event_type' => 'fundação',
            'image_url' => 'assets/images/fundacao.jpg',
            'is_featured' => 1,
            'is_active' => 1
        ],
        [
            'title' => 'Centenário do CCCRJ',
            'description' => 'Comemoração dos 100 anos de existência da instituição',
            'content' => 'Em 2001, o CCCRJ comemorou seu centenário com uma grande festa e a publicação de uma obra especial sobre sua história.',
            'date' => '2001-12-19',
            'event_type' => 'comemoração',
            'image_url' => 'assets/images/centenario.jpg',
            'is_featured' => 1,
            'is_active' => 1
        ]
    ];
    
    $stmt = $pdo->prepare("INSERT INTO historical_events (title, description, content, date, event_type, image_url, is_featured, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($historicalEvents as $event) {
        $stmt->execute([
            $event['title'],
            $event['description'],
            $event['content'],
            $event['date'],
            $event['event_type'],
            $event['image_url'],
            $event['is_featured'],
            $event['is_active']
        ]);
    }
    
    echo "Banco de dados populado com dados de exemplo com sucesso!";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>