<?php

namespace ClassTest\Models;

use ClassTest\Database\Database;

class Question {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $sql = "INSERT INTO questions (
            section_id, question_text, question_type, marks, 
            position, options, correct_answer
        ) VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $this->db->execute($sql, [
            $data['section_id'],
            $data['question_text'],
            $data['question_type'],
            $data['marks'],
            $data['position'] ?? 0,
            isset($data['options']) ? json_encode($data['options']) : null,
            $data['correct_answer'] ?? null
        ]);
        
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $sql = "UPDATE questions SET 
                question_text = ?, 
                question_type = ?, 
                marks = ?, 
                position = ?,
                options = ?,
                correct_answer = ?
                WHERE id = ?";
        
        return $this->db->execute($sql, [
            $data['question_text'],
            $data['question_type'],
            $data['marks'],
            $data['position'] ?? 0,
            isset($data['options']) ? json_encode($data['options']) : null,
            $data['correct_answer'] ?? null,
            $id
        ]);
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM questions WHERE id = ?";
        $question = $this->db->fetchOne($sql, [$id]);
        
        if ($question && $question['options']) {
            $question['options'] = json_decode($question['options'], true);
        }
        
        return $question;
    }
    
    public function findBySection($sectionId) {
        $sql = "SELECT * FROM questions WHERE section_id = ? ORDER BY position";
        $questions = $this->db->fetchAll($sql, [$sectionId]);
        
        foreach ($questions as &$question) {
            if ($question['options']) {
                $question['options'] = json_decode($question['options'], true);
            }
        }
        
        return $questions;
    }
    
    public function delete($id) {
        $sql = "DELETE FROM questions WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
}
