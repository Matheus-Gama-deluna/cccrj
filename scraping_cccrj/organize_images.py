import os
import shutil
import json
from pathlib import Path

class ImageOrganizer:
    def __init__(self, source_dir="cccrj_content", output_dir="extracted_content"):
        self.source_dir = Path(source_dir)
        self.output_dir = Path(output_dir)
        self.image_extensions = ['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.tiff', '.webp']
        
        # Estrutura de diretórios de destino
        self.dest_dirs = {
            'institucional': self.output_dir / 'midia' / 'imagens' / 'institucional',
            'crmc': self.output_dir / 'midia' / 'imagens' / 'crmc',
            'publicacoes': self.output_dir / 'midia' / 'imagens' / 'publicacoes',
            'eventos': self.output_dir / 'midia' / 'imagens' / 'eventos',
            'diversos': self.output_dir / 'midia' / 'imagens' / 'diversos'
        }
        
        # Criar diretórios de destino
        for dir_path in self.dest_dirs.values():
            dir_path.mkdir(parents=True, exist_ok=True)
    
    def is_image_file(self, filename):
        """Verifica se o arquivo é uma imagem com base na extensão"""
        return Path(filename).suffix.lower() in self.image_extensions
    
    def organize_images(self):
        """Organiza as imagens nos diretórios apropriados"""
        print("Organizando imagens...")
        
        # Dicionário para armazenar metadados das imagens
        image_metadata = {key: [] for key in self.dest_dirs}
        
        # Procurar por imagens em todo o diretório de origem
        for root, _, files in os.walk(self.source_dir):
            for file in files:
                if self.is_image_file(file):
                    src_path = Path(root) / file
                    rel_path = src_path.relative_to(self.source_dir)
                    
                    # Determinar o diretório de destino com base no caminho
                    dest_category = self._categorize_image(rel_path)
                    dest_dir = self.dest_dirs[dest_category]
                    
                    # Criar estrutura de diretórios de destino se necessário
                    dest_path = dest_dir / rel_path.name
                    dest_path.parent.mkdir(parents=True, exist_ok=True)
                    
                    try:
                        # Copiar a imagem para o diretório de destino
                        if not dest_path.exists():
                            shutil.copy2(src_path, dest_path)
                        
                        # Adicionar metadados
                        image_metadata[dest_category].append({
                            'nome': file,
                            'caminho_original': str(rel_path),
                            'caminho_destino': str(dest_path.relative_to(self.output_dir)),
                            'tamanho': f"{src_path.stat().st_size / 1024:.1f} KB",
                            'formato': Path(file).suffix[1:].upper()
                        })
                        
                    except Exception as e:
                        print(f"Erro ao processar {src_path}: {e}")
        
        # Salvar metadados
        self._save_metadata(image_metadata)
        print("Organização de imagens concluída!")
    
    def _categorize_image(self, rel_path):
        """Categoriza a imagem com base no caminho de origem"""
        path_str = str(rel_path).lower()
        
        if 'crmc' in path_str:
            return 'crmc'
        elif 'revista' in path_str or 'boletim' in path_str:
            return 'publicacoes'
        elif 'evento' in path_str or 'palestra' in path_str or 'seminario' in path_str:
            return 'eventos'
        elif 'logo' in path_str or 'marca' in path_str or 'institucional' in path_str:
            return 'institucional'
        else:
            return 'diversos'
    
    def _save_metadata(self, image_metadata):
        """Salva os metadados das imagens em arquivos JSON"""
        for category, images in image_metadata.items():
            if images:  # Apenas salvar se houver imagens na categoria
                metadata_file = self.dest_dirs[category] / 'metadata.json'
                with open(metadata_file, 'w', encoding='utf-8') as f:
                    json.dump({
                        'categoria': category,
                        'total_imagens': len(images),
                        'imagens': images
                    }, f, ensure_ascii=False, indent=2)

if __name__ == "__main__":
    print("Iniciando organização de imagens...")
    organizer = ImageOrganizer()
    organizer.organize_images()
    print("Processo concluído!")
