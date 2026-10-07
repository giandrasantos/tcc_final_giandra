<?php
/**
 * MathPlay Solutions — Histórico de Partidas
 * Studio Game Over
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
requireAuth();
refreshSessionCache();

$pageTitle = 'Histórico | MathPlay Solutions';
$extraCss  = ['css/dashboard.css'];
require_once __DIR__ . '/includes/header.php';

$pdo = getDB();
$userId = (int) $_SESSION['user_id'];

// Query all matches
$stmt = $pdo->prepare(
    'SELECT id, jogo, dificuldade, pontuacao, acertos, erros, xp_ganho, data_partida
       FROM matches
      WHERE user_id = :uid
      ORDER BY data_partida DESC'
);
$stmt->execute([':uid' => $userId]);
$matches = $stmt->fetchAll();

// Stats summary
$stmtSummary = $pdo->prepare(
    'SELECT COUNT(*) as total_partidas,
            COALESCE(SUM(pontuacao), 0) as total_pontos,
            COALESCE(SUM(acertos), 0) as total_acertos,
            COALESCE(SUM(erros), 0) as total_erros
       FROM matches
      WHERE user_id = :uid'
);
$stmtSummary->execute([':uid' => $userId]);
$summary = $stmtSummary->fetch();
?>

<div class="container py-4">

    <div class="flex justify-between items-center mb-4 flex-wrap gap-md">
        <div>
            <h1 class="font-display text-gradient display-title mb-1">Seu Histórico</h1>
            <p class="text-secondary m-0">Acompanhe todas as suas jogadas, acertos e evolução ao longo do tempo</p>
        </div>
        <div>
            <a href="dashboard.php" class="btn btn-outline">
                <i data-lucide="arrow-left"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Summary Stats Row -->
    <div class="grid-4 gap-md mb-4">
        <div class="card p-3 bg-tertiary text-center">
            <span class="text-secondary small">PARTIDAS JOGADAS</span>
            <h2 class="font-display text-primary m-0 mt-1"><?= number_format($summary['total_partidas']) ?></h2>
        </div>
        <div class="card p-3 bg-tertiary text-center">
            <span class="text-secondary small">PONTUAÇÃO ACUMULADA</span>
            <h2 class="font-display text-warning m-0 mt-1"><?= number_format($summary['total_pontos']) ?></h2>
        </div>
        <div class="card p-3 bg-tertiary text-center">
            <span class="text-secondary small">TOTAL DE ACERTOS</span>
            <h2 class="font-display text-success m-0 mt-1"><?= number_format($summary['total_acertos']) ?> ✅</h2>
        </div>
        <div class="card p-3 bg-tertiary text-center">
            <span class="text-secondary small">TOTAL DE ERROS</span>
            <h2 class="font-display text-danger m-0 mt-1"><?= number_format($summary['total_erros']) ?> ❌</h2>
        </div>
    </div>

    <?php if (empty($matches)): ?>
        <div class="card text-center p-5 animate-fade-in my-4">
            <div class="fs-1 mb-3">🕹️</div>
            <h2 class="font-display text-primary mb-2">Nenhuma partida registrada ainda!</h2>
            <p class="text-secondary mb-4">Escolha um dos nossos jogos educativos e comece agora mesmo a marcar pontos!</p>
            <div class="flex justify-center gap-md flex-wrap">
                <a href="jogos/matematica.php" class="btn btn-primary btn-lg">
                    <i data-lucide="calculator"></i> Desafio Matemático
                </a>
                <a href="jogos/portugues.php" class="btn btn-secondary btn-lg">
                    <i data-lucide="book"></i> Desafio das Palavras
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="card p-4 animate-fade-in">
            <div class="table-responsive">
                <table class="table w-full text-left" style="border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border-color); color: var(--text-secondary);">
                            <th class="p-3">JOGO</th>
                            <th class="p-3">DIFICULDADE</th>
                            <th class="p-3">PONTOS</th>
                            <th class="p-3">ACERTOS</th>
                            <th class="p-3">ERROS</th>
                            <th class="p-3">APROVEITAMENTO</th>
                            <th class="p-3">XP GANHO</th>
                            <th class="p-3">DATA</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($matches as $m): ?>
                            <?php
                            $totalAns = $m['acertos'] + $m['erros'];
                            $taxa = $totalAns > 0 ? round(($m['acertos'] / $totalAns) * 100) : 0;
                            $diffClass = $m['dificuldade'] === 'facil' ? 'badge-easy' : ($m['dificuldade'] === 'medio' ? 'badge-medium' : 'badge-hard');
                            $jogoLabel = $m['jogo'] === 'matematica' ? '🧮 Matemática' : '📚 Português';
                            ?>
                            <tr style="border-bottom: 1px solid var(--border-light);" class="table-row-hover">
                                <td class="p-3 font-display"><strong><?= $jogoLabel ?></strong></td>
                                <td class="p-3"><span class="badge <?= $diffClass ?>"><?= ucfirst($m['dificuldade']) ?></span></td>
                                <td class="p-3 text-warning font-display">+<?= number_format($m['pontuacao']) ?></td>
                                <td class="p-3 text-success font-display"><?= $m['acertos'] ?></td>
                                <td class="p-3 text-danger font-display"><?= $m['erros'] ?></td>
                                <td class="p-3">
                                    <div class="flex items-center gap-sm">
                                        <div style="width: 50px; background: rgba(255,255,255,0.1); height: 6px; border-radius: 3px; overflow: hidden;">
                                            <div style="width: <?= $taxa ?>%; height: 100%; background: var(--<?= $taxa >= 70 ? 'success' : ($taxa >= 50 ? 'warning' : 'danger') ?>);"></div>
                                        </div>
                                        <span class="small font-display"><?= $taxa ?>%</span>
                                    </div>
                                </td>
                                <td class="p-3 text-primary font-display">+<?= $m['xp_ganho'] ?> XP</td>
                                <td class="p-3 text-secondary small"><?= date('d/m/Y H:i', strtotime($m['data_partida'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
