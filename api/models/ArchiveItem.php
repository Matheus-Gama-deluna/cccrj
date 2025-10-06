<?php
// api/models/ArchiveItem.php

require_once __DIR__ . '/../config/database.php';

class ArchiveItem {
    private $pdo;
    
    public function __construct() {
        $this->pdo = connectDatabase();
    }
    
    public function getAll($limit = null, $offset = null) {
        try {
            $sql = "SELECT * FROM archive_items WHERE is_active = 1 ORDER BY date DESC";
            
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
            error_log("Erro ao buscar itens do acervo: " . $e->getMessage());
            throw new Exception("Não foi possível carregar os itens do acervo.");
        }
    }
    
    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM archive_items WHERE id = :id AND is_active = 1");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erro ao buscar item do acervo: " . $e->getMessage());
            throw new Exception("Não foi possível carregar o item do acervo.");
        }
    }
    
    public function create($data) {
        try {
            $sql = "INSERT INTO archive_items (title, description, file_path, date, item_type, category, metadata, is_active) 
                    VALUES (:title, :description, :file_path, :date, :item_type, :category, :metadata, :is_active)";
            
            $stmt = $this->pdo->prepare($sql);
            
            $stmt->bindValue(':title', $data['title']);
            $stmt->bindValue(':description', $data['description']);
            $stmt->bindValue(':file_path', $data['file_path']);
            $stmt->bindValue(':date', $data['date']);
            $stmt->bindValue(':item_type', $data['item_type']);
            $stmt->bindValue(':category', $data['category']);
            $stmt->bindValue(':metadata', $data['metadata']);
            $stmt->bindValue(':is_active', $data['is_active'], PDO::PARAM_BOOL);
            
            $stmt->execute();
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erro ao criar item do acervo: " . $e->getMessage());
            throw new Exception("Não foi possível criar o item do acervo.");
        }
    }
    
    public function update($id, $data) {
        try {
            $sql = "UPDATE archive_items SET 
                    title = :title, 
                    description = :description, 
                    file_path = :file_path, 
                    date = :date, 
                    item_type = :item_type, 
                    category = :category, 
                    metadata = :metadata, 
                    is_active = :is_active 
                    WHERE id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':title', $data['title']);
            $stmt->bindValue(':description', $data['description']);
            $stmt->bindValue(':file_path', $data['file_path']);
            $stmt->bindValue(':date', $data['date']);
            $stmt->bindValue(':item_type', $data['item_type']);
            $stmt->bindValue(':category', $data['category']);
            $stmt->bindValue(':metadata', $data['metadata']);
            $stmt->bindValue(':is_active', $data['is_active'], PDO::PARAM_BOOL);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar item do acervo: " . $e->getMessage());
            throw new Exception("Não foi possível atualizar o item do acervo.");
        }
    }
    
    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM archive_items WHERE id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao deletar item do acervo: " . $e->getMessage());
            throw new Exception("Não foi possível deletar o item do acervo.");
        }
    }
}
?>