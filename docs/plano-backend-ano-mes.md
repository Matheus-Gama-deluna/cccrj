# Plano de Implementação: Reorganização do Backend por Ano/Mês

## 📋 Análise da Estrutura Atual

### Sistema Atual
- **LocalFileService**: Trabalha com pastas simples (`boletins/`, `reports/`)
- **Upload**: Salva arquivos diretamente em `data/boletins/` com nomes únicos
- **Listagem**: Usa `scandir()` em pasta única, não navega subpastas
- **Download**: Busca arquivo por nome na pasta base
- **Frontend**: `reports.js` carrega lista simples sem hierarquia

### Boletins Existentes
- **Estrutura Atual**: `data/boletins_site/2017/01_Janeiro/02-01-17.pdf`
- **Volume**: 365+ boletins organizados por ano/mês
- **Nomenclatura**:
  - Diários: `DD-MM-YY.pdf` (02-01-17.pdf)
  - Mensais: `Boletim_Mensal_[MÊS].pdf`

### Problemas Identificados
1. **Inconsistência**: Frontend usa estrutura ano/mês, mas backend usa pasta simples
2. **Upload**: Novos boletins vão para pasta simples, quebrando padrão
3. **Listagem**: Não navega pela estrutura de pastas existente
4. **Busca**: Limitada à pasta simples, ignora estrutura organizada

## 🎯 Objetivos da Reorganização

### Funcionalidades Desejadas
1. **Upload Inteligente**: Criar estrutura ano/mês baseada na data do boletim
2. **Listagem Hierárquica**: Navegar e filtrar por ano/mês
3. **Busca Avançada**: Incluir estrutura ano/mês nos resultados
4. **Compatibilidade**: Manter APIs existentes funcionando
5. **Migração**: Mover arquivos existentes para estrutura correta

### Nova Estrutura de Pastas
```
data/
├── boletins_site/          # Pasta principal dos boletins organizados
│   ├── 2017/              # Ano
│   │   ├── 01_Janeiro/    # Mês
│   │   │   ├── 02-01-17.pdf
│   │   │   ├── 03-01-17.pdf
│   │   │   └── Boletim_Mensal_JAN.pdf
│   │   ├── 02_Fevereiro/
│   │   └── ...
│   ├── 2018/
│   └── ...
├── boletins/              # Pasta legada (compatibilidade)
└── reports/               # Relatórios técnicos
```

## 🏗️ Arquitetura Proposta

### 1. LocalFileService Aprimorado
```php
class LocalFileService {
    // NOVO: Suporte à estrutura ano/mês
    public function uploadFileByDate($file, $title, $date, $type = 'boletins')
    public function listFilesByStructure($type = 'boletins', $year = null, $month = null)
    public function getFilePath($fileName, $type = 'boletins', $date = null)

    // MANTIDO: Compatibilidade com APIs existentes
    public function uploadFile($file, $title, $isBoletim = false)
    public function listFiles($type = 'reports')
}
```

### 2. APIs Atualizadas
```
APIs Existentes (compatibilidade):
├── /api/reports/list.php (mantém funcionalidade atual)
├── /api/reports/download.php (mantém funcionalidade atual)
└── /api/upload_report.php (mantém funcionalidade atual)

APIs Novas (funcionalidades ano/mês):
├── /api/boletins/list_by_year.php (listagem por ano)
├── /api/boletins/list_by_month.php (listagem por mês)
├── /api/boletins/upload_by_date.php (upload com data)
└── /api/boletins/migrate.php (migração automática)
```

### 3. Sistema de Metadados
```json
{
  "filename": "02-01-17.pdf",
  "relative_path": "2017/01_Janeiro/02-01-17.pdf",
  "full_path": "data/boletins_site/2017/01_Janeiro/02-01-17.pdf",
  "year": 2017,
  "month": 1,
  "month_name": "Janeiro",
  "date": "2017-01-02",
  "type": "diario|mensal|especial",
  "title": "Boletim Diário - 02 de Janeiro de 2017",
  "size": 111170,
  "modified": "2017-01-02T00:00:00Z"
}
```

## 📅 Cronograma de Implementação

### Fase 1: Fundamentos (1 semana)
1. **LocalFileService Aprimorado**
   - Adicionar métodos para estrutura ano/mês
   - Manter compatibilidade com APIs existentes
   - Criar funções de navegação hierárquica

2. **APIs de Listagem**
   - `/api/boletins/list_by_year.php` - Lista anos disponíveis
   - `/api/boletins/list_by_month.php` - Lista meses de um ano
   - `/api/boletins/list_by_date.php` - Lista boletins de mês específico

3. **Script de Migração**
   - Script para mover boletins existentes
   - Preservar estrutura atual como backup
   - Validar integridade dos arquivos

### Fase 2: Upload Inteligente (1 semana)
1. **Upload por Data**
   - `/api/boletins/upload_by_date.php`
   - Interface admin para inserir data
   - Validação de datas e estrutura de pastas

2. **Integração Frontend**
   - Atualizar `reports.js` para usar novas APIs
   - Adicionar navegação por ano/mês
   - Manter compatibilidade com visualização atual

3. **Sistema de Fallback**
   - Upload normal continua funcionando
   - Detecção automática de data pelo nome do arquivo
   - Criação automática de estrutura de pastas

### Fase 3: Busca e Navegação (1 semana)
1. **Busca Hierárquica**
   - Integrar estrutura ano/mês na busca
   - Filtros por ano/mês no frontend
   - Navegação breadcrumb

2. **Interface Avançada**
   - Seletor de ano/mês no admin
   - Visualização em árvore da estrutura
   - Filtros visuais por período

3. **Otimização**
   - Cache de estrutura de pastas
   - Indexação para busca rápida
   - Compressão de respostas JSON

## 💻 Implementação Detalhada

### 1. LocalFileService.php - Novos Métodos
```php
class LocalFileService {
    // NOVO: Upload com estrutura ano/mês
    public function uploadFileByDate($file, $title, $date, $type = 'boletins') {
        // Validar arquivo
        if (!$this->validateFile($file)) {
            throw new Exception('Arquivo inválido');
        }

        // Extrair ano/mês da data
        $year = date('Y', strtotime($date));
        $month = date('m', strtotime($date));
        $monthName = $this->getMonthName($month);

        // Criar estrutura de pastas
        $yearDir = $this->getBoletinsPath() . $year . '/';
        $monthDir = $yearDir . $month . '_' . $monthName . '/';

        if (!is_dir($monthDir)) {
            mkdir($monthDir, 0755, true);
        }

        // Gerar nome único
        $fileName = $this->generateUniqueFileName($title, $file['name']);

        // Mover arquivo
        $destinationPath = $monthDir . $fileName;
        if (!move_uploaded_file($file['tmp_name'], $destinationPath)) {
            throw new Exception('Falha ao mover arquivo');
        }

        return [
            'name' => $fileName,
            'relative_path' => $year . '/' . $month . '_' . $monthName . '/' . $fileName,
            'full_path' => $destinationPath,
            'year' => $year,
            'month' => $month,
            'date' => $date,
            'size' => $file['size']
        ];
    }

    // NOVO: Listagem hierárquica
    public function listFilesByStructure($type = 'boletins', $year = null, $month = null) {
        $baseDir = $this->getBoletinsPath();

        if ($year && $month) {
            // Listar mês específico
            $monthDir = $baseDir . $year . '/' . $month . '/';
            return $this->scanDirectoryRecursive($monthDir);
        } elseif ($year) {
            // Listar ano específico
            $yearDir = $baseDir . $year . '/';
            return $this->scanDirectoryRecursive($yearDir);
        } else {
            // Listar todos (hierárquico)
            return $this->scanYearsAndMonths($baseDir);
        }
    }

    // NOVO: Buscar arquivo na estrutura
    public function findFileInStructure($fileName, $type = 'boletins') {
        $baseDir = $this->getBoletinsPath();

        // Buscar recursivamente
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($baseDir)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getFilename() === $fileName) {
                return [
                    'full_path' => $file->getPathname(),
                    'relative_path' => str_replace($baseDir, '', $file->getPathname()),
                    'year' => basename(dirname(dirname($file->getPathname()))),
                    'month' => basename(dirname($file->getPathname())),
                    'size' => $file->getSize(),
                    'modified' => date('c', $file->getMTime())
                ];
            }
        }

        return null;
    }
}
```

### 2. API de Upload por Data
```php
// /api/boletins/upload_by_date.php
<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

try {
    require_once __DIR__ . '/../services/LocalFileService.php';

    // Validar parâmetros
    $file = $_FILES['file'] ?? null;
    $title = $_POST['title'] ?? '';
    $date = $_POST['date'] ?? ''; // YYYY-MM-DD
    $type = $_POST['type'] ?? 'boletins';

    if (!$file || !$date) {
        throw new Exception('Arquivo e data são obrigatórios');
    }

    $fileService = new LocalFileService();
    $result = $fileService->uploadFileByDate($file, $title, $date, $type);

    echo json_encode([
        'success' => true,
        'message' => 'Boletim enviado com sucesso!',
        'file' => $result
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
```

### 3. API de Listagem Hierárquica
```php
// /api/boletins/list_by_month.php
<?php
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../services/LocalFileService.php';

    $year = $_GET['year'] ?? null;
    $month = $_GET['month'] ?? null; // formato: 01_Janeiro
    $type = $_GET['type'] ?? 'boletins';

    $fileService = new LocalFileService();
    $files = $fileService->listFilesByStructure($type, $year, $month);

    echo json_encode([
        'success' => true,
        'data' => $files,
        'year' => $year,
        'month' => $month,
        'total' => count($files)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
```

### 4. Script de Migração
```php
// /api/boletins/migrate.php
<?php
header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../services/LocalFileService.php';

    $fileService = new LocalFileService();
    $baseDir = $fileService->getBoletinsPath();

    // Listar arquivos na pasta atual
    $currentFiles = $fileService->listFiles('boletins');
    $migrated = 0;
    $errors = [];

    foreach ($currentFiles as $file) {
        try {
            // Extrair data do nome do arquivo
            $date = $fileService->extractDateFromFilename($file['name']);

            if ($date) {
                // Recriar arquivo na nova estrutura
                $fileService->migrateFileToDateStructure($file['name'], $date, 'boletins');
                $migrated++;
            }
        } catch (Exception $e) {
            $errors[] = "Erro migrando {$file['name']}: " . $e->getMessage();
        }
    }

    echo json_encode([
        'success' => true,
        'migrated' => $migrated,
        'errors' => $errors,
        'message' => "Migração concluída: {$migrated} arquivos migrados"
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
```

## 🎨 Interface Frontend

### 1. Navegação por Ano/Mês
```javascript
// Novo componente de navegação
class BoletinsNavigation {
    constructor() {
        this.currentYear = null;
        this.currentMonth = null;
        this.init();
    }

    async init() {
        await this.loadYears();
        this.setupEventListeners();
    }

    async loadYears() {
        try {
            const response = await fetch('api/boletins/list_by_year.php?type=boletins');
            const data = await response.json();

            if (data.success) {
                this.renderYears(data.data);
            }
        } catch (error) {
            console.error('Erro carregando anos:', error);
        }
    }

    renderYears(years) {
        // Renderizar seletor de anos
        // Renderizar lista de meses do ano selecionado
        // Renderizar boletins do mês selecionado
    }
}
```

### 2. Seletor Visual
```
┌─────────────────────────────────────────────────┐
│ 📅 Selecionar Período                           │
├─────────────────────────────────────────────────┤
│ 📆 Ano: [2024 ▼] [2023] [2022] [2021] [2020]    │
│ 📅 Mês: [Todos ▼] [Janeiro] [Fevereiro] ...     │
├─────────────────────────────────────────────────┤
│ 🗂️ 2017                                        │
│   ├── 📁 Janeiro (22 boletins)                  │
│   ├── 📁 Fevereiro (19 boletins)                │
│   └── 📁 Março (21 boletins)                    │
├─────────────────────────────────────────────────┤
│ 📄 02-01-17.pdf    📅 02/01/2017    ⬇️ Download  │
│ 📄 03-01-17.pdf    📅 03/01/2017    ⬇️ Download  │
└─────────────────────────────────────────────────┘
```

## 🔄 Estratégia de Migração

### 1. Abordagem Gradual
1. **Backup**: Criar cópia da estrutura atual
2. **Migração**: Mover arquivos para nova estrutura
3. **Teste**: Validar todos os links e downloads
4. **Rollback**: Manter estrutura antiga como fallback

### 2. Detecção de Data
```php
// Extrair data do nome do arquivo
public function extractDateFromFilename($filename) {
    // Padrões: DD-MM-YY.pdf, YYYY-MM-DD.pdf, etc.
    $patterns = [
        '/(\d{2})-(\d{2})-(\d{2})\.pdf$/i',  // 02-01-17.pdf
        '/(\d{4})-(\d{2})-(\d{2})\.pdf$/i',  // 2017-01-02.pdf
        '/(\d{2})_(\d{2})_(\d{4})\.pdf$/i',   // 02_01_2017.pdf
    ];

    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $filename, $matches)) {
            if (strlen($matches[1]) === 2) {
                // Formato DD-MM-YY
                return "20{$matches[3]}-{$matches[2]}-{$matches[1]}";
            } else {
                // Formato YYYY-MM-DD
                return "{$matches[1]}-{$matches[2]}-{$matches[3]}";
            }
        }
    }

    return null; // Não conseguiu extrair data
}
```

### 3. Validação e Testes
```php
// Validar estrutura após migração
public function validateMigration() {
    $totalFiles = 0;
    $filesByYear = [];

    // Contar arquivos por ano/mês
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($this->getBoletinsPath())
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'pdf') {
            $path = $file->getPathname();
            $relativePath = str_replace($this->getBoletinsPath(), '', $path);

            // Validar estrutura
            if (preg_match('/^(\d{4})\/(\d{2}_[^\/]+)\/(.+\.pdf)$/', $relativePath, $matches)) {
                $year = $matches[1];
                $filesByYear[$year] = ($filesByYear[$year] ?? 0) + 1;
                $totalFiles++;
            }
        }
    }

    return [
        'total_files' => $totalFiles,
        'years' => $filesByYear,
        'is_valid' => $totalFiles > 0
    ];
}
```

## 📊 Considerações Técnicas

### Performance
- **Cache**: Cache da estrutura de pastas por 1 hora
- **Indexação**: Índice por ano/mês para busca rápida
- **Lazy Loading**: Carregar boletins sob demanda
- **Compressão**: Compactar respostas JSON

### Compatibilidade
- **APIs Legadas**: Continuam funcionando
- **URLs Antigas**: Redirect para nova estrutura
- **Frontend Atual**: Funciona com fallbacks
- **Admin Panel**: Interface atual + nova

### Segurança
- **Validação**: Sanitizar nomes de pastas e arquivos
- **Permissões**: Controle de acesso às pastas
- **Backup**: Backup automático antes da migração
- **Rollback**: Script para reverter migração

## 🎯 Critérios de Sucesso

### Funcionais
- ✅ Upload cria estrutura ano/mês automaticamente
- ✅ Listagem navega pela estrutura hierárquica
- ✅ Download encontra arquivos na estrutura correta
- ✅ Busca inclui filtros por ano/mês
- ✅ APIs antigas continuam funcionando

### De Performance
- ✅ Listagem < 1s para anos
- ✅ Listagem < 2s para meses
- ✅ Upload < 5s independente do tamanho
- ✅ Busca < 2s com filtros

### De Usabilidade
- ✅ Interface intuitiva de navegação
- ✅ Filtros visuais funcionais
- ✅ Responsividade completa
- ✅ Acessibilidade WCAG 2.1

## 🚀 Implementação Prioritária

### Semana 1: Fundamentos
1. **LocalFileService** com métodos ano/mês
2. **APIs de listagem** hierárquica
3. **Script de migração** dos arquivos existentes

### Semana 2: Upload e Interface
1. **API de upload** por data
2. **Frontend atualizado** com navegação
3. **Testes e validação** completa

### Semana 3: Otimização
1. **Performance** e cache
2. **Interface admin** aprimorada
3. **Documentação** e testes finais

---

*Este plano será implementado mantendo total compatibilidade com o sistema existente, permitindo uma migração gradual e segura para a nova estrutura ano/mês.*
