<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIM-Distan</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #10b981;
            --primary-dark: #059669;
            --bg-gradient-1: #0f172a;
            --bg-gradient-2: #1e293b;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--bg-gradient-1) 0%, var(--bg-gradient-2) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
        }

        /* Decorative background elements */
        .bg-circle {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 1;
            opacity: 0.15;
        }
        
        .bg-circle-1 {
            width: 400px;
            height: 400px;
            background-color: var(--primary-color);
            top: -100px;
            left: -100px;
        }

        .bg-circle-2 {
            width: 500px;
            height: 500px;
            background-color: #3b82f6;
            bottom: -150px;
            right: -150px;
        }

        .login-container {
            z-index: 10;
            width: 100%;
            max-width: 950px;
            padding: 15px;
        }

        .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .brand-side {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.25) 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            color: #fff;
            position: relative;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        .form-side {
            padding: 50px;
        }

        .login-title {
            font-weight: 700;
            color: #f8fafc;
            margin-bottom: 8px;
        }

        .login-subtitle {
            color: #94a3b8;
            font-size: 0.9rem;
            margin-bottom: 30px;
        }

        .form-label {
            color: #cbd5e1;
            font-weight: 500;
            font-size: 0.85rem;
            margin-bottom: 6px;
        }

        .form-control {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #f8fafc;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 0.95rem;
            transition: all 0.2s ease-in-out;
        }

        .form-control:focus {
            background: rgba(15, 23, 42, 0.8);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25);
            color: #fff;
        }

        .btn-login {
            background-color: var(--primary-color);
            border: none;
            color: #fff;
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.2s ease;
            margin-top: 10px;
        }

        .btn-login:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.35);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .form-check-label {
            color: #94a3b8;
            font-size: 0.85rem;
            cursor: pointer;
        }

        .form-check-input {
            background-color: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.15);
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Branding Illustration Styling */
        .icon-box {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
        }

        .brand-name {
            font-weight: 800;
            font-size: 1.8rem;
            background: linear-gradient(to right, #ffffff, #a7f3d0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 5px;
        }

        .brand-tagline {
            color: #a7f3d0;
            font-size: 0.9rem;
            text-align: center;
            opacity: 0.8;
            max-width: 250px;
            line-height: 1.4;
        }

        /* Media Queries */
        @media (max-width: 768px) {
            .brand-side {
                display: none !important;
            }
            .form-side {
                padding: 35px 25px;
            }
        }
    </style>
</head>
<body>

    <!-- Circles Background -->
    <div class="bg-circle bg-circle-1"></div>
    <div class="bg-circle bg-circle-2"></div>

    <div class="login-container">
        <div class="glass-card">
            <div class="row g-0">
                <!-- Brand Side (Left) -->
                <div class="col-md-5 brand-side text-center">
                    <div class="icon-box">
                        <!-- Sprout/Agriculture Leaf SVG -->
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                        </svg>
                    </div>
                    <h1 class="brand-name">SIM-Distan</h1>
                    <p class="brand-tagline">Sistem Informasi Manajemen Dinas Pertanian Terpadu</p>
                </div>
                
                <!-- Form Side (Right) -->
                <div class="col-md-7 form-side">
                    <h2 class="login-title">Selamat Datang</h2>
                    <p class="login-subtitle">Masuk untuk mengakses Dashboard Dinas Pertanian</p>

                    <!-- Alert Errors -->
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-3 p-3 mb-4" role="alert">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email Input -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Alamat Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="nama@domain.com" required autofocus autocomplete="username">
                        </div>

                        <!-- Password Input -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="password" class="form-label">Kata Sandi</label>
                            </div>
                            <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required autocomplete="current-password">
                        </div>

                        <!-- Remember Me -->
                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Ingat Saya di Perangkat Ini</label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-login w-100">
                            Masuk Ke Sistem
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
