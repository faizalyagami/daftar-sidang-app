<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Pendaftaran Sidang Skripsi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow-x: hidden;
        }
        
        /* Animated background */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
        }
        
        .bg-animation .circle {
            position: absolute;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            animation: float 20s infinite;
        }
        
        .circle:nth-child(1) {
            width: 300px;
            height: 300px;
            top: -100px;
            left: -100px;
            animation-delay: 0s;
        }
        
        .circle:nth-child(2) {
            width: 500px;
            height: 500px;
            bottom: -200px;
            right: -200px;
            animation-delay: 5s;
        }
        
        .circle:nth-child(3) {
            width: 200px;
            height: 200px;
            top: 50%;
            left: 50%;
            animation-delay: 10s;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(10deg);
            }
        }
        
        /* Login Container */
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
            padding: 20px;
        }
        
        /* Login Card */
        .login-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 30px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            backdrop-filter: blur(10px);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .login-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.3);
        }
        
        /* Header */
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .login-header::before {
            content: '🎓';
            position: absolute;
            font-size: 100px;
            right: -20px;
            bottom: -30px;
            opacity: 0.1;
            transform: rotate(-15deg);
        }
        
        .login-header i {
            font-size: 60px;
            margin-bottom: 15px;
            display: inline-block;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        
        .login-header h3 {
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .login-header p {
            opacity: 0.9;
            font-size: 14px;
        }
        
        /* Body */
        .login-body {
            padding: 40px;
        }
        
        /* Form Groups */
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }
        
        .input-group {
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            transition: all 0.3s;
            background: white;
        }
        
        .input-group:focus-within {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .input-group-text {
            background: transparent;
            border: none;
            color: #667eea;
            font-size: 18px;
            padding: 12px 15px;
        }
        
        .form-control {
            border: none;
            padding: 12px 15px;
            font-size: 14px;
            background: transparent;
        }
        
        .form-control:focus {
            box-shadow: none;
            outline: none;
        }
        
        /* Button */
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 14px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            color: white;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        /* Info Section */
        .login-footer {
            padding: 20px 40px 40px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
            background: #f8f9fa;
        }
        
        .info-text {
            font-size: 12px;
            color: #666;
        }
        
        .info-text i {
            color: #667eea;
            margin-right: 5px;
        }
        
        /* Alert */
        .alert-custom {
            border-radius: 15px;
            padding: 12px 20px;
            margin-bottom: 20px;
            border: none;
            font-size: 13px;
        }
        
        /* Demo Credentials */
        .demo-credentials {
            background: #f0f4ff;
            border-radius: 15px;
            padding: 15px;
            margin-top: 20px;
        }
        
        .demo-credentials h6 {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #667eea;
        }
        
        .demo-item {
            font-size: 12px;
            padding: 5px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .demo-item strong {
            min-width: 80px;
            color: #333;
        }
        
        .demo-item span {
            color: #666;
            font-family: monospace;
        }
        
        .copy-btn {
            cursor: pointer;
            color: #667eea;
            font-size: 14px;
            transition: color 0.3s;
        }
        
        .copy-btn:hover {
            color: #764ba2;
        }
        
        /* Loading Spinner */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-right: 8px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Responsive */
        @media (max-width: 480px) {
            .login-header {
                padding: 30px;
            }
            
            .login-body {
                padding: 30px;
            }
            
            .login-footer {
                padding: 20px 30px 30px;
            }
            
            .login-header i {
                font-size: 40px;
            }
            
            .login-header h3 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="bg-animation">
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
    </div>
    
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <i class="bi bi-mortarboard-fill"></i>
                <h3>Sistem Pendaftaran</h3>
                <p>Sidang Skripsi & Ujian Metodologi Penelitian</p>
            </div>
            
            <div class="login-body">
                @if($errors->any())
                    <div class="alert alert-danger alert-custom">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ $errors->first() }}
                    </div>
                @endif
                
                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf
                    
                    <div class="form-group">
                        <label>
                            <i class="bi bi-person-badge me-1"></i>
                            Username / NPM
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-person"></i>
                            </span>
                            <input type="text" 
                                   class="form-control @error('username') is-invalid @enderror" 
                                   name="username" 
                                   value="{{ old('username') }}" 
                                   placeholder="Masukkan NPM atau Username"
                                   autofocus
                                   required>
                        </div>
                        <small class="text-muted" style="font-size: 11px;">
                            <i class="bi bi-info-circle"></i> 
                            Gunakan NPM untuk login sebagai mahasiswa
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <i class="bi bi-lock me-1"></i>
                            Password
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-key"></i>
                            </span>
                            <input type="password" 
                                   class="form-control" 
                                   name="password" 
                                   placeholder="Masukkan password"
                                   required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-login" id="submitBtn">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </button>
                </form>
                
                <!-- Demo Credentials -->
                <div class="demo-credentials">
                    <h6>
                        <i class="bi bi-info-circle-fill"></i> 
                        Demo Akun
                    </h6>
                    <div class="demo-item">
                        <strong>Admin:</strong>
                        <span>admin</span>
                        <i class="bi bi-copy copy-btn" onclick="copyToClipboard('admin')"></i>
                    </div>
                    <div class="demo-item">
                        <strong>Reviewer:</strong>
                        <span>reviewer</span>
                        <i class="bi bi-copy copy-btn" onclick="copyToClipboard('reviewer')"></i>
                    </div>
                    <div class="demo-item">
                        <strong>Mahasiswa:</strong>
                        <span>1234567890</span>
                        <i class="bi bi-copy copy-btn" onclick="copyToClipboard('1234567890')"></i>
                    </div>
                    <div class="demo-item">
                        <strong>Password:</strong>
                        <span>password123</span>
                        <i class="bi bi-copy copy-btn" onclick="copyToClipboard('password123')"></i>
                    </div>
                </div>
            </div>
            
            <div class="login-footer">
                <p class="info-text">
                    <i class="bi bi-shield-check"></i>
                    Sistem Informasi Pendaftaran Sidang Skripsi
                </p>
                <p class="info-text mb-0">
                    Fakultas Psikologi Universitas Islam Bandung
                </p>
            </div>
        </div>
    </div>
    
    <script>
        // Loading state on form submit
        document.getElementById('loginForm')?.addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="loading-spinner"></span> Memproses...';
        });
        
        // Copy to clipboard function
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Show temporary tooltip
                const btn = event.target;
                const originalIcon = btn.className;
                btn.className = 'bi bi-check-circle-fill text-success';
                setTimeout(() => {
                    btn.className = originalIcon;
                }, 1000);
            });
        }
        
        // Add animation to input fields
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.parentElement.classList.remove('focused');
            });
        });
        
        // Enter key submit
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    document.getElementById('loginForm').submit();
                }
            });
        });
    </script>
</body>
</html>