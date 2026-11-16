<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TriSeva | NSS Smart Community Portal</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        :root {
            --nss-navy: #002147;
            --nss-navy-hover: #001229;
            --tricolor-saffron: #FF671F;
            --tricolor-green: #046A38;
            --accent-blue: #1A56DB;
            --bg-light: #F4F7FC;
            --card-bg: #FFFFFF;
            --text-main: #1E293B;
            --text-muted: #64748B;
            --border-color: #E2E8F0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: var(--bg-light);
            background-image: 
                radial-gradient(at 0% 0%, rgba(255, 103, 31, 0.06) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(4, 106, 56, 0.06) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(0, 33, 71, 0.03) 0px, transparent 100%);
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* NSS Watermark Background Image */
        body::before {
            content: "";
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background: url('logo.jpeg') center center no-repeat;
            background-size: 720px;
            opacity: 0.06;
            filter: blur(0.5px);
            z-index: -2;
            pointer-events: none;
        }

        /* Soft Gradient Overlay over background */
        body::after {
            content: "";
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background: linear-gradient(135deg, rgba(238, 244, 255, 0.4), rgba(255, 255, 255, 0.4));
            z-index: -1;
            pointer-events: none;
        }

        /* Top Header Branding */
        .gov-header {
            position: absolute;
            top: 12px;
            left: 32px;
            display: flex;
            align-items: center;
            gap: 14px;
            z-index: 10;
        }

        .gov-header img {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #fff;
            padding: 2px;
            box-shadow: 0 4px 14px rgba(0, 33, 71, 0.12);
            border: 2px solid #fff;
            object-fit: cover;
        }

        .gov-header-text h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--nss-navy);
            letter-spacing: -0.3px;
            line-height: 1.1;
        }

        .gov-header-text p {
            font-size: 12px;
            font-weight: 600;
            color: var(--tricolor-saffron);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Main Login Card Container */
        .login-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
            background: var(--card-bg);
            padding: 40px 36px;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0, 33, 71, 0.08);
            border: 1px solid rgba(226, 232, 240, 0.8);
            animation: cardSlideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes cardSlideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Tricolor Top Accent Line */
        .card-accent-bar {
            position: absolute;
            top: 0;
            left: 24px;
            right: 24px;
            height: 4px;
            background: linear-gradient(90deg, var(--tricolor-saffron) 0%, #FFFFFF 50%, var(--tricolor-green) 100%);
            border-radius: 0 0 4px 4px;
        }

        .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(0, 33, 71, 0.05);
            color: var(--nss-navy);
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .badge-pill i {
            color: var(--tricolor-saffron);
        }

        .login-card h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 30px;
            font-weight: 700;
            color: var(--nss-navy);
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }

        .subtitle {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 28px;
            line-height: 1.5;
        }

        /* Form Controls */
        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-main);
        }

        .input-box {
            display: flex;
            align-items: center;
            background: #F8FAFC;
            border: 1.5px solid var(--border-color);
            border-radius: 12px;
            padding: 0 16px;
            transition: all 0.25s ease;
        }

        .input-box:focus-within {
            background: #FFFFFF;
            border-color: var(--nss-navy);
            box-shadow: 0 0 0 4px rgba(0, 33, 71, 0.08);
        }

        .input-box i.input-icon {
            color: #94A3B8;
            font-size: 16px;
            margin-right: 12px;
            transition: color 0.2s ease;
        }

        .input-box:focus-within i.input-icon {
            color: var(--nss-navy);
        }

        .input-box input {
            border: none;
            outline: none;
            height: 48px;
            width: 100%;
            font-size: 14.5px;
            color: var(--text-main);
            background: transparent;
            font-weight: 500;
        }

        .input-box input::placeholder {
            color: #94A3B8;
            font-weight: 400;
        }

        .toggle-pwd {
            cursor: pointer;
            color: #94A3B8;
            padding: 4px;
            transition: color 0.2s ease;
        }

        .toggle-pwd:hover {
            color: var(--text-main);
        }

        /* Remember & Forgot Password */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            font-size: 13.5px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            color: var(--text-muted);
            font-weight: 500;
            user-select: none;
        }

        .remember-me input[type="checkbox"] {
            accent-color: var(--nss-navy);
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .forgot-link {
            text-decoration: none;
            color: var(--accent-blue);
            font-weight: 600;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            text-decoration: underline;
        }

        /* Submit Button */
        .login-btn {
            width: 100%;
            height: 50px;
            border: none;
            border-radius: 12px;
            background: var(--nss-navy);
            color: #FFFFFF;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.25s ease;
            box-shadow: 0 4px 12px rgba(0, 33, 71, 0.15);
        }

        .login-btn:hover {
            background: var(--nss-navy-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(0, 33, 71, 0.25);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            margin: 24px 0;
        }

        .divider::before, .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: var(--border-color);
        }

        .divider span {
            padding: 0 14px;
            font-size: 12px;
            font-weight: 600;
            color: #94A3B8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Google Sign-in Button */
        .google-btn {
            width: 100%;
            height: 48px;
            border-radius: 12px;
            border: 1.5px solid var(--border-color);
            background: #FFFFFF;
            color: var(--text-main);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            font-size: 14.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .google-btn:hover {
            background: #F8FAFC;
            border-color: #CBD5E1;
        }

        .google-btn img {
            width: 20px;
            height: 20px;
        }

        /* Footer & Links */
        .register-prompt {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: var(--text-muted);
        }

        .register-prompt a {
            color: var(--nss-navy);
            font-weight: 700;
            text-decoration: none;
            margin-left: 4px;
        }

        .register-prompt a:hover {
            text-decoration: underline;
        }

        .footer {
            margin-top: 32px;
            text-align: center;
            border-top: 1px solid #F1F5F9;
            padding-top: 20px;
        }

        .footer-motto {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--nss-navy);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .footer-copyright {
            font-size: 11.5px;
            color: #94A3B8;
            margin-top: 4px;
        }

        /* Responsive Adjustments */
        @media (max-width: 520px) {
            body {
                padding: 16px;
                align-items: flex-start;
                padding-top: 90px;
            }

            body::before {
                background-size: 450px;
            }

            .gov-header {
                top: 16px;
                left: 20px;
            }

            .gov-header img {
                width: 44px;
                height: 44px;
            }

            .gov-header-text h2 {
                font-size: 20px;
            }

            .login-card {
                padding: 32px 24px;
            }

            .login-card h1 {
                font-size: 26px;
            }
        }
    </style>
</head>
<body>

<!-- Header Branding -->
<div class="gov-header">
    <img src="trilogo1.png" alt="TriSeva Logo" onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/e/e3/NSS-symbol.png'">
    <div class="gov-header-text">
        <h2>TriSeva</h2>
        <p>Not Me, But You</p>
    </div>
</div>

<!-- Main Login Card -->
<div class="login-card">
    <div class="card-accent-bar"></div>

    <div class="badge-pill">
        <i class="fa-solid fa-flag"></i> National Service Scheme
    </div>

    <h1>Volunteer Sign In</h1>
    <p class="subtitle">Access your NSS portal to lead, serve, and transform communities.</p>

    <form action="login_process.php" method="POST" id="loginForm">
        <!-- Email Field -->
        <div class="input-group">
            <label for="email">Email Address</label>
            <div class="input-box">
                <i class="fa-solid fa-envelope input-icon"></i>
                <input 
                    type="email" 
                    id="email"
                    name="email" 
                    placeholder="student@mits.ac.in" 
                    required 
                    autocomplete="email">
            </div>
        </div>

        <!-- Password Field -->
        <div class="input-group">
            <label for="password">Password</label>
            <div class="input-box">
                <i class="fa-solid fa-lock input-icon"></i>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    placeholder="••••••••••••" 
                    required 
                    autocomplete="current-password">
                <i class="fa-solid fa-eye toggle-pwd" onclick="togglePassword()" id="eye" title="Toggle Password Visibility"></i>
            </div>
        </div>

        <!-- Options: Remember & Forgot Password -->
        <div class="form-options">
            <label class="remember-me">
                <input type="checkbox" name="remember">
                Remember session
            </label>
            <a href="forgot_password.php" class="forgot-link">Forgot password?</a>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="login-btn" id="loginBtn">
            <i class="fa-solid fa-right-to-bracket"></i>
            Sign In to Portal
        </button>
    </form>

    <!-- Divider -->
    <div class="divider">
        <span>or authentication via</span>
    </div>

    <!-- Google Login -->
    <button class="google-btn" type="button" id="googleLogin">
        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google Logo">
        Continue with Google
    </button>

    <!-- Registration Link -->
    <div class="register-prompt">
        New volunteer? <a href="register.php">Create Account</a>
    </div>

    <!-- Footer Information -->
    <div class="footer">
        <div class="footer-motto">
            <i class="fa-solid fa-hand-holding-heart" style="color: var(--tricolor-saffron);"></i>
            Empowering Youth for Community Service
        </div>
        <div class="footer-copyright">
            © 2026 TriSeva Platform | MITS University
        </div>
    </div>
</div>

<!-- Firebase Compat Scripts -->
<script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-auth-compat.js"></script>

<script>
    // Firebase Config Initialization
    const firebaseConfig = {
        apiKey: "AIzaSyCAMP7_5PasMDCvEzr7JqdHVD9vaujTLJk",
        authDomain: "triseva-nss.firebaseapp.com",
        projectId: "triseva-nss",
        storageBucket: "triseva-nss.firebasestorage.app",
        messagingSenderId: "933562008425",
        appId: "1:933562008425:web:39787183a73e004c6873f8",
        measurementId: "G-0QW9TZWQX9"
    };
    firebase.initializeApp(firebaseConfig);
    const auth = firebase.auth();

    // Toggle Password Visibility
    function togglePassword() {
        const password = document.getElementById("password");
        const eye = document.getElementById("eye");

        if (password.type === "password") {
            password.type = "text";
            eye.classList.remove("fa-eye");
            eye.classList.add("fa-eye-slash");
        } else {
            password.type = "password";
            eye.classList.remove("fa-eye-slash");
            eye.classList.add("fa-eye");
        }
    }

    // Form Submission Loader
    document.getElementById("loginForm").addEventListener("submit", function () {
        const btn = document.getElementById("loginBtn");
        btn.disabled = true;
        btn.style.opacity = "0.8";
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Authenticating...';
    });

    // Google Sign-In Trigger
    document.getElementById("googleLogin").addEventListener("click", function () {
        const provider = new firebase.auth.GoogleAuthProvider();
        auth.signInWithPopup(provider)
        .then((result) => {
            const user = result.user;
            fetch("google_login.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    name: user.displayName,
                    email: user.email,
                    photo: user.photoURL,
                    uid: user.uid
                })
            })
            .then(response => response.text())
            .then(data => {
                console.log(data);
                window.location.href = "home.php";
            });
        })
        .catch((error) => {
            console.error(error);
            alert(error.message);
        });
    });
</script>

</body>
</html>