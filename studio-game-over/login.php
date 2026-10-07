<?php
/**
 * MathPlay Solutions — Login Page
 * Studio Game Over
 */

require_once 'config/database.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if already authenticated
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MathPlay Solutions — Faça login para continuar jogando">
    <title>Login — MathPlay Solutions</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body class="auth-body">

    <!-- Animated background particles -->
    <div class="particles-container" aria-hidden="true">
        <?php for ($i = 1; $i <= 20; $i++): ?>
            <div class="particle particle-<?= $i ?>"></div>
        <?php endfor; ?>
    </div>

    <div class="auth-wrapper">

        <!-- ===== LEFT COLUMN — Branding ===== -->
        <div class="auth-brand">

            <!-- Floating geometric shapes -->
            <div class="geo-shapes" aria-hidden="true">
                <div class="geo geo-circle"></div>
                <div class="geo geo-triangle"></div>
                <div class="geo geo-square"></div>
                <div class="geo geo-diamond"></div>
                <div class="geo geo-hexagon"></div>
            </div>

            <div class="brand-content">
                <!-- Studio logo area -->
                <div class="studio-logo">
                    <div class="studio-icon" aria-hidden="true">
                        <svg viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="40" cy="40" r="38" stroke="url(#grad_logo)" stroke-width="2" fill="rgba(139,92,246,0.1)"/>
                            <path d="M20 50 L40 20 L60 50 Z" fill="none" stroke="url(#grad_logo)" stroke-width="2" stroke-linejoin="round"/>
                            <circle cx="40" cy="42" r="8" fill="url(#grad_logo)" opacity="0.8"/>
                            <path d="M32 56 Q40 50 48 56" stroke="url(#grad_logo)" stroke-width="2" fill="none" stroke-linecap="round"/>
                            <defs>
                                <linearGradient id="grad_logo" x1="0" y1="0" x2="80" y2="80" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#c77dff"/>
                                    <stop offset="100%" stop-color="#ff007f"/>
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                    <p class="studio-name">STUDIO GAME OVER</p>
                    <p class="studio-presents"><em>apresenta</em></p>
                </div>

                <!-- Main title -->
                <div class="brand-title-block">
                    <h1 class="brand-main-title">MATHPLAY<br>SOLUTIONS</h1>
                    <p class="brand-subtitle">Transformando o aprendizado em aventura</p>
                </div>

                <!-- Decorative stats -->
                <div class="brand-stats">
                    <div class="brand-stat">
                        <span class="stat-num">2</span>
                        <span class="stat-label">Jogos</span>
                    </div>
                    <div class="brand-stat-divider"></div>
                    <div class="brand-stat">
                        <span class="stat-num">∞</span>
                        <span class="stat-label">Aventuras</span>
                    </div>
                    <div class="brand-stat-divider"></div>
                    <div class="brand-stat">
                        <span class="stat-num">100%</span>
                        <span class="stat-label">Diversão</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== RIGHT COLUMN — Login Form ===== -->
        <div class="auth-form-side">
            <div class="auth-card">

                <!-- Card header -->
                <div class="auth-card-header">
                    <div class="auth-card-icon" aria-hidden="true">
                        <i data-lucide="gamepad-2"></i>
                    </div>
                    <h2 class="auth-card-title">Bem-vindo de volta!</h2>
                    <p class="auth-card-subtitle">Acesse sua conta e continue jogando</p>
                </div>

                <!-- Error message -->
                <div id="login-error" class="feedback-box feedback-error" role="alert" aria-live="assertive" hidden>
                    <i data-lucide="alert-circle"></i>
                    <span id="login-error-msg"></span>
                </div>

                <!-- Login Form -->
                <form id="login-form" novalidate autocomplete="on">

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i data-lucide="mail"></i> E-mail
                        </label>
                        <div class="input-wrapper">
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control"
                                placeholder="seu@email.com"
                                required
                                autocomplete="email"
                                aria-required="true"
                            >
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="senha" class="form-label">
                            <i data-lucide="lock"></i> Senha
                        </label>
                        <div class="input-wrapper input-password">
                            <input
                                type="password"
                                id="senha"
                                name="senha"
                                class="form-control"
                                placeholder="Sua senha"
                                required
                                autocomplete="current-password"
                                aria-required="true"
                            >
                            <button
                                type="button"
                                class="btn-toggle-password"
                                id="toggle-senha"
                                aria-label="Mostrar senha"
                                tabindex="0"
                            >
                                <i data-lucide="eye" id="eye-icon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember me -->
                    <div class="form-check-row">
                        <label class="form-check">
                            <input type="checkbox" id="lembrar" name="lembrar" class="form-check-input">
                            <span class="form-check-custom"></span>
                            <span class="form-check-label">Lembrar de mim</span>
                        </label>
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        id="btn-login"
                        class="btn btn-primary btn-lg w-full"
                        aria-label="Entrar na conta"
                    >
                        <span class="btn-text">
                            <i data-lucide="log-in"></i>
                            ENTRAR
                        </span>
                        <span class="btn-loading" hidden>
                            <span class="spinner"></span>
                            Carregando...
                        </span>
                    </button>

                </form>

                <!-- Footer link -->
                <p class="auth-card-footer">
                    Ainda não tem conta?
                    <a href="cadastro.php" class="auth-link">Cadastre-se</a>
                </p>

            </div><!-- /.auth-card -->
        </div><!-- /.auth-form-side -->

    </div><!-- /.auth-wrapper -->

    <script>
    (function () {
        'use strict';

        // Initialize Lucide icons
        lucide.createIcons();

        /* ── Password toggle ── */
        const toggleBtn  = document.getElementById('toggle-senha');
        const senhaInput = document.getElementById('senha');
        const eyeIcon    = document.getElementById('eye-icon');

        toggleBtn.addEventListener('click', function () {
            const isPassword = senhaInput.type === 'password';
            senhaInput.type  = isPassword ? 'text' : 'password';
            toggleBtn.setAttribute('aria-label', isPassword ? 'Ocultar senha' : 'Mostrar senha');
            eyeIcon.setAttribute('data-lucide', isPassword ? 'eye-off' : 'eye');
            lucide.createIcons();
        });

        /* ── Form submit ── */
        const form      = document.getElementById('login-form');
        const btnLogin  = document.getElementById('btn-login');
        const errorBox  = document.getElementById('login-error');
        const errorMsg  = document.getElementById('login-error-msg');

        function showError(msg) {
            errorMsg.textContent = msg;
            errorBox.hidden = false;
            lucide.createIcons();
            errorBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function hideError() {
            errorBox.hidden = true;
        }

        function setLoading(loading) {
            const btnText    = btnLogin.querySelector('.btn-text');
            const btnLoading = btnLogin.querySelector('.btn-loading');
            btnLogin.disabled = loading;
            btnText.hidden    = loading;
            btnLoading.hidden = !loading;
        }

        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            hideError();

            const email = document.getElementById('email').value.trim();
            const senha = document.getElementById('senha').value;

            // Client-side validation
            if (!email) { showError('Por favor, informe seu e-mail.'); return; }
            if (!senha)  { showError('Por favor, informe sua senha.'); return; }

            setLoading(true);

            try {
                const formData = new FormData();
                formData.append('email', email);
                formData.append('senha', senha);

                const response = await fetch('api/login.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    throw new Error('Erro de servidor: ' + response.status);
                }

                const data = await response.json();

                if (data.success) {
                    // Brief success feedback before redirect
                    btnLogin.querySelector('.btn-loading').innerHTML =
                        '<span class="spinner"></span> Entrando...';
                    setTimeout(function () {
                        window.location.href = 'dashboard.php';
                    }, 600);
                } else {
                    showError(data.message || 'Erro ao fazer login. Tente novamente.');
                    setLoading(false);
                }

            } catch (err) {
                console.error('[Login Error]', err);
                showError('Falha na conexão. Verifique sua internet e tente novamente.');
                setLoading(false);
            }
        });

        /* ── Subtle input focus highlight ── */
        document.querySelectorAll('.form-control').forEach(function (input) {
            input.addEventListener('focus', function () {
                input.closest('.input-wrapper').classList.add('focused');
            });
            input.addEventListener('blur', function () {
                input.closest('.input-wrapper').classList.remove('focused');
            });
        });

    })();
    </script>

</body>
</html>
