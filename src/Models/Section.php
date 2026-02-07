<?php

namespace ClassTest\Models;

use ClassTest\Database\Database;

class Section {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $sql = "INSERT INTO sections (assessment_id, title, description, section_type, position) 
                VALUES (?, ?, ?, ?, ?)";
        
        $this->db->execute($sql, [
            $data['assessment_id'],
            $data['title'],
            $data['description'] ?? '',
            $data['section_type'],
            $data['position'] ?? 0
        ]);
        
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $sql = "UPDATE sections SET title = ?, description = ?, section_type = ?, position = ? WHERE id = ?";
        
        return $this->db->execute($sql, [
            $data['title'],
            $data['description'] ?? '',
            $data['section_type'],
            $data['position'] ?? 0,
            $id
        ]);
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM sections WHERE id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }
    
    public function findByAssessment($assessmentId) {
        $sql = "SELECT * FROM sections WHERE assessment_id = ? ORDER BY position";
        return $this->db->fetchAll($sql, [$assessmentId]);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM sections WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
}
