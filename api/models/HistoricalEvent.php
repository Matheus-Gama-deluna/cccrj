<?php
// api/models/HistoricalEvent.php

// Modelo HistoricalEvent para eventos históricos

require_once __DIR__ . '/../config/database.php';

class HistoricalEvent {
    private $pdo;
    
    public function __construct() {
        $this->pdo = connectDatabase();
    }
    
    public function getAll($limit = null, $offset = null) {
        try {
            $sql = "SELECT * FROM historical_events WHERE is_active = 1 ORDER BY date ASC";
            
            if ($limit !== null) {
                $sql .= " LIMIT :limit";
                if ($offset !== null) {
                    $sql .= " OFFSET :offset";
                }
            }
            
            $stmt = $this->pdo->prepare($sql);
            
            if ($limit !== null) {
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                if ($offset !== null) {
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                }
            }
            
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar eventos históricos: " . $e->getMessage());
            throw new Exception("Não foi possível carregar os eventos históricos.");
        }
    }
    
    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM historical_events WHERE id = :id AND is_active = 1");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erro ao buscar evento histórico: " . $e->getMessage());
            throw new Exception("Não foi possível carregar o evento histórico.");
        }
    }
    
    public function create($data) {
        try {
            $sql = "INSERT INTO historical_events (title, description, content, date, event_type, image_url, is_featured, is_active) 
                    VALUES (:title, :description, :content, :date, :event_type, :image_url, :is_featured, :is_active)";
            
            $stmt = $this->pdo->prepare($sql);
            
            $stmt->bindValue(':title', $data['title']);
            $stmt->bindValue(':description', $data['description']);
            $stmt->bindValue(':content', $data['content']);
            $stmt->bindValue(':date', $data['date']);
            $stmt->bindValue(':event_type', $data['event_type']);
            $stmt->bindValue(':image_url', $data['image_url']);
            $stmt->bindValue(':is_featured', $data['is_featured'], PDO::PARAM_BOOL);
            $stmt->bindValue(':is_active', $data['is_active'], PDO::PARAM_BOOL);
            
            $stmt->execute();
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erro ao criar evento histórico: " . $e->getMessage());
            throw new Exception("Não foi possível criar o evento histórico.");
        }
    }
    
    public function update($id, $data) {
        try {
            $sql = "UPDATE historical_events SET 
                    title = :title, 
                    description = :description, 
                    content = :content, 
                    date = :date, 
                    event_type = :event_type, 
                    image_url = :image_url, 
                    is_featured = :is_featured, 
                    is_active = :is_active 
                    WHERE id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':title', $data['title']);
            $stmt->bindValue(':description', $data['description']);
            $stmt->bindValue(':content', $data['content']);
            $stmt->bindValue(':date', $data['date']);
            $stmt->bindValue(':event_type', $data['event_type']);
            $stmt->bindValue(':image_url', $data['image_url']);
            $stmt->bindValue(':is_featured', $data['is_featured'], PDO::PARAM_BOOL);
            $stmt->bindValue(':is_active', $data['is_active'], PDO::PARAM_BOOL);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar evento histórico: " . $e->getMessage());
            throw new Exception("Não foi possível atualizar o evento histórico.");
        }
    }
    
    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM historical_events WHERE id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao deletar evento histórico: " . $e->getMessage());
            throw new Exception("Não foi possível deletar o evento histórico.");
        }
    }
}
?>