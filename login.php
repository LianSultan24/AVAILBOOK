<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ETOK Car AC Services</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow-x: hidden;
        }

        /* Navigation Bar */
        .navbar-custom {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-brand img {
            height: 50px;
        }

        .navbar-custom .nav-link {
            color: #333;
            font-weight: 500;
            margin: 0 15px;
            transition: color 0.3s;
        }

        .navbar-custom .nav-link:hover {
            color: #ff9800;
        }

        .nav-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-left: 10px;
        }

        .nav-title .text-air {
            color: #3498db;
        }

        .nav-title .text-services {
            color: #ff9800;
        }

        /* Login Section */
        .login-section {
            position: relative;
            height: 100vh;
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                        url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 800"><rect fill="%23654321" width="1200" height="800"/></svg>');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            padding-top: 80px;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 50px;
            max-width: 450px;
            width: 90%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .login-title {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 30px;
            color: #333;
        }

        .login-title .text-in {
            color: #ff9800;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            padding: 15px 45px 15px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: #ff9800;
            box-shadow: 0 0 0 0.2rem rgba(255, 152, 0, 0.25);
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            z-index: 10;
            cursor: pointer;
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            background-color: #ff9800;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .login-btn:hover {
            background-color: #e68900;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 152, 0, 0.3);
        }

        .forgot-password {
            text-align: center;
            margin-top: 15px;
        }

        .forgot-password a {
            color: #ff9800;
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .create-account {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }

        .create-account a {
            color: #ff9800;
            text-decoration: none;
            font-weight: 600;
        }

        .create-account a:hover {
            text-decoration: underline;
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .login-container {
                padding: 30px 20px;
            }

            .login-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect fill='%232c3e50' width='100' height='100' rx='10'/%3E%3Ctext x='50' y='55' font-size='40' font-weight='bold' fill='white' text-anchor='middle'%3EETOK%3C/text%3E%3C/svg%3E" alt="ETOK Logo">
                <span class="nav-title">Car <span class="text-air">Air</span> Conditioning <span class="text-services">Services</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#about">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#contact">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Login Section -->
    <section class="login-section">
        <div class="login-container">
            <h1 class="login-title">Log<span class="text-in">in</span></h1>
            
            <div id="alertContainer"></div>
            
            <form id="loginForm">
                <div class="form-group">
                    <div class="input-group">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                        <i class="bi bi-envelope-fill input-icon"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <i class="bi bi-eye-slash-fill input-icon" id="togglePassword"></i>
                    </div>
                </div>
                
                <button type="submit" class="login-btn">Login</button>
            </form>
            
            <div class="forgot-password">
                <a href="forgot_password.php">Forgot Password?</a>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const password = document.getElementById('password');
        
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            this.classList.toggle('bi-eye-fill');
            this.classList.toggle('bi-eye-slash-fill');
        });
        
        // Login form submission
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const passwordValue = document.getElementById('password').value;
            const alertContainer = document.getElementById('alertContainer');
            
            try {
                const response = await fetch('api_login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        email: email,
                        password: passwordValue
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Show success message
                    alertContainer.innerHTML = `
                        <div class="alert alert-success" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            Login successful! Redirecting...
                        </div>
                    `;
                    
                    // Redirect based on role
                    setTimeout(() => {
                        if (result.data.role === 'Admin') {
                            window.location.href = 'admin_dashboard.php';
                        } else if (result.data.role === 'Staff') {
                            window.location.href = 'staff_dashboard.php';
                        } else {
                            // If somehow other role, redirect to login
                            window.location.href = 'login.php';
                        }
                    }, 1000);
                } else {
                    // Show error message
                    alertContainer.innerHTML = `
                        <div class="alert alert-danger" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            ${result.message}
                        </div>
                    `;
                }
            } catch (error) {
                alertContainer.innerHTML = `
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        An error occurred. Please try again.
                    </div>
                `;
                console.error('Login error:', error);
            }
        });
    </script>
</body>
</html>