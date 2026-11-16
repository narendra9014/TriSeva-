<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TriSeva - Official NSS Community Platform Register</title>

  <!-- Google Fonts & Lucide Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>

  <!-- Firebase App & Auth SDKs (v10 Modular via CDN) -->
  <script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js";
    import { getAuth, GoogleAuthProvider, signInWithPopup } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";

    // Your Firebase Configuration (Match this with your login page config)
    const firebaseConfig = {
      apiKey: "AIzaSyCAMP7_5PasMDCvEzr7JqdHVD9vaujTLJk",
      authDomain: "triseva-nss.firebaseapp.com",
      projectId: "triseva-nss",
      storageBucket: "triseva-nss.firebasestorage.app",
      messagingSenderId: "933562008425",
      appId: "1:933562008425:web:39787183a73e004c6873f8",
      measurementId: "G-0QW9TZWQX9"
    };

    // Initialize Firebase
    const app = initializeApp(firebaseConfig);
    const auth = getAuth(app);
    const provider = new GoogleAuthProvider();

    // Attach Google Sign-In listener to the button
    document.addEventListener("DOMContentLoaded", () => {
      const googleBtn = document.getElementById("googleRegisterBtn");
      if (googleBtn) {
        googleBtn.addEventListener("click", () => {
          signInWithPopup(auth, provider)
            .then((result) => {
              const user = result.user;
              
              // You can send user details (Name, Email, UID) to your backend
              console.log("Registered with Google:", user);

              // Redirect or process registration backend
              // E.g., window.location.href = "google_auth_process.php?email=" + encodeURIComponent(user.email) + "&name=" + encodeURIComponent(user.displayName);
              alert("Welcome " + user.displayName + "! Registration successful.");
              window.location.href = "dashboard.php"; 
            })
            .catch((error) => {
              console.error("Google Sign-In Error:", error.message);
              alert("Google Sign-In failed: " + error.message);
            });
        });
      }
    });
  </script>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Plus Jakarta Sans', sans-serif;
    }

    body {
      min-height: 100vh;
      background: #f1f5f9;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 20px;
      color: #0f172a;
      position: relative;
      overflow-x: hidden;
    }

    /* Background Geometric Overlays */
    .vertex-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: 
        radial-gradient(at 0% 0%, rgba(255, 153, 51, 0.08) 0px, transparent 50%),
        radial-gradient(at 100% 100%, rgba(19, 136, 8, 0.08) 0px, transparent 50%),
        radial-gradient(at 50% 50%, rgba(0, 31, 84, 0.05) 0px, transparent 50%);
      pointer-events: none;
      z-index: 1;
    }

    .vertex-grid {
      position: absolute;
      inset: 0;
      background-image: radial-gradient(#cbd5e1 1.2px, transparent 1.2px);
      background-size: 24px 24px;
      opacity: 0.6;
      pointer-events: none;
      z-index: 1;
    }

    /* Tricolor Ribbon */
    .tricolor-stripe {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #FF9933 0%, #FF9933 33.33%, #FFFFFF 33.33%, #FFFFFF 66.66%, #138808 66.66%, #138808 100%);
      z-index: 100;
    }

    .main-wrapper {
      width: 100%;
      max-width: 480px;
      z-index: 10;
      margin: 20px 0;
    }

    /* Government & NSS Header */
    .gov-header {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(12px);
      border-radius: 16px 16px 0 0;
      padding: 16px 24px;
      border: 1px solid rgba(226, 232, 240, 0.8);
      border-bottom: none;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
    }

    .gov-brand {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .nss-logo-svg {
      width: 48px;
      height: 48px;
      flex-shrink: 0;
      filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
    }

    .brand-titles h2 {
      font-size: 20px;
      font-weight: 800;
      color: #001f54;
      line-height: 1.1;
    }

    .brand-titles h2 span {
      color: #2563eb;
    }

    .brand-titles p {
      font-size: 11px;
      font-weight: 700;
      color: #64748b;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      margin-top: 2px;
    }

    .gov-badge {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      border-left: 1px solid #e2e8f0;
      padding-left: 12px;
    }

    .gov-badge span {
      font-size: 10px;
      font-weight: 800;
      color: #001f54;
      letter-spacing: 0.5px;
      text-transform: uppercase;
    }

    /* Form Card */
    .register-card {
      background: #ffffff;
      padding: 32px;
      border-radius: 0 0 20px 20px;
      border: 1px solid rgba(226, 232, 240, 0.9);
      box-shadow: 0 20px 25px -5px rgba(0, 31, 84, 0.05);
    }

    .motto-banner {
      background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%);
      border: 1px solid #dbeafe;
      border-left: 4px solid #001f54;
      padding: 10px 14px;
      border-radius: 8px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .motto-text {
      font-size: 12px;
      font-weight: 800;
      color: #001f54;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .motto-sub {
      font-size: 11px;
      font-weight: 600;
      color: #2563eb;
    }

    .card-title {
      font-size: 24px;
      font-weight: 800;
      color: #001f54;
      letter-spacing: -0.5px;
      margin-bottom: 4px;
    }

    .subtitle {
      color: #64748b;
      font-size: 13.5px;
      margin-bottom: 20px;
      font-weight: 500;
    }

    /* Google Sign-In Button */
    .google-btn {
      width: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
      background: #ffffff;
      border: 1.5px solid #cbd5e1;
      border-radius: 10px;
      padding: 12px;
      font-size: 14px;
      font-weight: 700;
      color: #334155;
      cursor: pointer;
      transition: all 0.2s ease;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    }

    .google-btn:hover {
      background: #f8fafc;
      border-color: #94a3b8;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    .google-icon {
      width: 18px;
      height: 18px;
    }

    /* Divider */
    .divider {
      display: flex;
      align-items: center;
      text-align: center;
      margin: 20px 0;
      color: #94a3b8;
      font-size: 12px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .divider::before, .divider::after {
      content: '';
      flex: 1;
      border-bottom: 1px solid #e2e8f0;
    }

    .divider span {
      padding: 0 12px;
    }

    /* Input Groups */
    .input-group {
      margin-bottom: 16px;
    }

    .input-group label {
      display: block;
      margin-bottom: 6px;
      font-size: 12.5px;
      font-weight: 700;
      color: #1e293b;
    }

    .input-wrapper {
      position: relative;
      display: flex;
      align-items: center;
    }

    .input-wrapper i.field-icon {
      position: absolute;
      left: 14px;
      color: #64748b;
      width: 18px;
      height: 18px;
    }

    .input-wrapper input {
      width: 100%;
      padding: 12px 14px 12px 42px;
      border: 1.5px solid #cbd5e1;
      border-radius: 10px;
      font-size: 14px;
      font-weight: 500;
      color: #0f172a;
      background: #f8fafc;
      transition: all 0.2s ease;
    }

    .input-wrapper input:focus {
      outline: none;
      border-color: #001f54;
      background: #ffffff;
      box-shadow: 0 0 0 3px rgba(0, 31, 84, 0.1);
    }

    .input-wrapper .toggle-pw {
      position: absolute;
      right: 14px;
      cursor: pointer;
      color: #94a3b8;
      width: 18px;
      height: 18px;
    }

    .input-wrapper .toggle-pw:hover {
      color: #001f54;
    }

    .register-btn {
      width: 100%;
      padding: 14px;
      background: #001f54;
      color: #ffffff;
      border: none;
      border-radius: 10px;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      margin-top: 8px;
      box-shadow: 0 4px 12px rgba(0, 31, 84, 0.2);
      transition: all 0.2s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .register-btn:hover {
      background: #003b8b;
      box-shadow: 0 6px 16px rgba(0, 59, 139, 0.3);
      transform: translateY(-1px);
    }

    .login-link {
      text-align: center;
      margin-top: 20px;
      font-size: 13.5px;
      color: #64748b;
      font-weight: 500;
    }

    .login-link a {
      text-decoration: none;
      color: #003b8b;
      font-weight: 700;
      margin-left: 4px;
    }

    .login-link a:hover {
      text-decoration: underline;
    }

    .footer-divider {
      margin-top: 24px;
      padding-top: 18px;
      border-top: 1px solid #f1f5f9;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .footer-text {
      color: #64748b;
      font-size: 12px;
      font-weight: 600;
    }

    .error-text {
      color: #dc2626;
      font-size: 12px;
      font-weight: 600;
      margin-top: 4px;
      display: none;
    }

    @media (max-width: 520px) {
      .main-wrapper {
        margin: 10px 0;
      }
      .register-card {
        padding: 24px 20px;
      }
    }
  </style>
</head>
<body>

  <div class="tricolor-stripe"></div>

  <div class="vertex-overlay"></div>
  <div class="vertex-grid"></div>

  <div class="main-wrapper">

    <!-- Official Header -->
    <div class="gov-header">
      <div class="gov-brand">
        <svg viewBox="0 0 100 100" class="nss-logo-svg">
          <circle cx="50" cy="50" r="48" fill="#001f54" />
          <circle cx="50" cy="50" r="40" fill="#ffffff" />
          <circle cx="50" cy="50" r="32" fill="#d32f2f" />
          <circle cx="50" cy="50" r="14" fill="#ffffff" />
          <g stroke="#ffffff" stroke-width="3">
            <line x1="50" y1="18" x2="50" y2="82" />
            <line x1="18" y1="50" x2="82" y2="50" />
            <line x1="27" y1="27" x2="73" y2="73" />
            <line x1="27" y1="73" x2="73" y2="27" />
          </g>
          <circle cx="50" cy="50" r="8" fill="#001f54" />
        </svg>

        <div class="brand-titles">
          <h2>Tri<span>Seva</span></h2>
          <p>NSS Smart Community</p>
        </div>
      </div>

      <div class="gov-badge">
        <span>Youth Affairs</span>
        <span>& Sports</span>
      </div>
    </div>

    <!-- Form Container -->
    <div class="register-card">

      <div class="motto-banner">
        <span class="motto-text">"NOT ME BUT YOU"</span>
        <span class="motto-sub">National Service Scheme</span>
      </div>

      <h1 class="card-title">Create Account</h1>
      <p class="subtitle">Join TriSeva to serve your community</p>

      <!-- Google Sign-In Button -->
      <button type="button" id="googleRegisterBtn" class="google-btn">
        <svg class="google-icon" viewBox="0 0 24 24">
          <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
          <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
          <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
          <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
        </svg>
        <span>Continue with Google</span>
      </button>

      <div class="divider">
        <span>or register with email</span>
      </div>

      <form id="registerForm" action="register_process.php" method="POST" onsubmit="return validatePasswords()">

        <!-- Full Name -->
        <div class="input-group">
          <label for="fullname">Full Name</label>
          <div class="input-wrapper">
            <i data-lucide="user" class="field-icon"></i>
            <input type="text" id="fullname" name="fullname" placeholder="Enter Full Name" required autocomplete="name">
          </div>
        </div>

        <!-- Email Address -->
        <div class="input-group">
          <label for="email">Email Address</label>
          <div class="input-wrapper">
            <i data-lucide="mail" class="field-icon"></i>
            <input type="email" id="email" name="email" placeholder="Enter Email Address" required autocomplete="email">
          </div>
        </div>

        <!-- Phone Number -->
        <div class="input-group">
          <label for="phone">Phone Number</label>
          <div class="input-wrapper">
            <i data-lucide="phone" class="field-icon"></i>
            <input type="tel" id="phone" name="phone" placeholder="Enter Mobile Number" required autocomplete="tel">
          </div>
        </div>

        <!-- Password -->
        <div class="input-group">
          <label for="password">Password</label>
          <div class="input-wrapper">
            <i data-lucide="lock" class="field-icon"></i>
            <input type="password" id="password" name="password" placeholder="Create Password" required autocomplete="new-password">
            <i data-lucide="eye" class="toggle-pw" onclick="togglePasswordVisibility('password', this)"></i>
          </div>
        </div>

        <!-- Confirm Password -->
        <div class="input-group">
          <label for="confirm_password">Confirm Password</label>
          <div class="input-wrapper">
            <i data-lucide="shield-check" class="field-icon"></i>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required autocomplete="new-password">
            <i data-lucide="eye" class="toggle-pw" onclick="togglePasswordVisibility('confirm_password', this)"></i>
          </div>
          <div id="errorMsg" class="error-text">Passwords do not match. Please verify again.</div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="register-btn">
          <span>Register</span>
          <i data-lucide="arrow-right" style="width: 18px; height: 18px;"></i>
        </button>

      </form>

      <div class="login-link">
        Already have an account?
        <a href="login.php">Login</a>
      </div>

      <div class="footer-divider">
        <i data-lucide="shield-heart" style="width: 15px; height: 15px; color: #138808;"></i>
        <p class="footer-text">Together for Healthcare, Safety & Environment</p>
      </div>

    </div>

  </div>

  <script>
    lucide.createIcons();

    function togglePasswordVisibility(inputId, icon) {
      const input = document.getElementById(inputId);
      if (input.type === 'password') {
        input.type = 'text';
        icon.setAttribute('data-lucide', 'eye-off');
      } else {
        input.type = 'password';
        icon.setAttribute('data-lucide', 'eye');
      }
      lucide.createIcons();
    }

    function validatePasswords() {
      const pw = document.getElementById('password').value;
      const confirmPw = document.getElementById('confirm_password').value;
      const errorMsg = document.getElementById('errorMsg');

      if (pw !== confirmPw) {
        errorMsg.style.display = 'block';
        return false;
      }
      errorMsg.style.display = 'none';
      return true;
    }
  </script>

</body>
</html>