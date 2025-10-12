<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KAIZEN 360 - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-color: #10b981;
            --error-color: #ef4444;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --glass-bg: rgba(255, 255, 255, 0.25);
            --glass-border: rgba(255, 255, 255, 0.18);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(-45deg, #667eea, #764ba2, #f093fb, #f5576c);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Animated background particles */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(120, 219, 255, 0.3) 0%, transparent 50%);
            animation: float 20s ease-in-out infinite;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .login-container {
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 10;
        }

        .login-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: 
                0 8px 32px rgba(31, 38, 135, 0.37),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            overflow: hidden;
            animation: slideUp 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(60px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .login-header {
            background: var(--primary-gradient);
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(from 0deg, rgba(255,255,255,0) 0deg, rgba(255,255,255,0.1) 90deg, rgba(255,255,255,0) 180deg);
            animation: rotate 15s linear infinite;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .logo-container {
            position: relative;
            z-index: 1;
            margin-bottom: 20px;
        }

        .logo-icon {
            width: 70px;
            height: 70px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            animation: logoFloat 3s ease-in-out infinite;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-10px) rotate(5deg); }
        }

        .logo-icon i {
            font-size: 32px;
            color: #ffd700;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));
        }

        .login-header h1 {
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
            letter-spacing: -0.5px;
        }

        .login-header p {
            font-size: 16px;
            opacity: 0.9;
            position: relative;
            z-index: 1;
            font-weight: 400;
            text-shadow: 0 1px 3px rgba(0,0,0,0.3);
        }

        .login-form {
            padding: 40px 30px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .form-group {
            margin-bottom: 28px;
            position: relative;
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.2s; }
        .form-group:nth-child(3) { animation-delay: 0.3s; }

        .form-label {
            display: block;
            margin-bottom: 12px;
            font-weight: 600;
            color: var(--text-primary);
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            position: relative;
        }

        .form-label::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary-gradient);
            transition: width 0.3s ease;
        }

        .form-group:focus-within .form-label::after {
            width: 30px;
        }

        .input-container {
            position: relative;
        }

        .form-control {
            width: 100%;
            padding: 18px 24px 18px 54px;
            border: 2px solid #e5e7eb;
            border-radius: 16px;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            color: var(--text-primary);
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 
                0 0 0 4px rgba(102, 126, 234, 0.1),
                0 8px 25px rgba(102, 126, 234, 0.15);
            transform: translateY(-2px);
        }

        .form-control:hover:not(:focus) {
            border-color: #cbd5e0;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .input-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: var(--text-secondary);
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .form-group:focus-within .input-icon {
            color: #667eea;
            transform: translateY(-50%) scale(1.1);
        }

        select.form-control {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 20px center;
            background-repeat: no-repeat;
            background-size: 16px;
            padding-right: 50px;
        }

        .btn {
            width: 100%;
            padding: 20px;
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            margin-top: 16px;
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            animation: fadeInUp 0.6s ease-out 0.4s forwards;
            opacity: 0;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.6s ease;
        }

        .btn:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
            background: var(--secondary-gradient);
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn:active {
            transform: translateY(-2px);
        }

        .btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .spinner-border {
            width: 1.2rem;
            height: 1.2rem;
            border: 0.15em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .d-none {
            display: none !important;
        }

        .alert {
            margin: 20px 30px;
            padding: 18px 24px;
            border-radius: 16px;
            font-size: 14px;
            font-weight: 600;
            border: none;
            backdrop-filter: blur(10px);
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
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
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            border-left: 4px solid #dc2626;
        }

        .invalid-feedback {
            display: block;
            margin-top: 8px;
            font-size: 13px;
            color: var(--error-color);
            font-weight: 600;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .is-invalid {
            border-color: var(--error-color) !important;
            background: rgba(239, 68, 68, 0.05) !important;
            animation: shake 0.5s ease-in-out;
        }

        .is-invalid:focus {
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1) !important;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Enhanced responsive design */
        @media (max-width: 768px) {
            .login-container {
                margin: 0 15px;
                max-width: 380px;
            }
            
            .login-header {
                padding: 40px 25px 30px;
            }
            
            .login-form {
                padding: 30px 25px;
            }

            .login-header h1 {
                font-size: 28px;
            }

            .form-control {
                padding: 16px 20px 16px 50px;
                font-size: 16px;
            }

            .btn {
                padding: 18px;
                font-size: 15px;
            }

            .logo-icon {
                width: 60px;
                height: 60px;
            }

            .logo-icon i {
                font-size: 28px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 15px;
            }
            
            .login-card {
                border-radius: 20px;
            }
            
            .login-header {
                padding: 35px 20px 25px;
            }
            
            .login-form {
                padding: 25px 20px;
            }
        }

        /* Success state animations */
        .success-state {
            animation: successPulse 0.6s ease-out;
        }

        @keyframes successPulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        /* Focus indicators for accessibility */
        .form-control:focus-visible,
        .btn:focus-visible {
            outline: 2px solid #667eea;
            outline-offset: 2px;
        }

    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
<img src="{{ asset('images/Kaizen logo.png') }}" alt="Kaizen Logo" class="img-fluid" style="max-height: 200px;">
            </div>

            <!-- Error Display Section -->
            <div id="errorAlert" class="alert alert-danger" style="display: none;"></div>

            <form method="POST" action="{{ config('app.url') }}/login" id="loginForm" class="login-form">
                @csrf
                
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           placeholder="Enter your email address"
                           required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           id="password" 
                           name="password" 
                           placeholder="Enter your password"
                           required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
              
           <!--     <div class="form-group">
                    <label for="role" class="form-label">Select Role</label>
                    <select class="form-control @error('role') is-invalid @enderror" 
                            id="role" 
                            name="role" 
                            required>
                        <option value="">Choose your role</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="purchase_team" {{ old('role') == 'purchase_team' ? 'selected' : '' }}>Purchase Team</option>
                        <option value="inventory_manager" {{ old('role') == 'inventory_manager' ? 'selected' : '' }}>Inventory Manager</option>
                      <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div> -->
                
                <button type="submit" class="btn" id="loginBtn">
                    <span class="spinner-border spinner-border-sm d-none" role="status" id="loginSpinner"></span>
                    <i class="fas fa-sign-in-alt"></i>
                    <span id="loginText">Sign In</span>
                </button>
            </form>
        </div>
    </div>
<!-- CSRF META TAG - Add this to your layout head -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<script>
// CSRF TOKEN SETUP
window.Laravel = {
    csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
};

// Set up CSRF token for all AJAX requests
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': window.Laravel.csrfToken
    }
});

// Alternative for vanilla JS fetch requests
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Allow normal form submission - no JavaScript override

// IMPROVED CSRF TOKEN GETTER
function getCSRFToken() {
    // Try multiple methods to get CSRF token
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    if (tokenMeta) {
        return tokenMeta.getAttribute('content');
    }
    
    const tokenInput = document.querySelector('input[name="_token"]');
    if (tokenInput) {
        return tokenInput.value;
    }
    
    // Last resort - try to get from Laravel window object
    if (window.Laravel && window.Laravel.csrfToken) {
        return window.Laravel.csrfToken;
    }
    
    console.error('CSRF token not found!');
    return '';
}

// REFRESH CSRF TOKEN FUNCTION
function refreshCSRFToken() {
    fetch('/refresh-csrf', {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.token) {
            // Update meta tag
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            if (metaTag) {
                metaTag.setAttribute('content', data.token);
            }
            
            // Update all forms
            document.querySelectorAll('input[name="_token"]').forEach(input => {
                input.value = data.token;
            });
            
            // Update Laravel object
            if (window.Laravel) {
                window.Laravel.csrfToken = data.token;
            }
        }
    })
    .catch(error => {
        console.error('Failed to refresh CSRF token:', error);
    });
}

// AUTO-REFRESH CSRF TOKEN EVERY 30 MINUTES
setInterval(refreshCSRFToken, 30 * 60 * 1000);

// HANDLE CSRF TOKEN EXPIRATION
window.addEventListener('beforeunload', function() {
    // Refresh token before page unload
    refreshCSRFToken();
});
</script>
   
 </body>
</html>
