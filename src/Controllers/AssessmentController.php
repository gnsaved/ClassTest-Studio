<?php

namespace ClassTest\Controllers;

use ClassTest\Models\Assessment;
use ClassTest\Models\Section;
use ClassTest\Models\Question;
use ClassTest\Models\Submission;
use ClassTest\Database\Database;
use ClassTest\Helpers\Auth;

class AssessmentController {
    private $assessmentModel;
    private $sectionModel;
    private $questionModel;
    private $submissionModel;
    private $db;
    
    public function __construct() {
        $this->assessmentModel = new Assessment();
        $this->sectionModel = new Section();
        $this->questionModel = new Question();
        $this->submissionModel = new Submission();
        $this->db = Database::getInstance();
    }
    
    public function index() {
        $assessments = $this->assessmentModel->findAll();
        require __DIR__ . '/../Views/exams/index.php';
    }
    
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'exam_type_id' => $_POST['exam_type_id'],
                'subject_id' => $_POST['subject_id'],
                'title' => $_POST['title'],
                'term' => $_POST['term'] ?? null,
                'session' => $_POST['session'] ?? null,
                'duration_minutes' => $_POST['duration_minutes'] ?? 60,
                'scheduled_at' => $_POST['scheduled_at'] ?? null,
                'created_by' => Auth::user()['id']
            ];
            
            $assessmentId = $this->assessmentModel->create($data);
            header("Location: /assessment/edit/$assessmentId");
            exit;
        }
        
        $examTypes = $this->db->fetchAll("SELECT * FROM exam_types");
        $subjects = $this->db->fetchAll("SELECT * FROM subjects");
        require __DIR__ . '/../Views/exams/create.php';
    }
    
    public function edit($id) {
        $assessment = $this->assessmentModel->findById($id);
        $sections = $this->sectionModel->findByAssessment($id);
        
        foreach ($sections as &$section) {
            $section['questions'] = $this->questionModel->findBySection($section['id']);
        }
        
        require __DIR__ . '/../Views/exams/edit.php';
    }
    
    public function addSection() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'assessment_id' => $_POST['assessment_id'],
                'title' => $_POST['title'],
                'description' => $_POST['description'] ?? '',
                'section_type' => $_POST['section_type'],
                'position' => $_POST['position'] ?? 0
            ];
            
            $sectionId = $this->sectionModel->create($data);
            echo json_encode(['success' => true, 'section_id' => $sectionId]);
        }
    }
    
    public function addQuestion() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'section_id' => $_POST['section_id'],
                'question_text' => $_POST['question_text'],
                'question_type' => $_POST['question_type'],
                'marks' => $_POST['marks'],
                'position' => $_POST['position'] ?? 0
            ];
            
            if ($_POST['question_type'] === 'mcq') {
                $data['options'] = $_POST['options'];
                $data['correct_answer'] = $_POST['correct_answer'];
            }
            
            $questionId = $this->questionModel->create($data);
            
            $this->assessmentModel->updateTotalMarks($_POST['assessment_id']);
            
            echo json_encode(['success' => true, 'question_id' => $questionId]);
        }
    }
    
    public function publish($id) {
        $this->assessmentModel->publish($id);
        header("Location: /assessment/edit/$id");
        exit;
    }
    
    public function view($id) {
        $assessment = $this->assessmentModel->findById($id);
        $sections = $this->sectionModel->findByAssessment($id);
        
        foreach ($sections as &$section) {
            $section['questions'] = $this->questionModel->findBySection($section['id']);
        }
        
        require __DIR__ . '/../Views/exams/view.php';
    }
    
    public function take($id) {
        $user = Auth::user();
        $assessment = $this->assessmentModel->findById($id);
        
        $existingSubmission = $this->submissionModel->findByUserAndAssessment($user['id'], $id);
        
        if (!$existingSubmission) {
            $submissionId = $this->submissionModel->create([
                'assessment_id' => $id,
                'user_id' => $user['id'],
                'total_marks' => $assessment['total_marks']
            ]);
        } else {
            $submissionId = $existingSubmission['id'];
        }
        
        header("Location: /submission/$submissionId");
        exit;
    }
}
