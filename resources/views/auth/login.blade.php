<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login · Bellavella Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <style>
    /* Animated gradient orbs */
    .orb {
      position: absolute;
      border-radius: 50%;
      filter: blur(80px);
      opacity: 0.35;
      animation: float 8s ease-in-out infinite;
    }
    .orb-1 { width: 380px; height: 380px; background: #e0e7ff; top: -80px; right: -60px; animation-delay: 0s; }
    .orb-2 { width: 300px; height: 300px; background: #fce7f3; bottom: -60px; left: -80px; animation-delay: 3s; }
    .orb-3 { width: 200px; height: 200px; background: #f0fdf4; top: 50%; left: 30%; animation-delay: 5s; }
    @keyframes float {
      0%, 100% { transform: translateY(0) scale(1); }
      50%       { transform: translateY(-20px) scale(1.04); }
    }

    /* Card shimmer on hover */
    .login-card {
      transition: box-shadow 0.3s ease, transform 0.3s ease;
    }
    .login-card:hover {
      box-shadow: 0 24px 64px rgba(0,0,0,0.09);
      transform: translateY(-2px);
    }

    /* Input focus ring */
    .form-input:focus {
      border-color: rgba(0,0,0,0.4);
      box-shadow: 0 0 0 3px rgba(0,0,0,0.05);
    }

    /* Password toggle button */
    .pw-toggle { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #9ca3af; }
    .pw-toggle:hover { color: #374151; }

    /* Shake animation for error */
    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      20%       { transform: translateX(-6px); }
      40%       { transform: translateX(6px); }
      60%       { transform: translateX(-4px); }
      80%       { transform: translateX(4px); }
    }
    .shake { animation: shake 0.4s ease; }

    /* Submit button loading state */
    .btn-login { position: relative; overflow: hidden; }
    .btn-login::after {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.12), transparent);
      transform: translateX(-100%);
      transition: transform 0.4s;
    }
    .btn-login:hover::after { transform: translateX(100%); }
  </style>
</head>
<body class="antialiased min-h-screen flex items-center justify-center relative overflow-hidden" style="background:#F6F6F6;">

  <!-- Decorative orbs -->
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>
  <div class="orb orb-3"></div>

  <!-- Login Card -->
  <div class="login-card relative z-10 bg-white border border-gray-100 rounded-[2rem] shadow-xl w-full max-w-md mx-4 p-10">

    <!-- Logo -->
    <div class="flex items-center gap-3 mb-10">
      <div class="w-9 h-9 rounded-full bg-black flex items-center justify-center shadow-sm flex-shrink-0">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.5">
          <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"/>
          <path d="M2 12H22"/><path d="M12 2V22"/>
        </svg>
      </div>
      <span class="text-black font-semibold text-xl tracking-tight">Bellavella</span>
    </div>

    <!-- Heading -->
    <div class="mb-8">
      <h1 class="text-2xl font-semibold text-gray-900 tracking-tight mb-1">Welcome back</h1>
      <p class="text-sm text-gray-400">Sign in to your admin account</p>
    </div>

    @if($errors->any())
    <!-- Error alert -->
    <div id="errorAlert" class="shake flex items-center gap-3 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm font-medium mb-6">
      <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i>
      {{ $errors->first() }}
    </div>
    @endif

    <!-- Form -->
    <form method="POST" action="{{ route('login') }}" id="loginForm" class="space-y-5">
      @csrf

      <!-- Email -->
      <div>
        <label class="form-label">Email address</label>
        <div class="relative">
          <input
            type="email"
            name="email"
            id="email"
            value="admin@bellavella.com"
            placeholder="admin@bellavella.com"
            class="form-input pl-11"
            autocomplete="email"
          >
          <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
            <i data-lucide="mail" class="w-4 h-4"></i>
          </span>
        </div>
        <p class="text-[10px] text-gray-400 mt-1">Leave empty to auto-login as first user</p>
      </div>

      <!-- Password -->
      <div>
        <div class="flex items-center justify-between mb-2">
          <label class="form-label mb-0">Password</label>
          <a href="#" class="text-xs text-gray-400 hover:text-black transition-colors">Forgot password?</a>
        </div>
        <div class="relative">
          <input
            type="password"
            name="password"
            id="password"
            placeholder="••••••••"
            class="form-input pl-11 pr-11"
            autocomplete="current-password"
          >
          <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
            <i data-lucide="lock" class="w-4 h-4"></i>
          </span>
          <button type="button" class="pw-toggle" onclick="togglePassword()" id="pwToggle" aria-label="Toggle password">
            <i data-lucide="eye" class="w-4 h-4" id="pwIcon"></i>
          </button>
        </div>
      </div>

      <!-- Remember me -->
      <div class="flex items-center gap-2.5">
        <input
          type="checkbox"
          name="remember"
          id="remember"
          class="w-4 h-4 rounded border-gray-300 accent-black cursor-pointer"
        >
        <label for="remember" class="text-sm text-gray-500 cursor-pointer select-none">Remember me for 30 days</label>
      </div>

      <!-- Submit -->
      <button
        type="submit"
        id="submitBtn"
        class="btn-login btn btn-primary w-full justify-center py-3 text-base font-semibold rounded-xl mt-2"
      >
        <i data-lucide="log-in" class="w-4 h-4"></i>
        Sign In
      </button>

    </form>

    <!-- Footer -->
    <p class="text-center text-xs text-gray-400 mt-8">
      &copy; {{ date('Y') }} Bellavella. Admin Panel.
    </p>

  </div>

  <script>
    lucide.createIcons();

    // Password visibility toggle
    function togglePassword() {
      const input  = document.getElementById('password');
      const icon   = document.getElementById('pwIcon');
      const isHidden = input.type === 'password';
      input.type = isHidden ? 'text' : 'password';
      icon.setAttribute('data-lucide', isHidden ? 'eye-off' : 'eye');
      lucide.createIcons();
    }

    // Submit loading state
    document.getElementById('loginForm').addEventListener('submit', function () {
      const btn = document.getElementById('submitBtn');
      btn.innerHTML = '<svg class="animate-spin w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg> Signing in…';
      btn.disabled = true;
    });
  </script>
</body>
</html>
