 <?php

namespace ClassTest\Database;

class Migration {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function up() {
        $this->createUsersTable();
        $this->createExamTypesTable();
        $this->createSubjectsTable();
        $this->createAssessmentsTable();
        $this->createSectionsTable();
        $this->createQuestionsTable();
        $this->createSubmissionsTable();
        $this->createAnswersTable();
        $this->insertSampleData();
    }
    
    private function createUsersTable() {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role VARCHAR(50) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        
        $this->db->execute($sql);
    }
    
    private function createExamTypesTable() {
        $sql = "CREATE TABLE IF NOT EXISTS exam_types (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        
        $this->db->execute($sql);
    }
    
    private function createSubjectsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS subjects (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255) NOT NULL,
            code VARCHAR(50),
            description TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        
        $this->db->execute($sql);
    }
    
    private function createAssessmentsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS assessments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            exam_type_id INTEGER NOT NULL,
            subject_id INTEGER NOT NULL,
            title VARCHAR(255) NOT NULL,
            term VARCHAR(100),
            session VARCHAR(100),
            duration_minutes INTEGER,
            total_marks INTEGER DEFAULT 0,
            status VARCHAR(50) DEFAULT 'draft',
            scheduled_at DATETIME,
            created_by INTEGER NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            published_at DATETIME,
            FOREIGN KEY (exam_type_id) REFERENCES exam_types(id),
            FOREIGN KEY (subject_id) REFERENCES subjects(id),
            FOREIGN KEY (created_by) REFERENCES users(id)
        )";
        
        $this->db->execute($sql);
    }
    
    private function createSectionsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS sections (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            assessment_id INTEGER NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            section_type VARCHAR(50) NOT NULL,
            position INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE
        )";
        
        $this->db->execute($sql);
    }
    
    private function createQuestionsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS questions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            section_id INTEGER NOT NULL,
            question_text TEXT NOT NULL,
            question_type VARCHAR(50) NOT NULL,
            marks INTEGER NOT NULL,
            position INTEGER DEFAULT 0,
            options TEXT,
            correct_answer TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE
        )";
        
        $this->db->execute($sql);
    }
    
    private function createSubmissionsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS submissions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            assessment_id INTEGER NOT NULL,
            user_id INTEGER NOT NULL,
            status VARCHAR(50) DEFAULT 'in_progress',
            score INTEGER DEFAULT 0,
            total_marks INTEGER DEFAULT 0,
            started_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            submitted_at DATETIME,
            FOREIGN KEY (assessment_id) REFERENCES assessments(id),
            FOREIGN KEY (user_id) REFERENCES users(id)
        )";
        
        $this->db->execute($sql);
    }
    
    private function createAnswersTable() {
        $sql = "CREATE TABLE IF NOT EXISTS answers (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            submission_id INTEGER NOT NULL,
            question_id INTEGER NOT NULL,
            answer_text TEXT,
            is_correct INTEGER DEFAULT 0,
            marks_obtained INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (submission_id) REFERENCES submissions(id) ON DELETE CASCADE,
            FOREIGN KEY (question_id) REFERENCES questions(id)
        )";
        
        $this->db->execute($sql);
    }
    
    private function insertSampleData() {
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $studentPassword = password_hash('student123', PASSWORD_DEFAULT);
        
        $this->db->execute(
            "INSERT OR IGNORE INTO users (id, name, email, password, role) VALUES (?, ?, ?, ?, ?)",
            [1, 'Dr. Sarah Johnson', 'admin@classtest.com', $adminPassword, 'admin']
        );
        
        $students = [
            [2, 'Emma Williams', 'emma.w@student.edu', $studentPassword, 'student'],
            [3, 'Michael Brown', 'michael.b@student.edu', $studentPassword, 'student'],
            [4, 'Sophia Davis', 'sophia.d@student.edu', $studentPassword, 'student'],
            [5, 'James Wilson', 'james.w@student.edu', $studentPassword, 'student'],
            [6, 'Olivia Taylor', 'olivia.t@student.edu', $studentPassword, 'student'],
            [7, 'Noah Anderson', 'noah.a@student.edu', $studentPassword, 'student']
        ];
        
        foreach ($students as $student) {
            $this->db->execute(
                "INSERT OR IGNORE INTO users (id, name, email, password, role) VALUES (?, ?, ?, ?, ?)",
                $student
            );
        }
        
        $examTypes = [
            ['Midterm Examination', 'Mid-semester assessment'],
            ['Final Examination', 'End of semester assessment'],
            ['Quiz', 'Short quiz assessment'],
            ['Assignment', 'Take-home assignment']
        ];
        
        foreach ($examTypes as $type) {
            $this->db->execute(
                "INSERT OR IGNORE INTO exam_types (name, description) VALUES (?, ?)",
                $type
            );
        }
        
        $subjects = [
            ['Mathematics', 'MATH101', 'Advanced Mathematics'],
            ['Physics', 'PHYS101', 'General Physics'],
            ['Chemistry', 'CHEM101', 'General Chemistry'],
            ['Biology', 'BIO101', 'Life Sciences'],
            ['Computer Science', 'CS101', 'Introduction to Programming'],
            ['English Literature', 'ENG201', 'Modern Literature']
        ];
        
        foreach ($subjects as $subject) {
            $this->db->execute(
                "INSERT OR IGNORE INTO subjects (name, code, description) VALUES (?, ?, ?)",
                $subject
            );
        }
        
        $assessments = [
            [1, 1, 'Mathematics Midterm Exam', 'First Term', '2024/2025', 90, 'published', 100, '2024-03-15 09:00:00'],
            [1, 2, 'Physics Final Examination', 'Second Term', '2024/2025', 120, 'published', 150, '2024-06-20 10:00:00'],
            [3, 3, 'Chemistry Quiz 1', 'First Term', '2024/2025', 30, 'published', 25, '2024-04-10 14:00:00'],
            [2, 1, 'Mathematics Final Exam', 'Second Term', '2024/2025', 120, 'draft', 0, null],
            [3, 5, 'Programming Quiz', 'First Term', '2024/2025', 45, 'published', 50, '2024-05-05 11:00:00']
        ];
        
        foreach ($assessments as $idx => $assessment) {
            $this->db->execute(
                "INSERT OR IGNORE INTO assessments (id, exam_type_id, subject_id, title, term, session, duration_minutes, status, total_marks, scheduled_at, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                array_merge([$idx + 1], $assessment, [1])
            );
        }
        
        $sections = [
            [1, 1, 'Multiple Choice Questions', 'Answer all questions', 'mcq', 1],
            [2, 1, 'Essay Questions', 'Choose 2 out of 3', 'essay', 2],
            [3, 2, 'Part A: Mechanics', 'Multiple choice section', 'mcq', 1],
            [4, 3, 'Chemical Reactions', 'Short answer questions', 'mcq', 1],
            [5, 5, 'Coding Problems', 'Write functioning code', 'essay', 1]
        ];
        
        foreach ($sections as $idx => $section) {
            $this->db->execute(
                "INSERT OR IGNORE INTO sections (id, assessment_id, title, description, section_type, position) VALUES (?, ?, ?, ?, ?, ?)",
                array_merge([$idx + 1], $section)
            );
        }
        
        $questions = [
            [1, 'What is the derivative of x²?', 'mcq', 10, 1, json_encode(['a' => 'x', 'b' => '2x', 'c' => '2', 'd' => 'x³']), 'b'],
            [1, 'Solve: 2x + 5 = 15', 'mcq', 10, 2, json_encode(['a' => 'x = 5', 'b' => 'x = 10', 'c' => 'x = 7.5', 'd' => 'x = 20']), 'a'],
            [2, 'Explain the Pythagorean theorem and provide two real-world applications.', 'essay', 30, 1, null, null],
            [3, 'Newton\'s first law states that:', 'mcq', 15, 1, json_encode(['a' => 'F = ma', 'b' => 'An object at rest stays at rest', 'c' => 'Energy is conserved', 'd' => 'Action = Reaction']), 'b'],
            [4, 'Balance the equation: H2 + O2 → H2O', 'mcq', 10, 1, json_encode(['a' => '2H2 + O2 → 2H2O', 'b' => 'H2 + O2 → H2O', 'c' => '4H2 + 2O2 → 4H2O', 'd' => 'H2 + 2O2 → H2O2']), 'a'],
            [5, 'Write a function to reverse a string in Python', 'essay', 25, 1, null, null]
        ];
        
        foreach ($questions as $idx => $question) {
            $this->db->execute(
                "INSERT OR IGNORE INTO questions (id, section_id, question_text, question_type, marks, position, options, correct_answer) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                array_merge([$idx + 1], $question)
            );
        }
        
        $this->db->execute("UPDATE assessments SET total_marks = 50 WHERE id = 1");
        $this->db->execute("UPDATE assessments SET total_marks = 15 WHERE id = 2");
        $this->db->execute("UPDATE assessments SET total_marks = 10 WHERE id = 3");
        $this->db->execute("UPDATE assessments SET total_marks = 25 WHERE id = 5");
        
        $submissions = [
            [2, 1, 50, 45, 'submitted', '2024-03-15 10:25:00'],
            [3, 1, 50, 38, 'submitted', '2024-03-15 10:30:00'],
            [4, 1, 50, 50, 'submitted', '2024-03-15 10:20:00'],
            [5, 1, 50, 42, 'submitted', '2024-03-15 10:28:00'],
            [6, 1, 50, 35, 'submitted', '2024-03-15 10:35:00'],
            [7, 1, 50, 48, 'submitted', '2024-03-15 10:22:00'],
            [2, 3, 10, 10, 'submitted', '2024-04-10 14:25:00'],
            [3, 3, 10, 8, 'submitted', '2024-04-10 14:20:00'],
            [4, 3, 10, 10, 'submitted', '2024-04-10 14:18:00']
        ];
        
        foreach ($submissions as $idx => $submission) {
            $this->db->execute(
                "INSERT OR IGNORE INTO submissions (id, user_id, assessment_id, total_marks, score, status, submitted_at) VALUES (?, ?, ?, ?, ?, ?, ?)",
                array_merge([$idx + 1], [$submission[0], $submission[1], $submission[2], $submission[3], $submission[4], $submission[5]])
            );
        }
        
        $answers = [
            [1, 1, 1, 'b', 1, 10],
            [1, 2, 2, 'a', 1, 10],
            [2, 1, 1, 'b', 1, 10],
            [2, 2, 2, 'c', 0, 0]
        ];
        
        foreach ($answers as $idx => $answer) {
            $this->db->execute(
                "INSERT OR IGNORE INTO answers (id, submission_id, question_id, answer_text, is_correct, marks_obtained) VALUES (?, ?, ?, ?, ?, ?)",
                array_merge([$idx + 1], $answer)
            );
        }
    }
    
    public function down() {
        $tables = [
            'answers',
            'submissions',
            'questions',
            'sections',
            'assessments',
            'subjects',
            'exam_types',
            'users'
        ];
        
        foreach ($tables as $table) {
            $this->db->execute("DROP TABLE IF EXISTS $table");
        }
    }
}
