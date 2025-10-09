<?php
// api/models/CrmcItem.php

// Modelo CrmcItem para itens do Centro de Referência e Memória do Café

require_once __DIR__ . '/../config/database.php';

class CrmcItem {
    private $pdo;
    
    public function __construct() {
        $this->pdo = connectDatabase();
    }
    
    public function getAll($limit = null, $offset = null) {
        try {
            $sql = "SELECT * FROM crmc_items WHERE is_active = 1 ORDER BY created_at DESC";
            
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
            error_log("Erro ao buscar itens do CRMC: " . $e->getMessage());
            throw new Exception("Não foi possível carregar os itens do CRMC.");
        }
    }
    
    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM crmc_items WHERE id = :id AND is_active = 1");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erro ao buscar item do CRMC: " . $e->getMessage());
            throw new Exception("Não foi possível carregar o item do CRMC.");
        }
    }
    
    public function getByCategory($category) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM crmc_items WHERE category = :category AND is_active = 1 ORDER BY created_at DESC");
            $stmt->bindValue(':category', $category);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar itens do CRMC por categoria: " . $e->getMessage());
            throw new Exception("Não foi possível carregar os itens do CRMC por categoria.");
        }
    }
    
    public function create($data) {
        try {
            $sql = "INSERT INTO crmc_items (title, description, content, category, image_url, file_path, is_active, created_at) 
                    VALUES (:title, :description, :content, :category, :image_url, :file_path, :is_active, NOW())";
            
            $stmt = $this->pdo->prepare($sql);
            
            $stmt->bindValue(':title', $data['title']);
            $stmt->bindValue(':description', $data['description']);
            $stmt->bindValue(':content', $data['content']);
            $stmt->bindValue(':category', $data['category']);
            $stmt->bindValue(':image_url', $data['image_url']);
            $stmt->bindValue(':file_path', $data['file_path']);
            $stmt->bindValue(':is_active', $data['is_active'], PDO::PARAM_BOOL);
            
            $stmt->execute();
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erro ao criar item do CRMC: " . $e->getMessage());
            throw new Exception("Não foi possível criar o item do CRMC.");
        }
    }
    
    public function update($id, $data) {
        try {
            $sql = "UPDATE crmc_items SET 
                    title = :title, 
                    description = :description, 
                    content = :content, 
                    category = :category, 
                    image_url = :image_url, 
                    file_path = :file_path, 
                    is_active = :is_active 
                    WHERE id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':title', $data['title']);
            $stmt->bindValue(':description', $data['description']);
            $stmt->bindValue(':content', $data['content']);
            $stmt->bindValue(':category', $data['category']);
            $stmt->bindValue(':image_url', $data['image_url']);
            $stmt->bindValue(':file_path', $data['file_path']);
            $stmt->bindValue(':is_active', $data['is_active'], PDO::PARAM_BOOL);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar item do CRMC: " . $e->getMessage());
            throw new Exception("Não foi possível atualizar o item do CRMC.");
        }
    }
    
    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM crmc_items WHERE id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao deletar item do CRMC: " . $e->getMessage());
            throw new Exception("Não foi possível deletar o item do CRMC.");
        }
    }
}
?>