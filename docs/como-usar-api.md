# Como usar a nova implementação

## 1. Upload de Arquivo
```bash
curl -X POST http://localhost/api/upload_report.php \
  -F "file=@relatorio.pdf" \
  -F "title=Relatório Mensal" \
  -F "is_boletim=false"
```

## 2. Listar Arquivos
```bash
curl http://localhost/api/reports/list.php?page=1&per_page=10
```

## 3. Download de Arquivo
```bash
curl "http://localhost/api/reports/download.php?file=nome_arquivo.pdf&type=reports" \
  --output relatorio.pdf
```

## 4. Executar Migração FTP
```bash
php api/migrate_from_ftp.php
```
