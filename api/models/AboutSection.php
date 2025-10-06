<?php
// api/models/AboutSection.php

// Modelo AboutSection para seções sobre o CCCRJ

require_once __DIR__ . '/../config/database.php';

class AboutSection {
    private $pdo;
    
    public function __construct() {
        $this->pdo = connectDatabase();
    }
    
    public function getAll($limit = null, $offset = null) {
        try {
            $sql = "SELECT * FROM about_sections WHERE is_active = 1 ORDER BY `order` ASC";
            
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
            error_log("Erro ao buscar seções sobre: " . $e->getMessage());
            throw new Exception("Não foi possível carregar as seções sobre.");
        }
    }
    
    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM about_sections WHERE id = :id AND is_active = 1");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erro ao buscar seção sobre: " . $e->getMessage());
            throw new Exception("Não foi possível carregar a seção sobre.");
        }
    }
    
    public function getByType($type) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM about_sections WHERE section_type = :type AND is_active = 1 ORDER BY `order` ASC");
            $stmt->bindValue(':type', $type);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erro ao buscar seções por tipo: " . $e->getMessage());
            throw new Exception("Não foi possível carregar as seções por tipo.");
        }
    }
    
    public function create($data) {
        try {
            $sql = "INSERT INTO about_sections (title, content, section_type, `order`, is_active, image_url) 
                    VALUES (:title, :content, :section_type, :order, :is_active, :image_url)";
            
            $stmt = $this->pdo->prepare($sql);
            
            $stmt->bindValue(':title', $data['title']);
            $stmt->bindValue(':content', $data['content']);
            $stmt->bindValue(':section_type', $data['section_type']);
            $stmt->bindValue(':order', $data['order']);
            $stmt->bindValue(':is_active', $data['is_active'], PDO::PARAM_BOOL);
            $stmt->bindValue(':image_url', $data['image_url']);
            
            $stmt->execute();
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erro ao criar seção sobre: " . $e->getMessage());
            throw new Exception("Não foi possível criar a seção sobre.");
        }
    }
    
    public function update($id, $data) {
        try {
            $sql = "UPDATE about_sections SET 
                    title = :title, 
                    content = :content, 
                    section_type = :section_type, 
                    `order` = :order, 
                    is_active = :is_active, 
                    image_url = :image_url 
                    WHERE id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':title', $data['title']);
            $stmt->bindValue(':content', $data['content']);
            $stmt->bindValue(':section_type', $data['section_type']);
            $stmt->bindValue(':order', $data['order']);
            $stmt->bindValue(':is_active', $data['is_active'], PDO::PARAM_BOOL);
            $stmt->bindValue(':image_url', $data['image_url']);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar seção sobre: " . $e->getMessage());
            throw new Exception("Não foi possível atualizar a seção sobre.");
        }
    }
    
    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM about_sections WHERE id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao deletar seção sobre: " . $e->getMessage());
            throw new Exception("Não foi possível deletar a seção sobre.");
        }
    }
}
?>