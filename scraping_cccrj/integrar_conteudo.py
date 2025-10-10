import os
import shutil
import json
from pathlib import Path

def criar_backup(caminho_original, sufixo='_backup'):
    """Cria um backup do arquivo ou diretório original"""
    if not os.path.exists(caminho_original):
        return
        
    caminho_backup = f"{caminho_original}{sufixo}"
    
    # Se já existir um backup, adiciona um número ao final
    contador = 1
    while os.path.exists(caminho_backup):
        caminho_backup = f"{caminho_original}{sufixo}_{contador}"
        contador += 1
    
    # Copia o arquivo/diretório
    if os.path.isfile(caminho_original):
        shutil.copy2(caminho_original, caminho_backup)
    else:
        shutil.copytree(caminho_original, caminho_backup)
    
    print(f"Backup criado: {caminho_backup}")

def carregar_json(caminho):
    """Carrega um arquivo JSON"""
    try:
        with open(caminho, 'r', encoding='utf-8') as f:
            return json.load(f)
    except Exception as e:
        print(f"Erro ao carregar {caminho}: {e}")
        return None

def integrar_conteudo():
    """Integra o conteúdo extraído ao site atual"""
    # Caminhos importantes
    base_dir = Path(__file__).parent
    extracted_dir = base_dir / 'extracted_content'
    site_dir = base_dir.parent  # Pasta raiz do site
    
    print("Iniciando integração de conteúdo...")
    
    # 1. Integrar conteúdo institucional
    print("\n1. Integrando conteúdo institucional...")
    institucional_src = extracted_dir / 'institucional'
    if institucional_src.exists():
        # Criar diretório de dados se não existir
        dados_dir = site_dir / 'data' / 'institucional'
        dados_dir.mkdir(parents=True, exist_ok=True)
        
        # Copiar arquivos JSON
        for json_file in institucional_src.glob('*.json'):
            destino = dados_dir / json_file.name
            criar_backup(destino)
            shutil.copy2(json_file, destino)
            print(f"  - {json_file.name} copiado para {destino}")
    
    # 2. Integrar conteúdo do CRMC
    print("\n2. Integrando conteúdo do CRMC...")
    crmc_src = extracted_dir / 'crmc'
    if crmc_src.exists():
        # Criar diretório de dados do CRMC
        crmc_dest = site_dir / 'data' / 'crmc'
        crmc_dest.mkdir(parents=True, exist_ok=True)
        
        # Copiar arquivos JSON
        for json_file in crmc_src.glob('*.json'):
            destino = crmc_dest / json_file.name
            criar_backup(destino)
            shutil.copy2(json_file, destino)
            print(f"  - {json_file.name} copiado para {destino}")
    
    # 3. Integrar publicações
    print("\n3. Integrando publicações...")
    publicacoes_src = extracted_dir / 'publicacoes'
    if publicacoes_src.exists():
        # Criar diretório de publicações
        pub_dest = site_dir / 'data' / 'publicacoes'
        pub_dest.mkdir(parents=True, exist_ok=True)
        
        # Copiar boletins
        boletins_src = publicacoes_src / 'boletins'
        if boletins_src.exists():
            boletins_dest = pub_dest / 'boletins'
            boletins_dest.mkdir(exist_ok=True)
            
            for item in boletins_src.iterdir():
                if item.is_file():
                    destino = boletins_dest / item.name
                    criar_backup(destino)
                    shutil.copy2(item, destino)
                    print(f"  - Boletim {item.name} copiado")
        
        # Copiar revistas
        revistas_src = publicacoes_src / 'revistas'
        if revistas_src.exists():
            revistas_dest = pub_dest / 'revistas'
            revistas_dest.mkdir(exist_ok=True)
            
            for item in revistas_src.iterdir():
                if item.is_file():
                    destino = revistas_dest / item.name
                    criar_backup(destino)
                    shutil.copy2(item, destino)
                    print(f"  - Revista {item.name} copiada")
    
    # 4. Integrar mídias
    print("\n4. Integrando mídias...")
    midia_src = extracted_dir / 'midia'
    if midia_src.exists():
        # Diretório de mídia do site
        midia_dest = site_dir / 'assets' / 'media'
        midia_dest.mkdir(parents=True, exist_ok=True)
        
        # Copiar imagens
        imagens_src = midia_src / 'imagens'
        if imagens_src.exists():
            for categoria in imagens_src.iterdir():
                if categoria.is_dir():
                    categoria_dest = midia_dest / 'imagens' / categoria.name
                    categoria_dest.mkdir(parents=True, exist_ok=True)
                    
                    # Copiar apenas arquivos, não subdiretórios
                    for img in categoria.glob('*.*'):
                        if img.is_file():
                            destino = categoria_dest / img.name
                            criar_backup(destino)
                            shutil.copy2(img, destino)
                            print(f"  - Imagem {img.name} copiada para {categoria.name}")
    
    print("\nIntegração concluída com sucesso!")

if __name__ == "__main__":
    integrar_conteudo()
