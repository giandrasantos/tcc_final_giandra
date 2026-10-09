<?php
/**
 * index.php — MathPlay Solutions Public Landing Page
 * Studio Game Over
 *
 * Self-contained: no header.php / footer.php includes.
 * All CSS and JavaScript are embedded inline.
 */

// ---------------------------------------------------------------------------
// Session: redirect authenticated users straight to the dashboard
// ---------------------------------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (($_SESSION['tipo_usuario'] ?? '') === 'professor') {
    header('Location: professor_dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="MathPlay Solutions — Plataforma de jogos educativos para Matemática e Português do Ensino Fundamental II. Desenvolvido por Studio Game Over." />
    <meta name="theme-color" content="#9d4edd" />
    <meta property="og:title" content="MathPlay Solutions | Studio Game Over" />
    <meta property="og:description" content="Transformando o aprendizado em uma aventura inesquecível. 2 jogos educativos, sistema de níveis, medalhas e muito mais." />
    <title>MathPlay Solutions | Studio Game Over</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/accessibility.css?v=<?= filemtime(__DIR__ . '/assets/css/accessibility.css') ?>" />
    <script src="assets/js/accessibility.js?v=<?= filemtime(__DIR__ . '/assets/js/accessibility.js') ?>" defer></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

    <style>
        /* ================================================================
           CSS CUSTOM PROPERTIES — Design Tokens
        ================================================================ */
        :root {
            /* Colors */
            --clr-bg:           #0F0B1E;
            --clr-bg-2:         #181130;
            --clr-bg-card:      rgba(255,255,255,0.04);
            --clr-bg-card-h:    rgba(255,255,255,0.08);
            --clr-border:       rgba(255,0,127,0.25);
            --clr-border-h:     rgba(157,78,221,0.5);

            --clr-purple:       #9D4EDD;
            --clr-purple-light: #C77DFF;
            --clr-purple-dark:  #5A189A;
            --clr-cyan:         #FF007F;
            --clr-cyan-light:   #FF53CD;
            --clr-orange:       #FF53CD;
            --clr-pink:         #FF007F;

            --clr-text:         #e2e8f0;
            --clr-text-muted:   #D4C2FC;
            --clr-text-dim:     #8A7BA8;
            --clr-white:        #ffffff;

            /* Gradients */
            --grad-primary:     linear-gradient(135deg, #9D4EDD 0%, #FF007F 100%);
            --grad-hero-title:  linear-gradient(135deg, #C77DFF 0%, #FF007F 50%, #FF53CD 100%);
            --grad-orange:      linear-gradient(135deg, #FF53CD 0%, #FF007F 100%);
            --grad-card-math:   linear-gradient(135deg, rgba(157,78,221,0.2) 0%, rgba(255,0,127,0.15) 100%);
            --grad-card-lang:   linear-gradient(135deg, rgba(255,0,127,0.2) 0%, rgba(255,83,205,0.15) 100%);

            /* Shadows */
            --shadow-glow-purple: 0 0 40px rgba(157,78,221,0.4);
            --shadow-glow-cyan:   0 0 40px rgba(255,0,127,0.4);
            --shadow-card:        0 8px 32px rgba(0,0,0,0.5);
            --shadow-card-h:      0 20px 60px rgba(0,0,0,0.7);

            /* Typography */
            --font-display: 'Orbitron', sans-serif;
            --font-body:    'Inter', sans-serif;

            /* Spacing */
            --section-pad:  clamp(4rem, 8vw, 7rem) clamp(1rem, 5vw, 2rem);
            --nav-h:        72px;

            /* Transitions */
            --trans-fast:   0.15s ease;
            --trans-mid:    0.3s ease;
            --trans-slow:   0.5s ease;

            /* Radius */
            --radius-sm:    8px;
            --radius-md:    16px;
            --radius-lg:    24px;
            --radius-xl:    32px;
        }

        /* ================================================================
           CSS RESET & BASE
        ================================================================ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html {
            scroll-behavior: smooth;
            font-size: 16px;
        }

        body {
            font-family: var(--font-body);
            background-color: var(--clr-bg);
            color: var(--clr-text);
            line-height: 1.6;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        img { display: block; max-width: 100%; }
        a { text-decoration: none; color: inherit; }
        button { cursor: pointer; border: none; background: none; font: inherit; }
        ul { list-style: none; }

        /* Accessibility */
        .sr-only {
            position: absolute; width: 1px; height: 1px;
            padding: 0; margin: -1px; overflow: hidden;
            clip: rect(0,0,0,0); white-space: nowrap; border: 0;
        }
        :focus-visible {
            outline: 2px solid var(--clr-purple-light);
            outline-offset: 3px;
            border-radius: 4px;
        }

        /* Scroll-animation base state */
        .anim-fade-up {
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        .anim-fade-up.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .anim-fade-in {
            opacity: 0;
            transition: opacity 0.7s ease;
        }
        .anim-fade-in.visible { opacity: 1; }

        /* Stagger delays */
        .delay-1 { transition-delay: 0.1s !important; }
        .delay-2 { transition-delay: 0.2s !important; }
        .delay-3 { transition-delay: 0.3s !important; }
        .delay-4 { transition-delay: 0.4s !important; }
        .delay-5 { transition-delay: 0.5s !important; }
        .delay-6 { transition-delay: 0.6s !important; }

        /* ================================================================
           SCROLLBAR
        ================================================================ */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--clr-bg); }
        ::-webkit-scrollbar-thumb {
            background: var(--clr-purple);
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover { background: var(--clr-purple-light); }

        /* ================================================================
           SHARED COMPONENTS
        ================================================================ */

        /* --- Buttons --- */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.5rem;
            border-radius: var(--radius-sm);
            font-family: var(--font-display);
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.06em;
            transition: all var(--trans-mid);
            white-space: nowrap;
        }
        .btn svg { width: 16px; height: 16px; flex-shrink: 0; }

        .btn-primary {
            background: var(--grad-primary);
            color: var(--clr-white);
            box-shadow: 0 4px 20px rgba(139,92,246,0.35);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(139,92,246,0.5);
            filter: brightness(1.08);
        }
        .btn-primary:active { transform: translateY(0); }

        .btn-outline {
            background: transparent;
            color: var(--clr-text);
            border: 1.5px solid var(--clr-border);
        }
        .btn-outline:hover {
            border-color: var(--clr-purple-light);
            color: var(--clr-purple-light);
            background: rgba(139,92,246,0.08);
            transform: translateY(-2px);
        }

        .btn-lg {
            padding: 0.9rem 2.2rem;
            font-size: 0.9rem;
        }
        .btn-lg svg { width: 20px; height: 20px; }

        .btn-hero-primary {
            padding: 1rem 2.5rem;
            font-size: 0.95rem;
            border-radius: var(--radius-md);
            background: var(--grad-primary);
            color: var(--clr-white);
            box-shadow: 0 8px 32px rgba(139,92,246,0.4);
        }
        .btn-hero-primary:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 16px 48px rgba(139,92,246,0.55);
        }
        .btn-hero-secondary {
            padding: 1rem 2.5rem;
            font-size: 0.95rem;
            border-radius: var(--radius-md);
            background: transparent;
            color: var(--clr-text);
            border: 1.5px solid rgba(255,255,255,0.2);
        }
        .btn-hero-secondary:hover {
            border-color: var(--clr-cyan);
            color: var(--clr-cyan);
            background: rgba(6,182,212,0.08);
            transform: translateY(-3px);
        }

        /* --- Section Titles --- */
        .section-label {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-family: var(--font-display);
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            color: var(--clr-purple-light);
            text-transform: uppercase;
            margin-bottom: 0.8rem;
        }
        .section-label::before, .section-label::after {
            content: '';
            display: inline-block;
            width: 24px;
            height: 1px;
            background: var(--clr-purple-light);
            opacity: 0.5;
        }

        .section-title {
            font-family: var(--font-display);
            font-size: clamp(1.6rem, 3.5vw, 2.6rem);
            font-weight: 800;
            line-height: 1.2;
            color: var(--clr-white);
            margin-bottom: 1rem;
        }
        .section-title span {
            background: var(--grad-hero-title);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-subtitle {
            font-size: 1.05rem;
            color: var(--clr-text-muted);
            max-width: 600px;
            line-height: 1.7;
        }

        .text-center { text-align: center; }
        .text-center .section-subtitle { margin: 0 auto; }

        /* --- Container --- */
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 clamp(1rem, 4vw, 2rem);
        }

        /* --- Gradient Divider --- */
        .section-divider {
            width: 80px;
            height: 3px;
            background: var(--grad-primary);
            border-radius: 2px;
            margin: 1rem auto 2.5rem;
        }

        /* ================================================================
           1. NAVBAR
        ================================================================ */
        #navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            height: var(--nav-h);
            display: flex;
            align-items: center;
            background: rgba(15,11,30,0.75);
            backdrop-filter: blur(20px) saturate(1.5);
            -webkit-backdrop-filter: blur(20px) saturate(1.5);
            border-bottom: 1px solid var(--clr-border);
            transition: box-shadow var(--trans-mid), background var(--trans-mid);
        }
        #navbar.scrolled {
            box-shadow: 0 4px 30px rgba(0,0,0,0.5);
            background: rgba(15,11,30,0.92);
        }

        .nav-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 clamp(1rem, 4vw, 2rem);
            display: flex;
            align-items: center;
            width: 100%;
        }

        /* Brand */
        .nav-brand {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
            flex-shrink: 0;
        }
        .nav-brand-icon {
            width: 38px; height: 38px;
            background: var(--grad-primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-glow-purple);
        }
        .nav-brand-icon svg { width: 20px; height: 20px; color: white; }
        .nav-brand-text { line-height: 1.15; }
        .nav-brand-name {
            display: block;
            font-family: var(--font-display);
            font-size: 0.75rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            color: var(--clr-white);
        }
        .nav-brand-sub {
            display: block;
            font-size: 0.6rem;
            color: var(--clr-purple-light);
            letter-spacing: 0.1em;
            font-family: var(--font-display);
        }

        /* Nav links spacer */
        .nav-spacer { flex: 1; }

        /* Nav actions */
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* Hamburger */
        .nav-hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            padding: 8px;
            border-radius: var(--radius-sm);
            transition: background var(--trans-fast);
        }
        .nav-hamburger:hover { background: rgba(255,255,255,0.05); }
        .hamburger-bar {
            display: block;
            width: 22px;
            height: 2px;
            background: var(--clr-text);
            border-radius: 2px;
            transition: transform var(--trans-mid), opacity var(--trans-mid);
            transform-origin: center;
        }
        .nav-hamburger.open .hamburger-bar:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .nav-hamburger.open .hamburger-bar:nth-child(2) { opacity: 0; transform: scaleX(0); }
        .nav-hamburger.open .hamburger-bar:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        /* Mobile nav menu (hidden by default) */
        .nav-mobile-menu {
            display: none;
            position: fixed;
            top: var(--nav-h);
            left: 0; right: 0;
            background: rgba(15,11,30,0.97);
            border-bottom: 1px solid var(--clr-border);
            padding: 1.5rem clamp(1rem, 4vw, 2rem);
            flex-direction: column;
            gap: 0.75rem;
            backdrop-filter: blur(20px);
            z-index: 999;
        }
        .nav-mobile-menu.open { display: flex; }
        .nav-mobile-link {
            display: block;
            padding: 0.75rem 1rem;
            border-radius: var(--radius-sm);
            color: var(--clr-text);
            font-weight: 500;
            transition: background var(--trans-fast), color var(--trans-fast);
        }
        .nav-mobile-link:hover { background: rgba(139,92,246,0.1); color: var(--clr-purple-light); }
        .nav-mobile-divider { height: 1px; background: var(--clr-border); margin: 0.25rem 0; }
        .nav-mobile-btns { display: flex; gap: 0.75rem; margin-top: 0.25rem; }
        .nav-mobile-btns .btn { flex: 1; justify-content: center; }

        /* ================================================================
           2. HERO SECTION
        ================================================================ */
        #hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: calc(var(--nav-h) + 2rem) 1rem 3rem;
        }

        /* Canvas for particle animation */
        #hero-canvas {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }

        /* Grid overlay */
        .hero-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(139,92,246,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(139,92,246,0.04) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
        }

        /* Floating geometric shapes */
        .hero-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(1px);
            animation: floatShape linear infinite;
            pointer-events: none;
            opacity: 0;
        }
        .hero-shape.s1 {
            width: 80px; height: 80px;
            border: 2px solid rgba(139,92,246,0.3);
            top: 15%; left: 8%;
            animation-duration: 12s;
            animation-delay: 0s;
            border-radius: 12px;
            transform: rotate(30deg);
        }
        .hero-shape.s2 {
            width: 50px; height: 50px;
            background: rgba(6,182,212,0.08);
            border: 1px solid rgba(6,182,212,0.3);
            top: 70%; left: 5%;
            animation-duration: 15s;
            animation-delay: -5s;
            clip-path: polygon(50% 0%, 0% 100%, 100% 100%);
            border-radius: 0;
        }
        .hero-shape.s3 {
            width: 100px; height: 100px;
            border: 2px solid rgba(139,92,246,0.2);
            top: 25%; right: 10%;
            animation-duration: 18s;
            animation-delay: -3s;
            clip-path: polygon(25% 0%, 75% 0%, 100% 50%, 75% 100%, 25% 100%, 0% 50%);
            border-radius: 0;
        }
        .hero-shape.s4 {
            width: 60px; height: 60px;
            background: rgba(249,115,22,0.06);
            border: 1px solid rgba(249,115,22,0.25);
            bottom: 25%; right: 8%;
            animation-duration: 10s;
            animation-delay: -7s;
        }
        .hero-shape.s5 {
            width: 120px; height: 120px;
            border: 1px solid rgba(6,182,212,0.15);
            top: 55%; left: 50%;
            margin-left: -300px;
            animation-duration: 20s;
            animation-delay: -10s;
        }
        .hero-shape.s6 {
            width: 40px; height: 40px;
            background: rgba(139,92,246,0.1);
            border: 1px solid rgba(139,92,246,0.4);
            bottom: 15%; left: 15%;
            animation-duration: 9s;
            animation-delay: -2s;
            clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%);
        }

        @keyframes floatShape {
            0%   { transform: translateY(0px) rotate(0deg); opacity: 0.6; }
            25%  { transform: translateY(-20px) rotate(10deg); opacity: 0.8; }
            50%  { transform: translateY(-10px) rotate(20deg); opacity: 0.6; }
            75%  { transform: translateY(-25px) rotate(10deg); opacity: 0.9; }
            100% { transform: translateY(0px) rotate(0deg); opacity: 0.6; }
        }

        /* Radial glow */
        .hero-glow {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }
        .hero-glow-1 {
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(139,92,246,0.15) 0%, transparent 70%);
            top: 50%; left: 50%;
            transform: translate(-50%, -60%);
        }
        .hero-glow-2 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(6,182,212,0.1) 0%, transparent 70%);
            bottom: 10%; right: 15%;
        }

        /* Hero content */
        .hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 820px;
        }

        .hero-label {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(139,92,246,0.12);
            border: 1px solid rgba(139,92,246,0.3);
            color: var(--clr-purple-light);
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.4rem 1rem;
            border-radius: 100px;
            margin-bottom: 2rem;
            letter-spacing: 0.04em;
            animation: fadeDown 0.8s ease forwards;
        }

        .hero-title {
            font-family: var(--font-display);
            font-size: clamp(3rem, 9vw, 7rem);
            font-weight: 900;
            line-height: 1.1;
            letter-spacing: -0.02em;
            margin-bottom: 1.5rem;
            overflow: visible;
        }
        .hero-title-line {
            display: block;
            background: var(--grad-hero-title);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: slideInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }
        .hero-title-line:nth-child(1) { animation-delay: 0.2s; }
        .hero-title-line:nth-child(2) { animation-delay: 0.4s; }

        .hero-subtitle {
            font-size: clamp(1rem, 2.5vw, 1.25rem);
            color: var(--clr-text);
            font-weight: 500;
            margin-bottom: 0.75rem;
            animation: fadeUp 0.8s ease 0.6s forwards;
            opacity: 0;
        }

        .hero-description {
            font-size: clamp(0.85rem, 2vw, 1rem);
            color: var(--clr-text-muted);
            max-width: 580px;
            margin: 0 auto 2.5rem;
            line-height: 1.7;
            animation: fadeUp 0.8s ease 0.75s forwards;
            opacity: 0;
        }

        .hero-cta {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeUp 0.8s ease 0.9s forwards;
            opacity: 0;
        }

        /* Floating controller icon */
        .hero-icon-float {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px; height: 80px;
            background: var(--grad-primary);
            border-radius: 50%;
            margin: 0 auto 1.5rem;
            box-shadow: var(--shadow-glow-purple);
            animation: floatIcon 4s ease-in-out infinite, fadeUp 0.8s ease 0.1s forwards;
            opacity: 0;
        }
        .hero-icon-float svg { width: 38px; height: 38px; color: white; }

        @keyframes floatIcon {
            0%, 100% { transform: translateY(0px); }
            50%       { transform: translateY(-12px); }
        }

        /* Scroll indicator */
        .hero-scroll {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.4rem;
            color: var(--clr-text-dim);
            font-size: 0.7rem;
            font-family: var(--font-display);
            letter-spacing: 0.1em;
            animation: scrollBounce 2s ease-in-out infinite;
        }
        .hero-scroll-dot {
            width: 24px; height: 40px;
            border: 2px solid rgba(255,255,255,0.15);
            border-radius: 12px;
            display: flex;
            justify-content: center;
            padding-top: 6px;
        }
        .hero-scroll-dot::before {
            content: '';
            width: 4px; height: 8px;
            background: var(--clr-purple-light);
            border-radius: 2px;
            animation: scrollDot 2s ease-in-out infinite;
        }
        @keyframes scrollDot {
            0%, 100% { transform: translateY(0); opacity: 1; }
            100%      { transform: translateY(14px); opacity: 0; }
        }
        @keyframes scrollBounce {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50%       { transform: translateX(-50%) translateY(5px); }
        }

        /* Keyframe helpers */
        @keyframes fadeDown {
            from { opacity: 0; transform: translateY(-20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(60px) skewY(3deg); }
            to   { opacity: 1; transform: translateY(0) skewY(0deg); }
        }

        /* ================================================================
           3. ABOUT SECTION
        ================================================================ */
        #about {
            padding: var(--section-pad);
            position: relative;
        }
        #about::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, var(--clr-purple) 50%, transparent 100%);
            opacity: 0.3;
        }

        .about-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 3.5rem;
        }

        .about-card {
            background: var(--clr-bg-card);
            border: 1px solid var(--clr-border);
            border-radius: var(--radius-lg);
            padding: 2.25rem 2rem;
            transition: all var(--trans-mid);
            position: relative;
            overflow: hidden;
        }
        .about-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: var(--grad-primary);
            opacity: 0;
            transition: opacity var(--trans-mid);
        }
        .about-card:hover {
            transform: translateY(-6px);
            background: var(--clr-bg-card-h);
            border-color: var(--clr-border-h);
            box-shadow: var(--shadow-card-h);
        }
        .about-card:hover::before { opacity: 1; }

        .about-card-icon {
            width: 56px; height: 56px;
            background: var(--grad-primary);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            box-shadow: var(--shadow-glow-purple);
        }
        .about-card-icon svg { width: 26px; height: 26px; color: white; }

        .about-card-title {
            font-family: var(--font-display);
            font-size: 1rem;
            font-weight: 700;
            color: var(--clr-white);
            margin-bottom: 0.65rem;
            letter-spacing: 0.03em;
        }
        .about-card-text {
            font-size: 0.9rem;
            color: var(--clr-text-muted);
            line-height: 1.65;
        }

        /* ================================================================
           4. GAMES SECTION
        ================================================================ */
        #games {
            padding: var(--section-pad);
            background: var(--clr-bg-2);
            position: relative;
        }

        .games-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
            gap: 2rem;
            margin-top: 3.5rem;
        }

        .game-card {
            border-radius: var(--radius-xl);
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
            border: 1px solid transparent;
            transition: all var(--trans-slow);
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        .game-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            padding: 1px;
            background: var(--grad-border);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }
        .game-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 32px 80px rgba(0,0,0,0.6);
        }

        .game-card-math {
            background: var(--grad-card-math);
            --grad-border: linear-gradient(135deg, #8b5cf6, #06b6d4);
        }
        .game-card-math:hover { box-shadow: 0 32px 80px rgba(139,92,246,0.2); }

        .game-card-lang {
            background: var(--grad-card-lang);
            --grad-border: linear-gradient(135deg, #f97316, #06b6d4);
        }
        .game-card-lang:hover { box-shadow: 0 32px 80px rgba(249,115,22,0.2); }

        /* Big icon */
        .game-card-icon {
            width: 72px; height: 72px;
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            flex-shrink: 0;
        }
        .game-card-icon-math { background: linear-gradient(135deg, rgba(139,92,246,0.25), rgba(6,182,212,0.25)); border: 1px solid rgba(139,92,246,0.3); }
        .game-card-icon-lang { background: linear-gradient(135deg, rgba(249,115,22,0.25), rgba(6,182,212,0.25)); border: 1px solid rgba(249,115,22,0.3); }

        .game-card-header { display: flex; align-items: center; gap: 1rem; }
        .game-card-header-text {}
        .game-card-title {
            font-family: var(--font-display);
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--clr-white);
            margin-bottom: 0.2rem;
        }
        .game-card-genre { font-size: 0.75rem; color: var(--clr-text-muted); letter-spacing: 0.05em; }

        .game-card-desc {
            font-size: 0.92rem;
            color: var(--clr-text-muted);
            line-height: 1.65;
        }

        /* Difficulty badges */
        .game-card-badges {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 100px;
            font-size: 0.7rem;
            font-weight: 700;
            font-family: var(--font-display);
            letter-spacing: 0.06em;
        }
        .badge-easy   { background: rgba(34,197,94,0.15);  color: #4ade80; border: 1px solid rgba(34,197,94,0.3); }
        .badge-medium { background: rgba(234,179,8,0.15);  color: #fbbf24; border: 1px solid rgba(234,179,8,0.3); }
        .badge-hard   { background: rgba(239,68,68,0.15);  color: #f87171; border: 1px solid rgba(239,68,68,0.3); }

        /* Topics list */
        .game-topics {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
        }
        .game-topic {
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--clr-border);
            color: var(--clr-text-muted);
            font-size: 0.72rem;
            padding: 0.2rem 0.65rem;
            border-radius: 6px;
        }

        .game-card .btn { align-self: flex-start; margin-top: auto; }
        .btn-math  { background: linear-gradient(135deg, #8b5cf6, #06b6d4); color: white; box-shadow: 0 4px 20px rgba(139,92,246,0.3); }
        .btn-math:hover  { filter: brightness(1.1); transform: translateY(-2px); }
        .btn-lang  { background: linear-gradient(135deg, #f97316, #06b6d4); color: white; box-shadow: 0 4px 20px rgba(249,115,22,0.3); }
        .btn-lang:hover  { filter: brightness(1.1); transform: translateY(-2px); }

        /* ================================================================
           5. HOW IT WORKS
        ================================================================ */
        #how {
            padding: var(--section-pad);
        }

        .steps-row {
            display: flex;
            align-items: flex-start;
            gap: 0;
            margin-top: 3.5rem;
            position: relative;
            flex-wrap: wrap;
            justify-content: center;
        }

        .step {
            flex: 1;
            min-width: 140px;
            max-width: 180px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
            padding: 0 0.5rem;
        }

        /* Connecting line between steps */
        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 28px;
            left: calc(50% + 32px);
            width: calc(100% - 32px);
            height: 2px;
            background: linear-gradient(90deg, var(--clr-purple) 0%, var(--clr-cyan) 100%);
            opacity: 0.25;
        }

        .step-bubble {
            width: 56px; height: 56px;
            background: var(--grad-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.85rem;
            box-shadow: var(--shadow-glow-purple);
            position: relative;
            z-index: 1;
            flex-shrink: 0;
        }
        .step-bubble svg { width: 22px; height: 22px; color: white; }

        .step-number {
            position: absolute;
            top: -6px; right: -6px;
            width: 20px; height: 20px;
            background: var(--clr-bg);
            border: 2px solid var(--clr-purple-light);
            border-radius: 50%;
            font-family: var(--font-display);
            font-size: 0.55rem;
            font-weight: 800;
            color: var(--clr-purple-light);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .step-title {
            font-family: var(--font-display);
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--clr-white);
            margin-bottom: 0.4rem;
            letter-spacing: 0.04em;
            line-height: 1.3;
        }
        .step-desc {
            font-size: 0.72rem;
            color: var(--clr-text-muted);
            line-height: 1.5;
        }

        /* ================================================================
           6. GAMIFICATION
        ================================================================ */
        #gamification {
            padding: var(--section-pad);
            background: var(--clr-bg-2);
            position: relative;
            overflow: hidden;
        }
        #gamification::before {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(139,92,246,0.08) 0%, transparent 70%);
            top: 50%; left: 50%;
            transform: translate(-50%,-50%);
            pointer-events: none;
        }

        .gami-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-top: 3.5rem;
        }

        .gami-card {
            background: var(--clr-bg-card);
            border: 1px solid var(--clr-border);
            border-radius: var(--radius-lg);
            padding: 2rem 1.75rem;
            transition: all var(--trans-mid);
            position: relative;
            text-align: center;
        }
        .gami-card:hover {
            transform: translateY(-5px);
            border-color: var(--clr-border-h);
            box-shadow: var(--shadow-card);
            background: var(--clr-bg-card-h);
        }

        .gami-card-icon {
            font-size: 2.2rem;
            margin-bottom: 1rem;
            display: block;
            filter: drop-shadow(0 0 12px currentColor);
        }
        .gami-card-title {
            font-family: var(--font-display);
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--clr-white);
            margin-bottom: 0.6rem;
            letter-spacing: 0.04em;
        }
        .gami-card-text {
            font-size: 0.82rem;
            color: var(--clr-text-muted);
            line-height: 1.55;
        }

        .gami-card-bar {
            height: 3px;
            background: var(--grad-primary);
            border-radius: 2px;
            margin: 1.25rem auto 0;
            width: 40px;
            opacity: 0.6;
            transition: width var(--trans-mid), opacity var(--trans-mid);
        }
        .gami-card:hover .gami-card-bar { width: 80px; opacity: 1; }

        /* ================================================================
           7. CTA SECTION
        ================================================================ */
        #cta {
            padding: var(--section-pad);
            position: relative;
            overflow: hidden;
            text-align: center;
        }
        .cta-bg {
            position: absolute;
            inset: 0;
            background:
                linear-gradient(135deg, rgba(139,92,246,0.12) 0%, rgba(6,182,212,0.08) 100%),
                var(--clr-bg);
        }
        .cta-glow {
            position: absolute;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(139,92,246,0.2) 0%, transparent 70%);
            top: 50%; left: 50%;
            transform: translate(-50%,-50%);
            pointer-events: none;
        }
        .cta-border {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, var(--clr-purple) 50%, transparent 100%);
            opacity: 0.4;
        }
        .cta-content {
            position: relative;
            z-index: 1;
            max-width: 640px;
            margin: 0 auto;
        }
        .cta-title {
            font-family: var(--font-display);
            font-size: clamp(1.5rem, 4vw, 2.5rem);
            font-weight: 900;
            color: var(--clr-white);
            margin-bottom: 1rem;
            line-height: 1.2;
        }
        .cta-title span {
            background: var(--grad-hero-title);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .cta-subtitle {
            font-size: 1rem;
            color: var(--clr-text-muted);
            margin-bottom: 2.5rem;
            line-height: 1.65;
        }
        .btn-cta {
            padding: 1.1rem 3rem;
            font-size: 1rem;
            border-radius: var(--radius-md);
            background: var(--grad-primary);
            color: white;
            font-family: var(--font-display);
            font-weight: 700;
            letter-spacing: 0.06em;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            box-shadow: 0 8px 40px rgba(139,92,246,0.45);
            transition: all var(--trans-mid);
        }
        .btn-cta svg { width: 20px; height: 20px; }
        .btn-cta:hover {
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 16px 60px rgba(139,92,246,0.6);
        }

        /* ================================================================
           8. FOOTER
        ================================================================ */
        #footer {
            background: #080810;
            border-top: 1px solid var(--clr-border);
            padding: 4rem clamp(1rem, 4vw, 2rem) 0;
        }
        .footer-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 3rem;
            padding-bottom: 3rem;
        }

        .footer-brand { }
        .footer-brand-logo {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            margin-bottom: 1rem;
        }
        .footer-brand-icon {
            width: 42px; height: 42px;
            background: var(--grad-primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .footer-brand-icon svg { width: 22px; height: 22px; color: white; }
        .footer-brand-name {
            font-family: var(--font-display);
            font-size: 0.85rem;
            font-weight: 800;
            color: var(--clr-white);
            letter-spacing: 0.06em;
        }
        .footer-tagline {
            font-size: 0.85rem;
            color: var(--clr-text-muted);
            line-height: 1.65;
            max-width: 280px;
            margin-bottom: 1rem;
        }
        .footer-copy {
            font-size: 0.75rem;
            color: var(--clr-text-dim);
        }

        .footer-col-title {
            font-family: var(--font-display);
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            color: var(--clr-purple-light);
            text-transform: uppercase;
            margin-bottom: 1.25rem;
        }
        .footer-links { display: flex; flex-direction: column; gap: 0.65rem; }
        .footer-link {
            font-size: 0.85rem;
            color: var(--clr-text-muted);
            transition: color var(--trans-fast);
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        .footer-link:hover { color: var(--clr-purple-light); }
        .footer-link svg { width: 13px; height: 13px; opacity: 0.5; }

        .footer-bottom {
            max-width: 1200px;
            margin: 0 auto;
            border-top: 1px solid var(--clr-border);
            padding: 1.5rem 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .footer-bottom-text {
            font-size: 0.75rem;
            color: var(--clr-text-dim);
        }
        .footer-version {
            font-family: var(--font-display);
            font-size: 0.65rem;
            color: var(--clr-purple-light);
            background: rgba(139,92,246,0.1);
            border: 1px solid rgba(139,92,246,0.2);
            padding: 0.2rem 0.6rem;
            border-radius: 100px;
            letter-spacing: 0.06em;
        }

        /* ================================================================
           RESPONSIVE
        ================================================================ */
        @media (max-width: 768px) {
            :root { --nav-h: 64px; }

            .nav-actions .btn-outline { display: none; }
            .nav-hamburger { display: flex; }

            .hero-shape.s1, .hero-shape.s5 { display: none; }

            .steps-row { gap: 1.5rem; }
            .step { min-width: 110px; max-width: 140px; }
            .step:not(:last-child)::after { display: none; }

            .footer-grid { grid-template-columns: 1fr; gap: 2rem; }

            .games-grid { grid-template-columns: 1fr; }
            .game-card { padding: 1.75rem; }
        }

        @media (max-width: 480px) {
            .hero-cta { flex-direction: column; align-items: center; }
            .btn-hero-primary, .btn-hero-secondary { width: 100%; justify-content: center; max-width: 300px; }

            .steps-row { flex-direction: column; align-items: center; gap: 1.25rem; }
            .step { max-width: 260px; width: 100%; flex-direction: row; text-align: left; gap: 1rem; }
            .step-bubble { flex-shrink: 0; margin-bottom: 0; }

            .gami-grid { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 360px) {
            .gami-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- ======================================================================
     NAVBAR
====================================================================== -->
<header id="navbar" role="banner">
    <div class="nav-inner">

        <!-- Brand -->
        <a href="#hero" class="nav-brand" aria-label="MathPlay Solutions — Ir ao topo">
            <div class="nav-brand-icon" aria-hidden="true">
                <i data-lucide="gamepad-2"></i>
            </div>
            <div class="nav-brand-text">
                <span class="nav-brand-name">STUDIO GAME OVER</span>
                <span class="nav-brand-sub">MathPlay Solutions</span>
            </div>
        </a>

        <div class="nav-spacer"></div>

        <!-- Desktop buttons -->
        <nav class="nav-actions" aria-label="Ações de autenticação">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php" class="btn btn-primary" aria-label="Ir para o Dashboard">
                    <i data-lucide="layout-dashboard"></i>
                    Meu Dashboard
                </a>
                <a href="perfil.php" class="btn btn-outline" aria-label="Ver meu perfil">
                    <i data-lucide="user"></i>
                    Perfil
                </a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline" aria-label="Entrar na conta">
                    <i data-lucide="log-in"></i>
                    Entrar
                </a>
                <a href="cadastro.php" class="btn btn-primary" aria-label="Criar nova conta">
                    <i data-lucide="user-plus"></i>
                    Cadastrar
                </a>
            <?php endif; ?>
        </nav>

        <!-- Hamburger (mobile) -->
        <button
            class="nav-hamburger"
            id="navHamburger"
            type="button"
            aria-label="Abrir menu"
            aria-expanded="false"
            aria-controls="navMobileMenu"
        >
            <span class="hamburger-bar"></span>
            <span class="hamburger-bar"></span>
            <span class="hamburger-bar"></span>
        </button>

    </div>
</header>

<!-- Mobile menu -->
<div class="nav-mobile-menu" id="navMobileMenu" aria-label="Menu de navegação mobile" hidden>
    <a href="#about"         class="nav-mobile-link" aria-label="Ir para Sobre">Sobre</a>
    <a href="#games"         class="nav-mobile-link" aria-label="Ir para Jogos">Os Jogos</a>
    <a href="#how"           class="nav-mobile-link" aria-label="Ir para Como Funciona">Como Funciona</a>
    <a href="#gamification"  class="nav-mobile-link" aria-label="Ir para Gamificação">Gamificação</a>
    <div class="nav-mobile-divider" role="separator"></div>
    <div class="nav-mobile-btns">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="dashboard.php" class="btn btn-primary btn-lg">Meu Dashboard</a>
            <a href="perfil.php" class="btn btn-outline btn-lg">Meu Perfil</a>
        <?php else: ?>
            <a href="login.php"    class="btn btn-outline btn-lg">Entrar</a>
            <a href="cadastro.php" class="btn btn-primary btn-lg">Cadastrar</a>
        <?php endif; ?>
    </div>
</div>


<!-- ======================================================================
     HERO
====================================================================== -->
<section id="hero" aria-label="Apresentação MathPlay Solutions">

    <!-- Particle canvas -->
    <canvas id="hero-canvas" aria-hidden="true"></canvas>

    <!-- Grid overlay -->
    <div class="hero-grid" aria-hidden="true"></div>

    <!-- Radial glows -->
    <div class="hero-glow hero-glow-1" aria-hidden="true"></div>
    <div class="hero-glow hero-glow-2" aria-hidden="true"></div>

    <!-- Floating geometric shapes -->
    <div class="hero-shape s1" aria-hidden="true"></div>
    <div class="hero-shape s2" aria-hidden="true"></div>
    <div class="hero-shape s3" aria-hidden="true"></div>
    <div class="hero-shape s4" aria-hidden="true"></div>
    <div class="hero-shape s5" aria-hidden="true"></div>
    <div class="hero-shape s6" aria-hidden="true"></div>

    <!-- Content -->
    <div class="hero-content">

        <!-- Floating icon -->
        <div class="hero-icon-float" aria-hidden="true">
            <i data-lucide="gamepad-2"></i>
        </div>

        <!-- Label -->
        <p class="hero-label" aria-hidden="true">
            🎮 Powered by Studio Game Over
        </p>

        <!-- Main title -->
        <h1 class="hero-title" aria-label="MathPlay Solutions">
            <span class="hero-title-line">MATHPLAY</span>
            <span class="hero-title-line">SOLUTIONS</span>
        </h1>

        <!-- Subtitle -->
        <p class="hero-subtitle">
            Transformando o aprendizado em uma aventura inesquecível
        </p>

        <!-- Description -->
        <p class="hero-description">
            2 jogos educativos, sistema de níveis, medalhas e muito mais —
            projetado para estudantes do Ensino Fundamental II
        </p>

        <!-- CTA Buttons -->
        <div class="hero-cta" role="group" aria-label="Ações principais">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php" class="btn btn-hero-primary" aria-label="Ir para meu Dashboard">
                    MEU DASHBOARD
                    <i data-lucide="arrow-right"></i>
                </a>
                <a href="jogos/matematica.php" class="btn btn-hero-secondary" aria-label="Jogar Desafio Matemático">
                    JOGAR AGORA
                    <i data-lucide="play-circle"></i>
                </a>
            <?php else: ?>
                <a href="cadastro.php" class="btn btn-hero-primary" aria-label="Começar agora, criar conta gratuita">
                    COMEÇAR AGORA
                    <i data-lucide="arrow-right"></i>
                </a>
                <a href="login.php" class="btn btn-hero-secondary" aria-label="Já tenho conta, fazer login">
                    JÁ TENHO CONTA
                    <i data-lucide="log-in"></i>
                </a>
            <?php endif; ?>
        </div>
        </div>

    </div>

    <!-- Scroll indicator -->
    <div class="hero-scroll" aria-hidden="true">
        <div class="hero-scroll-dot"></div>
        <span>SCROLL</span>
    </div>

</section>


<!-- ======================================================================
     ABOUT SECTION
====================================================================== -->
<section id="about" aria-labelledby="about-heading">
    <div class="container">

        <div class="text-center anim-fade-up">
            <p class="section-label">Sobre a plataforma</p>
            <h2 class="section-title" id="about-heading">
                O Que é <span>MathPlay Solutions</span>?
            </h2>
            <p class="section-subtitle">
                Uma plataforma gamificada que torna o aprendizado de Matemática e Português mais envolvente e eficaz para alunos do Ensino Fundamental II.
            </p>
            <div class="section-divider" role="presentation"></div>
        </div>

        <div class="about-grid">

            <article class="about-card anim-fade-up delay-1" aria-label="Para Estudantes">
                <div class="about-card-icon" aria-hidden="true">
                    <i data-lucide="graduation-cap"></i>
                </div>
                <h3 class="about-card-title">Para Estudantes</h3>
                <p class="about-card-text">
                    Desenvolvido para alunos do 6º ao 9º ano do Ensino Fundamental II, com conteúdo alinhado à BNCC e linguagem acessível.
                </p>
            </article>

            <article class="about-card anim-fade-up delay-2" aria-label="Aprender Jogando">
                <div class="about-card-icon" aria-hidden="true">
                    <i data-lucide="gamepad"></i>
                </div>
                <h3 class="about-card-title">Aprender Jogando</h3>
                <p class="about-card-text">
                    Transforma conteúdos de Matemática e Português em desafios interativos com mecânicas de jogo que mantêm o engajamento.
                </p>
            </article>

            <article class="about-card anim-fade-up delay-3" aria-label="Acompanhe o Progresso">
                <div class="about-card-icon" aria-hidden="true">
                    <i data-lucide="trending-up"></i>
                </div>
                <h3 class="about-card-title">Acompanhe o Progresso</h3>
                <p class="about-card-text">
                    Sistema de XP, níveis, medalhas e ranking para motivar continuamente e celebrar cada conquista do aluno.
                </p>
            </article>

        </div>
    </div>
</section>


<!-- ======================================================================
     GAMES SECTION
====================================================================== -->
<section id="games" aria-labelledby="games-heading">
    <div class="container">

        <div class="text-center anim-fade-up">
            <p class="section-label">Os Jogos</p>
            <h2 class="section-title" id="games-heading">
                Dois Desafios, <span>Infinitas Possibilidades</span>
            </h2>
            <p class="section-subtitle">
                Escolha o jogo, selecione a dificuldade e mergulhe em uma experiência de aprendizado que você não vai querer parar.
            </p>
            <div class="section-divider" role="presentation"></div>
        </div>

        <div class="games-grid">

            <!-- Desafio Matemático -->
            <article class="game-card game-card-math anim-fade-up delay-1" aria-label="Desafio Matemático">

                <div class="game-card-header">
                    <div class="game-card-icon game-card-icon-math" aria-hidden="true">
                        🧠
                    </div>
                    <div class="game-card-header-text">
                        <p class="game-card-title">Desafio Matemático</p>
                        <p class="game-card-genre">MATEMÁTICA · LÓGICA</p>
                    </div>
                </div>

                <p class="game-card-desc">
                    Domine números, frações, geometria, equações e muito mais através de desafios dinâmicos e progressivos que testam e desenvolvem seu raciocínio lógico.
                </p>

                <div class="game-card-badges" role="list" aria-label="Níveis de dificuldade">
                    <span class="badge badge-easy"   role="listitem">Fácil</span>
                    <span class="badge badge-medium" role="listitem">Médio</span>
                    <span class="badge badge-hard"   role="listitem">Difícil</span>
                </div>

                <div class="game-topics" role="list" aria-label="Tópicos abordados">
                    <span class="game-topic" role="listitem">Números Inteiros</span>
                    <span class="game-topic" role="listitem">Frações</span>
                    <span class="game-topic" role="listitem">Porcentagem</span>
                    <span class="game-topic" role="listitem">Geometria</span>
                    <span class="game-topic" role="listitem">Equações</span>
                    <span class="game-topic" role="listitem">e mais...</span>
                </div>

                <a href="cadastro.php" class="btn btn-math" aria-label="Jogar Desafio Matemático">
                    <i data-lucide="play"></i>
                    JOGAR AGORA
                </a>

            </article>

            <!-- Desafio das Palavras -->
            <article class="game-card game-card-lang anim-fade-up delay-2" aria-label="Desafio das Palavras">

                <div class="game-card-header">
                    <div class="game-card-icon game-card-icon-lang" aria-hidden="true">
                        📚
                    </div>
                    <div class="game-card-header-text">
                        <p class="game-card-title">Desafio das Palavras</p>
                        <p class="game-card-genre">PORTUGUÊS · LINGUAGEM</p>
                    </div>
                </div>

                <p class="game-card-desc">
                    Explore ortografia, gramática, figuras de linguagem e interpretação textual de forma divertida, ampliando seu domínio da língua portuguesa.
                </p>

                <div class="game-card-badges" role="list" aria-label="Níveis de dificuldade">
                    <span class="badge badge-easy"   role="listitem">Fácil</span>
                    <span class="badge badge-medium" role="listitem">Médio</span>
                    <span class="badge badge-hard"   role="listitem">Difícil</span>
                </div>

                <div class="game-topics" role="list" aria-label="Tópicos abordados">
                    <span class="game-topic" role="listitem">Ortografia</span>
                    <span class="game-topic" role="listitem">Acentuação</span>
                    <span class="game-topic" role="listitem">Sinônimos</span>
                    <span class="game-topic" role="listitem">Concordância</span>
                    <span class="game-topic" role="listitem">Figuras de Linguagem</span>
                    <span class="game-topic" role="listitem">e mais...</span>
                </div>

                <a href="cadastro.php" class="btn btn-lang" aria-label="Jogar Desafio das Palavras">
                    <i data-lucide="play"></i>
                    JOGAR AGORA
                </a>

            </article>

        </div>
    </div>
</section>


<!-- ======================================================================
     HOW IT WORKS
====================================================================== -->
<section id="how" aria-labelledby="how-heading">
    <div class="container">

        <div class="text-center anim-fade-up">
            <p class="section-label">Passo a passo</p>
            <h2 class="section-title" id="how-heading">
                Como <span>Funciona</span>?
            </h2>
            <p class="section-subtitle">
                Em apenas seis passos simples, você começa sua jornada de aprendizado gamificada.
            </p>
            <div class="section-divider" role="presentation"></div>
        </div>

        <ol class="steps-row" aria-label="Passos para começar a jogar">

            <li class="step anim-fade-up delay-1">
                <div class="step-bubble" aria-hidden="true">
                    <i data-lucide="user-plus"></i>
                    <span class="step-number">1</span>
                </div>
                <h3 class="step-title">Cadastre-se</h3>
                <p class="step-desc">Crie sua conta gratuita em segundos</p>
            </li>

            <li class="step anim-fade-up delay-2">
                <div class="step-bubble" aria-hidden="true">
                    <i data-lucide="gamepad-2"></i>
                    <span class="step-number">2</span>
                </div>
                <h3 class="step-title">Escolha o Jogo</h3>
                <p class="step-desc">Matemática ou Português — você decide</p>
            </li>

            <li class="step anim-fade-up delay-3">
                <div class="step-bubble" aria-hidden="true">
                    <i data-lucide="target"></i>
                    <span class="step-number">3</span>
                </div>
                <h3 class="step-title">Selecione a Dificuldade</h3>
                <p class="step-desc">Fácil, Médio ou Difícil</p>
            </li>

            <li class="step anim-fade-up delay-4">
                <div class="step-bubble" aria-hidden="true">
                    <i data-lucide="brain"></i>
                    <span class="step-number">4</span>
                </div>
                <h3 class="step-title">Responda as Questões</h3>
                <p class="step-desc">Desafie seus conhecimentos com perguntas dinâmicas</p>
            </li>

            <li class="step anim-fade-up delay-5">
                <div class="step-bubble" aria-hidden="true">
                    <i data-lucide="trophy"></i>
                    <span class="step-number">5</span>
                </div>
                <h3 class="step-title">Ganhe XP e Medalhas</h3>
                <p class="step-desc">Acumule pontos e desbloqueie conquistas</p>
            </li>

            <li class="step anim-fade-up delay-6">
                <div class="step-bubble" aria-hidden="true">
                    <i data-lucide="bar-chart-2"></i>
                    <span class="step-number">6</span>
                </div>
                <h3 class="step-title">Suba no Ranking</h3>
                <p class="step-desc">Compita e alcance o topo da tabela</p>
            </li>

        </ol>
    </div>
</section>


<!-- ======================================================================
     GAMIFICATION
====================================================================== -->
<section id="gamification" aria-labelledby="gami-heading">
    <div class="container" style="position:relative;z-index:1;">

        <div class="text-center anim-fade-up">
            <p class="section-label">Sistema de Recompensas</p>
            <h2 class="section-title" id="gami-heading">
                Gamificação que <span>Motiva</span>
            </h2>
            <p class="section-subtitle">
                Mecanismos de jogo cuidadosamente projetados para manter os alunos engajados e celebrar cada conquista no aprendizado.
            </p>
            <div class="section-divider" role="presentation"></div>
        </div>

        <div class="gami-grid">

            <article class="gami-card anim-fade-up delay-1" aria-label="Sistema de XP">
                <span class="gami-card-icon" aria-hidden="true">⚡</span>
                <h3 class="gami-card-title">Sistema de XP</h3>
                <p class="gami-card-text">
                    Ganhe pontos de experiência a cada questão respondida corretamente e acompanhe sua evolução em tempo real.
                </p>
                <div class="gami-card-bar" role="presentation"></div>
            </article>

            <article class="gami-card anim-fade-up delay-2" aria-label="Sistema de Níveis">
                <span class="gami-card-icon" aria-hidden="true">🎯</span>
                <h3 class="gami-card-title">Níveis</h3>
                <p class="gami-card-text">
                    Evolua de Iniciante a Mestre conforme aprende. Cada nível traz novos desafios e reconhecimento.
                </p>
                <div class="gami-card-bar" role="presentation"></div>
            </article>

            <article class="gami-card anim-fade-up delay-3" aria-label="Sistema de Medalhas">
                <span class="gami-card-icon" aria-hidden="true">🏅</span>
                <h3 class="gami-card-title">Medalhas</h3>
                <p class="gami-card-text">
                    Desbloqueie conquistas exclusivas pelo seu desempenho. Cada medalha conta uma história do seu progresso.
                </p>
                <div class="gami-card-bar" role="presentation"></div>
            </article>

            <article class="gami-card anim-fade-up delay-4" aria-label="Ranking Global">
                <span class="gami-card-icon" aria-hidden="true">🏆</span>
                <h3 class="gami-card-title">Ranking Global</h3>
                <p class="gami-card-text">
                    Compita com outros estudantes e alcance o topo. Veja sua posição e acompanhe os melhores jogadores.
                </p>
                <div class="gami-card-bar" role="presentation"></div>
            </article>

        </div>
    </div>
</section>


<!-- ======================================================================
     CTA
====================================================================== -->
<section id="cta" aria-labelledby="cta-heading">
    <div class="cta-bg" aria-hidden="true"></div>
    <div class="cta-glow" aria-hidden="true"></div>
    <div class="cta-border" aria-hidden="true"></div>

    <div class="cta-content anim-fade-up">
        <p class="section-label">Comece agora</p>
        <h2 class="cta-title" id="cta-heading">
            Pronto para transformar<br>
            <span>seu aprendizado?</span>
        </h2>
        <p class="cta-subtitle">
            Junte-se agora e comece sua jornada no MathPlay Solutions. 100% gratuito, sem complicações.
        </p>
        <a href="cadastro.php" class="btn-cta" aria-label="Começar gratuitamente, criar conta">
            <i data-lucide="rocket"></i>
            COMEÇAR GRATUITAMENTE
        </a>
    </div>
</section>


<!-- ======================================================================
     JAVASCRIPT — all inline, self-contained
====================================================================== -->
<script>
(function () {
    'use strict';

    /* ──────────────────────────────────────────────────────────────
       1. LUCIDE ICONS
    ────────────────────────────────────────────────────────────── */
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });

    /* ──────────────────────────────────────────────────────────────
       2. NAVBAR SCROLL SHADOW
    ────────────────────────────────────────────────────────────── */
    (function initNavScroll() {
        var navbar = document.getElementById('navbar');
        if (!navbar) return;

        function onScroll() {
            if (window.scrollY > 20) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }

        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }());

    /* ──────────────────────────────────────────────────────────────
       3. HAMBURGER MOBILE MENU
    ────────────────────────────────────────────────────────────── */
    (function initHamburger() {
        var btn  = document.getElementById('navHamburger');
        var menu = document.getElementById('navMobileMenu');
        if (!btn || !menu) return;

        btn.addEventListener('click', function () {
            var isOpen = !menu.hidden;
            menu.hidden = isOpen;
            btn.setAttribute('aria-expanded', String(!isOpen));
            btn.classList.toggle('open', !isOpen);
            document.body.style.overflow = isOpen ? '' : 'hidden';
        });

        // Close on link click
        menu.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                menu.hidden = true;
                btn.setAttribute('aria-expanded', 'false');
                btn.classList.remove('open');
                document.body.style.overflow = '';
            });
        });

        // Close on outside click
        document.addEventListener('click', function (e) {
            if (!menu.hidden && !btn.contains(e.target) && !menu.contains(e.target)) {
                menu.hidden = true;
                btn.setAttribute('aria-expanded', 'false');
                btn.classList.remove('open');
                document.body.style.overflow = '';
            }
        });
    }());

    /* ──────────────────────────────────────────────────────────────
       4. SMOOTH SCROLL FOR ANCHOR LINKS
    ────────────────────────────────────────────────────────────── */
    (function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
            anchor.addEventListener('click', function (e) {
                var target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    var navH = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--nav-h'), 10) || 72;
                    var top  = target.getBoundingClientRect().top + window.scrollY - navH;
                    window.scrollTo({ top: top, behavior: 'smooth' });
                }
            });
        });
    }());

    /* ──────────────────────────────────────────────────────────────
       5. CANVAS PARTICLE ANIMATION
    ────────────────────────────────────────────────────────────── */
    (function initParticles() {
        var canvas = document.getElementById('hero-canvas');
        if (!canvas) return;

        var ctx = canvas.getContext('2d');
        var particles = [];
        var mouse = { x: null, y: null, radius: 120 };
        var raf;

        // Config
        var CONFIG = {
            particleCount: window.innerWidth < 600 ? 50 : 100,
            maxDist:       140,
            speed:         0.4,
            radius:        2,
            colorA:        [139, 92, 246],   // purple
            colorB:        [6, 182, 212],    // cyan
        };

        function resize() {
            canvas.width  = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;
        }

        function Particle() {
            this.reset();
        }
        Particle.prototype.reset = function () {
            this.x    = Math.random() * canvas.width;
            this.y    = Math.random() * canvas.height;
            this.vx   = (Math.random() - 0.5) * CONFIG.speed;
            this.vy   = (Math.random() - 0.5) * CONFIG.speed;
            this.r    = Math.random() * CONFIG.radius + 0.5;
            this.t    = Math.random(); // 0=purple, 1=cyan, blend
        };

        function lerp(a, b, t) { return a + (b - a) * t; }

        Particle.prototype.draw = function () {
            var r = Math.round(lerp(CONFIG.colorA[0], CONFIG.colorB[0], this.t));
            var g = Math.round(lerp(CONFIG.colorA[1], CONFIG.colorB[1], this.t));
            var b = Math.round(lerp(CONFIG.colorA[2], CONFIG.colorB[2], this.t));
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.r, 0, Math.PI * 2);
            ctx.fillStyle = 'rgba(' + r + ',' + g + ',' + b + ',0.65)';
            ctx.fill();
        };

        Particle.prototype.update = function () {
            this.x += this.vx;
            this.y += this.vy;

            // Bounce off edges
            if (this.x < 0 || this.x > canvas.width)  this.vx *= -1;
            if (this.y < 0 || this.y > canvas.height)  this.vy *= -1;

            // Mouse repel
            if (mouse.x !== null) {
                var dx = this.x - mouse.x;
                var dy = this.y - mouse.y;
                var dist = Math.sqrt(dx * dx + dy * dy);
                if (dist < mouse.radius) {
                    var force = (mouse.radius - dist) / mouse.radius;
                    this.vx += (dx / dist) * force * 0.8;
                    this.vy += (dy / dist) * force * 0.8;
                    // Clamp speed
                    var spd = Math.sqrt(this.vx * this.vx + this.vy * this.vy);
                    if (spd > 3) { this.vx = (this.vx / spd) * 3; this.vy = (this.vy / spd) * 3; }
                }
            }
        };

        function drawLines() {
            for (var i = 0; i < particles.length; i++) {
                for (var j = i + 1; j < particles.length; j++) {
                    var dx = particles[i].x - particles[j].x;
                    var dy = particles[i].y - particles[j].y;
                    var dist = Math.sqrt(dx * dx + dy * dy);

                    if (dist < CONFIG.maxDist) {
                        var alpha = (1 - dist / CONFIG.maxDist) * 0.3;
                        var t = (particles[i].t + particles[j].t) / 2;
                        var r = Math.round(lerp(CONFIG.colorA[0], CONFIG.colorB[0], t));
                        var g = Math.round(lerp(CONFIG.colorA[1], CONFIG.colorB[1], t));
                        var b = Math.round(lerp(CONFIG.colorA[2], CONFIG.colorB[2], t));
                        ctx.beginPath();
                        ctx.moveTo(particles[i].x, particles[i].y);
                        ctx.lineTo(particles[j].x, particles[j].y);
                        ctx.strokeStyle = 'rgba(' + r + ',' + g + ',' + b + ',' + alpha + ')';
                        ctx.lineWidth = 0.8;
                        ctx.stroke();
                    }
                }
            }
        }

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            for (var i = 0; i < particles.length; i++) {
                particles[i].update();
                particles[i].draw();
            }
            drawLines();
            raf = requestAnimationFrame(animate);
        }

        function init() {
            resize();
            particles = [];
            for (var i = 0; i < CONFIG.particleCount; i++) {
                particles.push(new Particle());
            }
            if (raf) cancelAnimationFrame(raf);
            animate();
        }

        // Mouse tracking
        canvas.addEventListener('mousemove', function (e) {
            var rect = canvas.getBoundingClientRect();
            mouse.x = e.clientX - rect.left;
            mouse.y = e.clientY - rect.top;
        }, { passive: true });
        canvas.addEventListener('mouseleave', function () {
            mouse.x = null;
            mouse.y = null;
        });

        // Touch
        canvas.addEventListener('touchmove', function (e) {
            var rect = canvas.getBoundingClientRect();
            var t = e.touches[0];
            mouse.x = t.clientX - rect.left;
            mouse.y = t.clientY - rect.top;
        }, { passive: true });
        canvas.addEventListener('touchend', function () {
            mouse.x = null; mouse.y = null;
        });

        // Responsive
        var resizeTimer;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(init, 200);
        });

        // Pause when not visible
        document.addEventListener('visibilitychange', function () {
            if (document.hidden) {
                cancelAnimationFrame(raf);
            } else {
                animate();
            }
        });

        init();
    }());

    /* ──────────────────────────────────────────────────────────────
       6. INTERSECTION OBSERVER — SCROLL ANIMATIONS
    ────────────────────────────────────────────────────────────── */
    (function initScrollAnim() {
        if (!('IntersectionObserver' in window)) {
            // Fallback: make all visible immediately
            document.querySelectorAll('.anim-fade-up, .anim-fade-in').forEach(function (el) {
                el.classList.add('visible');
            });
            return;
        }

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold:  0.12,
            rootMargin: '0px 0px -40px 0px'
        });

        document.querySelectorAll('.anim-fade-up, .anim-fade-in').forEach(function (el) {
            observer.observe(el);
        });
    }());

    /* ──────────────────────────────────────────────────────────────
       7. COUNTER ANIMATION
       (ready for use: call animateCounter(el, target, duration))
    ────────────────────────────────────────────────────────────── */
    function animateCounter(el, target, duration) {
        var start = 0;
        var startTime = null;
        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            var progress = Math.min((timestamp - startTime) / duration, 1);
            var ease = 1 - Math.pow(1 - progress, 3); // cubic ease-out
            el.textContent = Math.floor(ease * target);
            if (progress < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    /* Expose globally for optional use */
    window.MathPlay = { animateCounter: animateCounter };

}());
</script>

</body>
</html>
