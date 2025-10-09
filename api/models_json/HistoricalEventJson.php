<?php
// api/models_json/HistoricalEventJson.php

require_once __DIR__ . '/JsonModel.php';

class HistoricalEventJson extends JsonModel {
    public function __construct() {
        parent::__construct(__DIR__ . '/../../data/content/history.json');
    }
}
?>