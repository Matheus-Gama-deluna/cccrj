import json
import os
from pathlib import Path
from datetime import datetime

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

def generate_report():
    """Gera um relatório detalhado do conteúdo extraído"""
    base_dir = Path("extracted_content")
    report = {
        'data_geracao': datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
        'diretorio_base': str(base_dir.absolute()),
        'estatisticas': {},
        'conteudo': {}
    }
    
    # Mapear diretórios principais
    main_dirs = {
        'institucional': 'Informações Institucionais',
        'crmc': 'Centro de Referência e Memória do Café (CRMC)',
        'publicacoes': 'Publicações',
        'midia': 'Mídia'
    }
    
    # Coletar estatísticas
    for dir_name, dir_label in main_dirs.items():
        dir_path = base_dir / dir_name
        if dir_path.exists() and dir_path.is_dir():
            # Contar arquivos e calcular tamanho
            file_count = sum(1 for _ in dir_path.rglob('*') if _.is_file())
            dir_size = get_directory_size(dir_path)
            
            report['estatisticas'][dir_label] = {
                'arquivos': file_count,
                'tamanho': format_size(dir_size),
                'tamanho_bytes': dir_size
            }
            
            # Listar subdiretórios e arquivos principais
            report['conteudo'][dir_label] = {}
            
            if dir_path.name == 'midia':
                # Tratamento especial para mídia
                media_dirs = ['imagens', 'documentos']
                for media_dir in media_dirs:
                    media_path = dir_path / media_dir
                    if media_path.exists():
                        sub_items = {}
                        for item in media_path.iterdir():
                            if item.is_dir():
                                sub_items[item.name] = {
                                    'tipo': 'pasta',
                                    'itens': len(list(item.rglob('*')))
                                }
                            else:
                                sub_items[item.name] = {
                                    'tipo': 'arquivo',
                                    'tamanho': format_size(item.stat().st_size)
                                }
                        report['conteudo'][dir_label][media_dir] = sub_items
            else:
                # Para outros diretórios principais
                for item in dir_path.iterdir():
                    if item.is_dir():
                        sub_items = {}
                        for sub_item in item.iterdir():
                            if sub_item.is_file():
                                sub_items[sub_item.name] = {
                                    'tipo': 'arquivo',
                                    'tamanho': format_size(sub_item.stat().st_size)
                                }
                        report['conteudo'][dir_label][item.name] = {
                            'tipo': 'pasta',
                            'itens': len(sub_items),
                            'conteudo': sub_items
                        }
                    else:
                        report['conteudo'][dir_label][item.name] = {
                            'tipo': 'arquivo',
                            'tamanho': format_size(item.stat().st_size)
                        }
    
    # Calcular totais
    total_arquivos = sum(s['arquivos'] for s in report['estatisticas'].values())
    total_tamanho = sum(s['tamanho_bytes'] for s in report['estatisticas'].values())
    
    report['estatisticas']['TOTAL'] = {
        'arquivos': total_arquivos,
        'tamanho': format_size(total_tamanho)
    }
    
    # Salvar relatório em JSON
    report_path = base_dir / 'relatorio_extracao.json'
    with open(report_path, 'w', encoding='utf-8') as f:
        json.dump(report, f, ensure_ascii=False, indent=2)
    
    # Gerar relatório em formato de texto
    txt_report = f"""
    ===================================
    RELATÓRIO DE EXTRAÇÃO - CCCRJ
    ===================================
    Data: {report['data_geracao']}
    Diretório: {report['diretorio_base']}
    
    ========== ESTATÍSTICAS ==========
    """
    
    for category, stats in report['estatisticas'].items():
        if 'arquivos' in stats:
            txt_report += f"\n{category.upper()}:"
            txt_report += f"\n  • Arquivos: {stats['arquivos']:,}"
            txt_report += f"\n  • Tamanho: {stats['tamanho']}"
    
    txt_report += "\n\n========== CONTEÚDO EXTRAÍDO ==========\n"
    
    for category, contents in report['conteudo'].items():
        txt_report += f"\n{category.upper()}:\n"
        for subcategory, items in contents.items():
            if isinstance(items, dict):
                txt_report += f"  • {subcategory}:\n"
                for item, props in items.items():
                    if props.get('tipo') == 'pasta':
                        txt_report += f"      - {item}/ ({props.get('itens', 0)} itens)\n"
                    else:
                        txt_report += f"      - {item} ({props.get('tamanho', 'N/A')})\n"
    # Salvar relatório em texto
    txt_report_path = base_dir / 'relatorio_extracao.txt'
    with open(txt_report_path, 'w', encoding='utf-8') as f:
        f.write(txt_report)
    
    print(f"Relatório gerado com sucesso!")
    print(f"- JSON: {report_path}")
    print(f"- Texto: {txt_report_path}")
    print("\nResumo do conteúdo extraído:")
    print(txt_report)

if __name__ == "__main__":
    print("Gerando relatório de extração...")
    generate_report()
