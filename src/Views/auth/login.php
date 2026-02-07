<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - ClassTest Studio</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f5f7fa;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-box {
            background: white;
            width: 100%;
            max-width: 420px;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 1.5rem;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .header p {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .session-info {
            background: #f8f9fa;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 25px;
            font-size: 0.85rem;
            color: #495057;
        }

        .session-info strong {
            color: #2c3e50;
        }

        .role-select {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
        }

        .role-btn {
            flex: 1;
            padding: 10px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .role-btn.active {
            border-color: #3498db;
            background: #ebf5fb;
            color: #3498db;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #495057;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            font-size: 0.95rem;
        }

        .form-group input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .form-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-size: 0.85rem;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .forgot-link {
            color: #3498db;
            text-decoration: none;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
        }

        .btn-login:hover {
            background: #2980b9;
        }

        .divider {
            text-align: center;
            margin: 20px 0;
            color: #adb5bd;
            font-size: 0.85rem;
        }

        .sso-buttons {
            display: flex;
            gap: 10px;
        }

        .sso-btn {
            flex: 1;
            padding: 10px;
            border: 1px solid #dee2e6;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
        }

        .sso-btn:hover {
            background: #f8f9fa;
        }

        .demo-note {
            margin-top: 25px;
            padding: 15px;
            background: #fff3cd;
            border-left: 3px solid #ffc107;
            border-radius: 4px;
            font-size: 0.85rem;
        }

        .demo-note strong {
            display: block;
            margin-bottom: 8px;
            color: #856404;
        }

        .demo-note code {
            background: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.8rem;
        }

        .alert {
            padding: 12px;
            background: #f8d7da;
            color: #721c24;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="header">
            <h1>ClassTest Studio</h1>
            <p>Assessment Management System</p>
        </div>

        <div class="session-info">
            <strong>Current Session:</strong> 2024/2025 Academic Year<br>
            <strong>Active Term:</strong> Second Term
        </div>

        <?php if (isset($error)): ?>
            <div class="alert"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="role-select">
            <button class="role-btn" onclick="selectRole('student')">Student</button>
            <button class="role-btn active" onclick="selectRole('teacher')">Teacher/Admin</button>
        </div>

        <form method="POST" action="/login">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="yourname@school.edu" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-footer">
                <label class="remember">
                    <input type="checkbox" name="remember">
                    Remember me
                </label>
                <a href="#" class="forgot-link">Forgot password?</a>
            </div>
            
            <button type="submit" class="btn-login">Sign In</button>
        </form>

        <div class="divider">or continue with</div>

       

       
    </div>

    <script>
        function selectRole(role) {
            document.querySelectorAll('.role-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            if (role === 'student') {
                document.getElementById('email').placeholder = 'student.id@school.edu';
            } else {
                document.getElementById('email').placeholder = 'yourname@school.edu';
            }
        }
    </script>
</body>
</html>
