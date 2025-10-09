<?php
// api/models_json/ClippingJson.php

require_once __DIR__ . '/JsonModel.php';

class ClippingJson extends JsonModel {
    public function __construct() {
        parent::__construct(__DIR__ . '/../../data/content/clipping.json');
    }
}
?>