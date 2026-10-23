<?php
/**
 * MathPlay Solutions — Jogo 1: Desafio Matemático
 * Studio Game Over
 */

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/auth.php';
requireRole('aluno');
refreshSessionCache();

$dificuldadeRaw = strtolower($_GET['dificuldade'] ?? '');
$dificuldade = in_array($dificuldadeRaw, ['facil', 'medio', 'dificil'], true) ? $dificuldadeRaw : null;

$pageTitle = 'Desafio Matemático | MathPlay Solutions';
$extraCss = ['css/jogos.css'];
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="container py-4">

<?php if (!$dificuldade): ?>
    <!-- Difficulty selection screen -->
    <div class="difficulty-selection card text-center animate-fade-in my-4">
        <div class="game-badge-header mb-3">
            <span class="game-icon" style="font-size: 3.5rem;">🧮</span>
            <h1 class="text-gradient display-title mt-2">Desafio Matemático</h1>
            <p class="text-secondary fs-5">Escolha o nível de dificuldade para começar sua jornada de aprendizado!</p>
        </div>

        <div class="grid-3 gap-lg my-4">
            <a href="matematica.php?dificuldade=facil" class="card card-hover text-center p-4 border-success text-decoration-none">
                <div class="diff-emoji" style="font-size: 2.5rem;">🟢</div>
                <h3 class="text-success font-display my-2">Fácil</h3>
                <p class="text-secondary small">Questões introdutórias. Números inteiros, frações simples e porcentagens básicas.</p>
                <div class="badge badge-easy mt-2">+10 XP / Pontos por acerto</div>
            </a>

            <a href="matematica.php?dificuldade=medio" class="card card-hover text-center p-4 border-warning text-decoration-none">
                <div class="diff-emoji" style="font-size: 2.5rem;">🟡</div>
                <h3 class="text-warning font-display my-2">Médio</h3>
                <p class="text-secondary small">Questões intermediárias. Equações de 1º grau, geometria e operações combinadas.</p>
                <div class="badge badge-medium mt-2">+20 XP / Pontos por acerto</div>
            </a>

            <a href="matematica.php?dificuldade=dificil" class="card card-hover text-center p-4 border-danger text-decoration-none">
                <div class="diff-emoji" style="font-size: 2.5rem;">🔴</div>
                <h3 class="text-danger font-display my-2">Difícil</h3>
                <p class="text-secondary small">Desafios avançados. Potenciação, radiciação, sistemas de equações e problemas lógicos.</p>
                <div class="badge badge-hard mt-2">+30 XP / Pontos por acerto</div>
            </a>
        </div>

        <div class="mt-3">
            <a href="../dashboard.php" class="btn btn-outline">
                <i data-lucide="arrow-left"></i> Voltar ao Dashboard
            </a>
        </div>
    </div>

<?php else: ?>
    <!-- Active Game Screen -->
    <div class="game-container animate-fade-in" id="game-container">

        <!-- Top Status Bar -->
        <div class="game-header-bar flex justify-between items-center mb-3 p-3 card">
            <div class="flex items-center gap-md">
                <span style="font-size: 1.8rem;">🧮</span>
                <div>
                    <h2 class="font-display m-0 fs-5">Desafio Matemático</h2>
                    <span class="badge badge-<?= $dificuldade ?>">Dificuldade: <?= ucfirst($dificuldade) ?></span>
                </div>
            </div>

            <div class="game-metrics flex items-center gap-lg">
                <div class="text-center">
                    <span class="text-secondary small">QUESTÃO</span>
                    <div class="font-display fs-5 text-primary"><span id="q-current">1</span>/10</div>
                </div>
                <div class="text-center">
                    <span class="text-secondary small">PONTOS</span>
                    <div class="font-display fs-5 text-warning" id="score-display">0</div>
                </div>
                <div class="text-center">
                    <span class="text-secondary small">SEQUÊNCIA</span>
                    <div class="font-display fs-5 text-accent" id="streak-display">🔥 0</div>
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="progress-bar mb-4" style="height: 10px; background: rgba(255,255,255,0.1); border-radius: 5px; overflow: hidden;">
            <div id="game-progress-bar" class="progress-fill" style="width: 0%; height: 100%; background: linear-gradient(90deg, var(--primary), var(--secondary)); transition: width 0.3s ease;"></div>
        </div>

        <!-- Loading Spinner -->
        <div id="game-loading" class="card text-center p-5">
            <div class="spinner border-primary mx-auto mb-3" style="width: 3rem; height: 3rem; border-width: 4px;"></div>
            <h3>Carregando Desafios...</h3>
            <p class="text-secondary">Preparando perguntas incríveis da Studio Game Over</p>
        </div>

        <!-- Question Section -->
        <div id="question-card" class="card p-4 mb-4" style="display: none;">
            <div class="question-header mb-3 pb-3 border-bottom border-light">
                <span class="badge badge-easy mb-2" id="q-category">Matemática</span>
                <h3 class="question-title font-body fs-4" id="q-text">Carregando pergunta...</h3>
            </div>

            <!-- Answer Options -->
            <div class="answers-grid grid-2 gap-md my-4" id="answers-grid">
                <button class="answer-option btn btn-outline text-left p-3 flex items-center gap-md w-full" data-option="a">
                    <span class="option-pill font-display">A</span>
                    <span class="option-text fs-5" id="opt-a-text"></span>
                </button>
                <button class="answer-option btn btn-outline text-left p-3 flex items-center gap-md w-full" data-option="b">
                    <span class="option-pill font-display">B</span>
                    <span class="option-text fs-5" id="opt-b-text"></span>
                </button>
                <button class="answer-option btn btn-outline text-left p-3 flex items-center gap-md w-full" data-option="c">
                    <span class="option-pill font-display">C</span>
                    <span class="option-text fs-5" id="opt-c-text"></span>
                </button>
                <button class="answer-option btn btn-outline text-left p-3 flex items-center gap-md w-full" data-option="d">
                    <span class="option-pill font-display">D</span>
                    <span class="option-text fs-5" id="opt-d-text"></span>
                </button>
            </div>

            <!-- Action Button -->
            <div class="text-center mt-4">
                <button id="btn-responder" class="btn btn-primary btn-lg px-5" disabled>
                    <i data-lucide="check-circle"></i> RESPONDER
                </button>
            </div>

            <!-- Feedback Area -->
            <div id="feedback-container" class="mt-4 p-4 card" style="display: none;">
                <div id="feedback-status" class="flex items-center gap-md fs-4 mb-3"></div>

                <!-- Tip Card (On Wrong Answer) -->
                <div id="tip-box" class="card p-3 my-3 bg-tertiary border-warning" style="display: none;">
                    <div class="flex items-center gap-sm text-warning font-display mb-1">
                        <i data-lucide="lightbulb"></i> 💡 DICA DO STUDIO GAME OVER
                    </div>
                    <p id="tip-content" class="m-0 text-secondary"></p>
                </div>

                <!-- Explanation Box -->
                <div id="explanation-box" class="card p-3 my-3 bg-tertiary border-primary" style="display: none;">
                    <div class="flex items-center gap-sm text-secondary font-display mb-1">
                        <i data-lucide="book-open"></i> 📖 EXPLICAÇÃO
                    </div>
                    <p id="explanation-content" class="m-0 text-secondary"></p>
                </div>

                <!-- Motivational Quote -->
                <div id="motivational-banner" class="text-center font-display text-warning my-3 fs-5"></div>

                <div class="text-center mt-3">
                    <button id="btn-proxima" class="btn btn-accent btn-lg px-5">
                        PRÓXIMA QUESTÃO <i data-lucide="arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Result Overlay/Screen -->
        <div id="result-screen" class="card text-center p-5 my-4 animate-fade-in" style="display: none;">
            <div class="result-badge mb-3" style="font-size: 4rem;">🎯</div>
            <h1 class="font-display text-gradient display-title">Partida Finalizada!</h1>
            <p class="text-secondary fs-5" id="result-subtitle">Você demonstrou um excelente raciocínio matemático!</p>
            <p id="save-status" class="text-secondary" role="status" aria-live="polite">Salvando partida...</p>

            <div class="grid-4 gap-md my-4">
                <div class="card p-3 bg-tertiary">
                    <span class="text-secondary small">PONTUAÇÃO</span>
                    <h2 class="font-display text-warning m-0" id="res-score">0</h2>
                </div>
                <div class="card p-3 bg-tertiary">
                    <span class="text-secondary small">ACERTOS</span>
                    <h2 class="font-display text-success m-0" id="res-acertos">0</h2>
                </div>
                <div class="card p-3 bg-tertiary">
                    <span class="text-secondary small">ERROS</span>
                    <h2 class="font-display text-danger m-0" id="res-erros">0</h2>
                </div>
                <div class="card p-3 bg-tertiary">
                    <span class="text-secondary small">XP CONQUISTADO</span>
                    <h2 class="font-display text-secondary m-0" id="res-xp">+0</h2>
                </div>
            </div>

            <!-- Unlocked Achievements -->
            <div id="new-achievements-box" class="my-4 card p-4 border-warning bg-tertiary" style="display: none;">
                <h3 class="text-warning font-display mb-3">🏅 Conquistas Desbloqueadas!</h3>
                <div id="achievements-list" class="flex flex-col gap-sm"></div>
            </div>

            <div class="flex justify-center gap-md mt-4 flex-wrap">
                <a href="matematica.php?dificuldade=<?= $dificuldade ?>" class="btn btn-primary btn-lg">
                    <i data-lucide="rotate-ccw"></i> JOGAR NOVAMENTE
                </a>
                <a href="portugues.php" class="btn btn-secondary btn-lg">
                    <i data-lucide="book"></i> DESAFIO DAS PALAVRAS
                </a>
                <a href="../dashboard.php" class="btn btn-outline btn-lg">
                    <i data-lucide="layout-dashboard"></i> DASHBOARD
                </a>
            </div>
        </div>

    </div>

    <!-- Hidden Game Config Element -->
    <div id="game-config"
         data-jogo="matematica"
         data-dificuldade="<?= $dificuldade ?>"
         data-user-id="<?= $_SESSION['user_id'] ?>"></div>

    <script src="../assets/js/matematica.js" defer></script>
<?php endif; ?>

</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
