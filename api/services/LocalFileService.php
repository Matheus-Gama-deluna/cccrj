<?php
class LocalFileService {
    private $basePath;
    private $reportsPath;
    private $boletinsPath;
    private $boletinsLegacyPath;
    private $tempPath;
    private $allowedExtensions;
    private $maxFileSize;
    private $metadataPath;
    private $metadataFile;
    private $boletinsBasePath;
    private $metadata;

    public function __construct() {
        $this->basePath = __DIR__ . '/../../data/';
        $this->reportsPath = $this->basePath . 'reports/';
        $this->boletinsPath = $this->basePath . 'boletins/';  // Usar apenas boletins/
        $this->boletinsLegacyPath = $this->basePath . 'boletins/'; // Mesma pasta
        $this->tempPath = __DIR__ . '/../../temp/';
        $this->allowedExtensions = ['pdf'];
        $this->maxFileSize = 10 * 1024 * 1024; // 10MB
        $this->metadataPath = $this->basePath . 'metadata/';
        $this->metadataFile = $this->metadataPath . 'boletins_metadata.json';
        $this->boletinsBasePath = rtrim($this->normalizePath($this->boletinsPath), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->metadata = null;

        $this->ensureDirectoriesExist();
        $this->loadMetadata();
    }

    private function validateFile($file) {
        // Verificar se o arquivo foi enviado corretamente
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        // Verificar tamanho do arquivo (máximo 10MB)
        if ($file['size'] > $this->maxFileSize) {
            return false;
        }

        // Verificar extensão
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedExtensions)) {
            return false;
        }

        return true;
    }

    private function generateUniqueFileName($title, $originalName) {
        // Remover caracteres especiais e espaços do título
        $cleanTitle = preg_replace('/[^a-zA-Z0-9]/', '_', $title);
        $cleanTitle = substr($cleanTitle, 0, 50); // Limitar tamanho

        // Obter extensão do arquivo original
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);

        // Gerar nome único com timestamp
        $timestamp = date('Ymd_His');
        $uniqueId = substr(uniqid(), -6);

        return "{$cleanTitle}_{$timestamp}_{$uniqueId}.{$extension}";
    }

    private function ensureDirectoriesExist() {
        $directories = [$this->basePath, $this->reportsPath, $this->boletinsPath, $this->tempPath, $this->metadataPath];
        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }

    public function listFiles($type = 'reports') {
        if ($type === 'boletins') {
            return $this->getMetadataEntries($type);
        }

        $directory = $this->reportsPath;
        if (!is_dir($directory)) {
            return [];
        }

        $files = scandir($directory);
        $files = array_filter($files, function($file) {
            return !in_array($file, ['.', '..']) && pathinfo($file, PATHINFO_EXTENSION) === 'pdf';
        });

        $fileInfo = [];
        foreach ($files as $file) {
            $filePath = $directory . $file;
            $fileInfo[] = [
                'name' => $file,
                'path' => $filePath,
                'size' => filesize($filePath),
                'modified' => date('c', filemtime($filePath)),
                'url' => '/api/reports/download?file=' . urlencode($file) . '&type=' . $type
            ];
        }

        usort($fileInfo, function($a, $b) {
            return strtotime($b['modified']) - strtotime($a['modified']);
        });

        return $fileInfo;
    }

    public function listFilesByStructure($type = 'boletins', $year = null, $month = null) {
        if ($type !== 'boletins') {
            return $this->listFiles($type);
        }

        $metadata = $this->getMetadataEntries($type);

        if ($year && $month) {
            return array_values(array_filter($metadata, function ($entry) use ($year, $month) {
                return $entry['year'] === (string) $year && $entry['month_dir'] === $month;
            }));
        } elseif ($year) {
            $result = [];
            foreach ($metadata as $entry) {
                if ($entry['year'] === (string) $year) {
                    $monthKey = $entry['month_dir'];
                    if (!isset($result[$monthKey])) {
                        $result[$monthKey] = [
                            'count' => 0,
                            'files' => []
                        ];
                    }
                    $result[$monthKey]['count']++;
                    $result[$monthKey]['files'][] = $entry;
                }
            }
            return [$year => $result];
        }

        $structure = [];
        foreach ($metadata as $entry) {
            $yearKey = $entry['year'];
            $monthKey = $entry['month_dir'];

            if (!isset($structure[$yearKey])) {
                $structure[$yearKey] = [];
            }

            if (!isset($structure[$yearKey][$monthKey])) {
                $structure[$yearKey][$monthKey] = [
                    'count' => 0,
                    'files' => []
                ];
            }

            $structure[$yearKey][$monthKey]['count']++;
            $structure[$yearKey][$monthKey]['files'][] = $entry;
        }

        return $structure;
    }

    public function findFileInStructure($fileName, $type = 'boletins') {
        if ($type !== 'boletins') {
            $files = $this->listFiles($type);
            foreach ($files as $file) {
                if ($file['name'] === $fileName) {
                    return $file;
                }
            }
            return null;
        }

        $metadata = $this->getMetadataEntries($type);

        foreach ($metadata as $entry) {
            if ($entry['name'] === $fileName) {
                return $this->metadataEntryToFileInfo($entry);
            }
        }

        return null;
    }

    public function downloadFile($fileName, $type = 'reports') {
        $directory = $type === 'boletins' ? $this->boletinsPath : $this->reportsPath;
        $filePath = $directory . basename($fileName);

        if (!file_exists($filePath)) {
            if ($type === 'boletins') {
                $foundFile = $this->findFileInStructure($fileName, $type);
                if ($foundFile) {
                    $fullPath = $foundFile['full_path'] ?? $this->buildFullPathFromRelative($foundFile['relative_path'] ?? $fileName);
                    return $this->normalizePath($fullPath);
                }
            }
            throw new Exception('Arquivo não encontrado');
        }

        return $filePath;
    }

    public function resolveBoletimPath(?string $relativeDirectory, string $fileName) {
        if (empty($relativeDirectory)) {
            return null;
        }

        $relativeDirectory = $this->normalizeRelativePath($relativeDirectory);
        $relativePath = rtrim($relativeDirectory, '/') . '/' . $fileName;

        $fullPath = $this->buildFullPathFromRelative($relativePath);

        return file_exists($fullPath) ? $fullPath : null;
    }

    public function uploadFile($file, $title, $isBoletim = false) {
        if (!$this->validateFile($file)) {
            throw new Exception('Arquivo inválido');
        }

        $fileName = $this->generateUniqueFileName($title, $file['name']);
        $relativePath = $fileName;
        $destinationPath = $this->reportsPath . $fileName;
        $date = null;
        $year = null;
        $month = null;
        $monthName = null;

        if ($isBoletim) {
            $date = $this->extractDateFromFilename($file['name']) ?? $this->convertToDDMMYYYY(date('Y-m-d'));
            $year = date('Y', strtotime($date));
            $month = date('m', strtotime($date));
            $monthName = $this->getMonthName($month);

            $relativePath = $year . '/' . $month . '_' . $monthName . '/' . $fileName;
            $destinationDir = $this->boletinsPath . $year . '/';
            if (!is_dir($destinationDir)) {
                mkdir($destinationDir, 0755, true);
            }

            $monthDir = $destinationDir . $month . '_' . $monthName . '/';
            if (!is_dir($monthDir)) {
                mkdir($monthDir, 0755, true);
            }

            $destinationPath = $monthDir . $fileName;
        }

        if (!move_uploaded_file($file['tmp_name'], $destinationPath)) {
            throw new Exception('Falha ao mover arquivo');
        }

        if ($isBoletim) {
            $entry = $this->buildMetadataEntry([
                'name' => $fileName,
                'relative_path' => $relativePath,
                'full_path' => $destinationPath,
                'size' => $file['size'],
                'modified' => date('c'),
                'date' => $date,
                'year' => $year,
                'month_number' => $month,
                'month' => $monthName,
                'month_dir' => $month . '_' . $monthName,
            ]);

            $this->removeMetadataEntry(null, $relativePath);
            $this->appendMetadataEntry($entry);
        }

        return [
            'name' => $fileName,
            'relative_path' => $relativePath,
            'full_path' => $destinationPath,
            'size' => $file['size'],
            'uploaded_at' => date('Y-m-d H:i:s'),
            'year' => $year,
            'month' => $month,
            'month_name' => $monthName,
            'date' => $date
        ];
    }

    public function uploadFileByDate($file, $title, $dateString, $type = 'boletins') {
        if (!$this->validateFile($file)) {
            throw new Exception('Arquivo inválido');
        }

        // Converter data para DD-MM-YYYY
        $date = $this->convertToDDMMYYYY($dateString);
        $year = date('Y', strtotime($dateString));
        $month = date('m', strtotime($dateString));
        $monthName = $this->getMonthName($month);

        $fileName = $this->generateUniqueFileName($title, $file['name']);
        $relativePath = $year . '/' . $month . '_' . $monthName . '/' . $fileName;

        // Criar diretórios
        $destinationDir = $this->boletinsPath . $year . '/';
        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        $monthDir = $destinationDir . $month . '_' . $monthName . '/';
        if (!is_dir($monthDir)) {
            mkdir($monthDir, 0755, true);
        }

        $destinationPath = $monthDir . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $destinationPath)) {
            throw new Exception('Falha ao mover arquivo');
        }

        // Criar entrada de metadata
        $entry = $this->buildMetadataEntry([
            'name' => $fileName,
            'relative_path' => $relativePath,
            'full_path' => $destinationPath,
            'size' => $file['size'],
            'modified' => date('c'),
            'date' => $date,
            'year' => $year,
            'month_number' => $month,
            'month' => $monthName,
            'month_dir' => $month . '_' . $monthName,
        ]);

        // Adicionar ao metadata usando o sistema incremental
        $this->appendMetadataEntry($entry);

        return [
            'name' => $fileName,
            'relative_path' => $relativePath,
            'full_path' => $destinationPath,
            'size' => $file['size'],
            'uploaded_at' => date('Y-m-d H:i:s'),
            'year' => $year,
            'month' => $month,
            'month_name' => $monthName,
            'date' => $date
        ];
    }

    private function extractDateFromFilename($filename) {
        $patterns = [
            '/(\d{4})-(\d{2})-(\d{2})\.pdf$/i',  // YYYY-MM-DD
            '/(\d{2})-(\d{2})-(\d{4})\.pdf$/i',  // DD-MM-YYYY
            '/(\d{2})_(\d{2})_(\d{4})\.pdf$/i',  // DD_MM_YYYY
            '/(\d{2})-(\d{2})-(\d{2})\.pdf$/i',  // DD-MM-YY (novo para 2025)
            '/(\d{1,2})-(\d{1,2})-(\d{2})\.pdf$/i', // D-M-YY (novo para 2025)
            '/(\d{1,2})\.(\d{1,2})\.(\d{2})\.pdf$/i', // D.M.YY
            '/(\d{2})\.(\d{2})\.(\d{2})\.pdf$/i', // DD.MM.YY
            '/(\d{3,4})-(\d{2})\.pdf$/i', // YY-MM ou YYYY-MM (3101-25)
            '/(\d{2})(\d{2})-(\d{2})\.pdf$/i', // DDMM-YY (3101-25)
            '/(\d{1,2})-(\d{1,2})\.(\d{2})\.pdf$/i', // D-M.YY
            '/(\d{2})-(\d{1,2})\.(\d{2})\.pdf$/i', // DD-M.YY
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $filename, $matches)) {
                $groups = array_slice($matches, 1);

                if (count($groups) >= 2) {
                    $date = $this->convertGroupsToDate($groups);
                    if ($date && $this->isValidDate($date)) {
                        // NOVO: Retorna sempre no formato DD-MM-YYYY
                        return $this->convertToDDMMYYYY($date);
                    }
                }
            }
        }
        return null;
    }

    private function extractDayFromFilename($filename) {
        // Extrair apenas o dia do nome do arquivo
        $patterns = [
            '/(\d{2})-(\d{2})-(\d{2,4})\.pdf$/i',  // DD-MM-YY ou DD-MM-YYYY
            '/(\d{1,2})\.(\d{1,2})\.(\d{2,4})\.pdf$/i', // D.M.YY
            '/(\d{2})\.(\d{2})\.(\d{2,4})\.pdf$/i', // DD.MM.YY
            '/(\d{2})(\d{2})-(\d{2,4})\.pdf$/i', // DDMM-YY
            '/(\d{1,2})-(\d{1,2})\.(\d{2,4})\.pdf$/i', // D-M.YY
            '/(\d{2})-(\d{1,2})\.(\d{2,4})\.pdf$/i', // DD-M.YY
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $filename, $matches)) {
                $groups = array_slice($matches, 1);

                // Retorna o primeiro grupo como dia
                if (count($groups) >= 1 && is_numeric($groups[0])) {
                    $day = intval($groups[0]);
                    if ($day >= 1 && $day <= 31) {
                        return str_pad($day, 2, '0', STR_PAD_LEFT);
                    }
                }
            }
        }
        return null;
    }

    private function buildDateFromStructure($fileName, $year, $monthNumber, $monthName) {
        // 1. Tentar extrair data completa do nome do arquivo
        $extractedDate = $this->extractDateFromFilename($fileName);

        if ($extractedDate) {
            // Converter para DD-MM-YYYY se necessário
            return $this->convertToDDMMYYYY($extractedDate);
        }

        // 2. Para arquivos antigos: combinar estrutura + dia do nome
        $dayFromFile = $this->extractDayFromFilename($fileName);
        if ($dayFromFile) {
            return "{$dayFromFile}-{$monthNumber}-{$year}";
        }

        // 3. Fallback: primeiro dia do mês
        return "01-{$monthNumber}-{$year}";
    }

    public function uploadFileByDate($file, $title, $dateString, $type = 'boletins') {
        if (!$this->validateFile($file)) {
            throw new Exception('Arquivo inválido');
        }

        // Converter data para DD-MM-YYYY
        $date = $this->convertToDDMMYYYY($dateString);
        $year = date('Y', strtotime($dateString));
        $month = date('m', strtotime($dateString));
        $monthName = $this->getMonthName($month);

        $fileName = $this->generateUniqueFileName($title, $file['name']);
        $relativePath = $year . '/' . $month . '_' . $monthName . '/' . $fileName;

        // Criar diretórios
        $destinationDir = $this->boletinsPath . $year . '/';
        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        $monthDir = $destinationDir . $month . '_' . $monthName . '/';
        if (!is_dir($monthDir)) {
            mkdir($monthDir, 0755, true);
        }

        $destinationPath = $monthDir . $fileName;

        if (!move_uploaded_file($file['tmp_name'], $destinationPath)) {
            throw new Exception('Falha ao mover arquivo');
        }

        // Criar entrada de metadata
        $entry = $this->buildMetadataEntry([
            'name' => $fileName,
            'relative_path' => $relativePath,
            'full_path' => $destinationPath,
            'size' => $file['size'],
            'modified' => date('c'),
            'date' => $date,
            'year' => $year,
            'month_number' => $month,
            'month' => $monthName,
            'month_dir' => $month . '_' . $monthName,
        ]);

        // Adicionar ao metadata usando o sistema incremental
        $this->appendMetadataEntry($entry);

        return [
            'name' => $fileName,
            'relative_path' => $relativePath,
            'full_path' => $destinationPath,
            'size' => $file['size'],
            'uploaded_at' => date('Y-m-d H:i:s'),
            'year' => $year,
            'month' => $month,
            'month_name' => $monthName,
            'date' => $date
        ];
    }

    private function convertGroupsToDate($groups) {
        if (count($groups) === 3) {
            if (strlen($groups[0]) === 4) {
                // Formato YYYY-MM-DD
                return "{$groups[0]}-{$groups[1]}-{$groups[2]}";
            } elseif (strlen($groups[2]) === 2) {
                // Formato DD-MM-YY
                $year = $this->convertYear($groups[2]);
                return "{$year}-{$groups[1]}-{$groups[0]}";
            } elseif (strlen($groups[2]) === 4) {
                // Formato DD-MM-YYYY
                return "{$groups[2]}-{$groups[1]}-{$groups[0]}";
            }
        } elseif (count($groups) === 2) {
            // Formato YY-MM - assumir primeiro dia do mês
            if (strlen($groups[0]) === 2) {
                $year = $this->convertYear($groups[0]);
                return "{$year}-{$groups[1]}-01";
            } elseif (strlen($groups[0]) === 3 || strlen($groups[0]) === 4) {
                // Formato YYYY-MM ou YY-MM (3101-25 -> 2025-01-31)
                if (strlen($groups[0]) === 3) {
                    // DDMM -> DD-MM
                    $day = substr($groups[0], 0, 1);
                    $month = substr($groups[0], 1, 2);
                } else {
                    // DDMM -> DD-MM
                    $day = substr($groups[0], 0, 2);
                    $month = substr($groups[0], 2, 2);
                }

                // Validar dia e mês
                if ($day >= 1 && $day <= 31 && $month >= 1 && $month <= 12) {
                    $year = $this->convertYear($groups[1]);
                    return "{$year}-{$month}-{$day}";
                }
            } elseif (strlen($groups[1]) === 2) {
                // Formato D-M.YY ou DD-M.YY
                if (strlen($groups[0]) === 1) {
                    // D-M.YY -> DD-M-YY
                    $day = "0{$groups[0]}";
                } else {
                    // DD-M.YY
                    $day = $groups[0];
                }
                $year = $this->convertYear($groups[1]);
                return "{$year}-{$groups[1]}-{$day}";
            } else {
                // Formato YYYY-MM
                return "{$groups[0]}-{$groups[1]}-01";
            }
        }

        return null;
    }

    private function convertYear($yearYY) {
        $year = intval($yearYY);
        // Se o ano for menor que 25, considerar século 1900
        // Se for maior que 25, considerar século 2000
        return ($year < 25) ? "19{$year}" : "20{$year}";
    }

    private function isValidDate($dateString) {
        $timestamp = strtotime($dateString);
        if (!$timestamp) return false;

        // Verificar se a data não é muito no passado (antes de 1900) nem muito no futuro (após 2026)
        $date = getdate($timestamp);
        $currentYear = intval(date('Y'));

        return ($date['year'] >= 1900 && $date['year'] <= $currentYear + 2);
    }

    private function convertToDDMMYYYY($dateString) {
        // Converter YYYY-MM-DD para DD-MM-YYYY
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $dateString, $matches)) {
            return "{$matches[3]}-{$matches[2]}-{$matches[1]}";
        }
        return $dateString; // Retorna original se formato inválido
    }

    private function getFileHash($filePath) {
        if (!file_exists($filePath)) {
            return null;
        }
        return hash_file('sha256', $filePath);
    }

    private function isFileModified($filePath, $existingEntry) {
        $currentHash = $this->getFileHash($filePath);
        $storedHash = $existingEntry['hash'] ?? null;

        // Se não há hash armazenado, considerar como modificado
        if ($storedHash === null) {
            return true;
        }

        // Se o hash atual é diferente do armazenado, arquivo foi modificado
        return $currentHash !== $storedHash;
    }

    private function indexEntriesByPath() {
        $this->loadMetadata();
        $index = [];

        if (!isset($this->metadata['entries']) || !is_array($this->metadata['entries'])) {
            return $index;
        }

        foreach ($this->metadata['entries'] as $entry) {
            $relativePath = $entry['relative_path'] ?? '';
            if (!empty($relativePath)) {
                $index[$relativePath] = $entry;
            }
        }

        return $index;
    }

    private function findFilesToProcess() {
        $filesToProcess = [];
        $existingEntries = $this->indexEntriesByPath();

        // Percorre apenas a pasta de boletins (única)
        $searchPaths = [$this->boletinsBasePath];

        foreach ($searchPaths as $basePath) {
            if (!is_dir($basePath)) {
                continue;
            }

            $yearIterator = new DirectoryIterator($basePath);

            foreach ($yearIterator as $yearDir) {
                if (!$yearDir->isDir() || $yearDir->isDot() || !preg_match('/^\d{4}$/', $yearDir->getFilename())) {
                    continue;
                }

                $year = $yearDir->getFilename();
                $monthIterator = new DirectoryIterator($yearDir->getPathname());

                foreach ($monthIterator as $monthDir) {
                    if (!$monthDir->isDir() || $monthDir->isDot() || !preg_match('/^\d{2}_/', $monthDir->getFilename())) {
                        continue;
                    }

                    $fileIterator = new DirectoryIterator($monthDir->getPathname());

                    foreach ($fileIterator as $file) {
                        if (!$file->isFile() || strtolower($file->getExtension()) !== 'pdf') {
                            continue;
                        }

                        $fileName = $file->getFilename();

                        // Filtrar arquivos que não são boletins reais
                        if (!$this->isValidBoletimFile($fileName)) {
                            continue;
                        }

                        $relativePath = $year . '/' . $monthDir->getFilename() . '/' . $fileName;

                        // Verificar se é novo ou foi modificado
                        if (!isset($existingEntries[$relativePath]) ||
                            $this->isFileModified($file->getPathname(), $existingEntries[$relativePath])) {

                            $filesToProcess[] = [
                                'file' => $file,
                                'relative_path' => $relativePath,
                                'full_path' => $file->getPathname(),
                                'year' => $year,
                                'month_dir' => $monthDir->getFilename(),
                                'month_number' => substr($monthDir->getFilename(), 0, 2),
                                'month_name' => substr($monthDir->getFilename(), 3),
                            ];
                        }
                    }
                }
            }
        }

        return $filesToProcess;
    }

    private function updateMetadata() {
        $this->loadMetadata();
        $existingEntries = $this->indexEntriesByPath();
        $updated = false;

        // Processa apenas arquivos que precisam ser atualizados
        $filesToProcess = $this->findFilesToProcess();

        if (empty($filesToProcess)) {
            // Nenhum arquivo para processar
            return $this->metadata;
        }

        foreach ($filesToProcess as $fileInfo) {
            $relativePath = $fileInfo['relative_path'];

            // Determinar se é novo ou atualização
            $isNew = !isset($existingEntries[$relativePath]);
            $isModified = !$isNew && $this->isFileModified($fileInfo['full_path'], $existingEntries[$relativePath]);

            if ($isNew || $isModified) {
                // Construir entrada de metadata
                $date = $this->buildDateFromStructure(
                    $fileInfo['file']->getFilename(),
                    $fileInfo['year'],
                    $fileInfo['month_number'],
                    $fileInfo['month_name']
                ) ?? $this->convertToDDMMYYYY(date('Y-m-d', $fileInfo['file']->getMTime()));

                $entry = $this->buildMetadataEntry([
                    'name' => $fileInfo['file']->getFilename(),
                    'relative_path' => $relativePath,
                    'full_path' => $fileInfo['full_path'],
                    'size' => $fileInfo['file']->getSize(),
                    'modified' => date('c', $fileInfo['file']->getMTime()),
                    'date' => $date,
                    'year' => $fileInfo['year'],
                    'month_number' => $fileInfo['month_number'],
                    'month' => $fileInfo['month_name'],
                    'month_dir' => $fileInfo['month_dir'],
                ]);

                // Atualizar index
                $existingEntries[$relativePath] = $entry;
                $updated = true;
            }
        }

        // Salvar apenas se houver mudanças
        if ($updated) {
            $this->metadata['entries'] = array_values($existingEntries);
            $this->metadata['total'] = count($this->metadata['entries']);
            $this->metadata['updated_at'] = date('c');
            $this->metadata['update_type'] = 'incremental';
            $this->saveMetadata();
        }

        return $this->metadata;
    }

    private function getMonthName($monthNumber) {
        $months = [
            '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
            '04' => 'Abril', '05' => 'Maio', '06' => 'Junho',
            '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro',
            '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
        ];
        return $months[$monthNumber] ?? 'Desconhecido';
    }

    private function loadMetadata() {
        if (!file_exists($this->metadataFile)) {
            $this->regenerateMetadata();
            return;
        }

        $raw = file_get_contents($this->metadataFile);
        $decoded = json_decode($raw, true);

        if (!is_array($decoded) || !isset($decoded['entries'])) {
            $this->regenerateMetadata();
            return;
        }

        $this->metadata = array_merge(
            ['updated_at' => null, 'total' => 0, 'entries' => []],
            $decoded
        );
    }

    private function saveMetadata() {
        if ($this->metadata === null) {
            $this->metadata = ['updated_at' => null, 'total' => 0, 'entries' => []];
        }

        $this->metadata['updated_at'] = date('c');
        $this->metadata['total'] = count($this->metadata['entries']);

        file_put_contents(
            $this->metadataFile,
            json_encode($this->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }

    private function regenerateAllMetadata() {
        $entries = [];

        // Usa apenas a pasta principal de boletins
        $searchPaths = [$this->boletinsBasePath];

        foreach ($searchPaths as $basePath) {
            if (!is_dir($basePath)) {
                continue;
            }

            $yearIterator = new DirectoryIterator($basePath);

            foreach ($yearIterator as $yearDir) {
                if (!$yearDir->isDir() || $yearDir->isDot() || !preg_match('/^\d{4}$/', $yearDir->getFilename())) {
                    continue;
                }

                $year = $yearDir->getFilename();
                $monthIterator = new DirectoryIterator($yearDir->getPathname());

                foreach ($monthIterator as $monthDir) {
                    if (!$monthDir->isDir() || $monthDir->isDot() || !preg_match('/^\d{2}_/', $monthDir->getFilename())) {
                        continue;
                    }

                    $monthDirName = $monthDir->getFilename();
                    $monthNumber = substr($monthDirName, 0, 2);
                    $monthName = substr($monthDirName, 3);

                    $fileIterator = new DirectoryIterator($monthDir->getPathname());

                    foreach ($fileIterator as $file) {
                        if (!$file->isFile() || strtolower($file->getExtension()) !== 'pdf') {
                            continue;
                        }

                        $fileName = $file->getFilename();

                        // Filtrar arquivos que não são boletins reais
                        if ($this->isValidBoletimFile($fileName)) {
                            $relativePath = $year . '/' . $monthDirName . '/' . $fileName;
                            $date = $this->buildDateFromStructure($fileName, $year, $monthNumber, $monthName) ?? $this->convertToDDMMYYYY(date('Y-m-d', $file->getMTime()));

                            $entries[$relativePath] = $this->buildMetadataEntry([
                                'name' => $fileName,
                                'relative_path' => $relativePath,
                                'full_path' => $file->getPathname(),
                                'size' => $file->getSize(),
                                'modified' => date('c', $file->getMTime()),
                                'date' => $date,
                                'year' => $year,
                                'month_number' => $monthNumber,
                                'month' => $monthName,
                                'month_dir' => $monthDirName,
                            ]);
                        }
                    }
                }
            }
        }

        $this->metadata = [
            'updated_at' => date('c'),
            'total' => count($entries),
            'entries' => $this->sortMetadataEntries(array_values($entries))
        ];

        $this->saveMetadata();
    }

    public function regenerateMetadata($forceFull = false) {
        // Wrapper inteligente: decide entre completo ou incremental

        if ($forceFull || !file_exists($this->metadataFile)) {
            // Regeneração completa
            $this->regenerateAllMetadata();
            return $this->metadata;
        }

        // Atualização incremental
        return $this->updateMetadata();
    }

    public function regenerateBoletinsMetadata() {
        $this->regenerateMetadata();
        return $this->metadata;
    }

    private function isValidBoletimFile($fileName) {
        // Filtrar arquivos que não são boletins reais
        $invalidPatterns = [
            '/^boletim\.pdf$/i',
            '/^Boletim_Mensal_/i',
            '/\.tmp$/i',
            '/\.bak$/i',
            '/\.old$/i',
        ];

        foreach ($invalidPatterns as $pattern) {
            if (preg_match($pattern, $fileName)) {
                return false;
            }
        }

        return true;
    }

    private function getMetadataEntries($type = 'boletins') {
        if ($type !== 'boletins') {
            return $this->listFiles($type);
        }

        if ($this->metadata === null) {
            $this->regenerateMetadata();
        }

        if (!is_array($this->metadata) || !isset($this->metadata['entries'])) {
            $this->metadata = ['updated_at' => null, 'total' => 0, 'entries' => []];
        }

        $this->metadata['entries'] = array_map(fn($entry) => $this->ensureMetadataEntry($entry), $this->metadata['entries']);

        return $this->metadata['entries'];
    }

    private function buildMetadataEntry(array $data) {
        $year = (string) ($data['year'] ?? date('Y'));
        $monthNumber = str_pad((string) ($data['month_number'] ?? date('m')), 2, '0', STR_PAD_LEFT);
        $monthName = $data['month'] ?? $this->getMonthName($monthNumber);
        $monthDir = $data['month_dir'] ?? ($monthNumber . '_' . $monthName);
        $date = $data['date'] ?? $this->convertToDDMMYYYY(date('Y-m-d'));
        $relativePath = $this->normalizeRelativePath($data['relative_path'] ?? ($year . '/' . $monthDir . '/' . $data['name']));
        $relativeDirectory = $this->getRelativeDirectory($relativePath);
        $fullPath = $data['full_path'] ?? $this->buildFullPathFromRelative($relativePath);

        return $this->ensureMetadataEntry([
            'name' => $data['name'],
            'relative_path' => $relativePath,
            'full_path' => $fullPath,
            'size' => $data['size'],
            'modified' => $data['modified'],
            'date' => $date,
            'year' => $year,
            'month_number' => $monthNumber,
            'month' => $monthName,
            'month_dir' => $monthDir,
            'relative_directory' => $relativeDirectory,
            'url' => $this->buildDownloadUrl($data['name'], $relativeDirectory),
            'hash' => $data['hash'] ?? $this->getFileHash($fullPath),
            'file_version' => $data['file_version'] ?? 1,
        ]);
    }

    private function sortMetadataEntries(array $entries) {
        usort($entries, function ($a, $b) {
            $aDate = $a['date'] ?? $a['modified'] ?? '1970-01-01';
            $bDate = $b['date'] ?? $b['modified'] ?? '1970-01-01';

            $timeDiff = strtotime($bDate) - strtotime($aDate);
            if ($timeDiff !== 0) {
                return $timeDiff;
            }

            return strcmp($b['name'], $a['name']);
        });

        return $entries;
    }

    private function metadataEntryToFileInfo(array $entry) {
        $entry = $this->ensureMetadataEntry($entry);

        return [
            'name' => $entry['name'],
            'path' => $entry['full_path'],
            'relative_path' => $entry['relative_path'],
            'relative_directory' => $entry['relative_directory'] ?? $this->getRelativeDirectory($entry['relative_path']),
            'year' => $entry['year'],
            'month' => $entry['month'],
            'month_number' => $entry['month_number'],
            'size' => $entry['size'],
            'modified' => $entry['modified'],
            'date' => $entry['date'],
            'url' => $entry['url']
        ];
    }

    private function appendMetadataEntry(array $entry) {
        if ($this->metadata === null) {
            $this->loadMetadata();
        }

        if (!isset($this->metadata['entries']) || !is_array($this->metadata['entries'])) {
            $this->metadata['entries'] = [];
        }

        $this->metadata['entries'][] = $entry;
        $this->metadata['entries'] = $this->sortMetadataEntries($this->metadata['entries']);
        $this->saveMetadata();
    }

    private function ensureMetadataEntry(array $entry) {
        if (!isset($entry['name'])) {
            return $entry;
        }

        $entry['relative_path'] = $this->normalizeRelativePath($entry['relative_path'] ?? $entry['name']);

        if (empty($entry['full_path']) && !empty($entry['relative_path'])) {
            $entry['full_path'] = $this->buildFullPathFromRelative($entry['relative_path']);
        } elseif (!empty($entry['full_path'])) {
            $entry['full_path'] = $this->normalizePath($entry['full_path']);
        }

        if (empty($entry['date']) && !empty($entry['modified'])) {
            $entry['date'] = $this->convertToDDMMYYYY(date('Y-m-d', strtotime($entry['modified'])));
        }

        return $entry;
    }

    private function normalizeRelativePath(?string $path) {
        if (!$path) {
            return '';
        }

        $path = str_replace('\\', '/', $path);
        $path = preg_replace('#/+#', '/', $path);
        $path = ltrim($path, '/');

        return $path;
    }

    private function buildFullPathFromRelative(string $relativePath) {
        $relativePath = $this->normalizeRelativePath($relativePath);
        $fullPath = $this->boletinsBasePath . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);

        return $this->normalizePath($fullPath);
    }

    private function getRelativeDirectory(string $relativePath) {
        $relativePath = $this->normalizeRelativePath($relativePath);

        if ($relativePath === '') {
            return '';
        }

        $parts = explode('/', $relativePath);
        array_pop($parts);

        return implode('/', $parts);
    }

    private function buildDownloadUrl(string $fileName, ?string $relativeDirectory) {
        $query = [
            'file' => $fileName,
            'type' => 'boletins'
        ];

        if (!empty($relativeDirectory)) {
            $query['path'] = $relativeDirectory;
        }

        return '/api/reports/download.php?' . http_build_query($query);
    }

    private function normalizePath(?string $path) {
        if (!$path) {
            return $path;
        }

        $normalized = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        $real = realpath($normalized);

        return $real ?: $normalized;
    }

    private function removeMetadataEntry($fileName) {
        if ($this->metadata === null || !isset($this->metadata['entries'])) {
            return;
        }

        $this->metadata['entries'] = array_values(array_filter(
            $this->metadata['entries'],
            function ($entry) use ($fileName) {
                return $entry['name'] !== $fileName;
            }
        ));

        $this->saveMetadata();
    }
}
?>
