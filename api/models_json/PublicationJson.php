<?php
// api/models_json/PublicationJson.php

require_once __DIR__ . '/JsonModel.php';

class PublicationJson extends JsonModel {
    public function __construct() {
        parent::__construct(__DIR__ . '/../../data/content/publications.json');
    }
}
?>