import os
import sys
import time
from scraper import CCCRJScraper
from image_scraper import find_and_download_images

def main():
    print(\"Iniciando processo de scraping do site CCCRJ...\")
    
    # Criar diretório de saída
    output_dir = \"cccrj_content\"
    os.makedirs(output_dir, exist_ok=True)
    
    # Instanciar e executar o scraper principal
    scraper = CCCRJScraper(delay=1)
    
    print(\"Etapa 1: Executando scraper principal...\")
    start_time = time.time()
    scraper.run()
    end_time = time.time()
    print(f\"Scraper principal concluído em {end_time - start_time:.2f} segundos\")
    
    print(\"\\nEtapa 2: Procurando e baixando imagens adicionais...\")
    start_time = time.time()
    find_and_download_images(\"http://www.cccrj.com.br\", output_dir)
    end_time = time.time()
    print(f\"Download de imagens concluído em {end_time - start_time:.2f} segundos\")
    
    # Criar um arquivo de resumo
    summary_path = os.path.join(output_dir, \"resumo_scraping.txt\")
    with open(summary_path, 'w', encoding='utf-8') as f:
        f.write(\"Resumo do Scraping do Site CCCRJ\\n\")
        f.write(\"=\"*40 + \"\\n\")
        f.write(f\"Data do scraping: {time.strftime('%Y-%m-%d %H:%M:%S')}\\n\")
        f.write(f\"URL base: http://www.cccrj.com.br/\\n\")
        f.write(f\"Total de páginas/arquivos processados: {len(scraper.visited_urls)}\\n\")
        f.write(\"\\nPáginas/arquivos salvos:\\n\")
        for url in sorted(scraper.visited_urls):
            f.write(f\"- {url}\\n\")
    
    print(f\"\\nProcesso de scraping concluído com sucesso!\")
    print(f\"Conteúdos salvos em: {output_dir}\")
    print(f\"Arquivo de resumo criado: {summary_path}\")

if __name__ == \"__main__\":
    main()