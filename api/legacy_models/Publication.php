<?php
// api/models/Publication.php

// Modelo Publication para publicações históricas

require_once __DIR__ . '/../config/database.php';

class Publication {
    private $pdo;
    
    public function __construct() {
        $this->pdo = connectDatabase();
    }
    
    public function getAll($limit = null, $offset = null) {
        try {
            $sql = "SELECT * FROM publications WHERE is_active = 1 ORDER BY date DESC";
            
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
            error_log("Erro ao buscar publicações: " . $e->getMessage());
            throw new Exception("Não foi possível carregar as publicações.");
        }
    }
    
    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM publications WHERE id = :id AND is_active = 1");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Erro ao buscar publicação: " . $e->getMessage());
            throw new Exception("Não foi possível carregar a publicação.");
        }
    }
    
    public function create($data) {
        try {
            $sql = "INSERT INTO publications (title, description, file_path, date, number, type, is_active) 
                    VALUES (:title, :description, :file_path, :date, :number, :type, :is_active)";
            
            $stmt = $this->pdo->prepare($sql);
            
            $stmt->bindValue(':title', $data['title']);
            $stmt->bindValue(':description', $data['description']);
            $stmt->bindValue(':file_path', $data['file_path']);
            $stmt->bindValue(':date', $data['date']);
            $stmt->bindValue(':number', $data['number']);
            $stmt->bindValue(':type', $data['type']);
            $stmt->bindValue(':is_active', $data['is_active'], PDO::PARAM_BOOL);
            
            $stmt->execute();
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erro ao criar publicação: " . $e->getMessage());
            throw new Exception("Não foi possível criar a publicação.");
        }
    }
    
    public function update($id, $data) {
        try {
            $sql = "UPDATE publications SET 
                    title = :title, 
                    description = :description, 
                    file_path = :file_path, 
                    date = :date, 
                    number = :number, 
                    type = :type, 
                    is_active = :is_active 
                    WHERE id = :id";
            
            $stmt = $this->pdo->prepare($sql);
            
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':title', $data['title']);
            $stmt->bindValue(':description', $data['description']);
            $stmt->bindValue(':file_path', $data['file_path']);
            $stmt->bindValue(':date', $data['date']);
            $stmt->bindValue(':number', $data['number']);
            $stmt->bindValue(':type', $data['type']);
            $stmt->bindValue(':is_active', $data['is_active'], PDO::PARAM_BOOL);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao atualizar publicação: " . $e->getMessage());
            throw new Exception("Não foi possível atualizar a publicação.");
        }
    }
    
    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM publications WHERE id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erro ao deletar publicação: " . $e->getMessage());
            throw new Exception("Não foi possível deletar a publicação.");
        }
    }
}
?>