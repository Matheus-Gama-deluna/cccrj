<?php
// api/models_json/CrmcItemJson.php

require_once __DIR__ . '/JsonModel.php';

class CrmcItemJson extends JsonModel {
    public function __construct() {
        parent::__construct(__DIR__ . '/../../data/content/crmc.json');
    }
}
?>