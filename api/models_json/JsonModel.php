<?php
// api/models_json/JsonModel.php

class JsonModel {
    protected $dataFile;
    protected $data;
    
    public function __construct($dataFile) {
        $this->dataFile = $dataFile;
        $this->loadData();
    }
    
    protected function loadData() {
        if (file_exists($this->dataFile)) {
            $jsonContent = file_get_contents($this->dataFile);
            $this->data = json_decode($jsonContent, true);
            
            if ($this->data === null) {
                $this->data = ['data' => []];
            }
        } else {
            $this->data = ['data' => []];
            $this->saveData();
        }
    }
    
    protected function saveData() {
        file_put_contents($this->dataFile, json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    
    public function getAll($limit = null, $offset = null) {
        $allItems = $this->data['data'];
        $activeItems = array_filter($allItems, function($item) {
            return isset($item['is_active']) ? $item['is_active'] === true : true;
        });
        
        if ($limit !== null) {
            return array_slice(array_values($activeItems), $offset ?? 0, $limit);
        }
        
        return array_values($activeItems);
    }
    
    public function getById($id) {
        foreach ($this->data['data'] as $item) {
            if ($item['id'] == $id) {
                return $item;
            }
        }
        return null;
    }
    
    public function create($data) {
        $data['id'] = uniqid();
        $data['is_active'] = $data['is_active'] ?? true;
        
        $this->data['data'][] = $data;
        $this->saveData();
        
        return $data['id'];
    }
    
    public function update($id, $data) {
        foreach ($this->data['data'] as $index => &$item) {
            if ($item['id'] == $id) {
                $item = array_merge($item, $data);
                $this->saveData();
                return true;
            }
        }
        return false;
    }
    
    public function delete($id) {
        foreach ($this->data['data'] as $index => $item) {
            if ($item['id'] == $id) {
                unset($this->data['data'][$index]);
                $this->data['data'] = array_values($this->data['data']); // Reindexar
                $this->saveData();
                return true;
            }
        }
        return false;
    }
    
    public function count() {
        $activeItems = array_filter($this->data['data'], function($item) {
            return isset($item['is_active']) ? $item['is_active'] === true : true;
        });
        return count($activeItems);
    }
}
?>