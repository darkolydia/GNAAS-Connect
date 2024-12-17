<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | GNAAS Connect</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fa;
        }
        .form-page {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .input-group {
            margin-bottom: 15px;
        }
        .input-group label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        .input-group input:focus {
            border-color: #007bff;
            outline: none;
        }
        .options {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .btn {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .form-footer {
            text-align: center;
            font-size: 14px;
        }
        .form-footer a {
            color: #007bff;
            text-decoration: none;
        }
        .form-footer a:hover {
            text-decoration: underline;
        }
        .error-message {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <main class="form-page">
        <div class="form-container">
            <h2>Login</h2>
            <form id="loginForm" action="login.php" method="post" onsubmit="return validateForm(event)">
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your Ashesi email" required>
                    <span id="email-error" class="error-message" style="display:none;"></span>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <span id="password-error" class="error-message" style="display:none;"></span>
                </div>
                <div class="options">
                    <label><input type="checkbox"> Remember Me</label>
                    <a href="reset-password.html">Forgot password?</a>
                </div>
                <button type="submit" class="btn">Login</button>
                <p class="form-footer">Don't have an account? <a href="signup.html">Register here</a></p>
            </form>
        </div>
    </main>

    <script>
        function validateForm(event) {
            event.preventDefault();
            
            // Get the form inputs
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            let isValid = true;

            // Email validation
            const emailError = document.getElementById('email-error');
            const emailPattern = /^[a-z0-9._]+@ashesi\.edu\.gh$/;
            if (!emailPattern.test(email.value)) {
                emailError.textContent = 'Please enter a valid Ashesi email address.';
                emailError.style.display = 'block';
                isValid = false;
            } else {
                emailError.style.display = 'none';
            }

            // Password validation
            const passwordError = document.getElementById('password-error');
            if (password.value.length < 8) {
                passwordError.textContent = 'Password must be at least 8 characters long.';
                passwordError.style.display = 'block';
                isValid = false;
            } else {
                passwordError.style.display = 'none';
            }

            // If form is valid, submit it
            if (isValid) {
                document.getElementById('loginForm').submit();
            }
        }
    </script>
</body>
</html>
