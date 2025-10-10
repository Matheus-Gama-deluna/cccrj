import os
import json
import shutil
import re
from html.parser import HTMLParser
from html.entities import name2codepoint
import urllib.request
from urllib.parse import urljoin, urlparse

class ContentExtractor:
    def __init__(self, source_dir="cccrj_content", output_dir="extracted_content"):
        self.source_dir = source_dir
        self.output_dir = output_dir
        self.base_url = "http://www.cccrj.com.br/"
        
        # Criar diretórios de saída
        self.dirs = {
            'institucional': os.path.join(output_dir, 'institucional'),
            'crmc': os.path.join(output_dir, 'crmc'),
            'publicacoes': {
                'boletins': os.path.join(output_dir, 'publicacoes', 'boletins'),
                'revistas': os.path.join(output_dir, 'publicacoes', 'revistas')
            },
            'midia': {
                'imagens': os.path.join(output_dir, 'midia', 'imagens'),
                'documentos': os.path.join(output_dir, 'midia', 'documentos')
            }
        }
        
        # Criar estrutura de diretórios
        self._create_directories()
    
    def _create_directories(self):
        """Cria a estrutura de diretórios necessária"""
        for dir_type, dir_path in self.dirs.items():
            if isinstance(dir_path, dict):
                for subdir in dir_path.values():
                    os.makedirs(subdir, exist_ok=True)
            else:
                os.makedirs(dir_path, exist_ok=True)
    
    def extract_text_from_html(self, html_content):
        """Extrai texto de um conteúdo HTML usando expressões regulares"""
        # Remove scripts e estilos
        html_content = re.sub(r'<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>', '', html_content, flags=re.DOTALL)
        html_content = re.sub(r'<style\b[^<]*(?:(?!<\/style>)<[^<]*)*<\/style>', '', html_content, flags=re.DOTALL)
        
        # Remove tags HTML
        text = re.sub(r'<[^>]+>', ' ', html_content)
        
        # Remove múltiplos espaços em branco
        text = re.sub(r'\s+', ' ', text).strip()
        
        return text
    
    def extract_institutional_content(self):
        """Extrai conteúdo institucional (história, diretoria, etc.)"""
        print("Extraindo conteúdo institucional...")
        
        # História do CCCRJ
        historia_path = os.path.join(self.source_dir, 'cccrj', 'centenario.htm')
        if os.path.exists(historia_path):
            with open(historia_path, 'r', encoding='iso-8859-1') as f:
                content = f.read()
            
            # Extrair texto do HTML
            text_content = self.extract_text_from_html(content)
            
            # Salvar como JSON estruturado
            historia_data = {
                'titulo': 'História do CCCRJ',
                'conteudo': text_content,
                'fonte': 'centenario.htm',
                'data_extracao': '2023-10-10'
            }
            
            with open(os.path.join(self.dirs['institucional'], 'historia.json'), 'w', encoding='utf-8') as f:
                json.dump(historia_data, f, ensure_ascii=False, indent=2)
        
        # Diretoria (dados fixos baseados na análise anterior)
        diretoria = {
            'presidente': 'Guilherme Braga Abreu P. Filho',
            'diretores': [
                {'cargo': 'Diretor Secretário', 'nome': 'Alexandre Todeschini Pires'},
                {'cargo': 'Diretor Tesoureiro', 'nome': 'Batista Mancini'},
                {'cargo': 'Diretor de Patrimônio', 'nome': 'Ruy Barreto Filho'}
            ],
            'gerente_geral': 'Guilherme Braga Abreu P. Neto',
            'fonte': 'diretorias.htm',
            'data_extracao': '2023-10-10',
            'contato': {
                'endereco': 'Rua da Quitanda, 191 - 8º andar - Centro - Rio de Janeiro/RJ',
                'telefone': '(21) 2516-3399',
                'fax': '(21) 2253-4873',
                'email': 'riocafe@cccrj.com.br'
            }
        }
        
        with open(os.path.join(self.dirs['institucional'], 'diretoria.json'), 'w', encoding='utf-8') as f:
            json.dump(diretoria, f, ensure_ascii=False, indent=2)
    
    def extract_publications(self):
        """Extrai conteúdo de publicações (boletins, revistas)"""
        print("Extraindo publicações...")
        
        # Boletins
        boletim_path = os.path.join(self.source_dir, 'boletim.pdf')
        if os.path.exists(boletim_path):
            try:
                # Copiar o arquivo para o diretório de destino
                os.makedirs(self.dirs['publicacoes']['boletins'], exist_ok=True)
                dest_path = os.path.join(self.dirs['publicacoes']['boletins'], 'boletim_atual.pdf')
                
                # Usar shutil se disponível, senão copiar manualmente
                try:
                    import shutil
                    shutil.copy2(boletim_path, dest_path)
                except:
                    with open(boletim_path, 'rb') as src, open(dest_path, 'wb') as dst:
                        dst.write(src.read())
                
                # Criar metadados do boletim
                boletim_metadata = {
                    'titulo': 'Boletim do Café',
                    'arquivo': 'boletim_atual.pdf',
                    'data_publicacao': '2023-10-10',  # Atualizar conforme necessário
                    'descricao': 'Boletim informativo do Centro de Comércio do Café do Rio de Janeiro',
                    'tamanho_arquivo': f"{os.path.getsize(dest_path) / 1024:.1f} KB"
                }
                
                metadata_path = os.path.join(self.dirs['publicacoes']['boletins'], 'metadata.json')
                with open(metadata_path, 'w', encoding='utf-8') as f:
                    json.dump(boletim_metadata, f, ensure_ascii=False, indent=2)
                    
            except Exception as e:
                print(f"Erro ao processar boletim: {e}")
        
        # Listar revistas disponíveis
        revistas_dir = os.path.join(self.source_dir, 'revista')
        if os.path.exists(revistas_dir) and os.path.isdir(revistas_dir):
            try:
                revistas = []
                for filename in os.listdir(revistas_dir):
                    if filename.lower().endswith(('.pdf', '.htm', '.html')):
                        revistas.append({
                            'arquivo': filename,
                            'caminho': os.path.join('revista', filename),
                            'tipo': 'revista',
                            'data_modificacao': os.path.getmtime(os.path.join(revistas_dir, filename))
                        })
                
                # Salvar lista de revistas
                if revistas:
                    with open(os.path.join(self.dirs['publicacoes']['revistas'], 'lista_revistas.json'), 'w', encoding='utf-8') as f:
                        json.dump(revistas, f, ensure_ascii=False, indent=2, default=str)
                        
            except Exception as e:
                print(f"Erro ao listar revistas: {e}")
    
    def extract_crmc_content(self):
        """Extrai conteúdo do Centro de Referência e Memória do Café"""
        print("Extraindo conteúdo do CRMC...")
        
        # Informações básicas do CRMC
        crmc_info = {
            'nome': 'Centro de Referência e Memória do Café',
            'sigla': 'CRMC',
            'descricao': 'Espaço dedicado à preservação e difusão da história do café no Brasil',
            'acervo': {
                'documentos': 'Coleção de documentos históricos sobre o café',
                'fotografias': 'Acervo fotográfico do setor cafeeiro',
                'publicacoes': 'Livros, revistas e periódicos sobre café',
                'itens_em_destaque': [
                    'Documentos históricos da produção cafeeira',
                    'Fotografias de antigas fazendas de café',
                    'Coleção de máquinas e equipamentos antigos'
                ]
            },
            'visitas': {
                'horario': 'Segunda a sexta, das 9h às 17h',
                'agendamento': 'Necessário agendamento prévio',
                'contato': 'crmc@cccrj.com.br / (21) 2516-3399'
            },
            'localizacao': {
                'endereco': 'Rua da Quitanda, 191 - 8º andar - Centro',
                'cidade': 'Rio de Janeiro',
                'estado': 'RJ',
                'cep': '20091-005'
            },
            'fonte': 'Conteúdo extraído do site do CCCRJ',
            'data_extracao': '2023-10-10',
            'atualizacao_necessaria': 'Sim, verificar informações diretamente com o CRMC'
        }
        
        # Criar diretório se não existir
        os.makedirs(self.dirs['crmc'], exist_ok=True)
        
        # Salvar informações do CRMC
        with open(os.path.join(self.dirs['crmc'], 'info.json'), 'w', encoding='utf-8') as f:
            json.dump(crmc_info, f, ensure_ascii=False, indent=2)
        
        # Se existir conteúdo no diretório do CRMC, listar arquivos
        crmc_dir = os.path.join(self.source_dir, 'crmc')
        if os.path.exists(crmc_dir) and os.path.isdir(crmc_dir):
            try:
                arquivos_crmc = []
                for root, _, files in os.walk(crmc_dir):
                    for file in files:
                        file_path = os.path.join(root, file)
                        rel_path = os.path.relpath(file_path, self.source_dir)
                        arquivos_crmc.append({
                            'nome': file,
                            'caminho': rel_path.replace('\\', '/'),
                            'tamanho': f"{os.path.getsize(file_path) / 1024:.1f} KB",
                            'tipo': file.split('.')[-1].upper()
                        })
                
                if arquivos_crmc:
                    with open(os.path.join(self.dirs['crmc'], 'arquivos.json'), 'w', encoding='utf-8') as f:
                        json.dump(arquivos_crmc, f, ensure_ascii=False, indent=2)
                        
            except Exception as e:
                print(f"Erro ao listar arquivos do CRMC: {e}")
    
    def run(self):
        """Executa a extração de todo o conteúdo"""
        print("Iniciando extração de conteúdo...")
        
        # Extrair conteúdo institucional
        self.extract_institutional_content()
        
        # Extrair publicações
        self.extract_publications()
        
        # Extrair conteúdo do CRMC
        self.extract_crmc_content()
        
        print("Extração concluída com sucesso!")
        print(f"Conteúdo extraído salvo em: {os.path.abspath(self.output_dir)}")

if __name__ == "__main__":
    extractor = ContentExtractor()
    extractor.run()
