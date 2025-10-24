# Monitoramento e Manutenção

## Scripts Disponíveis

### 1. Setup Automático
```bash
# Windows
setup-migration.bat

# Linux
bash setup-migration.sh
```

### 2. Migração FTP
```bash
php api/migrate_from_ftp.php
```

### 3. Validação do Sistema
```bash
php validate_system.php
```

### 4. Teste das APIs
```bash
php api/test_localfileservice.php
```

## Monitoramento

### 1. Logs de Erro
- Arquivo: `logs/php_error_log`
- Configurado para capturar todos os erros

### 2. Cache
- Local: `cache/` (arquivos .cache)
- Expira automaticamente em 5 minutos
- Limpeza manual: `CacheService::clear()`

### 3. Backup
- Script cria backup automático: `../cccrj_backup_TIMESTAMP/`
- Contém todos os arquivos e configurações

## Manutenção

### 1. Limpeza de Cache
```php
$cache = new CacheService();
$cache->clear(); // Limpa todo cache
$cache->clear('specific_key'); // Limpa chave específica
```

### 2. Verificação de Arquivos
```php
$fileService = new LocalFileService();
$files = $fileService->listFiles('reports');
echo "Total de arquivos: " . count($files);
```

### 3. Verificação de Permissões
```bash
# Linux
ls -la pdf/
ls -la temp/
ls -la cache/

# Windows (via Explorer)
# Verificar propriedades de segurança das pastas
```

## Troubleshooting

### Problemas Comuns

1. **Erro 500 - Permissões**
   - Verificar chmod 755 nas pastas
   - Verificar proprietário www-data

2. **Upload Falha**
   - Verificar upload_max_filesize no php.ini
   - Verificar pasta temp/ existe e tem permissões

3. **API não Responde**
   - Verificar se Apache/Nginx está rodando
   - Verificar .htaccess (se aplicável)

4. **JWT Inválido**
   - Verificar JWT_SECRET no .env
   - Verificar se token não expirou (1h por padrão)

### Logs de Debug
```php
// Habilitar logs detalhados
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
```
