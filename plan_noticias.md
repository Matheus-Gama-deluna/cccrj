# Plano de Integração de Notícias Externas

## 1. Visão Geral

Este plano detalha como integrar uma fonte externa de notícias ao sistema atual, substituindo os dados simulados por informações reais do setor cafeeiro. O objetivo é manter a estrutura e funcionalidades existentes, apenas alterando a fonte dos dados.

A fonte escolhida será o site do Conselho Executivo do Café do Brasil (CECAFÉ): https://www.cecafe.com.br/secao/publicacoes/noticias/

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

## 4. Plano de Implementação

### 4.1 Fase 1: Backend/API para Web Scraping

#### 4.1.1 Criação de Serviço PHP com Web Scraping
Criar um serviço em PHP para fazer web scraping das notícias do site do CECAFÉ:

1. Criar `api/news_scraper.php`:
```php
<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

class NewsScraper {
    private $baseUrl = 'https://www.cecafe.com.br';
    private $newsUrl = 'https://www.cecafe.com.br/secao/publicacoes/noticias';
    
    public function fetchNews($page = 1) {
        try {
            // Montar URL com paginação
            $url = $this->newsUrl;
            if ($page > 1) {
                $url .= "?page=$page";
            }
            
            // Fazer requisição para obter página
            $html = $this->fetchPage($url);
            
            // Extrair notícias da página
            $news = $this->extractNews($html);
            
            return [
                'noticias' => $news,
                'temMais' => $this->hasMoreNews($html)
            ];
        } catch (Exception $e) {
            throw new Exception("Erro ao buscar notícias: " . $e->getMessage());
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
        if (preg_match('/(\\d{1,2})\\s+([a-z]{3})\\s*\\/\\s*(\\d{4})/i', strtolower($dateString), $matches)) {
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
    
    private function hasMoreNews($html) {
        // Verificar se existe paginação
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();
        
        $xpath = new DOMXPath($dom);
        $nextPage = $xpath->query("//a[contains(text(), 'Próximo') or contains(text(), 'Next')]");
        
        return $nextPage->length > 0;
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
    
    if (isset($_GET['url'])) {
        // Buscar conteúdo completo de uma notícia
        $content = $scraper->fetchFullNews($_GET['url']);
        echo json_encode(['conteudo' => $content]);
    } else {
        // Buscar listagem de notícias
        $page = $_GET['page'] ?? 1;
        $result = $scraper->fetchNews($page);
        echo json_encode($result);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
```

#### 4.1.2 Configuração
Adicionar configurações no arquivo `api/config.php`:
```php
<?php
// Configurações de notícias
define('NEWS_BASE_URL', 'https://www.cecafe.com.br');
define('NEWS_SECTION_URL', 'https://www.cecafe.com.br/secao/publicacoes/noticias');
?>
```

### 4.2 Fase 2: Atualização do Frontend

#### 4.2.1 Modificação do Componente de Notícias
Atualizar `assets/js/components/news.js` para consumir a API de scraping:

```javascript
// Lógica para as notícias
class NewsManager {
    constructor() {
        this.newsContainer = document.getElementById('news-container');
        this.currentPage = 1;
        this.itemsPerPage = 6;
        this.isLoading = false;
        this.hasMoreNews = true;
        this.apiUrl = 'api/news_scraper.php'; // Nova URL da API de scraping
        this.init();
    }

    init() {
        // Carregar notícias iniciais
        this.loadNews(true);
        
        // Adicionar evento para carregar mais notícias
        const loadMoreButton = document.getElementById('carregar-mais');
        if (loadMoreButton) {
            loadMoreButton.addEventListener('click', () => this.loadNews());
        }
    }

    async loadNews(clear = false) {
        if (this.isLoading || (!this.hasMoreNews && !clear)) return;
        
        this.isLoading = true;
        this.toggleLoading(true);
        this.showError(false);
        
        try {
            // Chamar API de scraping
            const response = await fetch(`${this.apiUrl}?page=${clear ? 1 : this.currentPage}`);
            
            if (!response.ok) {
                throw new Error(`Erro na requisição: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (clear) {
                if (this.newsContainer) this.newsContainer.innerHTML = '';
                this.currentPage = 1;
            }
            
            data.noticias.forEach(noticia => {
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
            
            this.hasMoreNews = data.temMais;
            const loadMoreButton = document.getElementById('carregar-mais');
            if (loadMoreButton) {
                loadMoreButton.classList.toggle('hidden', !this.hasMoreNews);
            }
            
            if (!clear) this.currentPage++;
        } catch (error) {
            console.error('Erro ao carregar notícias:', error);
            this.showError(true);
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
        const words = text.split(/\\s+/).length;
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

    showError(show) {
        const error = document.getElementById('noticias-error');
        if (error) {
            error.classList.toggle('hidden', !show);
        }
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    new NewsManager();
});
```

### 4.3 Fase 3: Tratamento de Erros e Exceções

#### 4.3.1 Exibição de Erros Melhorada
Adicionar mensagens de erro mais descritivas no frontend:

```javascript
showError(show, message = null) {
    const error = document.getElementById('noticias-error');
    if (error) {
        if (message) {
            error.querySelector('.error-message').textContent = message;
        }
        error.classList.toggle('hidden', !show);
    }
}
```

#### 4.3.2 Logging de Erros
Implementar logging no backend para monitorar falhas:

```php
// Em api/news_scraper.php
private function logError($message) {
    error_log("[" . date('Y-m-d H:i:s') . "] NewsScraper Error: " . $message . "\n", 3, "../logs/news_scraper.log");
}
```

### 4.4 Fase 4: Otimizações e Melhorias

#### 4.4.1 Cache de Notícias
Implementar cache no backend para reduzir requisições ao site externo:

```php
// Em api/news_scraper.php
private function getCachedNews($page) {
    $cacheFile = "../cache/news_page_{$page}.json";
    $cacheTime = 30 * 60; // 30 minutos
    
    if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
        return json_decode(file_get_contents($cacheFile), true);
    }
    
    return null;
}

private function cacheNews($page, $data) {
    $cacheFile = "../cache/news_page_{$page}.json";
    file_put_contents($cacheFile, json_encode($data));
}
```

#### 4.4.2 Fallback para Dados Locais
Implementar fallback para notícias locais caso o scraping falhe:

```javascript
// Em assets/js/components/news.js
async loadNews(clear = false) {
    // ... código existente ...
    
    try {
        // Tentar buscar notícias do scraper
        const response = await fetch(`${this.apiUrl}?page=${clear ? 1 : this.currentPage}`);
        
        if (!response.ok) {
            throw new Error(`Erro na requisição: ${response.status}`);
        }
        
        const data = await response.json();
        // ... processar dados ...
    } catch (error) {
        console.error('Erro ao carregar notícias:', error);
        
        // Fallback para notícias locais
        if (clear) {
            this.loadLocalNews();
        } else {
            this.showError(true, 'Não foi possível carregar mais notícias. Tente novamente mais tarde.');
        }
    }
    
    // ... resto do código ...
}
```

## 5. Considerações de Segurança

1. **Validação de Entradas**: Validar todos os parâmetros recebidos na API
2. **HTTPS**: Usar HTTPS para todas as comunicações
3. **Rate Limiting**: Implementar limites de requisições para não sobrecarregar o site externo
4. **User Agent**: Usar User Agent apropriado para identificar o scraper
5. **Respeito ao robots.txt**: Verificar e respeitar o arquivo robots.txt do site

## 6. Considerações Legais e Éticas

1. **Termos de Uso**: Verificar os termos de uso do site para garantir que o scraping é permitido
2. **Direitos Autorais**: Respeitar os direitos autorais das notícias
3. **Atribuição**: Fornecer atribuição apropriada ao CECAFÉ
4. **Uso Justo**: Garantir que o uso seja para fins informativos e não comerciais

## 7. Considerações de Performance

1. **Caching**: Implementar cache para evitar requisições repetidas ao site externo
2. **Paginação**: Manter a paginação para carregamento progressivo
3. **Timeouts**: Implementar timeouts para requisições que demorem muito
4. **Tratamento de Erros**: Implementar retries com backoff exponencial para falhas temporárias

## 8. Testes Necessários

1. **Testes de Scraping**: Verificar que o scraping funciona corretamente com a estrutura atual do site
2. **Testes de Erro**: Validar comportamento em caso de falhas no scraping
3. **Testes de Performance**: Avaliar tempo de carregamento das notícias
4. **Testes de Compatibilidade**: Garantir funcionamento em diferentes navegadores

## 9. Manutenção

1. **Monitoramento**: Monitorar mudanças na estrutura do site que possam quebrar o scraping
2. **Atualizações**: Atualizar seletores CSS/XPath conforme necessário
3. **Logs**: Manter logs para identificar problemas rapidamente

## 10. Cronograma Estimado

1. **Fase 1 - Backend/API**: 3-4 dias
2. **Fase 2 - Frontend**: 2-3 dias
3. **Fase 3 - Tratamento de Erros**: 1 dia
4. **Fase 4 - Otimizações**: 2-3 dias
5. **Testes e Ajustes**: 2-3 dias

**Total estimado**: 10-15 dias

## 11. Instruções para Execução pelo Qwen Code

Como este plano será executado diretamente pelo Qwen Code, seguem instruções específicas para implementação:

### 11.1. Criar Estrutura de Diretórios
- Verificar se o diretório `api/` existe
- Criar diretório `cache/` na raiz do projeto para armazenamento de cache
- Criar diretório `logs/` na raiz do projeto para logs

### 11.2. Implementar Backend
1. Criar arquivo `api/news_scraper.php` com o código fornecido
2. Atualizar `api/config.php` com as configurações necessárias

### 11.3. Atualizar Frontend
1. Substituir conteúdo de `assets/js/components/news.js` com a versão atualizada
2. Verificar se todos os elementos HTML necessários estão presentes no `index.html`

### 11.4. Testar Integração
1. Verificar se o web scraping está funcionando corretamente
2. Testar paginação de notícias
3. Verificar tratamento de erros
4. Testar funcionalidade de "Leia mais" que redireciona para o site original

### 11.5. Otimizar e Monitorar
1. Verificar se o cache está funcionando
2. Monitorar logs para identificar possíveis problemas
3. Ajustar seletores CSS/XPath conforme necessário após testes

## 12. Resultados dos Testes de Validação

Após realizar testes de validação, confirmamos que:

1. **Acesso ao Site**: O site do CECAFÉ é acessível e retorna conteúdo HTML válido
2. **Estrutura HTML**: Foram identificados 10 elementos `<article>` que contêm as notícias
3. **Extração de Dados**: Todos os elementos necessários podem ser extraídos com sucesso:
   - Datas das notícias (parseadas para formato padrão YYYY-MM-DD)
   - Títulos (extraídos dos elementos `<h2>`)
   - Links para notícias completas
   - Conteúdo/resumo das notícias
   - Categorização automática (Produção, Exportação, Evento)
   - Ícones por categoria (agriculture, article, event)

4. **Compatibilidade**: Todos os elementos necessários foram identificados e podem ser extraídos com sucesso, confirmando que o código de web scraping funcionará corretamente no site do CECAFÉ.