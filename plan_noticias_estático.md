# Plano de Integração de Notícias Externas - Versão Estática com Atualização via Admin (Implementação com Agente Qwen Code)

## 1. Visão Geral

Este plano detalha como integrar uma fonte externa de notícias ao sistema atual, substituindo os dados simulados por informações reais do setor cafeeiro. O objetivo é manter a estrutura e funcionalidades existentes, apenas alterando a fonte dos dados e adotando uma abordagem estática para melhor desempenho e simplicidade.

A fonte escolhida será o site do Conselho Executivo do Café do Brasil (CECAFÉ): https://www.cecafe.com.br/secao/publicacoes/noticias/

O plano será implementado utilizando o agente do Qwen Code, seguindo o protocolo PRAR (Perceber, Raciocinar, Agir, Refinar).

## 2. Análise da Fonte de Notícias

### 2.1 Estrutura do Site

Após análise do site, identificamos:

1. **Listagem de Notícias**: Notícias são exibidas em formato de cards contendo:
   - Data de publicação
   - Título da notícia
   - Nome do autor/redator
   - Resumo do conteúdo
   - Categorias

2. **Acesso ao Conteúdo Completo**: Cada notícia na listagem é um link para a página individual com conteúdo completo.

3. **Estrutura das URLs**:
   - Página principal: `https://www.cecafe.com.br/secao/publicacoes/noticias/`
   - Notícias individuais: `https://www.cecafe.com.br/secao/publicacoes/noticias/[titulo-formatado]`
   - Paginação: Numérica na parte inferior

## 3. Arquitetura Atual

### 3.1 Componentes Existentes
- `assets/js/components/news.js`: Classe NewsManager responsável pela exibição de notícias
- `index.html`: Seção de notícias com container `#news-container`
- Sistema de paginação com botão `#carregar-mais`
- Indicadores de carregamento e erro

### 3.2 Funcionalidades Atuais
- Exibição de cards de notícias com gradientes por categoria
- Paginação com carregamento progressivo
- Animações de entrada
- Formatação de datas e cálculo de tempo de leitura

## 4. Plano de Implementação - Abordagem Estática com Agente Qwen Code

### 4.1 Fase 1: Backend/API com Scraping e Armazenamento Estático

#### 4.1.1 Criação de Serviço PHP com Web Scraping e Geração de JSON Estático
O agente do Qwen Code irá criar um serviço em PHP para fazer web scraping das notícias do site do CECAFÉ e armazená-las em JSON estático:

1. Criar `api/news_scraper.php`:

O agente Qwen Code executará a seguinte implementação:
```php
<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

class NewsScraper {
    private $baseUrl = 'https://www.cecafe.com.br';
    private $newsUrl = 'https://www.cecafe.com.br/secao/publicacoes/noticias';
    private $cacheDir = '../cache/';
    private $dataDir = '../data/';
    
    public function __construct() {
        // Criar diretórios se não existirem
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
        if (!is_dir($this->dataDir)) {
            mkdir($this->dataDir, 0755, true);
        }
    }
    
    // Função para forçar atualização manual - irá buscar notícias e salvar em JSON estático
    public function forceUpdate() {
        try {
            // Buscar notícias atuais
            $html = $this->fetchPage($this->newsUrl);
            $newNews = $this->extractNews($html);
            
            // Salvar todas as notícias como arquivo JSON estático
            $jsonFile = $this->dataDir . "news.json";
            $result = [
                'noticias' => $newNews,
                'ultima_atualizacao' => date('Y-m-d H:i:s')
            ];
            
            file_put_contents($jsonFile, json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            // Registrar data de atualização
            $lastUpdateFile = $this->cacheDir . 'last_update.txt';
            file_put_contents($lastUpdateFile, date('Y-m-d'));
            
            return [
                'updated' => true,
                'items' => count($newNews),
                'message' => 'Notícias atualizadas manualmente e salvas em JSON estático',
                'json_file' => $jsonFile
            ];
        } catch (Exception $e) {
            return ['updated' => false, 'error' => $e->getMessage()];
        }
    }
    
    private function fetchPage($url) {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
                ]
            ]
        ]);
        
        $content = file_get_contents($url, false, $context);
        if ($content === FALSE) {
            throw new Exception("Falha ao carregar página: $url");
        }
        
        return $content;
    }
    
    private function extractNews($html) {
        $news = [];
        
        // Usar DOMDocument para parsing HTML
        $dom = new DOMDocument();
        libxml_use_internal_errors(true); // Suprimir erros de parsing
        $dom->loadHTML($html);
        libxml_clear_errors();
        
        // Localizar elementos das notícias
        // Com base nos testes, as notícias estão em elementos <article>
        $xpath = new DOMXPath($dom);
        $newsItems = $xpath->query("//article");
        
        foreach ($newsItems as $item) {
            try {
                $newsItem = $this->parseNewsItem($item, $xpath);
                if ($newsItem) {
                    $news[] = $newsItem;
                }
            } catch (Exception $e) {
                // Ignorar itens que não puderam ser parseados
                continue;
            }
        }
        
        return $news;
    }
    
    private function parseNewsItem($item, $xpath) {
        // Extrair dados da notícia
        // Com base nos testes, identificamos os seletores corretos
        
        // Título
        $titleNode = $xpath->query(".//h2[contains(@class, 'title') or contains(@class, 'titulo')] | .//h2", $item)->item(0);
        if (!$titleNode) return null;
        
        $title = trim($titleNode->textContent);
        
        // Link
        $linkNode = $xpath->query(".//h2/a | .//h3/a | .//a", $item)->item(0);
        $link = $linkNode ? $this->baseUrl . $linkNode->getAttribute('href') : '';
        
        // Data
        $dateNode = $xpath->query(".//time | .//*[@class='date' or @class='data'] | .//*[contains(text(), '/')]", $item)->item(0);
        $date = $dateNode ? $this->parseDate($dateNode->textContent) : date('Y-m-d');
        
        // Resumo
        $contentNodes = $xpath->query(".//div[contains(@class, 'content')] | .//div[contains(@class, 'excerpt')] | .//div[contains(@class, 'resumo')] | .//p", $item);
        $summary = '';
        if ($contentNodes->length > 0) {
            $summary = $this->createSummary($contentNodes->item(0)->textContent);
        }
        
        // Categoria (tentar determinar a partir do conteúdo)
        $category = $this->categorizeNews($title, $summary);
        
        return [
            'id' => crc32($link), // Gerar ID baseado no link
            'categoria' => $category,
            'data' => $date,
            'titulo' => $title,
            'resumo' => $summary,
            'conteudo' => '', // Será preenchido quando buscar notícia individual
            'icone' => $this->getIconByCategory($category),
            'link' => $link
        ];
    }
    
    private function parseDate($dateString) {
        // Converter datas em formatos como "9 set / 2025" para "YYYY-MM-DD"
        $months = [
            'jan' => '01', 'fev' => '02', 'mar' => '03', 'abr' => '04',
            'mai' => '05', 'jun' => '06', 'jul' => '07', 'ago' => '08',
            'set' => '09', 'out' => '10', 'nov' => '11', 'dez' => '12'
        ];
        
        // Padrão: "9 set / 2025"
        if (preg_match('/(\d{1,2})\s+([a-z]{3})\s*\/\s*(\d{4})/i', strtolower($dateString), $matches)) {
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $month = $months[$matches[2]] ?? '01';
            $year = $matches[3];
            return "$year-$month-$day";
        }
        
        // Se não conseguir parsear, retornar data atual
        return date('Y-m-d');
    }
    
    private function categorizeNews($title, $summary) {
        $text = strtolower($title . ' ' . $summary);
        
        if (strpos($text, 'produção') !== false || strpos($text, 'colheit') !== false) {
            return 'Produção';
        }
        
        if (strpos($text, 'exportação') !== false || strpos($text, 'export') !== false) {
            return 'Exportação';
        }
        
        if (strpos($text, 'evento') !== false || strpos($text, 'feira') !== false ||
            strpos($text, 'congresso') !== false || strpos($text, 'seminário') !== false) {
            return 'Evento';
        }
        
        return 'Produção'; // Categoria padrão
    }
    
    private function createSummary($content) {
        // Limitar a 100 caracteres e remover tags HTML
        $cleanContent = strip_tags($content);
        if (strlen($cleanContent) > 100) {
            return substr($cleanContent, 0, 97) . '...';
        }
        return $cleanContent;
    }
    
    private function getIconByCategory($category) {
        $icons = [
            'Produção' => 'agriculture',
            'Exportação' => 'article',
            'Evento' => 'event'
        ];
        
        return $icons[$category] ?? 'article';
    }
    
    // Função para obter notícias do arquivo JSON estático
    public function getNewsFromStaticFile() {
        $jsonFile = $this->dataDir . "news.json";
        if (file_exists($jsonFile)) {
            $jsonContent = file_get_contents($jsonFile);
            return json_decode($jsonContent, true);
        }
        return null;
    }
    
    public function fetchFullNews($url) {
        try {
            $html = $this->fetchPage($url);
            
            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
            libxml_clear_errors();
            
            $xpath = new DOMXPath($dom);
            
            // Extrair conteúdo completo da notícia
            // Nota: O seletor precisa ser ajustado com base na estrutura real do site
            $contentNodes = $xpath->query("//div[contains(@class, 'content') or contains(@class, 'noticia-conteudo')]");
            
            if ($contentNodes->length > 0) {
                $content = '';
                foreach ($contentNodes as $node) {
                    $content .= $dom->saveHTML($node);
                }
                return $content;
            }
            
            return 'Conteúdo não disponível';
        } catch (Exception $e) {
            return 'Erro ao carregar conteúdo: ' . $e->getMessage();
        }
    }
}

// Processar requisição
try {
    $scraper = new NewsScraper();
    
    // Verificar parâmetros de requisição
    if (isset($_GET['url'])) {
        // Buscar conteúdo completo de uma notícia
        $content = $scraper->fetchFullNews($_GET['url']);
        echo json_encode(['conteudo' => $content]);
    } else if (isset($_GET['force_update'])) {
        // Forçar atualização manual - busca e salva em JSON estático
        $result = $scraper->forceUpdate();
        echo json_encode($result);
    } else if (isset($_GET['get_static_news'])) {
        // Obter notícias do arquivo JSON estático
        $result = $scraper->getNewsFromStaticFile();
        if ($result) {
            echo json_encode($result);
        } else {
            echo json_encode(['noticias' => [], 'ultima_atualizacao' => null]);
        }
    } else {
        // Por padrão, obter notícias do arquivo estático
        $result = $scraper->getNewsFromStaticFile();
        if ($result) {
            echo json_encode($result);
        } else {
            // Se não houver arquivo, tentar atualizar
            $updateResult = $scraper->forceUpdate();
            if ($updateResult['updated']) {
                $result = $scraper->getNewsFromStaticFile();
                echo json_encode($result);
            } else {
                echo json_encode(['noticias' => [], 'ultima_atualizacao' => null, 'error' => $updateResult['error']]);
            }
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
```

#### 4.1.2 Configuração de diretórios
O agente Qwen Code verificará e criará, se necessário, os diretórios:
- `data/` para armazenamento do JSON estático das notícias
- `cache/` para armazenamento temporário e logs

### 4.2 Fase 2: Integração com Página de Administração usando Agente Qwen Code

#### 4.2.1 Atualização do Painel de Administração
O agente Qwen Code irá adicionar funcionalidade de atualização de notícias no painel administrativo:

1. O agente atualizará `admin.html` para incluir uma seção de atualização de notícias:

```html
<!-- Adicionar na seção de relatórios ou como nova seção -->
<section class="py-12 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-[#6B4423] mb-4">Atualização de Notícias</h2>
            <p class="text-lg text-[#8B2635]">Buscar últimas notícias do CECAFÉ</p>
        </div>
        
        <div class="max-w-2xl mx-auto bg-gradient-to-r from-emerald-50 to-teal-50 p-8 rounded-2xl shadow-lg">
            <div class="text-center mb-6">
                <span class="material-icons text-5xl text-[#8B2635]">rss_feed</span>
                <h3 class="text-xl font-bold text-gray-800 mt-4">Atualizar Notícias</h3>
                <p class="text-gray-600 mt-2">Buscar notícias mais recentes do site do CECAFÉ</p>
            </div>
            
            <div class="mt-6">
                <button id="updateNewsBtn" class="bg-[#8B2635] text-white font-semibold py-3 px-8 rounded-lg hover:bg-[#992D3D] transition-colors duration-300 w-full">
                    Atualizar Notícias Agora
                </button>
            </div>
            
            <div id="updateNewsStatus" class="mt-4 hidden p-4 rounded-lg"></div>
        </div>
    </div>
</section>
```

2. O agente criará ou atualizará `assets/js/admin.js` para incluir a funcionalidade:

```javascript
// Função para atualizar notícias
async function updateNews() {
    const updateBtn = document.getElementById('updateNewsBtn');
    const statusDiv = document.getElementById('updateNewsStatus');
    
    if (!updateBtn || !statusDiv) return;
    
    updateBtn.disabled = true;
    updateBtn.textContent = 'Atualizando...';
    statusDiv.innerHTML = '<div class="text-center"><div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-[#8B2635]"></div><p class="mt-2 text-[#6B4423]">Buscando notícias...</p></div>';
    statusDiv.classList.remove('hidden');
    
    try {
        const response = await fetch('api/news_scraper.php?force_update=1', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        });
        
        if (!response.ok) {
            throw new Error(`Erro na requisição: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.updated) {
            statusDiv.innerHTML = `
                <div class="text-center">
                    <span class="material-icons text-5xl text-green-500">check_circle</span>
                    <p class="mt-2 text-[#6B4423] font-medium">Notícias atualizadas com sucesso!</p>
                    <p class="mt-1 text-gray-600">${result.items} notícias encontradas</p>
                </div>
            `;
            statusDiv.classList.remove('bg-red-100', 'text-red-800');
            statusDiv.classList.add('bg-green-100', 'text-green-800');
        } else {
            statusDiv.innerHTML = `
                <div class="text-center">
                    <span class="material-icons text-5xl text-red-500">error</span>
                    <p class="mt-2 text-[#6B4423] font-medium">Erro ao atualizar notícias</p>
                    <p class="mt-1 text-gray-600">${result.error || 'Erro desconhecido'}</p>
                </div>
            `;
            statusDiv.classList.remove('bg-green-100', 'text-green-800');
            statusDiv.classList.add('bg-red-100', 'text-red-800');
        }
    } catch (error) {
        console.error('Erro ao atualizar notícias:', error);
        statusDiv.innerHTML = `
            <div class="text-center">
                <span class="material-icons text-5xl text-red-500">error</span>
                <p class="mt-2 text-[#6B4423] font-medium">Erro ao atualizar notícias</p>
                <p class="mt-1 text-gray-600">Verifique o console para mais detalhes</p>
            </div>
        `;
        statusDiv.classList.remove('bg-green-100', 'text-green-800');
        statusDiv.classList.add('bg-red-100', 'text-red-800');
    } finally {
        updateBtn.disabled = false;
        updateBtn.textContent = 'Atualizar Notícias Agora';
    }
}

// Adicionar evento ao botão de atualização de notícias
document.addEventListener('DOMContentLoaded', function() {
    const updateNewsBtn = document.getElementById('updateNewsBtn');
    if (updateNewsBtn) {
        updateNewsBtn.addEventListener('click', updateNews);
    }
});
```

### 4.3 Fase 3: Atualização do Frontend para Consumo de Dados Estáticos

#### 4.3.1 Modificação do Componente de Notícias
O agente Qwen Code atualizará `assets/js/components/news.js` para consumir o JSON estático:

```javascript
// Lógica para as notícias
class NewsManager {
    constructor() {
        this.newsContainer = document.getElementById('news-container');
        this.currentPage = 1;
        this.itemsPerPage = 6;
        this.isLoading = false;
        this.hasMoreNews = true;
        this.newsData = [];
        this.apiUrl = 'api/news_scraper.php?get_static_news=1'; // Nova URL para obter notícias estáticas
        this.init();
    }

    async init() {
        await this.loadNewsData();
        this.loadNews(true);
        
        // Adicionar evento para carregar mais notícias
        const loadMoreButton = document.getElementById('carregar-mais');
        if (loadMoreButton) {
            loadMoreButton.addEventListener('click', () => this.loadNews());
        }
    }

    async loadNewsData() {
        try {
            // Mostrar indicador de carregamento
            this.toggleLoading(true);
            
            // Chamar API para obter notícias estáticas
            const response = await fetch(this.apiUrl);
            
            if (!response.ok) {
                throw new Error(`Erro na requisição: ${response.status}`);
            }
            
            const data = await response.json();
            this.newsData = data.noticias || [];
            
            // Atualizar informações de última atualização (opcional)
            this.updateLastUpdateInfo(data.ultima_atualizacao);
        } catch (error) {
            console.error('Erro ao carregar dados das notícias:', error);
            this.showError(true, 'Não foi possível carregar as notícias. Tente novamente mais tarde.');
        } finally {
            this.toggleLoading(false);
        }
    }

    updateLastUpdateInfo(date) {
        const lastUpdateElement = document.getElementById('last-news-update');
        if (lastUpdateElement && date) {
            lastUpdateElement.textContent = `Última atualização: ${new Date(date).toLocaleString('pt-BR')}`;
        }
    }

    loadNews(clear = false) {
        if (this.isLoading) return;
        
        this.isLoading = true;
        this.toggleLoading(true);
        this.showError(false);
        
        try {
            if (clear) {
                if (this.newsContainer) this.newsContainer.innerHTML = '';
                this.currentPage = 1;
            }
            
            const startIndex = (this.currentPage - 1) * this.itemsPerPage;
            const endIndex = startIndex + this.itemsPerPage;
            const newsToShow = this.newsData.slice(startIndex, endIndex);
            
            this.hasMoreNews = endIndex < this.newsData.length;
            
            newsToShow.forEach(noticia => {
                const card = this.createNewsCard(noticia);
                if (this.newsContainer) this.newsContainer.appendChild(card);
                
                // Adicionar evento de clique para ler mais
                const readMoreButton = card.querySelector('.read-more-button');
                if (readMoreButton) {
                    readMoreButton.addEventListener('click', () => this.showFullNews(noticia.link));
                }
                
                // Animar entrada do card
                setTimeout(() => {
                    card.querySelector('article').classList.remove('opacity-0', 'translate-y-4');
                }, 100);
            });
            
            const loadMoreButton = document.getElementById('carregar-mais');
            if (loadMoreButton) {
                loadMoreButton.classList.toggle('hidden', !this.hasMoreNews);
            }
            
            this.currentPage++;
        } catch (error) {
            console.error('Erro ao carregar notícias:', error);
            this.showError(true, 'Ocorreu um erro ao carregar as notícias.');
        } finally {
            this.toggleLoading(false);
            this.isLoading = false;
        }
    }

    createNewsCard(noticia) {
        // Criar elemento para o card da notícia
        const article = document.createElement('article');
        article.className = 'bg-white rounded-2xl shadow-lg overflow-hidden card-hover opacity-0 translate-y-4 transition-all duration-500';
        
        // Gradientes por categoria
        const gradientes = {
            'Produção': 'from-[#8B2635] to-[#992D3D]',
            'Exportação': 'from-blue-400 to-indigo-500',
            'Evento': 'from-[#4A6B8A] to-[#8B2635]'
        };
        
        const gradientClass = gradientes[noticia.categoria] || 'from-[#8B2635] to-[#992D3D]';
        
        article.innerHTML = `
            <div class="h-48 bg-gradient-to-br ${gradientClass} relative">
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="material-icons text-white text-6xl">${noticia.icone || 'article'}</span>
                </div>
                <div class="absolute top-4 left-4 bg-white px-3 py-1 rounded-full text-sm font-medium text-[#8B2635]">
                    ${noticia.categoria}
                </div>
            </div>
            <div class="p-6">
                <div class="text-sm text-[#6B4423] mb-2">${this.formatDate(noticia.data)} • ${this.calculateReadingTime(noticia.resumo)}</div>
                <h3 class="text-xl font-bold text-[#6B4423] mb-3">${noticia.titulo}</h3>
                <p class="text-[#8B2635] mb-4">${noticia.resumo}</p>
                <button class="read-more-button text-[#8B2635] hover:text-[#992D3D] font-medium flex items-center group">
                    Leia mais 
                    <span class="material-icons ml-1 group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </button>
            </div>
        `;
        
        return article;
    }

    async showFullNews(newsUrl) {
        try {
            // Mostrar modal ou nova página com conteúdo completo
            // Esta função pode ser expandida para mostrar o conteúdo em um modal
            window.open(newsUrl, '_blank');
        } catch (error) {
            console.error('Erro ao carregar notícia completa:', error);
            alert('Não foi possível carregar a notícia completa. Você será redirecionado para o site original.');
            window.open(newsUrl, '_blank');
        }
    }

    formatDate(dateString) {
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        const date = new Date(dateString);
        // Formatar mês em português
        const months = {
            'January': 'janeiro', 'February': 'fevereiro', 'March': 'março',
            'April': 'abril', 'May': 'maio', 'June': 'junho',
            'July': 'julho', 'August': 'agosto', 'September': 'setembro',
            'October': 'outubro', 'November': 'novembro', 'December': 'dezembro'
        };
        
        const month = months[date.toLocaleString('en-US', { month: 'long' })] || date.toLocaleString('pt-BR', { month: 'long' });
        return `${date.getDate()} de ${month} de ${date.getFullYear()}`;
    }

    calculateReadingTime(text) {
        const wordsPerMinute = 200;
        const words = text.split(/\s+/).length;
        const minutes = Math.ceil(words / wordsPerMinute);
        return `${minutes} min de leitura`;
    }

    toggleLoading(show) {
        const loading = document.getElementById('noticias-loading');
        if (loading) {
            loading.classList.toggle('hidden', !show);
        }
        if (this.newsContainer) {
            this.newsContainer.classList.toggle('opacity-50', show);
        }
    }

    showError(show, message = null) {
        const error = document.getElementById('noticias-error');
        if (error) {
            if (message) {
                const messageElement = error.querySelector('.error-message');
                if (messageElement) {
                    messageElement.textContent = message;
                }
            }
            error.classList.toggle('hidden', !show);
        }
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    new NewsManager();
});
```

#### 4.3.2 Atualizar HTML para mostrar data de atualização
O agente Qwen Code adicionará um elemento para mostrar a data da última atualização das notícias no `index.html`:

```html
<!-- Na seção de notícias -->
<section class="py-20 bg-[#F5F0E8]">
    <div class="container mx-auto px-6">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-[#6B4423] mb-4">Notícias do Setor</h2>
            <p class="text-xl text-[#8B2635]">Últimas notícias do mercado cafeeiro</p>
            <p id="last-news-update" class="text-sm text-[#6B4423] mt-2"></p>
        </div>
        
        <!-- Container das notícias -->
        <div id="noticias-loading" class="text-center py-12 hidden">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-[#8B2635]"></div>
            <p class="mt-4 text-[#6B4423]">Carregando notícias...</p>
        </div>
        
        <div id="noticias-error" class="text-center py-12 hidden">
            <span class="material-icons text-5xl text-[#8B2635] mb-4">error</span>
            <p class="text-[#6B4423] text-xl error-message">Não foi possível carregar as notícias. Tente novamente mais tarde.</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="news-container">
            <!-- Notícias serão carregadas aqui dinamicamente -->
        </div>
        
        <div class="text-center mt-12">
            <button id="carregar-mais" class="bg-[#8B2635] text-white font-semibold py-3 px-8 rounded-lg hover:bg-[#992D3D] transition-colors duration-300">
                Carregar Mais Notícias
            </button>
        </div>
    </div>
</section>
```

### 4.4 Fase 4: Tratamento de Erros e Exceções

#### 4.4.1 Exibição de Erros Melhorada
O componente de notícias já inclui tratamento de erros conforme mostrado acima.

#### 4.4.2 Logging de Erros
O agente Qwen Code implementará logging no backend para monitorar falhas:

```php
// Em api/news_scraper.php
private function logError($message) {
    $logFile = $this->cacheDir . "../logs/news_scraper.log";
    error_log("[" . date('Y-m-d H:i:s') . "] NewsScraper Error: " . $message . "\n", 3, $logFile);
}
```

## 5. Considerações de Segurança

1. **Validação de Entradas**: Validar todos os parâmetros recebidos na API
2. **HTTPS**: Usar HTTPS para todas as comunicações
3. **Rate Limiting**: Implementar limites de requisições para não sobrecarregar o site externo
4. **User Agent**: Usar User Agent apropriado para identificar o scraper
5. **Respeito ao robots.txt**: Verificar e respeitar o arquivo robots.txt do site
6. **Autenticação de Admin**: Proteger a funcionalidade de atualização com autenticação

## 6. Considerações Legais e Éticas

1. **Termos de Uso**: Verificar os termos de uso do site CECAFÉ para garantir que o scraping é permitido
2. **Direitos Autorais**: Respeitar os direitos autorais das notícias
3. **Atribuição**: Fornecer atribuição apropriada ao CECAFÉ
4. **Uso Justo**: Garantir que o uso seja para fins informativos e não comerciais
5. **Frequência de Atualização**: Não atualizar com muita frequência para não sobrecarregar o servidor

## 7. Considerações de Performance

1. **JSON Estático**: Acesso mais rápido e eficiente que scraping em tempo real
2. **Sem Parsing em Tempo Real**: Não há parsing de HTML em cada requisição
3. **Cache de Longo Prazo**: Dados são armazenados localmente e só atualizados via admin
4. **Leveza**: Menor carga no servidor e melhor experiência do usuário

## 8. Implementação com Protocolo PRAR usando Agente Qwen Code

O agente Qwen Code seguirá o protocolo PRAR (Perceber, Raciocinar, Agir, Refinar) para implementar esta funcionalidade:

### 8.1 Perceber (Perceive)
O agente Qwen Code irá:
1. Analisar os arquivos existentes no projeto CCCRJ
2. Identificar os componentes atuais que lidam com notícias
3. Mapear a estrutura do diretório e arquivos relevantes
4. Avaliar o estado atual da seção de notícias

### 8.2 Raciocinar (Reason)
O agente Qwen Code irá:
1. Planejar as alterações necessárias para implementar a abordagem estática
2. Decidir onde cada componente deve ser modificado ou criado
3. Avaliar o impacto das alterações em outros componentes
4. Considerar as melhores práticas de desenvolvimento

### 8.3 Agir (Act)
O agente Qwen Code irá:
1. Criar os arquivos PHP necessários para scraping e armazenamento estático
2. Atualizar os arquivos JavaScript para consumir o JSON estático
3. Modificar os arquivos HTML para incluir os elementos necessários
4. Testar cada etapa da implementação para garantir funcionamento correto

### 8.4 Refinar (Refine)
O agente Qwen Code irá:
1. Verificar se todas as funcionalidades estão funcionando corretamente
2. Otimizar o código para melhor desempenho e legibilidade
3. Verificar tratamento de erros e casos extremos
4. Documentar quaisquer ajustes necessários após testes

## 9. Testes Necessários

1. **Testes de Scraping**: Verificar que o scraping funciona corretamente com a estrutura atual do site
2. **Testes de Erro**: Validar comportamento em caso de falhas no scraping
3. **Testes de Atualização**: Verificar que a atualização via admin funciona corretamente
4. **Testes de Performance**: Avaliar tempo de carregamento das notícias do JSON estático
5. **Testes de Compatibilidade**: Garantir funcionamento em diferentes navegadores

## 10. Manutenção

1. **Monitoramento**: Monitorar mudanças na estrutura do site que possam quebrar o scraping
2. **Atualizações**: O administrador atualiza as notícias conforme necessário via interface
3. **Logs**: Manter logs para identificar problemas rapidamente

## 11. Instruções para Execução com Agente Qwen Code

### 11.1. Estrutura de Diretórios
O agente Qwen Code criará automaticamente:
- `data/` para armazenamento do JSON estático das notícias
- `cache/` para armazenamento temporário e logs

### 11.2. Implementar Backend
1. O agente criará o arquivo `api/news_scraper.php` com o código fornecido
2. O agente garantirá que o diretório `data/` tenha permissões de escrita

### 11.3. Atualizar Frontend
1. O agente substituirá o conteúdo de `assets/js/components/news.js` com a versão atualizada
2. O agente atualizará `index.html` para incluir o elemento de data de atualização
3. O agente atualizará `admin.html` e `assets/js/admin.js` para incluir a funcionalidade de atualização

### 11.4. Testar Integração
1. O agente verificará se o scraping inicial funciona e gera o JSON estático
2. O agente testará a funcionalidade de atualização via admin
3. O agente verificará o carregamento das notícias estáticas na página principal
4. O agente testará o tratamento de erros

### 11.5. Otimizar e Monitorar
1. O agente verificará se o JSON estático está sendo carregado corretamente
2. O agente implementará mecanismos de log para identificar problemas
3. O agente ajustará seletores CSS/XPath conforme necessário após testes

## 12. Benefícios da Abordagem Estática

1. **Performance Superior**: Acesso instantâneo às notícias sem scraping em tempo real
2. **Maior Confiabilidade**: Não depende da disponibilidade do site externo em tempo real
3. **Controle Manual**: Atualizações são feitas manualmente via painel de administração
4. **Mais Seguro**: Menor risco de problemas legais ou bloqueio por scraping
5. **Hospedagem Simples**: Pode funcionar com hospedagem estática após a geração inicial
6. **Interface Administrativa**: Permite ao administrador atualizar conforme necessário

## 13. Fluxo de Atualização

1. Administrador acessa o painel admin.html
2. Clica no botão "Atualizar Notícias Agora"
3. O sistema faz scraping do site do CECAFÉ
4. As notícias são salvas em formato JSON estático em `data/news.json`
5. Na próxima visita à página principal, as notícias atualizadas são carregadas
6. A data da última atualização é exibida aos usuários

Essa abordagem combina a coleta automática de dados com a eficiência de uma solução estática, adicionando uma camada de controle via painel administrativo para manter as notícias atualizadas de forma manual quando necessário. A implementação será realizada com o agente Qwen Code, seguindo rigorosamente o protocolo PRAR para garantir qualidade e consistência no desenvolvimento.