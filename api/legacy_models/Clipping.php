<?php
// api/models/Clipping.php

// Modelo Clipping para conteúdo de clipping histórico

require_once __DIR__ . '/../config/database.php';

class Clipping {
    private $pdo;
    
    public function __construct() {
        $this->pdo = connectDatabase();
    }
    
    public function getAll($limit = null, $offset = null) {
        try {
            $sql = "SELECT * FROM clippings WHERE is_active = 1 ORDER BY date DESC";
            
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
            error_log("Erro ao buscar clippings: " . $e->getMessage());
            throw new Exception("Não foi possível carregar os clippings.");
        }
    }
    
    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM clippings WHERE id = :id AND is_active = 1");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erro ao buscar clipping: " . $e->getMessage());
            throw new Exception("Não foi possível carregar o clipping.");
        }
    }
    
    public function create($data) {
        try {
            $sql = "INSERT INTO clippings (title, summary, content, source_url, date, category, is_active) 
                    VALUES (:title, :summary, :content, :source_url, :date, :category, :is_active)";
            
            $stmt = $this->pdo->prepare($sql);
            
            $stmt->bindValue(':title', $data['title']);
            $stmt->bindValue(':summary', $data['summary']);
            $stmt->bindValue(':content', $data['content']);
            $stmt->bindValue(':source_url', $data['source_url']);
            $stmt->bindValue(':date', $data['date']);
            $stmt->bindValue(':category', $data['category']);
            $stmt->bindValue(':is_active', $data['is_active'], PDO::PARAM_BOOL);
            
            $stmt->execute();
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erro ao criar clipping: " . $e->getMessage());
            throw new Exception("Não foi possível criar o clipping.");
        }
    }
    
    public function update($id, $data) {
        try {
            $sql = "UPDATE clippings SET 
                    title = :title, 
                    summary = :summary, 
                    content = :content, 
                    source_url = :source_url, 
                    date = :date, 
                    category = :category, 
                    is_active = :is_active 
                    WHERE id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':title', $data['title']);
            $stmt->bindValue(':summary', $data['summary']);
            $stmt->bindValue(':content', $data['content']);
            $stmt->bindValue(':source_url', $data['source_url']);
            $stmt->bindValue(':date', $data['date']);
            $stmt->bindValue(':category', $data['category']);
            $stmt->bindValue(':is_active', $data['is_active'], PDO::PARAM_BOOL);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar clipping: " . $e->getMessage());
            throw new Exception("Não foi possível atualizar o clipping.");
        }
    }
    
    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM clippings WHERE id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao deletar clipping: " . $e->getMessage());
            throw new Exception("Não foi possível deletar o clipping.");
        }
    }
}
?>