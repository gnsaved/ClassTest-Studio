<?php

namespace ClassTest\Models;

use ClassTest\Database\Database;

class Submission {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $sql = "INSERT INTO submissions (assessment_id, user_id, status, total_marks) 
                VALUES (?, ?, ?, ?)";
        
        $this->db->execute($sql, [
            $data['assessment_id'],
            $data['user_id'],
            'in_progress',
            $data['total_marks'] ?? 0
        ]);
        
        return $this->db->lastInsertId();
    }
    
    public function submit($id) {
        $this->calculateScore($id);
        
        $sql = "UPDATE submissions SET status = 'submitted', submitted_at = CURRENT_TIMESTAMP WHERE id = ?";
        return $this->db->execute($sql, [$id]);
    }
    
    public function saveAnswer($submissionId, $questionId, $answerText) {
        $existing = $this->db->fetchOne(
            "SELECT id FROM answers WHERE submission_id = ? AND question_id = ?",
            [$submissionId, $questionId]
        );
        
        if ($existing) {
            $sql = "UPDATE answers SET answer_text = ? WHERE id = ?";
            return $this->db->execute($sql, [$answerText, $existing['id']]);
        } else {
            $sql = "INSERT INTO answers (submission_id, question_id, answer_text) VALUES (?, ?, ?)";
            $this->db->execute($sql, [$submissionId, $questionId, $answerText]);
            return $this->db->lastInsertId();
        }
    }
    
    public function calculateScore($submissionId) {
        $answers = $this->db->fetchAll(
            "SELECT a.*, q.question_type, q.correct_answer, q.marks 
             FROM answers a
             INNER JOIN questions q ON a.question_id = q.id
             WHERE a.submission_id = ?",
            [$submissionId]
        );
        
        $totalScore = 0;
        
        foreach ($answers as $answer) {
            $marksObtained = 0;
            $isCorrect = 0;
            
            if ($answer['question_type'] === 'mcq') {
                if (trim($answer['answer_text']) === trim($answer['correct_answer'])) {
                    $marksObtained = $answer['marks'];
                    $isCorrect = 1;
                }
            }
            
            $totalScore += $marksObtained;
            
            $this->db->execute(
                "UPDATE answers SET is_correct = ?, marks_obtained = ? WHERE id = ?",
                [$isCorrect, $marksObtained, $answer['id']]
            );
        }
        
        $this->db->execute(
            "UPDATE submissions SET score = ? WHERE id = ?",
            [$totalScore, $submissionId]
        );
        
        return $totalScore;
    }
    
    public function findByUserAndAssessment($userId, $assessmentId) {
        $sql = "SELECT * FROM submissions WHERE user_id = ? AND assessment_id = ?";
        return $this->db->fetchOne($sql, [$userId, $assessmentId]);
    }
    
    public function findById($id) {
        $sql = "SELECT s.*, a.title as assessment_title, u.name as user_name
                FROM submissions s
                INNER JOIN assessments a ON s.assessment_id = a.id
                INNER JOIN users u ON s.user_id = u.id
                WHERE s.id = ?";
        
        return $this->db->fetchOne($sql, [$id]);
    }
    
    public function findByAssessment($assessmentId) {
        $sql = "SELECT s.*, u.name as user_name, u.email as user_email
                FROM submissions s
                INNER JOIN users u ON s.user_id = u.id
                WHERE s.assessment_id = ? AND s.status = 'submitted'
                ORDER BY s.submitted_at DESC";
        
        return $this->db->fetchAll($sql, [$assessmentId]);
    }
    
    public function getAnswers($submissionId) {
        $sql = "SELECT a.*, q.question_text, q.question_type, q.marks
                FROM answers a
                INNER JOIN questions q ON a.question_id = q.id
                WHERE a.submission_id = ?";
        
        return $this->db->fetchAll($sql, [$submissionId]);
    }
    
    public function getStats($assessmentId) {
        $sql = "SELECT 
                COUNT(*) as total_submissions,
                AVG(score) as average_score,
                MAX(score) as highest_score,
                MIN(score) as lowest_score,
                (SELECT total_marks FROM submissions WHERE assessment_id = ? LIMIT 1) as total_marks
                FROM submissions 
                WHERE assessment_id = ? AND status = 'submitted'";
        
        return $this->db->fetchOne($sql, [$assessmentId, $assessmentId]);
    }
}
