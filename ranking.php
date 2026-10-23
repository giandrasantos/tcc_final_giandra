<?php
/**
 * MathPlay Solutions — Ranking de Alunos
 * Studio Game Over
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
requireRole('aluno');
refreshSessionCache();

$pageTitle = 'Ranking | MathPlay Solutions';
$extraCss  = ['css/dashboard.css'];
require_once __DIR__ . '/includes/header.php';

$pdo = getDB();
$userId = (int) $_SESSION['user_id'];
$filtro = in_array($_GET['filtro'] ?? 'geral', ['geral', 'matematica', 'portugues'], true) ? ($_GET['filtro'] ?? 'geral') : 'geral';

if ($filtro === 'geral') {
    $stmt = $pdo->prepare(
        'SELECT id, nome, username, avatar, serie, nivel, xp, pontuacao
           FROM users
          WHERE tipo_usuario = \'aluno\'
          ORDER BY pontuacao DESC, xp DESC
          LIMIT 10'
    );
    $stmt->execute();
} else {
    $stmt = $pdo->prepare(
        'SELECT u.id, u.nome, u.username, u.avatar, u.serie, u.nivel, u.xp, p.melhor_pontuacao as pontuacao
           FROM users u
           JOIN progress p ON u.id = p.user_id
          WHERE u.tipo_usuario = \'aluno\'
            AND p.jogo = :jogo
          ORDER BY p.melhor_pontuacao DESC, u.xp DESC
          LIMIT 10'
    );
    $stmt->execute([':jogo' => $filtro]);
}
$ranking = $stmt->fetchAll();

$levelNames = [1 => 'Iniciante', 2 => 'Aprendiz', 3 => 'Avançado', 4 => 'Especialista', 5 => 'Mestre'];
?>

<div class="container py-4">

    <div class="text-center mb-4 animate-fade-in">
        <div class="fs-1 mb-1">🏆</div>
        <h1 class="font-display text-gradient display-title">Ranking de Jogadores</h1>
        <p class="text-secondary fs-5">Veja os alunos com melhor desempenho e dispute o topo no Studio Game Over!</p>

        <!-- Filter Tabs -->
        <div class="flex justify-center gap-sm my-4 flex-wrap">
            <a href="ranking.php?filtro=geral" class="btn <?= $filtro === 'geral' ? 'btn-primary' : 'btn-outline' ?>">
                🌐 Ranking Geral
            </a>
            <a href="ranking.php?filtro=matematica" class="btn <?= $filtro === 'matematica' ? 'btn-primary' : 'btn-outline' ?>">
                🧮 Matemática
            </a>
            <a href="ranking.php?filtro=portugues" class="btn <?= $filtro === 'portugues' ? 'btn-primary' : 'btn-outline' ?>">
                📚 Português
            </a>
        </div>
    </div>

    <!-- Podium Section (Top 3) -->
    <?php if (count($ranking) >= 3): ?>
        <div class="podium-grid grid-3 gap-md mb-4 items-end max-w-900 mx-auto">
            <!-- 2nd Place -->
            <div class="card p-4 text-center bg-tertiary border-secondary animate-fade-in" style="transform: translateY(15px);">
                <div class="podium-badge text-secondary font-display fs-3">🥈 2º LUGAR</div>
                <div class="avatar-box mx-auto my-3" style="width: 80px; height: 80px; font-size: 3rem;">
                    <?= $ranking[1]['avatar'] === 'avatar1' ? '⚔️' : ($ranking[1]['avatar'] === 'avatar2' ? '🧙' : '👾') ?>
                </div>
                <h3 class="font-display m-0 text-truncate"><?= htmlspecialchars($ranking[1]['nome']) ?></h3>
                <p class="text-secondary small">@<?= htmlspecialchars($ranking[1]['username']) ?></p>
                <div class="badge badge-easy my-2">Nível <?= $ranking[1]['nivel'] ?> — <?= $levelNames[$ranking[1]['nivel']] ?? '' ?></div>
                <div class="font-display text-warning fs-4"><?= number_format($ranking[1]['pontuacao']) ?> pts</div>
            </div>

            <!-- 1st Place -->
            <div class="card p-4 text-center bg-tertiary border-warning animate-fade-in" style="border-width: 2px; box-shadow: 0 0 30px rgba(255, 215, 0, 0.3);">
                <div class="podium-badge text-warning font-display fs-2">🥇 1º LUGAR</div>
                <div class="avatar-box mx-auto my-3" style="width: 100px; height: 100px; font-size: 4rem;">
                    <?= $ranking[0]['avatar'] === 'avatar1' ? '⚔️' : ($ranking[0]['avatar'] === 'avatar2' ? '🧙' : '👑') ?>
                </div>
                <h2 class="font-display m-0 text-warning text-truncate"><?= htmlspecialchars($ranking[0]['nome']) ?></h2>
                <p class="text-secondary small">@<?= htmlspecialchars($ranking[0]['username']) ?></p>
                <div class="badge badge-medium my-2">Nível <?= $ranking[0]['nivel'] ?> — <?= $levelNames[$ranking[0]['nivel']] ?? '' ?></div>
                <div class="font-display text-warning fs-3"><?= number_format($ranking[0]['pontuacao']) ?> pts</div>
            </div>

            <!-- 3rd Place -->
            <div class="card p-4 text-center bg-tertiary border-accent animate-fade-in" style="transform: translateY(25px);">
                <div class="podium-badge text-accent font-display fs-4">🥉 3º LUGAR</div>
                <div class="avatar-box mx-auto my-3" style="width: 70px; height: 70px; font-size: 2.5rem;">
                    <?= $ranking[2]['avatar'] === 'avatar1' ? '⚔️' : ($ranking[2]['avatar'] === 'avatar2' ? '🧙' : '🏹') ?>
                </div>
                <h3 class="font-display m-0 text-truncate"><?= htmlspecialchars($ranking[2]['nome']) ?></h3>
                <p class="text-secondary small">@<?= htmlspecialchars($ranking[2]['username']) ?></p>
                <div class="badge badge-easy my-2">Nível <?= $ranking[2]['nivel'] ?> — <?= $levelNames[$ranking[2]['nivel']] ?? '' ?></div>
                <div class="font-display text-warning fs-4"><?= number_format($ranking[2]['pontuacao']) ?> pts</div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Full Leaderboard Table -->
    <div class="card p-4 animate-fade-in">
        <h3 class="font-display text-primary mb-3">Tabela de Classificação</h3>

        <?php if (empty($ranking)): ?>
            <p class="text-secondary text-center p-4">Nenhum jogador pontuou nesta categoria ainda. Seja o primeiro!</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table w-full text-left" style="border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border-color); color: var(--text-secondary);">
                            <th class="p-3">POSIÇÃO</th>
                            <th class="p-3">ALUNO</th>
                            <th class="p-3">SÉRIE</th>
                            <th class="p-3">NÍVEL</th>
                            <th class="p-3">XP</th>
                            <th class="p-3 text-right">PONTUAÇÃO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ranking as $index => $r): ?>
                            <?php
                            $pos = $index + 1;
                            $isSelf = ($r['id'] == $userId);
                            $posBadge = $pos === 1 ? '🥇' : ($pos === 2 ? '🥈' : ($pos === 3 ? '🥉' : '#' . $pos));
                            ?>
                            <tr style="border-bottom: 1px solid var(--border-light); <?= $isSelf ? 'background: rgba(157, 78, 221, 0.15);' : '' ?>" class="table-row-hover">
                                <td class="p-3 font-display fs-5"><?= $posBadge ?></td>
                                <td class="p-3 flex items-center gap-md">
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--bg-tertiary); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; border: 1px solid var(--primary);">
                                        🎮
                                    </div>
                                    <div>
                                        <div class="font-display <?= $isSelf ? 'text-primary' : '' ?>"><?= htmlspecialchars($r['nome']) ?> <?= $isSelf ? '(Você)' : '' ?></div>
                                        <div class="small text-secondary">@<?= htmlspecialchars($r['username']) ?></div>
                                    </div>
                                </td>
                                <td class="p-3 text-secondary"><?= $r['serie'] !== null ? htmlspecialchars(str_replace('°', 'º', $r['serie']), ENT_QUOTES, 'UTF-8') . ' Ano' : '—' ?></td>
                                <td class="p-3"><span class="badge badge-easy">Nível <?= $r['nivel'] ?></span></td>
                                <td class="p-3 text-primary font-display"><?= number_format($r['xp']) ?> XP</td>
                                <td class="p-3 text-right text-warning font-display fs-5"><?= number_format($r['pontuacao']) ?> pts</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
