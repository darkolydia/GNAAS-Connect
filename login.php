<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | GNAAS Connect</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .error-message {
            color: red;
            font-size: 14px;
        }
        .form-container {
            max-width: 400px;
            margin: auto;
            padding: 30px;
            border-radius: 8px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-footer a {
            color: #007bff;
        }
        .form-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body class="bg-light">
    <main class="form-page d-flex justify-content-center align-items-center vh-100">
        <div class="form-container">
            <h2 class="text-center mb-4">Login</h2>
            <form id="loginForm" action="login.php" method="post" onsubmit="return validateForm(event)">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your Ashesi email" required>
                    <div id="email-error" class="error-message" style="display:none;"></div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                    <div id="password-error" class="error-message" style="display:none;"></div>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <label><input type="checkbox"> Remember Me</label>
                    <a href="reset-password.html">Forgot password?</a>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
                <p class="form-footer text-center mt-3">Don't have an account? <a href="signup.html">Register here</a></p>
            </form>
        </div>
    </main>

    <!-- Bootstrap 5 JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <script>
        function validateForm(event) {
            event.preventDefault();
            
            // Get form inputs
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

            // Submit the form if valid
            if (isValid) {
                document.getElementById('loginForm').submit();
            }
        }
    </script>
</body>
</html>
