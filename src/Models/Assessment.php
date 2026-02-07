<?php

namespace ClassTest\Models;

use ClassTest\Database\Database;

class Assessment {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $sql = "INSERT INTO assessments (
            exam_type_id, subject_id, title, term, session, 
            duration_minutes, status, scheduled_at, created_by
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $this->db->execute($sql, [
            $data['exam_type_id'],
            $data['subject_id'],
            $data['title'],
            $data['term'] ?? null,
            $data['session'] ?? null,
            $data['duration_minutes'] ?? 60,
            $data['status'] ?? 'draft',
            $data['scheduled_at'] ?? null,
            $data['created_by']
        ]);
        
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            if ($key !== 'id') {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        $values[] = $id;
        $sql = "UPDATE assessments SET " . implode(', ', $fields) . ", updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        
        return $this->db->execute($sql, $values);
    }
    
    public function publish($id) {
        $sql = "UPDATE assessments SET status = 'published', published_at = CURRENT_TIMESTAMP WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    public function findById($id) {
        $sql = "SELECT a.*, 
                et.name as exam_type_name,
                s.name as subject_name,
                s.code as subject_code,
                u.name as creator_name
                FROM assessments a
                LEFT JOIN exam_types et ON a.exam_type_id = et.id
                LEFT JOIN subjects s ON a.subject_id = s.id
                LEFT JOIN users u ON a.created_by = u.id
                WHERE a.id = ?";
        
        return $this->db->fetchOne($sql, [$id]);
    }
    
    public function findAll($filters = []) {
        $sql = "SELECT a.*, 
                et.name as exam_type_name,
                s.name as subject_name,
                u.name as creator_name
                FROM assessments a
                LEFT JOIN exam_types et ON a.exam_type_id = et.id
                LEFT JOIN subjects s ON a.subject_id = s.id
                LEFT JOIN users u ON a.created_by = u.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= " AND a.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['subject_id'])) {
            $sql .= " AND a.subject_id = ?";
            $params[] = $filters['subject_id'];
        }
        
        $sql .= " ORDER BY a.created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM assessments WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    public function updateTotalMarks($id) {
        $sql = "UPDATE assessments 
                SET total_marks = (
                    SELECT COALESCE(SUM(q.marks), 0)
                    FROM questions q
                    INNER JOIN sections s ON q.section_id = s.id
                    WHERE s.assessment_id = ?
                )
                WHERE id = ?";
        
        return $this->db->execute($sql, [$id, $id]);
    }
    
    public function getPublishedAssessments() {
        $sql = "SELECT a.*, 
                et.name as exam_type_name,
                s.name as subject_name
                FROM assessments a
                LEFT JOIN exam_types et ON a.exam_type_id = et.id
                LEFT JOIN subjects s ON a.subject_id = s.id
                WHERE a.status = 'published'
                AND (a.scheduled_at IS NULL OR a.scheduled_at <= CURRENT_TIMESTAMP)
                ORDER BY a.created_at DESC";
        
        return $this->db->fetchAll($sql);
    }
}
