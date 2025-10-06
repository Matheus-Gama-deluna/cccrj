import os
import requests
from urllib.parse import urljoin, urlparse
from bs4 import BeautifulSoup

def find_and_download_images(base_url, output_dir):
    """Find and download images from all pages of the site"""
    print(f"Scanning {base_url} for images...")
    
    # Create images directory
    images_dir = os.path.join(output_dir, "images")
    os.makedirs(images_dir, exist_ok=True)
    
    # Common image extensions
    image_extensions = ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.svg', '.webp']
    
    # List of pages to scan for images
    pages_to_scan = [
        "/",
        "/index.htm",
        "/mapa.htm",
        "/fale.htm",
        "/cccrj/inicio.htm",
        "/crmc/inicio.htm",
        "/revista/inicio.htm",
        "/terminal/inicio.htm",
        "/rio/inicio.htm",
        "/links/inicio.htm"
    ]
    
    session = requests.Session()
    session.headers.update({
        'User-Agent': 'Mozilla/5.0 (compatible; CCCRJ-Scraper/1.0; +http://www.cccrj.com.br/)'
    })
    
    downloaded_images = set()
    
    for page in pages_to_scan:
        page_url = base_url + page.lstrip('/')
        try:
            print(f"Scanning {page_url} for images...")
            response = session.get(page_url)
            response.raise_for_status()
            
            soup = BeautifulSoup(response.text, 'html.parser')
            
            # Find all img tags
            img_tags = soup.find_all('img')
            
            for img in img_tags:
                img_url = img.get('src')
                if img_url:
                    full_img_url = urljoin(base_url, img_url)
                    
                    # Check if image has a valid extension
                    parsed_img_url = urlparse(full_img_url)
                    _, ext = os.path.splitext(parsed_img_url.path.lower())
                    
                    if ext in image_extensions:
                        if full_img_url not in downloaded_images:
                            try:
                                img_response = session.get(full_img_url)
                                img_response.raise_for_status()
                                
                                # Create a filename based on the image URL
                                filename = os.path.basename(parsed_img_url.path)
                                if not filename or '.' not in filename:
                                    filename = f"image_{len(downloaded_images)}{ext}"
                                
                                filepath = os.path.join(images_dir, filename)
                                
                                with open(filepath, 'wb') as f:
                                    f.write(img_response.content)
                                
                                print(f"Downloaded: {full_img_url} -> {filepath}")
                                downloaded_images.add(full_img_url)
                            except Exception as e:
                                print(f"Error downloading {full_img_url}: {e}")
        except Exception as e:
            print(f"Error scanning page {page_url}: {e}")
    
    print(f"Downloaded {len(downloaded_images)} images to {images_dir}")

if __name__ == "__main__":
    base_url = "http://www.cccrj.com.br"
    output_dir = "cccrj_content"
    find_and_download_images(base_url, output_dir)