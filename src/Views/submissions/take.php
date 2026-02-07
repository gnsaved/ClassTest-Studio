<?php $title = 'Take Assessment'; ?>
<?php ob_start(); ?>

<div class="take-assessment">
    <div class="assessment-header">
        <h1><?= htmlspecialchars($assessment['title']) ?></h1>
        <div class="timer" id="timer">
            Time Remaining: <span id="time"><?= $assessment['duration_minutes'] ?>:00</span>
        </div>
    </div>
    
    <div class="assessment-info">
        <p><strong>Subject:</strong> <?= htmlspecialchars($assessment['subject_name']) ?></p>
        <p><strong>Total Marks:</strong> <?= $assessment['total_marks'] ?></p>
    </div>
    
    <form id="assessmentForm">
        <input type="hidden" name="submission_id" value="<?= $submission['id'] ?>">
        
        <?php foreach ($sections as $section): ?>
        <div class="section">
            <h2><?= htmlspecialchars($section['title']) ?></h2>
            <?php if ($section['description']): ?>
                <p class="section-desc"><?= htmlspecialchars($section['description']) ?></p>
            <?php endif; ?>
            
            <?php foreach ($section['questions'] as $index => $question): ?>
            <div class="question">
                <div class="question-header">
                    <h3>Question <?= $index + 1 ?> (<?= $question['marks'] ?> marks)</h3>
                </div>
                
                <p class="question-text"><?= nl2br(htmlspecialchars($question['question_text'])) ?></p>
                
                <?php if ($question['question_type'] === 'mcq'): ?>
                <div class="mcq-options">
                    <?php foreach ($question['options'] as $key => $option): ?>
                    <div class="option">
                        <input type="radio" 
                               name="answer_<?= $question['id'] ?>" 
                               value="<?= $key ?>"
                               id="q<?= $question['id'] ?>_<?= $key ?>"
                               onchange="saveAnswer(<?= $question['id'] ?>, this.value)">
                        <label for="q<?= $question['id'] ?>_<?= $key ?>">
                            <?= htmlspecialchars($option) ?>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <textarea name="answer_<?= $question['id'] ?>" 
                          rows="6" 
                          class="essay-answer"
                          onblur="saveAnswer(<?= $question['id'] ?>, this.value)"></textarea>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
        
        <div class="form-actions">
            <button type="button" class="btn btn-primary btn-lg" onclick="submitAssessment()">
                Submit Assessment
            </button>
        </div>
    </form>
</div>

<script>
let timeRemaining = <?= $assessment['duration_minutes'] * 60 ?>;

function startTimer() {
    const timerInterval = setInterval(() => {
        timeRemaining--;
        
        const minutes = Math.floor(timeRemaining / 60);
        const seconds = timeRemaining % 60;
        
        document.getElementById('time').textContent = 
            `${minutes}:${seconds.toString().padStart(2, '0')}`;
        
        if (timeRemaining <= 0) {
            clearInterval(timerInterval);
            submitAssessment();
        }
    }, 1000);
}

async function saveAnswer(questionId, answer) {
    const formData = new FormData();
    formData.append('submission_id', <?= $submission['id'] ?>);
    formData.append('question_id', questionId);
    formData.append('answer', answer);
    
    await fetch('/submission/save-answer', {
        method: 'POST',
        body: formData
    });
}

function submitAssessment() {
    if (confirm('Are you sure you want to submit? You cannot change your answers after submission.')) {
        window.location.href = '/submission/submit/<?= $submission['id'] ?>';
    }
}

startTimer();
</script>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
