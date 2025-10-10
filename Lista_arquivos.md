# Lista de Arquivos Não Necessários para o Funcionamento do Sistema CCCRJ

Esta lista contém arquivos e diretórios que não são necessários para o funcionamento do sistema principal do Centro de Comércio do Café do Rio de Janeiro (CCCRJ).

## Arquivos PHP de Desenvolvimento e Utilidades

- `cleanup_development_environment.php` - Script para limpar ambiente de desenvolvimento
- `converter_dados_site_original.php` - Conversor de dados do site original (não necessário após migração)
- `documentation_tasks.php` - Tarefas relacionadas à documentação
- `export_migrated_content.php` - Exportação de conteúdo migrado (utilitário)
- `export_populated_content.php` - Exportação de conteúdo populado (utilitário)
- `generate_content_statistics.php` - Geração de estatísticas de conteúdo (relatório)
- `generate_dashboard_metrics.php` - Geração de métricas do painel (relatório)
- `generate_documentation.php` - Geração de documentação (utilitário)
- `generate_final_project_report.php` - Geração de relatório final do projeto
- `generate_full_system_report.php` - Geração de relatório completo do sistema
- `generate_performance_metrics.php` - Geração de métricas de desempenho
- `generate_quality_report.php` - Geração de relatórios de qualidade
- `generate_rss_feed.php` - Geração de feed RSS (não aparenta ser utilizado)
- `generate_scraped_content_stats.php` - Geração de estatísticas de conteúdo raspado
- `generate_sitemap.php` - Geração de sitemap (não aparenta ser utilizado)
- `generate_statistics.php` - Geração de estatísticas (relatório)
- `generate_system_report.php` - Geração de relatório do sistema
- `generate_technical_documentation.php` - Geração de documentação técnica
- `health_check.php` - Verificação de saúde do sistema (utilitário)
- `implement_components.php` - Implementação de componentes (utilitário de desenvolvimento)
- `import_exported_content.php` - Importação de conteúdo exportado (utilitário)
- `import_migrated_content.php` - Importação de conteúdo migrado (utilitário)
- `maintenance_tasks.php` - Tarefas de manutenção (não essenciais para funcionamento)
- `migrate_scraped_content.php` - Migração de conteúdo raspado (não necessário após migração)
- `populate_database_with_real_data.php` - Preenchimento com dados reais (utilitário)
- `populate_db_with_scraped_content.php` - Preenchimento com conteúdo raspado (utilitário)
- `populate_db.php` - Preenchimento do banco de dados (utilitário)
- `populate_from_scraped_content.php` - Preenchimento a partir de conteúdo raspado (utilitário)
- `setup_development_environment.php` - Configuração de ambiente de desenvolvimento
- `test_database_connection.php` - Teste de conexão com o banco de dados (utilitário)
- `verify_file_integrity.php` - Verificação de integridade de arquivos (utilitário)
- `verify_implementation.php` - Verificação de implementação (utilitário)
- `verify_migrated_content.php` - Verificação de conteúdo migrado (utilitário)
- `verify_populated_content.php` - Verificação de conteúdo populado (utilitário)
- `verify_project_files.php` - Verificação de arquivos do projeto (utilitário)

## Arquivos de Documentação e Planejamento

- `implementacao_site_original_atualizada.md` - Documento de implementação atualizado (não necessário para execução)
- `implementacao_site_original.md` - Documento de implementação original (não necessário para execução)
- `plan_copy.md` - Cópia de plano (não necessário para execução)
- `plan.md` - Documento de plano (não necessário para execução)
- `plano_implementacao.md` - Plano de implementação (não necessário para execução)
- `plano_index.md` - Plano de índice (não necessário para execução)
- `plano_integracao_ftp_fase1_php.md` - Plano de integração FTP fase 1 PHP (não necessário para execução)
- `plano_integracao_ftp_fase1.md` - Plano de integração FTP fase 1 (não necessário para execução)
- `plano_integracao_ftp.md` - Plano de integração FTP (não necessário para execução)
- `qwen.md` - Documento do assistente de IA (não necessário para execução)
- `RESUMO_IMPLEMENTACAO_COMPLETA.md` - Resumo de implementação completa (não necessário para execução)
- `RESUMO_IMPLEMENTACAO.md` - Resumo de implementação (não necessário para execução)

## Arquivos Temporários

- `temp_cccrj.html` - Arquivo HTML temporário
- `temp_mapa.html` - Arquivo de mapa temporário

## Diretórios Não Essenciais para Execução

- `scraping_cccrj/` - Conteúdo raspado do site original (não necessário após migração)
- `reports/` - Relatórios gerados (não essenciais para funcionamento do sistema)
- `docs/` - Documentação do projeto (não necessário para execução do sistema)
- `logs/` - Logs do sistema (não essenciais para funcionamento)
- `cache/` - Arquivos de cache (podem ser recriados pelo sistema)
- `temp/` - Arquivos temporários
- `utils/` - Utilitários (não essenciais para funcionamento)
- `qwen-code/` - Código do assistente de IA (não necessário para execução)

## Observações

Após a remoção destes arquivos, o sistema continuará funcionando normalmente, pois eles são compostos por:

1. Scripts utilitários usados durante o desenvolvimento e migração
2. Documentos de planejamento e especificação
3. Arquivos temporários
4. Pastas de logs e cache que podem ser recriadas conforme necessário

Os arquivos essenciais para o funcionamento do sistema incluem:

1. Arquivos HTML principais (`index.html`, `login.html`, `admin.html`)
2. Arquivos CSS e JavaScript
3. Arquivos PHP da API (em `/api/`)
4. Imagens e outros assets (em `/assets/`)
5. Arquivo de configuração do banco de dados (`api/config/database.php`)