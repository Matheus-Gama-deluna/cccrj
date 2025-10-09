<?php
// api/models_json/ArchiveItemJson.php

require_once __DIR__ . '/JsonModel.php';

class ArchiveItemJson extends JsonModel {
    public function __construct() {
        parent::__construct(__DIR__ . '/../../data/content/archive.json');
    }
}
?>