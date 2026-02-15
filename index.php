<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ETOK - Car Air Conditioning Services</title>
    
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

        .login-btn, .create-btn {
            padding: 8px 20px;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s;
            margin-left: 10px;
        }

        .login-btn {
            background-color: transparent;
            color: #333;
            border: 2px solid #333;
        }

        .login-btn:hover {
            background-color: #333;
            color: white;
        }

        .create-btn {
            background-color: #ff9800;
            color: white;
            border: 2px solid #ff9800;
        }

        .create-btn:hover {
            background-color: #e68900;
            border-color: #e68900;
        }

        /* Hero Section */
        .hero-section {
            position: relative;
            height: 100vh;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                        url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 800"><rect fill="%23654321" width="1200" height="800"/></svg>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            display: flex;
            align-items: center;
            color: white;
        }

        .hero-content {
            max-width: 600px;
            padding: 0 50px;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 20px;
        }

        .hero-content .text-reliable {
            font-size: 2rem;
            font-weight: 300;
            margin-bottom: 10px;
        }

        .hero-content .text-car {
            color: white;
        }

        .hero-content .text-air {
            color: #ff9800;
        }

        .hero-content .text-conditioning {
            color: white;
        }

        .hero-content .text-services {
            color: #ff9800;
        }

        .hero-content p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }

        .get-started-btn {
            background-color: #ff9800;
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 30px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .get-started-btn:hover {
            background-color: #e68900;
            transform: translateX(10px);
        }

        .hero-image {
            position: absolute;
            right: 100px;
            top: 50%;
            transform: translateY(-50%);
            max-width: 500px;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .hero-content h1 {
                font-size: 2.5rem;
            }

            .hero-image {
                display: none;
            }

            .hero-content {
                padding: 0 30px;
            }
        }

        @media (max-width: 768px) {
            .hero-content h1 {
                font-size: 2rem;
            }

            .hero-content .text-reliable {
                font-size: 1.5rem;
            }

            .navbar-custom .nav-link {
                margin: 5px 0;
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
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a href="login.php" class="btn login-btn">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <div class="text-reliable">Reliable</div>
                <h1>
                    <span class="text-car">Car</span> 
                    <span class="text-air">Air</span><br>
                    <span class="text-conditioning">Conditioning</span><br>
                    <span class="text-services">Services</span>
                </h1>
                <p>Get your car's fixed fast and book your appointment online in just a few clicks!</p>
                <button class="get-started-btn" onclick="window.location.href='login.php'">
                    Get Started
                    <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>
        
        <!-- Placeholder for AC gauges image - you can replace this with actual image -->
        <div class="hero-image">
            <svg viewBox="0 0 500 400" xmlns="http://www.w3.org/2000/svg">
                <!-- Blue Gauge -->
                <circle cx="150" cy="200" r="80" fill="#3498db" opacity="0.8"/>
                <circle cx="150" cy="200" r="60" fill="white"/>
                <text x="150" y="210" font-size="20" text-anchor="middle" fill="#3498db">PSI</text>
                
                <!-- Red Gauge -->
                <circle cx="350" cy="200" r="80" fill="#e74c3c" opacity="0.8"/>
                <circle cx="350" cy="200" r="60" fill="white"/>
                <text x="350" y="210" font-size="20" text-anchor="middle" fill="#e74c3c">PSI</text>
            </svg>
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>