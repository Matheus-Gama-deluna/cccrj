#!/bin/bash
# Script para atualização automática de notícias

# Diretório do projeto
PROJECT_DIR="C:/Users/matheus.luna/Documents/CCCRJ/cccrj"

# Executar atualização
curl -s "http://localhost/api/news_scraper.php?daily_update=1" > $PROJECT_DIR/logs/news_update_$(date +%Y%m%d).log 2>&1

# Verificar resultado
if [ $? -eq 0 ]; then
    echo "Atualização de notícias concluída com sucesso - $(date)" >> $PROJECT_DIR/logs/news_cron.log
else
    echo "Erro na atualização de notícias - $(date)" >> $PROJECT_DIR/logs/news_cron.log
fi