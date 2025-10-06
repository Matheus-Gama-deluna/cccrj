import os
import time
import requests
from urllib.parse import urljoin, urlparse
from bs4 import BeautifulSoup
import urllib.robotparser

class CCCRJScraper:
    def __init__(self, base_url="http://www.cccrj.com.br/", delay=1):
        self.base_url = base_url
        self.session = requests.Session()
        self.session.headers.update({
            'User-Agent': 'Mozilla/5.0 (compatible; CCCRJ-Scraper/1.0; +http://www.cccrj.com.br/)'
        })
        self.delay = delay  # Delay between requests in seconds
        self.visited_urls = set()
        self.to_visit = set()
        self.output_dir = "cccrj_content"
        
        # Create output directory
        os.makedirs(self.output_dir, exist_ok=True)

    def check_robots_txt(self, url):
        """Check if scraping is allowed according to robots.txt"""
        try:
            rp = urllib.robotparser.RobotFileParser()
            rp.set_url(urljoin(url, '/robots.txt'))
            rp.read()
            return rp.can_fetch('*', url)
        except:
            # If robots.txt doesn't exist or can't be parsed, assume scraping is allowed
            return True

    def is_valid_url(self, url):
        """Check if the URL is valid and belongs to the target domain"""
        parsed = urlparse(url)
        base_parsed = urlparse(self.base_url)
        return parsed.netloc == base_parsed.netloc

    def get_page(self, url):
        """Fetch a page and return its content"""
        try:
            if not self.check_robots_txt(url):
                print(f"Robots.txt disallows scraping of {url}")
                return None
                
            response = self.session.get(url)
            response.raise_for_status()
            return response
        except requests.RequestException as e:
            print(f"Error fetching {url}: {e}")
            return None

    def save_content(self, content, path):
        """Save content to a local file"""
        full_path = os.path.join(self.output_dir, path.lstrip('/'))
        
        # Create directory if it doesn't exist
        os.makedirs(os.path.dirname(full_path), exist_ok=True)
        
        # Determine if content is text or binary
        if isinstance(content, str):
            with open(full_path, 'w', encoding='utf-8') as f:
                f.write(content)
        else:
            with open(full_path, 'wb') as f:
                f.write(content)
        
        print(f"Saved: {full_path}")

    def extract_links(self, soup, base_url):
        """Extract all links from a page"""
        links = []
        for link in soup.find_all(['a', 'link', 'script', 'img'], href=True):
            href = link.get('href') or link.get('src')
            if href:
                full_url = urljoin(base_url, href)
                if self.is_valid_url(full_url) and full_url not in self.visited_urls:
                    links.append(full_url)
        return links

    def scrape_page(self, url):
        """Scrape a single page and extract all its content"""
        if url in self.visited_urls:
            return []

        print(f"Scraping: {url}")
        
        response = self.get_page(url)
        if not response:
            return []

        # Determine file path based on URL
        parsed_url = urlparse(url)
        path = parsed_url.path
        if not path or path == '/':
            path = '/index.htm'
        
        # Save the page content
        self.save_content(response.text, path)
        
        # Mark as visited
        self.visited_urls.add(url)
        
        # Extract links if this is an HTML page
        links = []
        if 'text/html' in response.headers.get('content-type', ''):
            soup = BeautifulSoup(response.text, 'html.parser')
            links = self.extract_links(soup, url)
        
        # Add new links to the queue
        for link in links:
            if link not in self.visited_urls:
                self.to_visit.add(link)
        
        # Respectful delay between requests
        time.sleep(self.delay)
        
        return links

    def download_file(self, url):
        """Download a file (PDF, image, etc.)"""
        if url in self.visited_urls:
            return

        print(f"Downloading: {url}")
        
        response = self.get_page(url)
        if not response:
            return

        # Determine file path based on URL
        parsed_url = urlparse(url)
        path = parsed_url.path
        
        # Save the file content
        self.save_content(response.content, path)
        
        # Mark as visited
        self.visited_urls.add(url)
        
        # Respectful delay between requests
        time.sleep(self.delay)

    def get_all_site_urls(self):
        """Generate a list of all URLs in the site structure based on our analysis"""
        urls = [
            "http://www.cccrj.com.br/",
            "http://www.cccrj.com.br/index.htm",
            "http://www.cccrj.com.br/mapa.htm",
            "http://www.cccrj.com.br/fale.htm",
            "http://www.cccrj.com.br/Style.css",
            "http://www.cccrj.com.br/javascript/jquery.s.min.js",
            "http://www.cccrj.com.br/boletim.pdf",
            "http://www.cccrj.com.br/Manual_PERT_Simples_Nacional.pdf",
            
            # CCCRJ section
            "http://www.cccrj.com.br/cccrj/inicio.htm",
            "http://www.cccrj.com.br/cccrj/constituicao.htm",
            "http://www.cccrj.com.br/cccrj/lancamento.htm",
            "http://www.cccrj.com.br/cccrj/estatuto.htm",
            "http://www.cccrj.com.br/cccrj/centenario.htm",
            "http://www.cccrj.com.br/cccrj/diretorias.htm",
            
            # CRMC section
            "http://www.cccrj.com.br/crmc/inicio.htm",
            "http://www.cccrj.com.br/crmc/cafeteria.htm",
            "http://www.cccrj.com.br/crmc/cafeteria/carta.htm",
            "http://www.cccrj.com.br/crmc/cafeteria/fotos.htm",
            "http://www.cccrj.com.br/crmc/biblioteca.htm",
            "http://www.cccrj.com.br/crmc/acervo/frame_acervo.htm",
            "http://www.cccrj.com.br/crmc/cultural.htm",
            "http://www.cccrj.com.br/crmc/cultural/exposicao.htm",
            "http://www.cccrj.com.br/crmc/dicas.htm",
            "http://www.cccrj.com.br/crmc/dicas_curiosidades.htm",
            
            # Revista section
            "http://www.cccrj.com.br/revista/inicio.htm",
            "http://www.cccrj.com.br/revista/acervo/Frame_acervo.htm",
            
            # Terminal section
            "http://www.cccrj.com.br/terminal/inicio.htm",
            
            # Rio section
            "http://www.cccrj.com.br/rio/inicio.htm",
            "http://www.cccrj.com.br/rio/historia.htm",
            "http://www.cccrj.com.br/rio/producao.htm",
            "http://www.cccrj.com.br/rio/exportacao.htm",
            
            # Boletim section
            "http://www.cccrj.com.br/Boletim/index.php",
            
            # Clipping and News sections
            "http://www.cccrj.com.br/clipping/frame_clipping.htm",
            "http://www.cccrj.com.br/noticias/frame_noticias.htm",
            
            # Links section
            "http://www.cccrj.com.br/links/inicio.htm",
        ]
        
        # Add common image paths based on our analysis
        image_paths = [
            "http://www.cccrj.com.br/Imagens_Home/",
            "http://www.cccrj.com.br/Imagens_Int/",
            "http://www.cccrj.com.br/Imagens/"
        ]
        
        # Add some common image extensions we might find
        image_extensions = ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.svg', '.webp']
        
        for path in image_paths:
            for ext in image_extensions:
                # We'll add some common image names based on typical site structures
                common_image_names = [
                    'logo', 'banner', 'header', 'footer', 'bg', 'background', 
                    'image', 'img', 'thumb', 'photo', 'icon', 'button'
                ]
                
                for name in common_image_names:
                    urls.append(f"{path}{name}{ext}")
        
        return urls

    def run(self):
        """Run the scraper"""
        print("Starting CCCRJ scraper...")
        
        # Get all URLs based on our site structure analysis
        all_urls = self.get_all_site_urls()
        
        print(f"Found {len(all_urls)} URLs to process")
        
        # Process each URL
        for url in all_urls:
            try:
                if any(url.endswith(ext) for ext in ['.pdf', '.jpg', '.jpeg', '.png', '.gif', '.bmp', '.svg', '.webp']):
                    # Download file
                    self.download_file(url)
                else:
                    # Scrape page
                    self.scrape_page(url)
            except Exception as e:
                print(f"Error processing {url}: {e}")
        
        print(f"Scraping completed! Scraped {len(self.visited_urls)} pages/files")
        print(f"Content saved to: {self.output_dir}")

if __name__ == "__main__":
    scraper = CCCRJScraper(delay=1)  # 1 second delay between requests
    scraper.run()