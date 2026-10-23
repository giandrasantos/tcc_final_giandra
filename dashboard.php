<?php
/**
 * dashboard.php — Student Dashboard
 * MathPlay Solutions | Studio Game Over
 *
 * Requires authenticated session. Shows XP/Level progress, stats,
 * game cards with difficulty selectors, achievements gallery,
 * and a motivational quote.
 */

require_once 'config/database.php';
require_once 'includes/auth.php';

// auth.php already calls session_start() if needed.
requireRole('aluno');

// Refresh nav session cache (keeps nav XP/level in sync)
refreshSessionCache();

$db      = getDB();
$user_id = (int) $_SESSION['user_id'];

// ---------------------------------------------------------------------------
// Fetch fresh user data
// ---------------------------------------------------------------------------
$stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    // User no longer exists — destroy session and redirect
    session_destroy();
    header('Location: login.php');
    exit;
}

// Keep session cache up to date
$_SESSION['user_xp']     = $user['xp'];
$_SESSION['user_nivel']  = $user['nivel'];
$_SESSION['user_nome']   = $user['nome'];
$_SESSION['user_avatar'] = $user['avatar'] ?? '';

// ---------------------------------------------------------------------------
// Fetch per-game progress
// ---------------------------------------------------------------------------
$stmt = $db->prepare('SELECT * FROM progress WHERE user_id = ?');
$stmt->execute([$user_id]);
$progressData = [];
foreach ($stmt->fetchAll() as $p) {
    $progressData[$p['jogo']] = $p;
}

// ---------------------------------------------------------------------------
// Fetch all achievements (with unlock status for this user)
// ---------------------------------------------------------------------------
$stmt = $db->prepare('
    SELECT a.*, ua.data_conquista
      FROM achievements a
      LEFT JOIN user_achievements ua
             ON a.id = ua.achievement_id
            AND ua.user_id = ?
     ORDER BY a.id ASC
');
$stmt->execute([$user_id]);
$achievements = $stmt->fetchAll();

// ---------------------------------------------------------------------------
// Level / XP calculations
// ---------------------------------------------------------------------------
$levelNames = [
    1 => 'Iniciante',
    2 => 'Aprendiz',
    3 => 'Avançado',
    4 => 'Especialista',
    5 => 'Mestre',
];
$levelThresholds = [
    1 => 1000,
    2 => 2500,
    3 => 5000,
    4 => 10000,
    5 => 99999,
];
$levelBaseXP = [
    1 => 0,
    2 => 1000,
    3 => 2500,
    4 => 5000,
    5 => 10000,
];

$currentXP    = (int) $user['xp'];
$currentLevel = (int) $user['nivel'];
$currentLevel = max(1, min(5, $currentLevel)); // clamp 1–5

$nextLevelXP  = $levelThresholds[$currentLevel] ?? 99999;
$prevLevelXP  = $levelBaseXP[$currentLevel]     ?? 0;
$xpRange      = max(1, $nextLevelXP - $prevLevelXP);
$xpProgress   = min(100, max(0, round((($currentXP - $prevLevelXP) / $xpRange) * 100)));
$levelName    = $levelNames[$currentLevel] ?? 'Mestre';

// ---------------------------------------------------------------------------
// Avatar URL
// ---------------------------------------------------------------------------
$avatarVal = $user['avatar'] ?? '';
$nomeEsc   = htmlspecialchars($user['nome'], ENT_QUOTES, 'UTF-8');
$avatarSrc = 'https://ui-avatars.com/api/?name=' . urlencode($user['nome']) . '&background=9D4EDD&color=fff&size=128&bold=true';

// ---------------------------------------------------------------------------
// Game progress helpers
// ---------------------------------------------------------------------------
$mathProgress  = $progressData['matematica'] ?? null;
$ptProgress    = $progressData['portugues']  ?? null;

$mathBestScore  = $mathProgress ? (int) ($mathProgress['melhor_pontuacao'] ?? 0) : 0;
$mathPartidas   = $mathProgress ? (int) ($mathProgress['partidas'] ?? 0)         : 0;
$ptBestScore    = $ptProgress   ? (int) ($ptProgress['melhor_pontuacao'] ?? 0)   : 0;
$ptPartidas     = $ptProgress   ? (int) ($ptProgress['partidas'] ?? 0)           : 0;

// Achievement icon map (index 0-based, matched by achievement id order)
$achievementIcons = ['🏆', '⚡', '🔥', '🎯', '🌟', '💎'];

// ---------------------------------------------------------------------------
// Header variables
// ---------------------------------------------------------------------------
$pageTitle = 'Dashboard';
$extraCss  = ['css/dashboard.css'];

require_once 'includes/header.php';
?>

<!-- ========================================================================= -->
<!-- DASHBOARD CONTENT                                                          -->
<!-- ========================================================================= -->
<div class="dashboard-wrap">

    <!-- ===================================================================== -->
    <!-- 1. WELCOME BANNER                                                      -->
    <!-- ===================================================================== -->
    <section class="welcome-banner" aria-label="Boas-vindas e progresso do jogador">

        <!-- Decorative background shapes -->
        <div class="banner-shapes" aria-hidden="true">
            <span class="bshape bshape--1"></span>
            <span class="bshape bshape--2"></span>
            <span class="bshape bshape--3"></span>
            <span class="bshape bshape--4"></span>
        </div>

        <!-- Avatar -->
        <div class="banner-avatar-wrap">
            <div class="banner-avatar-ring" aria-hidden="true"></div>
            <img
                src="<?= $avatarSrc ?>"
                alt="Avatar de <?= $nomeEsc ?>"
                class="banner-avatar"
                width="100"
                height="100"
                loading="eager"
            />
            <span class="banner-level-badge" aria-label="Nível <?= $currentLevel ?>">
                <?= $currentLevel ?>
            </span>
        </div>

        <!-- Info -->
        <div class="banner-info">
            <h1 class="banner-greeting">
                Olá, <span class="banner-name"><?= $nomeEsc ?></span>! 👋
            </h1>
            <div class="banner-level-tag" aria-label="Nível <?= $currentLevel ?> — <?= htmlspecialchars($levelName, ENT_QUOTES, 'UTF-8') ?>">
                <i data-lucide="zap" aria-hidden="true"></i>
                NÍVEL <?= $currentLevel ?> — <?= htmlspecialchars($levelName, ENT_QUOTES, 'UTF-8') ?>
            </div>

            <!-- XP Bar -->
            <div class="xp-bar-block">
                <div class="xp-bar-labels">
                    <span class="xp-current" id="xpCurrentDisplay">
                        <?= number_format($currentXP, 0, ',', '.') ?> XP
                    </span>
                    <span class="xp-next">
                        Meta: <?= number_format($nextLevelXP, 0, ',', '.') ?> XP
                    </span>
                </div>
                <div
                    class="xp-bar-track"
                    role="progressbar"
                    aria-valuenow="<?= $xpProgress ?>"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    aria-label="Progresso de XP: <?= $xpProgress ?>%"
                >
                    <div
                        class="xp-bar-fill"
                        id="xpBarFill"
                        data-target="<?= $xpProgress ?>"
                        style="width: 0%;"
                    ></div>
                </div>
                <p class="xp-percent-text" id="xpPercentText">
                    <i data-lucide="trending-up" aria-hidden="true"></i>
                    <?= $xpProgress ?>% para o próximo nível
                </p>
            </div>
        </div>

    </section><!-- /.welcome-banner -->

    <!-- ===================================================================== -->
    <!-- 3. GAME CARDS SECTION                                                  -->
    <!-- ===================================================================== -->
    <section class="games-section" aria-label="Escolha seu desafio">
        <div class="section-header">
            <h2 class="section-title">
                <i data-lucide="joystick" aria-hidden="true"></i>
                Escolha seu Desafio
            </h2>
            <p class="section-subtitle">Selecione um jogo e a dificuldade para começar</p>
        </div>

        <div class="games-grid">

            <!-- ---- Card: Desafio Matemático ---- -->
            <article class="game-card game-card--math" aria-label="Jogo: Desafio Matemático">
                <div class="game-card-glow" aria-hidden="true"></div>

                <div class="game-card-header">
                    <div class="game-icon-wrap game-icon-wrap--math" aria-hidden="true">
                        <span class="game-icon-emoji">🧠</span>
                    </div>
                    <div>
                        <h3 class="game-card-title">Desafio Matemático</h3>
                        <p class="game-card-desc">
                            Teste seus conhecimentos e supere desafios de Matemática.
                        </p>
                    </div>
                </div>

                <div class="game-card-stats">
                    <div class="game-stat">
                        <i data-lucide="star" aria-hidden="true"></i>
                        <span>Melhor: <strong><?= number_format($mathBestScore, 0, ',', '.') ?></strong></span>
                    </div>
                    <div class="game-stat">
                        <i data-lucide="layers" aria-hidden="true"></i>
                        <span>Partidas: <strong><?= $mathPartidas ?></strong></span>
                    </div>
                </div>

                <form class="game-play-form" action="jogos/matematica.php" method="get">
                    <label class="text-secondary small" for="math-difficulty">Escolha o nível do jogo:</label>
                    <select class="game-difficulty-select" id="math-difficulty" name="dificuldade">
                        <option value="facil">Fácil</option>
                        <option value="medio">Médio</option>
                        <option value="dificil">Difícil</option>
                    </select>
                    <button type="submit" class="btn btn-play-game" aria-label="Jogar Desafio Matemático">
                        <i data-lucide="play" aria-hidden="true"></i>
                        JOGAR AGORA
                    </button>
                </form>

                <!-- Decorative corner badge -->
                <span class="game-card-badge" aria-hidden="true">MATH</span>
            </article><!-- /.game-card--math -->

            <!-- ---- Card: Desafio das Palavras ---- -->
            <article class="game-card game-card--words" aria-label="Jogo: Desafio das Palavras">
                <div class="game-card-glow" aria-hidden="true"></div>

                <div class="game-card-header">
                    <div class="game-icon-wrap game-icon-wrap--words" aria-hidden="true">
                        <span class="game-icon-emoji">📚</span>
                    </div>
                    <div>
                        <h3 class="game-card-title">Desafio das Palavras</h3>
                        <p class="game-card-desc">
                            Teste seus conhecimentos de Língua Portuguesa.
                        </p>
                    </div>
                </div>

                <div class="game-card-stats">
                    <div class="game-stat">
                        <i data-lucide="star" aria-hidden="true"></i>
                        <span>Melhor: <strong><?= number_format($ptBestScore, 0, ',', '.') ?></strong></span>
                    </div>
                    <div class="game-stat">
                        <i data-lucide="layers" aria-hidden="true"></i>
                        <span>Partidas: <strong><?= $ptPartidas ?></strong></span>
                    </div>
                </div>

                <form class="game-play-form" action="jogos/portugues.php" method="get">
                    <label class="text-secondary small" for="portugues-difficulty">Escolha o nível do jogo:</label>
                    <select class="game-difficulty-select" id="portugues-difficulty" name="dificuldade">
                        <option value="facil">Fácil</option>
                        <option value="medio">Médio</option>
                        <option value="dificil">Difícil</option>
                    </select>
                    <button type="submit" class="btn btn-play-game" aria-label="Jogar Desafio das Palavras">
                        <i data-lucide="play" aria-hidden="true"></i>
                        JOGAR AGORA
                    </button>
                </form>

                <!-- Decorative corner badge -->
                <span class="game-card-badge" aria-hidden="true">WORDS</span>
            </article><!-- /.game-card--words -->

        </div>
    </section><!-- /.games-section -->


    <!-- ===================================================================== -->
    <!-- 4. ACHIEVEMENTS SECTION                                                -->
    <!-- ===================================================================== -->
    <section class="achievements-section" aria-label="Conquistas">
        <div class="section-header">
            <h2 class="section-title">
                <i data-lucide="trophy" aria-hidden="true"></i>
                Suas Conquistas
            </h2>
            <p class="section-subtitle">
                <?php
                    $unlockedCount = count(array_filter($achievements, fn($a) => $a['data_conquista'] !== null));
                    $totalCount    = count($achievements);
                ?>
                <?= $unlockedCount ?> de <?= $totalCount ?> conquistadas
            </p>
        </div>

        <?php if (empty($achievements)): ?>
            <p class="empty-state-text">
                <i data-lucide="award" aria-hidden="true"></i>
                Nenhuma conquista cadastrada ainda. Volte em breve!
            </p>
        <?php else: ?>
        <div class="achievements-grid">
            <?php foreach ($achievements as $i => $ach):
                $unlocked    = $ach['data_conquista'] !== null;
                $lockClass   = $unlocked ? 'achievement--unlocked' : 'achievement--locked';
                $iconEmoji   = $achievementIcons[$i % count($achievementIcons)];
                $conquDate   = $unlocked
                    ? date('d/m/Y', strtotime($ach['data_conquista']))
                    : null;
                $achName     = htmlspecialchars($ach['nome'] ?? 'Conquista', ENT_QUOTES, 'UTF-8');
                $achDesc     = htmlspecialchars($ach['descricao'] ?? '', ENT_QUOTES, 'UTF-8');
            ?>
            <div class="achievement-card <?= $lockClass ?>" aria-label="Conquista: <?= $achName ?><?= $unlocked ? ' (Conquistada)' : ' (Bloqueada)' ?>">
                <div class="achievement-icon-wrap">
                    <span class="achievement-emoji" aria-hidden="true"><?= $iconEmoji ?></span>
                    <?php if (!$unlocked): ?>
                    <div class="achievement-lock-overlay" aria-hidden="true">
                        <i data-lucide="lock"></i>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="achievement-body">
                    <h3 class="achievement-name"><?= $achName ?></h3>
                    <p class="achievement-desc"><?= $achDesc ?></p>
                    <?php if ($unlocked): ?>
                        <span class="achievement-date">
                            <i data-lucide="calendar-check" aria-hidden="true"></i>
                            Conquistado em <?= $conquDate ?>
                        </span>
                    <?php else: ?>
                        <span class="achievement-locked-label">
                            <i data-lucide="lock" aria-hidden="true"></i>
                            Bloqueado
                        </span>
                    <?php endif; ?>
                </div>
                <?php if ($unlocked): ?>
                    <div class="achievement-glow" aria-hidden="true"></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </section><!-- /.achievements-section -->


</div><!-- /.dashboard-wrap -->

<!-- =========================================================================
     Dashboard JavaScript
     ========================================================================= -->
<script src="assets/js/dashboard.js?v=<?= filemtime(__DIR__ . '/assets/js/dashboard.js') ?>" defer></script>

<?php require_once 'includes/footer.php'; ?>
