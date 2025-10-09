<?php
// api/models_json/AboutSectionJson.php

require_once __DIR__ . '/JsonModel.php';

class AboutSectionJson extends JsonModel {
    public function __construct() {
        parent::__construct(__DIR__ . '/../../data/content/about.json');
    }
}
?>