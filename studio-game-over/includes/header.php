<?php
/**
 * header.php — Global Page Header & Navigation
 * MathPlay Solutions | Studio Game Over
 *
 * Usage:
 *   $pageTitle = 'Dashboard';          // required — used in <title>
 *   $extraCss  = ['css/dashboard.css']; // optional — page-specific CSS
 *   require_once 'includes/header.php';
 *
 * Outputs everything from <!DOCTYPE html> through the closing </nav> tag.
 * Wrap your page content in <main> and close it in footer.php.
 */

// ---------------------------------------------------------------------------
// Session bootstrap
// ---------------------------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------------------------------------------------------------------------
// Page meta defaults
// ---------------------------------------------------------------------------
$pageTitle = $pageTitle ?? 'MathPlay Solutions';
$extraCss  = $extraCss  ?? [];

// ---------------------------------------------------------------------------
// Detect active nav item from current script name
// ---------------------------------------------------------------------------
$currentPage = basename($_SERVER['PHP_SELF']);

/**
 * Returns CSS class string "active" when the supplied filename matches
 * the currently executing script.
 */
function navClass(string $filename): string
{
    global $currentPage;
    return ($currentPage === $filename) ? 'active' : '';
}

// Jogos sub-pages that should keep the Jogos dropdown highlighted
$jogosPages = ['matematica.php', 'portugues.php'];
$jogosActive = in_array($currentPage, $jogosPages, true) ? 'active' : '';

// ---------------------------------------------------------------------------
// Session user data (populated by auth.php / refreshSessionCache)
// ---------------------------------------------------------------------------
$sessionNome   = htmlspecialchars($_SESSION['user_nome']   ?? 'Jogador',       ENT_QUOTES, 'UTF-8');
$sessionNivel  = (int) ($_SESSION['user_nivel']  ?? 1);
$sessionXp     = (int) ($_SESSION['user_xp']     ?? 0);
$sessionAvatar = htmlspecialchars($_SESSION['user_avatar'] ?? '',               ENT_QUOTES, 'UTF-8');

// ---------------------------------------------------------------------------
// Dynamic relative asset & link base paths (0 hardcoded paths)
// ---------------------------------------------------------------------------
$scriptPath  = $_SERVER['PHP_SELF'] ?? '';
$isSubfolder = (strpos($scriptPath, '/jogos/') !== false);
$assetBase   = $isSubfolder ? '../assets' : 'assets';
$linkBase    = $isSubfolder ? '../' : '';

// Fallback avatar — initials-based placeholder
$avatarSrc = 'https://ui-avatars.com/api/?name=' . urlencode($sessionNome) . '&background=9D4EDD&color=fff&size=64&font-size=0.45&bold=true';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="MathPlay Solutions — Plataforma de jogos educativos para matemática e português." />
    <meta name="theme-color" content="#9d4edd" />

    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> | MathPlay Solutions</title>

    <!-- ------------------------------------------------------------------ -->
    <!-- Google Fonts: Orbitron (display) + Inter (body)                    -->
    <!-- ------------------------------------------------------------------ -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    />

    <!-- ------------------------------------------------------------------ -->
    <!-- Lucide Icons (UMD bundle — createIcons() called after body)        -->
    <!-- ------------------------------------------------------------------ -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>

    <!-- ------------------------------------------------------------------ -->
    <!-- Global stylesheets                                                  -->
    <!-- ------------------------------------------------------------------ -->
    <link rel="stylesheet" href="<?= $assetBase ?>/css/style.css" />
    <link rel="stylesheet" href="<?= $assetBase ?>/css/responsivo.css" />

    <!-- ------------------------------------------------------------------ -->
    <!-- Page-specific stylesheets                                           -->
    <!-- ------------------------------------------------------------------ -->
    <?php foreach ($extraCss as $cssFile): ?>
    <link rel="stylesheet" href="<?= $assetBase . '/' . htmlspecialchars($cssFile, ENT_QUOTES, 'UTF-8') ?>" />
    <?php endforeach; ?>

    <!-- ------------------------------------------------------------------ -->
    <!-- Favicon                                                             -->
    <!-- ------------------------------------------------------------------ -->
    <link rel="icon" type="image/svg+xml" href="<?= $assetBase ?>/images/favicon.svg" />
    <link rel="alternate icon" href="<?= $assetBase ?>/images/favicon.ico" />
</head>
<body>

<!-- ======================================================================== -->
<!-- MAIN NAVIGATION                                                           -->
<!-- ======================================================================== -->
<nav class="main-nav" role="navigation" aria-label="Navegação principal">

    <!-- ------------------------------------------------------------------ -->
    <!-- Brand / Logo                                                        -->
    <!-- ------------------------------------------------------------------ -->
    <div class="nav-brand">
        <a href="<?= $linkBase ?>index.php" class="brand-link" aria-label="Ir para o Início">
            <div class="brand-logo" aria-hidden="true">
                <i data-lucide="gamepad-2"></i>
            </div>
            <div class="brand-text">
                <span class="brand-name">Studio Game Over</span>
                <span class="brand-subtitle">MathPlay Solutions</span>
            </div>
        </a>
    </div>

    <!-- ------------------------------------------------------------------ -->
    <!-- Hamburger toggle (mobile)                                           -->
    <!-- ------------------------------------------------------------------ -->
    <button
        class="nav-hamburger"
        id="navHamburger"
        aria-expanded="false"
        aria-controls="navMenu"
        aria-label="Abrir menu de navegação"
        type="button"
    >
        <span class="hamburger-bar"></span>
        <span class="hamburger-bar"></span>
        <span class="hamburger-bar"></span>
    </button>

    <!-- ------------------------------------------------------------------ -->
    <!-- Nav links                                                           -->
    <!-- ------------------------------------------------------------------ -->
    <ul class="nav-menu" id="navMenu" role="menubar">

        <!-- Início / Home -->
        <li class="nav-item" role="none">
            <a
                href="<?= $linkBase ?>index.php"
                class="nav-link <?= navClass('index.php') ?>"
                role="menuitem"
                aria-current="<?= ($currentPage === 'index.php') ? 'page' : 'false' ?>"
            >
                <i data-lucide="home" aria-hidden="true"></i>
                <span>Início</span>
            </a>
        </li>

        <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Dashboard (para usuários logados) -->
        <li class="nav-item" role="none">
            <a
                href="<?= $linkBase ?>dashboard.php"
                class="nav-link <?= navClass('dashboard.php') ?>"
                role="menuitem"
                aria-current="<?= ($currentPage === 'dashboard.php') ? 'page' : 'false' ?>"
            >
                <i data-lucide="layout-dashboard" aria-hidden="true"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <?php endif; ?>

        <!-- Jogos (dropdown) -->
        <li class="nav-item nav-item--dropdown <?= $jogosActive ?>" role="none">
            <button
                class="nav-link nav-dropdown-toggle <?= $jogosActive ?>"
                aria-haspopup="true"
                aria-expanded="false"
                type="button"
            >
                <i data-lucide="joystick" aria-hidden="true"></i>
                <span>Jogos</span>
                <i data-lucide="chevron-down" class="dropdown-caret" aria-hidden="true"></i>
            </button>

            <ul class="nav-dropdown" role="menu" aria-label="Submenu de jogos">
                <li role="none">
                    <a
                        href="<?= $linkBase ?>jogos/matematica.php"
                        class="nav-dropdown-link <?= navClass('matematica.php') ?>"
                        role="menuitem"
                    >
                        <i data-lucide="sigma" aria-hidden="true"></i>
                        <span>Matemática</span>
                    </a>
                </li>
                <li role="none">
                    <a
                        href="<?= $linkBase ?>jogos/portugues.php"
                        class="nav-dropdown-link <?= navClass('portugues.php') ?>"
                        role="menuitem"
                    >
                        <i data-lucide="book-open-text" aria-hidden="true"></i>
                        <span>Português</span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Ranking -->
        <li class="nav-item" role="none">
            <a
                href="<?= $linkBase ?>ranking.php"
                class="nav-link <?= navClass('ranking.php') ?>"
                role="menuitem"
                aria-current="<?= ($currentPage === 'ranking.php') ? 'page' : 'false' ?>"
            >
                <i data-lucide="trophy" aria-hidden="true"></i>
                <span>Ranking</span>
            </a>
        </li>

        <!-- Histórico -->
        <li class="nav-item" role="none">
            <a
                href="<?= $linkBase ?>historico.php"
                class="nav-link <?= navClass('historico.php') ?>"
                role="menuitem"
                aria-current="<?= ($currentPage === 'historico.php') ? 'page' : 'false' ?>"
            >
                <i data-lucide="clock-3" aria-hidden="true"></i>
                <span>Histórico</span>
            </a>
        </li>

        <!-- Perfil -->
        <li class="nav-item" role="none">
            <a
                href="<?= $linkBase ?>perfil.php"
                class="nav-link <?= navClass('perfil.php') ?>"
                role="menuitem"
                aria-current="<?= ($currentPage === 'perfil.php') ? 'page' : 'false' ?>"
            >
                <i data-lucide="user-circle" aria-hidden="true"></i>
                <span>Perfil</span>
            </a>
        </li>

    </ul><!-- /.nav-menu -->

    <!-- ------------------------------------------------------------------ -->
    <!-- User info panel                                                     -->
    <!-- ------------------------------------------------------------------ -->
    <div class="nav-user" aria-label="Informações do jogador">

        <!-- XP / Level badge -->
        <div class="nav-user-stats" aria-label="Nível <?= $sessionNivel ?>, <?= $sessionXp ?> XP">
            <span class="user-level-badge" title="Nível atual">
                <i data-lucide="zap" aria-hidden="true"></i>
                Nv.&nbsp;<?= $sessionNivel ?>
            </span>
            <span class="user-xp-badge" title="Pontos de experiência">
                <?= number_format($sessionXp, 0, ',', '.') ?>&nbsp;XP
            </span>
        </div>

        <!-- Avatar + name link to perfil -->
        <a href="<?= $linkBase ?>perfil.php" class="nav-user-profile" aria-label="Ver perfil de <?= $sessionNome ?>">
            <img
                src="<?= $avatarSrc ?>"
                alt="Avatar de <?= $sessionNome ?>"
                class="nav-avatar"
                width="40"
                height="40"
                loading="eager"
            />
            <span class="nav-user-name"><?= $sessionNome ?></span>
        </a>

        <!-- Logout -->
        <a
            href="<?= $linkBase ?>logout.php"
            class="nav-logout-btn"
            aria-label="Sair da conta"
            title="Sair"
        >
            <i data-lucide="log-out" aria-hidden="true"></i>
            <span class="sr-only">Sair</span>
        </a>

    </div><!-- /.nav-user -->

</nav><!-- /.main-nav -->

<!-- ======================================================================== -->
<!-- MAIN CONTENT — opened here, closed in footer.php                         -->
<!-- ======================================================================== -->
<main id="main-content" tabindex="-1">

<!-- ------------------------------------------------------------------ -->
<!-- Global JS (loaded early so helpers are available to page scripts)   -->
<!-- ------------------------------------------------------------------ -->
<script src="<?= $assetBase ?>/js/main.js" defer></script>

<script>
    // Initialise Lucide icons rendered up to this point (nav icons).
    // footer.php calls this again to cover any icons added after.
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });

    // ----------------------------------------------------------------
    // Hamburger / mobile menu toggle
    // ----------------------------------------------------------------
    (function () {
        const btn  = document.getElementById('navHamburger');
        const menu = document.getElementById('navMenu');

        if (!btn || !menu) return;

        btn.addEventListener('click', function () {
            const expanded = btn.getAttribute('aria-expanded') === 'true';
            btn.setAttribute('aria-expanded', String(!expanded));
            menu.classList.toggle('nav-menu--open');
            btn.classList.toggle('nav-hamburger--open');
        });

        // Close menu when a link is clicked (mobile UX)
        menu.querySelectorAll('a, button').forEach(function (el) {
            el.addEventListener('click', function () {
                if (menu.classList.contains('nav-menu--open')) {
                    btn.setAttribute('aria-expanded', 'false');
                    menu.classList.remove('nav-menu--open');
                    btn.classList.remove('nav-hamburger--open');
                }
            });
        });

        // ----------------------------------------------------------------
        // Dropdown toggle within nav
        // ----------------------------------------------------------------
        const dropdownToggles = document.querySelectorAll('.nav-dropdown-toggle');
        dropdownToggles.forEach(function (toggle) {
            toggle.addEventListener('click', function () {
                const parentLi  = toggle.closest('.nav-item--dropdown');
                const dropdown  = parentLi ? parentLi.querySelector('.nav-dropdown') : null;
                const isOpen    = toggle.getAttribute('aria-expanded') === 'true';

                // Close any other open dropdowns first
                dropdownToggles.forEach(function (other) {
                    if (other !== toggle) {
                        other.setAttribute('aria-expanded', 'false');
                        const otherLi = other.closest('.nav-item--dropdown');
                        if (otherLi) otherLi.classList.remove('dropdown--open');
                    }
                });

                toggle.setAttribute('aria-expanded', String(!isOpen));
                if (parentLi) parentLi.classList.toggle('dropdown--open', !isOpen);
            });
        });

        // Close dropdowns when clicking outside the nav
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.nav-item--dropdown')) {
                dropdownToggles.forEach(function (toggle) {
                    toggle.setAttribute('aria-expanded', 'false');
                    const li = toggle.closest('.nav-item--dropdown');
                    if (li) li.classList.remove('dropdown--open');
                });
            }
        });
    }());
</script>
