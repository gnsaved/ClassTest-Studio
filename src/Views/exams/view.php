<?php $title = 'Preview Assessment'; ?>
<?php ob_start(); ?>

<div class="view-assessment">
    <div class="assessment-header">
        <h1><?= htmlspecialchars($assessment['title']) ?></h1>
        <a href="/assessment/edit/<?= $assessment['id'] ?>" class="btn btn-secondary">Back to Edit</a>
    </div>
    
    <div class="assessment-info">
        <div class="info-grid">
            <div class="info-item">
                <strong>Subject:</strong> <?= htmlspecialchars($assessment['subject_name']) ?>
            </div>
            <div class="info-item">
                <strong>Type:</strong> <?= htmlspecialchars($assessment['exam_type_name']) ?>
            </div>
            <div class="info-item">
                <strong>Duration:</strong> <?= $assessment['duration_minutes'] ?> minutes
            </div>
            <div class="info-item">
                <strong>Total Marks:</strong> <?= $assessment['total_marks'] ?>
            </div>
            <?php if ($assessment['term']): ?>
            <div class="info-item">
                <strong>Term:</strong> <?= htmlspecialchars($assessment['term']) ?>
            </div>
            <?php endif; ?>
            <?php if ($assessment['session']): ?>
            <div class="info-item">
                <strong>Session:</strong> <?= htmlspecialchars($assessment['session']) ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php foreach ($sections as $sectionIndex => $section): ?>
    <div class="section-preview">
        <h2>Section <?= $sectionIndex + 1 ?>: <?= htmlspecialchars($section['title']) ?></h2>
        
        <?php if ($section['description']): ?>
        <p class="section-description"><?= htmlspecialchars($section['description']) ?></p>
        <?php endif; ?>
        
        <?php foreach ($section['questions'] as $qIndex => $question): ?>
        <div class="question-preview">
            <div class="question-header">
                <span class="question-number">Question <?= $qIndex + 1 ?></span>
                <span class="question-marks">(<?= $question['marks'] ?> marks)</span>
            </div>
            
            <p class="question-text"><?= nl2br(htmlspecialchars($question['question_text'])) ?></p>
            
            <?php if ($question['question_type'] === 'mcq' && $question['options']): ?>
            <div class="mcq-options">
                <?php foreach ($question['options'] as $key => $option): ?>
                <div class="option">
                    <span class="option-label"><?= strtoupper($key) ?>.</span>
                    <span><?= htmlspecialchars($option) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="essay-placeholder">
                <p><em>[Answer space for essay question]</em></p>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
</div>

<?php $content = ob_get_clean(); ?>
<?php require __DIR__ . '/../layouts/main.php'; ?>
