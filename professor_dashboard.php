<?php
/**
 * professor_dashboard.php — Student performance overview for teachers.
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

requireRole('professor');
refreshSessionCache();

$pdo = getDB();
$stmt = $pdo->query(
    "SELECT u.id, u.nome, u.username, u.serie,
            COUNT(m.id) AS total_partidas,
            COALESCE(SUM(m.pontuacao), 0) AS total_pontos,
            COALESCE(SUM(m.acertos), 0) AS total_acertos,
            COALESCE(SUM(m.erros), 0) AS total_erros,
            SUM(CASE WHEN m.jogo = 'matematica' THEN 1 ELSE 0 END) AS partidas_matematica,
            SUM(CASE WHEN m.jogo = 'portugues' THEN 1 ELSE 0 END) AS partidas_portugues
       FROM users u
       INNER JOIN matches m ON m.user_id = u.id
      WHERE u.tipo_usuario = 'aluno'
      GROUP BY u.id, u.nome, u.username, u.serie
      ORDER BY total_pontos DESC, total_partidas DESC, u.nome ASC"
);
$students = $stmt->fetchAll();

$totalStudents = count($students);
$totalMatches = array_sum(array_map(static fn(array $student): int => (int) $student['total_partidas'], $students));
$totalPoints = array_sum(array_map(static fn(array $student): int => (int) $student['total_pontos'], $students));
$studentScores = array_map(static fn(array $student): int => (int) $student['total_pontos'], $students);
$maxPoints = $studentScores === [] ? 1 : max(1, ...$studentScores);

$pageTitle = 'Painel do Professor';
$extraCss = ['css/dashboard.css', 'css/professor-dashboard.css'];
require_once __DIR__ . '/includes/header.php';
?>

<div class="dashboard-wrap professor-dashboard">
    <section class="welcome-banner professor-welcome" aria-labelledby="professor-heading">
        <div class="banner-info">
            <h1 class="banner-greeting" id="professor-heading">
                Desempenho dos <span class="banner-name">Alunos</span>
            </h1>
            <p>Acompanhe os resultados de todos os alunos que já jogaram na plataforma.</p>
        </div>
    </section>

    <section class="stats-grid professor-stats" aria-label="Resumo geral dos alunos">
        <article class="stat-card stat-card--blue">
            <div class="stat-card-body">
                <p class="stat-label">Alunos que já jogaram</p>
                <p class="stat-value"><?= number_format($totalStudents, 0, ',', '.') ?></p>
            </div>
        </article>
        <article class="stat-card stat-card--green">
            <div class="stat-card-body">
                <p class="stat-label">Partidas realizadas</p>
                <p class="stat-value"><?= number_format($totalMatches, 0, ',', '.') ?></p>
            </div>
        </article>
        <article class="stat-card stat-card--gold">
            <div class="stat-card-body">
                <p class="stat-label">Pontos acumulados</p>
                <p class="stat-value"><?= number_format($totalPoints, 0, ',', '.') ?></p>
            </div>
        </article>
    </section>

    <section class="card professor-chart-card" aria-labelledby="performance-chart-title">
        <div class="section-header">
            <h2 class="section-title" id="performance-chart-title">Pontuação por aluno</h2>
            <p class="section-subtitle">Pontuação total obtida nas partidas de Matemática e Português.</p>
        </div>

        <?php if ($students === []): ?>
            <div class="empty-state">
                <span class="empty-state-emoji" aria-hidden="true">📊</span>
                <p class="empty-state-text">Ainda não há alunos com partidas registradas.</p>
            </div>
        <?php else: ?>
            <ol class="student-performance-chart" aria-label="Gráfico de pontuação total por aluno">
                <?php foreach ($students as $student):
                    $studentName = htmlspecialchars($student['nome'], ENT_QUOTES, 'UTF-8');
                    $score = (int) $student['total_pontos'];
                    $barWidth = (int) round(($score / $maxPoints) * 100);
                ?>
                    <li class="student-chart-row">
                        <span class="student-chart-name"><?= $studentName ?></span>
                        <span
                            class="student-chart-track"
                            role="img"
                            aria-label="<?= $studentName ?>: <?= number_format($score, 0, ',', '.') ?> pontos"
                        >
                            <span class="student-chart-bar" style="width: <?= $barWidth ?>%"></span>
                        </span>
                        <strong class="student-chart-score"><?= number_format($score, 0, ',', '.') ?></strong>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>
    </section>

    <section class="card professor-students-card" aria-labelledby="student-list-title">
        <div class="section-header">
            <h2 class="section-title" id="student-list-title">Desempenho individual</h2>
            <p class="section-subtitle">Lista completa dos alunos que utilizaram os jogos.</p>
        </div>

        <?php if ($students === []): ?>
            <p class="text-secondary">Os dados aparecerão aqui após a primeira partida de um aluno.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table professor-students-table">
                    <thead>
                        <tr>
                            <th scope="col">Aluno</th>
                            <th scope="col">Série</th>
                            <th scope="col">Partidas</th>
                            <th scope="col">Matemática</th>
                            <th scope="col">Português</th>
                            <th scope="col">Pontos</th>
                            <th scope="col">Acertos</th>
                            <th scope="col">Erros</th>
                            <th scope="col">Aproveitamento</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student):
                            $answers = (int) $student['total_acertos'] + (int) $student['total_erros'];
                            $accuracy = $answers > 0 ? (int) round(((int) $student['total_acertos'] / $answers) * 100) : 0;
                        ?>
                            <tr>
                                <th scope="row">
                                    <?= htmlspecialchars($student['nome'], ENT_QUOTES, 'UTF-8') ?>
                                    <span class="student-username">@<?= htmlspecialchars($student['username'], ENT_QUOTES, 'UTF-8') ?></span>
                                </th>
                                <td><?= htmlspecialchars($student['serie'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= number_format((int) $student['total_partidas'], 0, ',', '.') ?></td>
                                <td><?= number_format((int) $student['partidas_matematica'], 0, ',', '.') ?></td>
                                <td><?= number_format((int) $student['partidas_portugues'], 0, ',', '.') ?></td>
                                <td><?= number_format((int) $student['total_pontos'], 0, ',', '.') ?></td>
                                <td><?= number_format((int) $student['total_acertos'], 0, ',', '.') ?></td>
                                <td><?= number_format((int) $student['total_erros'], 0, ',', '.') ?></td>
                                <td><?= $accuracy ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
