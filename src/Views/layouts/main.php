<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'ClassTest Studio' ?></title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <?php if (\ClassTest\Helpers\Auth::check()): ?>
        <?php require __DIR__ . '/../components/navbar.php'; ?>
    <?php endif; ?>
    
    <main class="main-content">
        <?php if (isset($content)) echo $content; ?>
    </main>
    
    <script src="/js/app.js"></script>
</body>
</html>
