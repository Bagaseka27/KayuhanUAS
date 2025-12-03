<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kayuhan Street Coffee</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            height: 100vh;
            display: flex;
            overflow: hidden;
        }

        .login-split-container {
            display: flex;
            width: 100%;
            height: 100vh;
        }

        /* ========== LEFT SIDE - SUPER PREMIUM ========== */
        .left-side {
            flex: 1;
            background: linear-gradient(135deg, rgba(0, 61, 46, 0.95), rgba(0, 42, 32, 0.98)),
                        url('{{ asset('images/coffee-background.jpg') }}') center/cover;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            color: white;
            padding: 40px;
            overflow: hidden;
        }

        /* Animated Gradient Overlay */
        .left-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 30%, rgba(212, 175, 55, 0.2), transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(154, 184, 124, 0.15), transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(245, 240, 230, 0.08), transparent 70%);
            animation: gradientShift 10s ease-in-out infinite;
        }

        @keyframes gradientShift {
            0%, 100% { opacity: 0.3; transform: scale(1) rotate(0deg); }
            50% { opacity: 0.6; transform: scale(1.1) rotate(5deg); }
        }

        /* Floating Particles */
        .left-side::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.15), transparent 70%);
            top: -100px;
            right: -100px;
            animation: floatParticle 15s ease-in-out infinite;
        }

        @keyframes floatParticle {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(-50px, 100px) scale(1.2); }
            66% { transform: translate(50px, -50px) scale(0.9); }
        }

        .logo-side {
            position: relative;
            z-index: 1;
            text-align: center;
            animation: fadeInScale 1s ease;
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        /* Logo dengan Glow Effect */
        .logo-wrapper {
            position: relative;
            display: inline-block;
            margin-bottom: 35px;
        }

        .logo-wrapper::before {
            content: '';
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.4), transparent 70%);
            border-radius: 50%;
            animation: pulseGlow 3s ease-in-out infinite;
        }

        @keyframes pulseGlow {
            0%, 100% { opacity: 0.5; transform: scale(0.95); }
            50% { opacity: 1; transform: scale(1.05); }
        }

        .logo-side img {
            width: 200px;
            height: 200px;
            position: relative;
            filter: drop-shadow(0 15px 40px rgba(0, 0, 0, 0.5));
            animation: logoFloat 4s ease-in-out infinite;
            transition: transform 0.3s ease;
        }

        .logo-side img:hover {
            transform: scale(1.1) rotate(5deg);
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        .brand-title {
            font-size: 3.5rem;
            font-weight: 900;
            letter-spacing: 5px;
            margin-bottom: 15px;
            text-shadow: 
                0 0 20px rgba(212, 175, 55, 0.5),
                0 5px 15px rgba(0, 0, 0, 0.4);
            background: linear-gradient(135deg, #fff 0%, #D4AF37 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: titleShine 3s ease-in-out infinite;
        }

        @keyframes titleShine {
            0%, 100% { filter: brightness(1); }
            50% { filter: brightness(1.3); }
        }

        .brand-subtitle {
            font-size: 1.3rem;
            font-weight: 300;
            letter-spacing: 4px;
            opacity: 0.95;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            animation: subtitleFade 2s ease-in-out infinite;
        }

        @keyframes subtitleFade {
            0%, 100% { opacity: 0.9; }
            50% { opacity: 1; }
        }

        /* Decorative Lines */
        .decorative-line {
            width: 150px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #D4AF37, transparent);
            margin: 25px auto;
            animation: lineExpand 2s ease-in-out infinite;
        }

        @keyframes lineExpand {
            0%, 100% { width: 100px; opacity: 0.5; }
            50% { width: 180px; opacity: 1; }
        }

        /* ========== RIGHT SIDE - GLASSMORPHISM ========== */
        .right-side {
            flex: 1;
            background: linear-gradient(135deg, #F5F0E6 0%, #e8e3d5 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        /* Subtle Pattern Background */
        .right-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                repeating-linear-gradient(45deg, transparent, transparent 35px, rgba(0, 61, 46, 0.02) 35px, rgba(0, 61, 46, 0.02) 70px);
            pointer-events: none;
        }

        .form-container {
            width: 100%;
            max-width: 460px;
            position: relative;
            z-index: 1;
        }

        /* Welcome Text dengan Gradient */
        .welcome-text {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, #003D2E 0%, #D4AF37 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 12px;
            animation: slideInRight 0.8s ease;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .welcome-subtitle {
            color: #666;
            margin-bottom: 45px;
            font-size: 1.05rem;
            font-weight: 400;
            animation: slideInRight 0.8s ease 0.2s backwards;
        }

        /* Form Group dengan Glassmorphism */
        .form-group {
            margin-bottom: 28px;
            animation: slideInRight 0.8s ease 0.4s backwards;
        }

        .input-wrapper {
            position: relative;
        }

        /* Icon dengan Pulse Effect */
        .input-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #003D2E;
            font-size: 1.2rem;
            z-index: 2;
            transition: all 0.3s ease;
        }

        .input-wrapper:focus-within .input-icon {
            color: #D4AF37;
            transform: translateY(-50%) scale(1.15);
            animation: iconPulse 0.5s ease;
        }

        @keyframes iconPulse {
            0%, 100% { transform: translateY(-50%) scale(1.15); }
            50% { transform: translateY(-50%) scale(1.3); }
        }

        /* Premium Input Fields */
        .form-control {
            width: 100%;
            padding: 18px 20px 18px 55px;
            border: 2px solid rgba(0, 61, 46, 0.1);
            border-radius: 16px;
            font-size: 1.05rem;
            font-family: 'Outfit', sans-serif;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 
                0 4px 12px rgba(0, 0, 0, 0.05),
                inset 0 1px 3px rgba(255, 255, 255, 0.9);
        }

        .form-control:focus {
            outline: none;
            border-color: #D4AF37;
            background: white;
            box-shadow: 
                0 8px 24px rgba(212, 175, 55, 0.25),
                0 0 0 5px rgba(212, 175, 55, 0.1),
                inset 0 1px 3px rgba(255, 255, 255, 0.9);
            transform: translateY(-2px);
        }

        .form-control::placeholder {
            color: #aaa;
            font-weight: 400;
        }

        /* Forgot Password dengan Hover Effect */
        .forgot-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 25px;
            color: #003D2E;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
        }

        .forgot-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #D4AF37, #003D2E);
            transition: width 0.3s ease;
        }

        .forgot-link:hover {
            color: #D4AF37;
            transform: translateX(5px);
        }

        .forgot-link:hover::after {
            width: 100%;
        }

        .forgot-link i {
            transition: transform 0.3s ease;
        }

        .forgot-link:hover i {
            transform: rotate(360deg);
        }

        /* SUPER PREMIUM BUTTON */
        .btn-login {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #003D2E 0%, #D4AF37 50%, #003D2E 100%);
            background-size: 200% 100%;
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 1.15rem;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            letter-spacing: 2px;
            text-transform: uppercase;
            box-shadow: 
                0 8px 24px rgba(0, 61, 46, 0.4),
                0 0 0 0 rgba(212, 175, 55, 0);
            position: relative;
            overflow: hidden;
            animation: slideInRight 0.8s ease 0.6s backwards;
        }

        /* Shimmer Effect */
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent, 
                rgba(255, 255, 255, 0.4), 
                transparent);
            transition: left 0.7s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            background-position: right center;
            transform: translateY(-3px) scale(1.02);
            box-shadow: 
                0 12px 36px rgba(0, 61, 46, 0.5),
                0 0 0 8px rgba(212, 175, 55, 0.2);
        }

        .btn-login:active {
            transform: translateY(-1px) scale(1);
            box-shadow: 
                0 6px 18px rgba(0, 61, 46, 0.4),
                0 0 0 4px rgba(212, 175, 55, 0.15);
        }

        /* Alert Premium */
        .alert {
            padding: 16px 20px;
            border-radius: 14px;
            margin-bottom: 28px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 12px;
            backdrop-filter: blur(10px);
            animation: alertSlide 0.5s ease;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        @keyframes alertSlide {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-danger {
            background: linear-gradient(135deg, #fee 0%, #fdd 100%);
            color: #c33;
            border-left: 5px solid #c33;
        }

        .alert i {
            font-size: 1.2rem;
            animation: iconBounce 0.6s ease;
        }

        @keyframes iconBounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        /* Responsive */
        @media (max-width: 968px) {
            .login-split-container {
                flex-direction: column;
            }

            .left-side {
                flex: 0 0 250px;
            }

            .logo-side img {
                width: 120px;
                height: 120px;
            }

            .brand-title {
                font-size: 2.2rem;
            }

            .brand-subtitle {
                font-size: 1rem;
            }

            .right-side {
                flex: 1;
            }

            .welcome-text {
                font-size: 2.2rem;
            }

            .form-container {
                max-width: 100%;
            }
        }

        @media (max-width: 576px) {
            .left-side {
                flex: 0 0 180px;
                padding: 20px;
            }

            .logo-side img {
                width: 90px;
                height: 90px;
            }

            .brand-title {
                font-size: 1.8rem;
            }

            .welcome-text {
                font-size: 1.8rem;
            }

            .form-control {
                padding: 16px 18px 16px 50px;
            }
        }
    </style>
</head>
<body>
    <div class="login-split-container">
        <!-- Left Side - Super Premium Logo & Brand -->
        <div class="left-side">
            <div class="logo-side">
                <div class="logo-wrapper">
                    <img src="{{ asset('images/kayuhan-logo.png') }}" alt="Kayuhan Logo">
                </div>
                
                <div class="decorative-line"></div>
                
                <h1 class="brand-title">KAYUHAN</h1>
                <p class="brand-subtitle">STREET COFFEE</p>
                
                <div class="decorative-line"></div>
            </div>
        </div>

        <!-- Right Side - Premium Form -->
        <div class="right-side">
            <div class="form-container">
                <h2 class="welcome-text">Welcome !</h2>
                <p class="welcome-subtitle">Silakan login untuk melanjutkan ke dashboard</p>

                @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
                @endif

                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fas fa-user input-icon"></i>
                            <input type="email" 
                                   name="email" 
                                   class="form-control" 
                                   placeholder="Email atau Username" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" 
                                   name="password" 
                                   class="form-control" 
                                   placeholder="Masukkan Password" 
                                   required>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>