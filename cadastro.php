<?php
/**
 * MathPlay Solutions — Registration Page
 * Studio Game Over
 */

require_once 'config/database.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if already authenticated
if (isset($_SESSION['user_id'])) {
    header('Location: ' . (($_SESSION['tipo_usuario'] ?? 'aluno') === 'professor' ? 'professor_dashboard.php' : 'dashboard.php'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MathPlay Solutions — Crie sua conta e comece a jogar">
    <title>Cadastro — MathPlay Solutions</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>

    <!-- Stylesheets -->
    <link rel="stylesheet" href="assets/css/style.css?v=<?= filemtime(__DIR__ . '/assets/css/style.css') ?>">
    <link rel="stylesheet" href="assets/css/login.css?v=<?= filemtime(__DIR__ . '/assets/css/login.css') ?>">
    <link rel="stylesheet" href="assets/css/accessibility.css?v=<?= filemtime(__DIR__ . '/assets/css/accessibility.css') ?>">
    <script src="assets/js/accessibility.js?v=<?= filemtime(__DIR__ . '/assets/js/accessibility.js') ?>" defer></script>
</head>
<body class="auth-body auth-cadastro-body">

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
                            <circle cx="40" cy="40" r="38" stroke="url(#grad_logo2)" stroke-width="2" fill="rgba(139,92,246,0.1)"/>
                            <path d="M20 50 L40 20 L60 50 Z" fill="none" stroke="url(#grad_logo2)" stroke-width="2" stroke-linejoin="round"/>
                            <circle cx="40" cy="42" r="8" fill="url(#grad_logo2)" opacity="0.8"/>
                            <path d="M32 56 Q40 50 48 56" stroke="url(#grad_logo2)" stroke-width="2" fill="none" stroke-linecap="round"/>
                            <defs>
                                <linearGradient id="grad_logo2" x1="0" y1="0" x2="80" y2="80" gradientUnits="userSpaceOnUse">
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
                </div>

                <!-- Join tagline -->
                <p class="brand-join-text">
                    Junte-se a milhares de estudantes que já tornaram o aprendizado uma aventura!
                </p>

                <!-- Feature list -->
                <ul class="brand-features" role="list">
                    <li class="brand-feature">
                        <span class="feature-icon" aria-hidden="true">🎮</span>
                        <div>
                            <strong>2 jogos educativos</strong>
                            <p>Matemática e Português de forma divertida</p>
                        </div>
                    </li>
                    <li class="brand-feature">
                        <span class="feature-icon" aria-hidden="true">🏆</span>
                        <div>
                            <strong>Sistema de medalhas e níveis</strong>
                            <p>Evolua seu personagem ao aprender</p>
                        </div>
                    </li>
                    <li class="brand-feature">
                        <span class="feature-icon" aria-hidden="true">📊</span>
                        <div>
                            <strong>Acompanhe seu progresso</strong>
                            <p>Visualize sua evolução em tempo real</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- ===== RIGHT COLUMN — Registration Form ===== -->
        <div class="auth-form-side auth-form-side--wide">
            <div class="auth-card auth-card--cadastro">

                <!-- Card header -->
                <div class="auth-card-header">
                    <div class="auth-card-icon" aria-hidden="true">
                        <i data-lucide="user-plus"></i>
                    </div>
                    <h2 class="auth-card-title">Crie sua conta</h2>
                    <p class="auth-card-subtitle">Selecione seu tipo de conta e preencha seus dados</p>
                </div>

                <!-- Feedback message -->
                <div id="cadastro-feedback" class="feedback-box" role="alert" aria-live="assertive" hidden></div>

                <!-- Registration Form -->
                <form id="cadastro-form" novalidate autocomplete="off">

                    <div class="form-group">
                        <label for="tipo_usuario" class="form-label">
                            <i data-lucide="users"></i> Tipo de conta
                        </label>
                        <div class="input-wrapper">
                            <select id="tipo_usuario" name="tipo_usuario" class="form-control form-select" required aria-required="true">
                                <option value="aluno" selected>Aluno</option>
                                <option value="professor">Professor</option>
                            </select>
                        </div>
                    </div>

                    <div class="student-only-fields">
                    <!-- ===== AVATAR SELECTION ===== -->
                    <fieldset class="avatar-fieldset">
                        <legend class="avatar-legend">
                            <i data-lucide="shield"></i>
                            Escolha seu Avatar Gamer
                        </legend>

                        <div class="avatar-grid" role="radiogroup" aria-label="Selecione um avatar">

                            <!-- Avatar 1: Warrior -->
                            <div class="avatar-option selected" data-avatar="avatar1" tabindex="0" role="radio" aria-checked="true" aria-label="Guerreiro">
                                <svg viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="avatar-svg">
                                    <!-- Body -->
                                    <circle cx="30" cy="30" r="28" fill="#1e3a5f"/>
                                    <!-- Armor torso -->
                                    <rect x="19" y="32" width="22" height="16" rx="3" fill="#2563eb"/>
                                    <rect x="22" y="32" width="16" height="3" fill="#60a5fa"/>
                                    <!-- Head -->
                                    <circle cx="30" cy="24" r="10" fill="#fcd34d"/>
                                    <!-- Helmet -->
                                    <path d="M20 22 Q30 10 40 22 L40 18 Q30 6 20 18 Z" fill="#1d4ed8"/>
                                    <rect x="27" y="10" width="6" height="5" rx="1" fill="#93c5fd"/>
                                    <!-- Eyes -->
                                    <circle cx="26" cy="25" r="2" fill="#1e3a5f"/>
                                    <circle cx="34" cy="25" r="2" fill="#1e3a5f"/>
                                    <!-- Shield (left) -->
                                    <path d="M10 34 L10 46 L17 50 L17 34 Z" fill="#1d4ed8" stroke="#60a5fa" stroke-width="1"/>
                                    <circle cx="13" cy="42" r="3" fill="#60a5fa"/>
                                    <!-- Sword (right) -->
                                    <rect x="43" y="20" width="3" height="22" rx="1" fill="#d1d5db"/>
                                    <rect x="40" y="32" width="9" height="3" rx="1" fill="#fbbf24"/>
                                    <rect x="44" y="18" width="2" height="4" rx="1" fill="#fbbf24"/>
                                </svg>
                                <span class="avatar-label">Guerreiro</span>
                            </div>

                            <!-- Avatar 2: Mage -->
                            <div class="avatar-option" data-avatar="avatar2" tabindex="0" role="radio" aria-checked="false" aria-label="Mago">
                                <svg viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="avatar-svg">
                                    <!-- Background -->
                                    <circle cx="30" cy="30" r="28" fill="#2d1b69"/>
                                    <!-- Robe body -->
                                    <path d="M16 35 L18 55 L42 55 L44 35 Q30 42 16 35Z" fill="#7c3aed"/>
                                    <path d="M22 35 L20 55 L40 55 L38 35 Q30 40 22 35Z" fill="#6d28d9"/>
                                    <!-- Head -->
                                    <circle cx="30" cy="26" r="9" fill="#fde68a"/>
                                    <!-- Wizard hat -->
                                    <path d="M18 26 L30 4 L42 26 Z" fill="#7c3aed"/>
                                    <rect x="16" y="24" width="28" height="5" rx="2" fill="#a78bfa"/>
                                    <!-- Hat star -->
                                    <text x="27" y="20" font-size="7" fill="#fbbf24">★</text>
                                    <!-- Eyes -->
                                    <circle cx="26" cy="27" r="2" fill="#1e1b4b"/>
                                    <circle cx="34" cy="27" r="2" fill="#1e1b4b"/>
                                    <!-- Stars around -->
                                    <text x="8" y="30" font-size="8" fill="#fbbf24" opacity="0.8">✦</text>
                                    <text x="46" y="22" font-size="6" fill="#a78bfa">✦</text>
                                    <text x="10" y="48" font-size="5" fill="#c4b5fd">★</text>
                                    <text x="46" y="44" font-size="7" fill="#fbbf24" opacity="0.7">✦</text>
                                    <!-- Staff -->
                                    <rect x="44" y="22" width="3" height="26" rx="1" fill="#8b5cf6"/>
                                    <circle cx="45" cy="20" r="5" fill="#a78bfa"/>
                                    <circle cx="45" cy="20" r="3" fill="#fbbf24"/>
                                </svg>
                                <span class="avatar-label">Mago</span>
                            </div>

                            <!-- Avatar 3: Archer -->
                            <div class="avatar-option" data-avatar="avatar3" tabindex="0" role="radio" aria-checked="false" aria-label="Arqueiro">
                                <svg viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="avatar-svg">
                                    <!-- Background -->
                                    <circle cx="30" cy="30" r="28" fill="#14532d"/>
                                    <!-- Body / tunic -->
                                    <path d="M18 35 L18 54 L42 54 L42 35 Q30 42 18 35Z" fill="#16a34a"/>
                                    <!-- Green hood cloak -->
                                    <path d="M16 26 Q30 16 44 26 L42 35 Q30 42 18 35 Z" fill="#15803d"/>
                                    <!-- Head -->
                                    <circle cx="30" cy="24" r="9" fill="#fde68a"/>
                                    <!-- Hood over head -->
                                    <path d="M18 22 Q30 10 42 22 L42 26 Q30 30 18 26 Z" fill="#15803d"/>
                                    <!-- Eyes -->
                                    <circle cx="26" cy="25" r="2" fill="#166534"/>
                                    <circle cx="34" cy="25" r="2" fill="#166534"/>
                                    <!-- Smile -->
                                    <path d="M26 29 Q30 32 34 29" stroke="#d97706" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                                    <!-- Bow -->
                                    <path d="M8 16 Q4 30 8 44" stroke="#92400e" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                                    <line x1="8" y1="16" x2="8" y2="44" stroke="#fbbf24" stroke-width="1"/>
                                    <!-- Arrow -->
                                    <line x1="10" y1="30" x2="46" y2="30" stroke="#d97706" stroke-width="1.5"/>
                                    <path d="M42 27 L47 30 L42 33 Z" fill="#d97706"/>
                                    <path d="M12 28 L10 30 L12 32" stroke="#16a34a" stroke-width="1.5" fill="none"/>
                                </svg>
                                <span class="avatar-label">Arqueiro</span>
                            </div>

                            <!-- Avatar 4: Ninja -->
                            <div class="avatar-option" data-avatar="avatar4" tabindex="0" role="radio" aria-checked="false" aria-label="Ninja">
                                <svg viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="avatar-svg">
                                    <!-- Background -->
                                    <circle cx="30" cy="30" r="28" fill="#111827"/>
                                    <!-- Body -->
                                    <path d="M18 36 L18 54 L42 54 L42 36 Q30 44 18 36Z" fill="#1f2937"/>
                                    <!-- Belt / sash -->
                                    <rect x="18" y="38" width="24" height="5" rx="1" fill="#dc2626"/>
                                    <!-- Head -->
                                    <circle cx="30" cy="26" r="10" fill="#374151"/>
                                    <!-- Mask (lower face cover) -->
                                    <path d="M20 26 Q30 36 40 26 L40 30 Q30 40 20 30 Z" fill="#111827"/>
                                    <!-- Eyes — red glowing -->
                                    <circle cx="25" cy="25" r="2.5" fill="#dc2626"/>
                                    <circle cx="35" cy="25" r="2.5" fill="#dc2626"/>
                                    <circle cx="25" cy="25" r="1" fill="#fca5a5"/>
                                    <circle cx="35" cy="25" r="1" fill="#fca5a5"/>
                                    <!-- Head band -->
                                    <rect x="20" y="18" width="20" height="5" rx="2" fill="#111827"/>
                                    <rect x="28" y="17" width="4" height="7" rx="1" fill="#dc2626"/>
                                    <!-- Throwing star left -->
                                    <g transform="translate(8, 30) rotate(15)">
                                        <polygon points="5,0 6,4 10,4 7,7 8,11 5,9 2,11 3,7 0,4 4,4" fill="#9ca3af" stroke="#d1d5db" stroke-width="0.5"/>
                                    </g>
                                    <!-- Throwing star right -->
                                    <g transform="translate(40, 18) rotate(-20)">
                                        <polygon points="5,0 6,4 10,4 7,7 8,11 5,9 2,11 3,7 0,4 4,4" fill="#9ca3af" stroke="#d1d5db" stroke-width="0.5"/>
                                    </g>
                                </svg>
                                <span class="avatar-label">Ninja</span>
                            </div>

                            <!-- Avatar 5: Robot -->
                            <div class="avatar-option" data-avatar="avatar5" tabindex="0" role="radio" aria-checked="false" aria-label="Robô">
                                <svg viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="avatar-svg">
                                    <!-- Background -->
                                    <circle cx="30" cy="30" r="28" fill="#0f172a"/>
                                    <!-- Body box -->
                                    <rect x="16" y="34" width="28" height="20" rx="4" fill="#334155"/>
                                    <rect x="20" y="38" width="20" height="8" rx="2" fill="#0f172a"/>
                                    <!-- Power button on chest -->
                                    <circle cx="30" cy="42" r="3" fill="#22d3ee"/>
                                    <rect x="29" y="39" width="2" height="4" fill="#0f172a"/>
                                    <!-- Head box -->
                                    <rect x="18" y="18" width="24" height="18" rx="4" fill="#475569"/>
                                    <!-- Antenna -->
                                    <rect x="29" y="10" width="2" height="9" fill="#94a3b8"/>
                                    <circle cx="30" cy="9" r="3" fill="#22d3ee"/>
                                    <!-- Eyes -->
                                    <rect x="21" y="22" width="8" height="6" rx="2" fill="#0f172a"/>
                                    <rect x="31" y="22" width="8" height="6" rx="2" fill="#0f172a"/>
                                    <rect x="22" y="23" width="6" height="4" rx="1" fill="#22d3ee" opacity="0.9"/>
                                    <rect x="32" y="23" width="6" height="4" rx="1" fill="#22d3ee" opacity="0.9"/>
                                    <!-- Mouth -->
                                    <rect x="23" y="30" width="14" height="3" rx="1" fill="#0f172a"/>
                                    <rect x="24" y="31" width="3" height="2" rx="0.5" fill="#22d3ee"/>
                                    <rect x="28" y="31" width="3" height="2" rx="0.5" fill="#22d3ee"/>
                                    <rect x="33" y="31" width="3" height="2" rx="0.5" fill="#22d3ee"/>
                                    <!-- Arms -->
                                    <rect x="7" y="36" width="9" height="5" rx="2" fill="#334155"/>
                                    <rect x="44" y="36" width="9" height="5" rx="2" fill="#334155"/>
                                </svg>
                                <span class="avatar-label">Robô</span>
                            </div>

                            <!-- Avatar 6: Alien -->
                            <div class="avatar-option" data-avatar="avatar6" tabindex="0" role="radio" aria-checked="false" aria-label="Alienígena">
                                <svg viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="avatar-svg">
                                    <!-- Background -->
                                    <circle cx="30" cy="30" r="28" fill="#064e3b"/>
                                    <!-- Body -->
                                    <ellipse cx="30" cy="44" rx="12" ry="10" fill="#10b981"/>
                                    <!-- Suit lines -->
                                    <ellipse cx="30" cy="44" rx="7" ry="6" fill="#059669"/>
                                    <circle cx="30" cy="44" r="3" fill="#34d399"/>
                                    <!-- Head -->
                                    <ellipse cx="30" cy="24" rx="13" ry="14" fill="#10b981"/>
                                    <!-- Antennae -->
                                    <line x1="22" y1="12" x2="16" y2="4" stroke="#10b981" stroke-width="2" stroke-linecap="round"/>
                                    <circle cx="15" cy="3" r="3" fill="#34d399"/>
                                    <line x1="38" y1="12" x2="44" y2="4" stroke="#10b981" stroke-width="2" stroke-linecap="round"/>
                                    <circle cx="45" cy="3" r="3" fill="#34d399"/>
                                    <!-- Big eyes -->
                                    <ellipse cx="24" cy="24" rx="6" ry="7" fill="#000"/>
                                    <ellipse cx="36" cy="24" rx="6" ry="7" fill="#000"/>
                                    <ellipse cx="24" cy="23" rx="4" ry="5" fill="#065f46"/>
                                    <ellipse cx="36" cy="23" rx="4" ry="5" fill="#065f46"/>
                                    <circle cx="24" cy="23" r="2.5" fill="#000"/>
                                    <circle cx="36" cy="23" r="2.5" fill="#000"/>
                                    <circle cx="25" cy="22" r="1" fill="#fff" opacity="0.7"/>
                                    <circle cx="37" cy="22" r="1" fill="#fff" opacity="0.7"/>
                                    <!-- Mouth (small) -->
                                    <path d="M26 33 Q30 37 34 33" stroke="#059669" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                                    <!-- Spots -->
                                    <circle cx="22" cy="39" r="2" fill="#34d399" opacity="0.6"/>
                                    <circle cx="38" cy="39" r="2" fill="#34d399" opacity="0.6"/>
                                </svg>
                                <span class="avatar-label">Alienígena</span>
                            </div>

                            <!-- Avatar 7: Knight -->
                            <div class="avatar-option" data-avatar="avatar7" tabindex="0" role="radio" aria-checked="false" aria-label="Cavaleiro">
                                <svg viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="avatar-svg">
                                    <!-- Background -->
                                    <circle cx="30" cy="30" r="28" fill="#1c1917"/>
                                    <!-- Full armor body -->
                                    <path d="M15 36 L15 54 L45 54 L45 36 Q30 44 15 36Z" fill="#78716c"/>
                                    <!-- Chest plate -->
                                    <path d="M20 34 L20 52 L40 52 L40 34 Q30 40 20 34Z" fill="#57534e"/>
                                    <!-- Chest cross -->
                                    <rect x="28" y="36" width="4" height="12" rx="1" fill="#d4af37"/>
                                    <rect x="24" y="41" width="12" height="4" rx="1" fill="#d4af37"/>
                                    <!-- Helmet -->
                                    <rect x="18" y="14" width="24" height="22" rx="6" fill="#78716c"/>
                                    <!-- Visor -->
                                    <rect x="20" y="22" width="20" height="8" rx="2" fill="#292524"/>
                                    <rect x="21" y="23" width="18" height="6" rx="1" fill="#1c1917"/>
                                    <!-- Eye slits in visor -->
                                    <rect x="22" y="24" width="7" height="2" rx="1" fill="#d4af37" opacity="0.8"/>
                                    <rect x="31" y="24" width="7" height="2" rx="1" fill="#d4af37" opacity="0.8"/>
                                    <!-- Plume on top -->
                                    <path d="M26 14 Q30 4 34 14" stroke="#dc2626" stroke-width="3" fill="none" stroke-linecap="round"/>
                                    <!-- Sword raised (right side) -->
                                    <rect x="45" y="8" width="3" height="28" rx="1" fill="#a8a29e"/>
                                    <rect x="42" y="22" width="9" height="3" rx="1" fill="#d4af37"/>
                                    <rect x="46" y="5" width="2" height="5" rx="1" fill="#d4af37"/>
                                    <!-- Shoulder guards -->
                                    <ellipse cx="15" cy="35" rx="6" ry="4" fill="#57534e"/>
                                    <ellipse cx="45" cy="35" rx="6" ry="4" fill="#57534e"/>
                                </svg>
                                <span class="avatar-label">Cavaleiro</span>
                            </div>

                            <!-- Avatar 8: Scientist -->
                            <div class="avatar-option" data-avatar="avatar8" tabindex="0" role="radio" aria-checked="false" aria-label="Cientista">
                                <svg viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="avatar-svg">
                                    <!-- Background -->
                                    <circle cx="30" cy="30" r="28" fill="#0c4a6e"/>
                                    <!-- Lab coat body -->
                                    <path d="M16 34 L14 54 L46 54 L44 34 Q30 42 16 34Z" fill="#f0f9ff"/>
                                    <!-- Lab coat lapels -->
                                    <path d="M30 34 L24 40 L24 54 L30 54 Z" fill="#e0f2fe"/>
                                    <path d="M30 34 L36 40 L36 54 L30 54 Z" fill="#e0f2fe"/>
                                    <!-- Pocket with pen -->
                                    <rect x="32" y="38" width="8" height="7" rx="1" fill="#bae6fd"/>
                                    <rect x="34" y="36" width="2" height="5" rx="1" fill="#0284c7"/>
                                    <rect x="37" y="36" width="2" height="5" rx="1" fill="#dc2626"/>
                                    <!-- Head -->
                                    <circle cx="30" cy="24" r="10" fill="#fde68a"/>
                                    <!-- Hair (messy) -->
                                    <path d="M20 20 Q22 12 30 14 Q38 12 40 20" fill="#374151"/>
                                    <path d="M20 20 L18 16 L22 18" fill="#374151"/>
                                    <path d="M40 20 L42 16 L38 18" fill="#374151"/>
                                    <!-- Glasses -->
                                    <rect x="21" y="23" width="7" height="5" rx="2" fill="none" stroke="#0369a1" stroke-width="1.5"/>
                                    <rect x="32" y="23" width="7" height="5" rx="2" fill="none" stroke="#0369a1" stroke-width="1.5"/>
                                    <line x1="28" y1="25" x2="32" y2="25" stroke="#0369a1" stroke-width="1.5"/>
                                    <line x1="18" y1="25" x2="21" y2="25" stroke="#0369a1" stroke-width="1"/>
                                    <line x1="39" y1="25" x2="42" y2="25" stroke="#0369a1" stroke-width="1"/>
                                    <!-- Eyes behind glasses -->
                                    <circle cx="24" cy="26" r="1.5" fill="#1e3a5f"/>
                                    <circle cx="35" cy="26" r="1.5" fill="#1e3a5f"/>
                                    <!-- Smile -->
                                    <path d="M26 30 Q30 33 34 30" stroke="#d97706" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                                    <!-- Lightning bolt (symbol of science) -->
                                    <path d="M8 28 L14 20 L11 30 L17 22 L13 34" stroke="#fbbf24" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span class="avatar-label">Cientista</span>
                            </div>

                        </div><!-- /.avatar-grid -->

                        <!-- Hidden input holding selected avatar value -->
                        <input type="hidden" id="avatar" name="avatar" value="avatar1">

                    </fieldset><!-- /.avatar-fieldset -->
                    </div>

                    <!-- ===== FORM FIELDS ===== -->

                    <div class="form-row">
                        <!-- Nome -->
                        <div class="form-group">
                            <label for="nome" class="form-label">
                                <i data-lucide="user"></i> Nome Completo
                            </label>
                            <div class="input-wrapper">
                                <input
                                    type="text"
                                    id="nome"
                                    name="nome"
                                    class="form-control"
                                    placeholder="Seu nome completo"
                                    required
                                    autocomplete="name"
                                    aria-required="true"
                                >
                            </div>
                            <span class="field-error" id="erro-nome" hidden></span>
                        </div>

                        <!-- Username -->
                        <div class="form-group">
                            <label for="username" class="form-label">
                                <i data-lucide="at-sign"></i> Username
                            </label>
                            <div class="input-wrapper">
                                <input
                                    type="text"
                                    id="username"
                                    name="username"
                                    class="form-control"
                                    placeholder="seu_username"
                                    required
                                    autocomplete="username"
                                    aria-required="true"
                                    aria-describedby="username-hint"
                                >
                            </div>
                            <span class="field-hint" id="username-hint">Letras minúsculas, números e _ apenas</span>
                            <span class="field-error" id="erro-username" hidden></span>
                        </div>
                    </div>

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
                        <span class="field-error" id="erro-email" hidden></span>
                    </div>

                    <div class="form-row">
                        <!-- Senha -->
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
                                    placeholder="Mínimo 8 caracteres"
                                    required
                                    autocomplete="new-password"
                                    aria-required="true"
                                    aria-describedby="password-strength-label"
                                >
                                <button type="button" class="btn-toggle-password" id="toggle-senha" aria-label="Mostrar senha">
                                    <i data-lucide="eye" id="eye-icon-senha"></i>
                                </button>
                            </div>
                            <!-- Password strength indicator -->
                            <div class="password-strength" aria-live="polite">
                                <div class="strength-bar">
                                    <div class="strength-fill" id="strength-fill"></div>
                                </div>
                                <span class="strength-label" id="password-strength-label"></span>
                            </div>
                            <span class="field-error" id="erro-senha" hidden></span>
                        </div>

                        <!-- Confirmar Senha -->
                        <div class="form-group">
                            <label for="confirmar-senha" class="form-label">
                                <i data-lucide="lock-keyhole"></i> Confirmar Senha
                            </label>
                            <div class="input-wrapper input-password">
                                <input
                                    type="password"
                                    id="confirmar-senha"
                                    name="confirmar_senha"
                                    class="form-control"
                                    placeholder="Repita sua senha"
                                    required
                                    autocomplete="new-password"
                                    aria-required="true"
                                >
                                <button type="button" class="btn-toggle-password" id="toggle-confirmar" aria-label="Mostrar confirmação de senha">
                                    <i data-lucide="eye" id="eye-icon-confirmar"></i>
                                </button>
                            </div>
                            <span class="field-error" id="erro-confirmar" hidden></span>
                        </div>
                    </div>

                    <!-- Série Escolar -->
                    <div class="student-only-fields">
                    <div class="form-group">
                        <label for="serie" class="form-label">
                            <i data-lucide="graduation-cap"></i> Série Escolar
                        </label>
                        <div class="input-wrapper">
                            <select id="serie" name="serie" class="form-control form-select" required aria-required="true">
                                <option value="" disabled selected>Selecione sua série</option>
                                <option value="6">6º Ano</option>
                                <option value="7">7º Ano</option>
                                <option value="8">8º Ano</option>
                                <option value="9">9º Ano</option>
                            </select>
                        </div>
                        <span class="field-error" id="erro-serie" hidden></span>
                    </div>
                    </div>

                    <!-- Terms -->
                    <p class="terms-text">
                        Ao se cadastrar, você concorda com nossos
                        <a href="#" class="auth-link">Termos de Uso</a>.
                    </p>

                    <!-- Submit -->
                    <button
                        type="submit"
                        id="btn-cadastro"
                        class="btn btn-primary btn-lg w-full"
                        aria-label="Criar minha conta"
                    >
                        <span class="btn-text">
                            <i data-lucide="rocket"></i>
                            CRIAR MINHA CONTA
                        </span>
                        <span class="btn-loading" hidden>
                            <span class="spinner"></span>
                            Criando conta...
                        </span>
                    </button>

                </form>

                <!-- Footer link -->
                <p class="auth-card-footer">
                    Já tem uma conta?
                    <a href="login.php" class="auth-link">Faça login</a>
                </p>

            </div><!-- /.auth-card -->
        </div><!-- /.auth-form-side -->

    </div><!-- /.auth-wrapper -->

    <script>
    (function () {
        'use strict';

        // Initialize Lucide icons
        lucide.createIcons();

        /* ─────────────────────────────────────────
           AVATAR SELECTION
        ───────────────────────────────────────── */
        const avatarOptions = document.querySelectorAll('.avatar-option');
        const avatarInput   = document.getElementById('avatar');

        avatarOptions.forEach(function (option) {
            function selectAvatar() {
                avatarOptions.forEach(function (opt) {
                    opt.classList.remove('selected');
                    opt.setAttribute('aria-checked', 'false');
                });
                option.classList.add('selected');
                option.setAttribute('aria-checked', 'true');
                avatarInput.value = option.dataset.avatar;
            }

            option.addEventListener('click', selectAvatar);
            option.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    selectAvatar();
                }
            });
        });

        /* ─────────────────────────────────────────
           PASSWORD TOGGLES
        ───────────────────────────────────────── */
        function setupToggle(btnId, inputId, iconId) {
            const btn   = document.getElementById(btnId);
            const input = document.getElementById(inputId);
            const icon  = document.getElementById(iconId);
            if (!btn || !input || !icon) return;

            btn.addEventListener('click', function () {
                const isPass = input.type === 'password';
                input.type = isPass ? 'text' : 'password';
                btn.setAttribute('aria-label', isPass ? 'Ocultar senha' : 'Mostrar senha');
                icon.setAttribute('data-lucide', isPass ? 'eye-off' : 'eye');
                lucide.createIcons();
            });
        }

        setupToggle('toggle-senha',    'senha',           'eye-icon-senha');
        setupToggle('toggle-confirmar','confirmar-senha', 'eye-icon-confirmar');

        /* ─────────────────────────────────────────
           PASSWORD STRENGTH INDICATOR
        ───────────────────────────────────────── */
        const senhaInput   = document.getElementById('senha');
        const strengthFill = document.getElementById('strength-fill');
        const strengthLabel = document.getElementById('password-strength-label');

        function calcStrength(password) {
            let score = 0;
            if (password.length >= 8)  score++;
            if (password.length >= 12) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[^A-Za-z0-9]/.test(password)) score++;
            return score;
        }

        senhaInput.addEventListener('input', function () {
            const val   = senhaInput.value;
            const score = calcStrength(val);

            strengthFill.className = 'strength-fill';

            if (val.length === 0) {
                strengthFill.style.width = '0%';
                strengthLabel.textContent = '';
                return;
            }

            const levels = ['', 'Muito fraca', 'Fraca', 'Moderada', 'Forte', 'Muito forte'];
            const classes = ['', 'strength-1', 'strength-2', 'strength-3', 'strength-4', 'strength-5'];
            const widths  = ['0%', '20%', '40%', '60%', '80%', '100%'];

            strengthFill.classList.add(classes[score]);
            strengthFill.style.width = widths[score];
            strengthLabel.textContent = levels[score];
        });

        /* ─────────────────────────────────────────
           USERNAME NORMALIZATION
        ───────────────────────────────────────── */
        const usernameInput = document.getElementById('username');
        usernameInput.addEventListener('input', function () {
            this.value = this.value.toLowerCase().replace(/[^a-z0-9_]/g, '');
        });

        const accountType = document.getElementById('tipo_usuario');
        const studentFields = document.querySelectorAll('.student-only-fields');
        const seriesInput = document.getElementById('serie');

        function updateRoleFields() {
            const isStudent = accountType.value === 'aluno';
            studentFields.forEach(field => {
                field.hidden = !isStudent;
            });
            seriesInput.required = isStudent;
        }

        accountType.addEventListener('change', updateRoleFields);
        updateRoleFields();

        /* ─────────────────────────────────────────
           FIELD-LEVEL HELPERS
        ───────────────────────────────────────── */
        function showFieldError(id, msg) {
            const el = document.getElementById(id);
            if (!el) return;
            el.textContent = msg;
            el.hidden = false;
        }

        function clearFieldError(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.textContent = '';
            el.hidden = true;
        }

        function clearAllErrors() {
            ['nome','username','email','senha','confirmar','serie'].forEach(function (f) {
                clearFieldError('erro-' + f);
                const inp = document.getElementById(f === 'confirmar' ? 'confirmar-senha' : f);
                if (inp) inp.classList.remove('input-invalid');
            });
            const fb = document.getElementById('cadastro-feedback');
            fb.hidden = true;
            fb.className = 'feedback-box';
        }

        /* ─────────────────────────────────────────
           FOCUS HIGHLIGHT
        ───────────────────────────────────────── */
        document.querySelectorAll('.form-control').forEach(function (input) {
            input.addEventListener('focus', function () {
                const wrapper = input.closest('.input-wrapper');
                if (wrapper) wrapper.classList.add('focused');
            });
            input.addEventListener('blur', function () {
                const wrapper = input.closest('.input-wrapper');
                if (wrapper) wrapper.classList.remove('focused');
            });
        });

        /* ─────────────────────────────────────────
           FORM SUBMIT
        ───────────────────────────────────────── */
        const form       = document.getElementById('cadastro-form');
        const btnCadastro = document.getElementById('btn-cadastro');
        const feedbackBox = document.getElementById('cadastro-feedback');

        function showFeedback(msg, type) {
            feedbackBox.textContent = msg;
            feedbackBox.className   = 'feedback-box feedback-' + type;
            feedbackBox.hidden = false;
            lucide.createIcons();
            feedbackBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function setLoading(loading) {
            const btnText    = btnCadastro.querySelector('.btn-text');
            const btnLoading = btnCadastro.querySelector('.btn-loading');
            btnCadastro.disabled = loading;
            btnText.hidden    = loading;
            btnLoading.hidden = !loading;
        }

        function validateForm() {
            clearAllErrors();
            let valid = true;

            const nome     = document.getElementById('nome').value.trim();
            const username = document.getElementById('username').value.trim();
            const email    = document.getElementById('email').value.trim();
            const senha    = document.getElementById('senha').value;
            const confirmar = document.getElementById('confirmar-senha').value;
            const serie    = document.getElementById('serie').value;
            const isStudent = accountType.value === 'aluno';

            if (!nome) {
                showFieldError('erro-nome', 'Por favor, informe seu nome.');
                document.getElementById('nome').classList.add('input-invalid');
                valid = false;
            }

            if (!username) {
                showFieldError('erro-username', 'Por favor, escolha um username.');
                document.getElementById('username').classList.add('input-invalid');
                valid = false;
            } else if (!/^[a-z0-9_]+$/.test(username)) {
                showFieldError('erro-username', 'Use apenas letras minúsculas, números e _.');
                document.getElementById('username').classList.add('input-invalid');
                valid = false;
            }

            if (!email) {
                showFieldError('erro-email', 'Por favor, informe seu e-mail.');
                document.getElementById('email').classList.add('input-invalid');
                valid = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showFieldError('erro-email', 'Formato de e-mail inválido.');
                document.getElementById('email').classList.add('input-invalid');
                valid = false;
            }

            if (!senha) {
                showFieldError('erro-senha', 'Por favor, crie uma senha.');
                document.getElementById('senha').classList.add('input-invalid');
                valid = false;
            } else if (senha.length < 8) {
                showFieldError('erro-senha', 'A senha deve ter pelo menos 8 caracteres.');
                document.getElementById('senha').classList.add('input-invalid');
                valid = false;
            }

            if (!confirmar) {
                showFieldError('erro-confirmar', 'Por favor, confirme sua senha.');
                document.getElementById('confirmar-senha').classList.add('input-invalid');
                valid = false;
            } else if (senha !== confirmar) {
                showFieldError('erro-confirmar', 'As senhas não coincidem.');
                document.getElementById('confirmar-senha').classList.add('input-invalid');
                valid = false;
            }

            if (isStudent && !serie) {
                showFieldError('erro-serie', 'Por favor, selecione sua série.');
                document.getElementById('serie').classList.add('input-invalid');
                valid = false;
            }

            return valid;
        }

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            if (!validateForm()) return;

            setLoading(true);

            const formData = new FormData(form);

            try {
                const response = await fetch('api/cadastro.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    throw new Error('Erro de servidor: ' + response.status);
                }

                const data = await response.json();

                if (data.success) {
                    showFeedback('🎉 ' + (data.message || 'Conta criada com sucesso! Redirecionando...'), 'success');
                    setTimeout(function () {
                        window.location.href = data.redirect || 'dashboard.php';
                    }, 1200);
                } else {
                    showFeedback(data.message || 'Erro ao criar conta. Tente novamente.', 'error');
                    setLoading(false);
                }

            } catch (err) {
                console.error('[Cadastro Error]', err);
                showFeedback('Falha na conexão. Verifique sua internet e tente novamente.', 'error');
                setLoading(false);
            }
        });

    })();
    </script>

</body>
</html>
