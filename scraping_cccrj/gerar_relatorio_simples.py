import os
from pathlib import Path

def format_size(bytes_size):
    """Formata o tamanho do arquivo para uma string legível"""
    for unit in ['B', 'KB', 'MB', 'GB']:
        if bytes_size < 1024.0:
            return f"{bytes_size:.1f} {unit}"
        bytes_size /= 1024.0
    return f"{bytes_size:.1f} TB"

def get_directory_size(path):
    """Calcula o tamanho total de um diretório em bytes"""
    total = 0
    for dirpath, _, filenames in os.walk(path):
        for f in filenames:
            fp = os.path.join(dirpath, f)
            if os.path.exists(fp):
                total += os.path.getsize(fp)
    return total

def generate_simple_report():
    """Gera um relatório simples do conteúdo extraído"""
    base_dir = Path("extracted_content")
    
    # Verificar se o diretório existe
    if not base_dir.exists():
        print(f"Erro: O diretório {base_dir} não foi encontrado.")
        return
    
    print("\n" + "="*50)
    print("RELATÓRIO DE CONTEÚDO EXTRAÍDO - CCCRJ")
    print("="*50)
    
    # Mapear diretórios principais
    main_dirs = {
        'institucional': 'Informações Institucionais',
        'crmc': 'Centro de Referência e Memória do Café (CRMC)',
        'publicacoes': 'Publicações',
        'midia': 'Mídia'
    }
    
    # Gerar relatório para cada diretório principal
    for dir_name, dir_label in main_dirs.items():
        dir_path = base_dir / dir_name
        if dir_path.exists() and dir_path.is_dir():
            print(f"\n{dir_label}:")
            print("-" * len(dir_label))
            
            # Contar arquivos e calcular tamanho
            file_count = sum(1 for _ in dir_path.rglob('*') if _.is_file())
            dir_size = get_directory_size(dir_path)
            
            print(f"Total de arquivos: {file_count}")
            print(f"Tamanho total: {format_size(dir_size)}")
            
            # Listar subdiretórios
            if dir_name == 'midia':
                print("\nSubdiretórios de mídia:")
                media_path = dir_path / 'imagens'
                if media_path.exists():
                    for subdir in media_path.iterdir():
                        if subdir.is_dir():
                            subdir_count = sum(1 for _ in subdir.rglob('*') if _.is_file())
                            print(f"  - {subdir.name}: {subdir_count} arquivos")
    
    print("\n" + "="*50)
    print("Relatório concluído!")
    print("="*50 + "\n")

if __name__ == "__main__":
    print("Gerando relatório de conteúdo extraído...")
    generate_simple_report()
