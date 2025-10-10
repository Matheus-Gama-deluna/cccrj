# Conteúdo Extraído do Site do CCCRJ

Este diretório contém o conteúdo extraído e estruturado do site do Centro de Comércio do Café do Rio de Janeiro (CCCRJ).

## Estrutura de Diretórios

- **institucional/**: Contém informações institucionais como história, diretoria e estatutos.
  - `historia.json`: História do CCCRJ
  - `diretoria.json`: Informações sobre a diretoria atual
  - `estatutos/`: Documentos relacionados aos estatutos da instituição

- **crmc/**: Dados sobre o Centro de Referência e Memória do Café
  - `info.json`: Informações gerais sobre o CRMC
  - `acervo/`: Dados sobre o acervo disponível
  - `exposicoes/`: Informações sobre exposições realizadas

- **publicacoes/**: Publicações do CCCRJ
  - `boletins/`: Boletins informativos
  - `revistas/`: Edições da Revista do Café

- **midia/**: Arquivos de mídia
  - `imagens/`: Imagens extraídas do site
  - `documentos/`: Documentos diversos em PDF, DOC, etc.

## Como Usar

1. Execute o script `content_extractor.py` para extrair e organizar o conteúdo:
   ```
   python content_extractor.py
   ```

2. Os arquivos serão organizados automaticamente na estrutura de diretórios descrita acima.

3. Utilize os arquivos JSON gerados para alimentar o site atual do CCCRJ.

## Atualização

Para atualizar o conteúdo extraído, basta executar novamente o script de extração. Os arquivos existentes serão substituídos.

## Notas

- Os arquivos HTML originais estão disponíveis no diretório `cccrj_content/`
- As imagens são referenciadas nos arquivos JSON com caminhos relativos
- Certifique-se de manter a estrutura de diretórios ao implementar no site
