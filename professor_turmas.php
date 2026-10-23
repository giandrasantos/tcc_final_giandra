<?php
/**
 * Comparative student performance grouped by school year.
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

requireRole('professor');
refreshSessionCache();

$pdo = getDB();
$stmt = $pdo->query(
    "SELECT u.serie,
            COUNT(DISTINCT u.id) AS total_alunos,
            COUNT(DISTINCT CASE WHEN m.id IS NOT NULL THEN u.id END) AS alunos_ativos,
            COUNT(m.id) AS total_partidas,
            COALESCE(SUM(m.pontuacao), 0) AS total_pontos,
            COALESCE(SUM(m.acertos), 0) AS total_acertos,
            COALESCE(SUM(m.erros), 0) AS total_erros
       FROM users u
       LEFT JOIN matches m ON m.user_id = u.id
      WHERE u.tipo_usuario = 'aluno'
        AND u.serie IS NOT NULL
      GROUP BY u.serie
      ORDER BY u.serie ASC"
);
$classes = array_map(static function (array $row): array {
    $activeStudents = (int) $row['alunos_ativos'];
    $answers = (int) $row['total_acertos'] + (int) $row['total_erros'];

    return [
        'serie' => (string) $row['serie'],
        'total_alunos' => (int) $row['total_alunos'],
        'alunos_ativos' => $activeStudents,
        'total_partidas' => (int) $row['total_partidas'],
        'total_pontos' => (int) $row['total_pontos'],
        'media_pontos' => $activeStudents > 0
            ? (int) round((int) $row['total_pontos'] / $activeStudents)
            : 0,
        'aproveitamento' => $answers > 0
            ? (int) round(((int) $row['total_acertos'] / $answers) * 100)
            : 0,
    ];
}, $stmt->fetchAll());

$totalStudents = array_sum(array_column($classes, 'total_alunos'));
$totalMatches = array_sum(array_column($classes, 'total_partidas'));
$totalPoints = array_sum(array_column($classes, 'total_pontos'));
$classColors = [
    '6°' => '#9D4EDD',
    '7°' => '#00E676',
    '8°' => '#FFC857',
    '9°' => '#38BDF8',
];
$pieSlices = [];
$pieStops = [];
$currentAngle = 0.0;

if ($totalPoints > 0) {
    foreach ($classes as $class) {
        $points = $class['total_pontos'];
        if ($points === 0) {
            continue;
        }

        $endAngle = $currentAngle + ($points / $totalPoints * 100);
        $color = $classColors[$class['serie']] ?? '#F472B6';
        $pieSlices[] = [
            'serie' => $class['serie'],
            'pontos' => $points,
            'percentual' => (int) round(($points / $totalPoints) * 100),
            'color' => $color,
        ];
        $pieStops[] = sprintf(
            '%s %.2f%% %.2f%%',
            $color,
            $currentAngle,
            $endAngle
        );
        $currentAngle = $endAngle;
    }
}
$pieGradient = implode(', ', $pieStops);

$pageTitle = 'Desempenho das Turmas';
$extraCss = ['css/dashboard.css', 'css/professor-dashboard.css'];
require_once __DIR__ . '/includes/header.php';
?>

<div class="dashboard-wrap professor-dashboard professor-classes">
    <section class="welcome-banner professor-welcome" aria-labelledby="classes-heading">
        <div class="banner-info">
            <h1 class="banner-greeting" id="classes-heading">
                Desempenho das <span class="banner-name">Turmas</span>
            </h1>
            <p>Compare os resultados conjuntos dos alunos por série escolar.</p>
        </div>
    </section>

    <section class="stats-grid professor-stats" aria-label="Resumo das turmas">
        <article class="stat-card stat-card--blue">
            <div class="stat-card-body">
                <p class="stat-label">Séries com alunos</p>
                <p class="stat-value"><?= number_format(count($classes), 0, ',', '.') ?></p>
            </div>
        </article>
        <article class="stat-card stat-card--green">
            <div class="stat-card-body">
                <p class="stat-label">Alunos cadastrados</p>
                <p class="stat-value"><?= number_format($totalStudents, 0, ',', '.') ?></p>
            </div>
        </article>
        <article class="stat-card stat-card--gold">
            <div class="stat-card-body">
                <p class="stat-label">Partidas realizadas</p>
                <p class="stat-value"><?= number_format($totalMatches, 0, ',', '.') ?></p>
            </div>
        </article>
    </section>

    <?php if ($classes === []): ?>
        <section class="card" aria-label="Sem turmas para comparar">
            <div class="empty-state">
                <span class="empty-state-emoji" aria-hidden="true">📊</span>
                <p class="empty-state-text">Ainda não há alunos cadastrados com uma série escolar para comparar.</p>
            </div>
        </section>
    <?php else: ?>
        <section class="card professor-chart-card class-pie-card" aria-labelledby="class-score-title">
            <div class="section-header">
                <h2 class="section-title" id="class-score-title">Comparativo de pontos por turma</h2>
                <p class="section-subtitle">Cada fatia representa a participação da série no total de pontos acumulados pelos alunos.</p>
            </div>

            <?php if ($totalPoints === 0): ?>
                <div class="empty-state">
                    <span class="empty-state-emoji" aria-hidden="true">📊</span>
                    <p class="empty-state-text">O gráfico aparecerá quando os alunos registrarem pontos nas partidas.</p>
                </div>
            <?php else: ?>
                <div class="class-pie-layout">
                    <div
                        class="class-pie-chart"
                        role="img"
                        aria-label="Gráfico de pizza com a distribuição dos <?= number_format($totalPoints, 0, ',', '.') ?> pontos por série"
                        style="background: conic-gradient(from -90deg, <?= htmlspecialchars($pieGradient, ENT_QUOTES, 'UTF-8') ?>)"
                    ></div>
                    <ul class="class-pie-legend" aria-label="Legenda de pontos por série">
                        <?php foreach ($pieSlices as $slice): ?>
                            <li class="class-pie-legend-item">
                                <span
                                    class="class-pie-swatch"
                                    style="background-color: <?= htmlspecialchars($slice['color'], ENT_QUOTES, 'UTF-8') ?>"
                                    aria-hidden="true"
                                ></span>
                                <span class="class-pie-legend-label">
                                    <?= htmlspecialchars($slice['serie'], ENT_QUOTES, 'UTF-8') ?> Ano
                                </span>
                                <strong><?= number_format($slice['pontos'], 0, ',', '.') ?> pts</strong>
                                <span class="class-pie-percentage"><?= $slice['percentual'] ?>%</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </section>

        <section class="card professor-students-card" aria-labelledby="class-summary-title">
            <div class="section-header">
                <h2 class="section-title" id="class-summary-title">Resumo comparativo</h2>
                <p class="section-subtitle">Visão conjunta dos alunos cadastrados em cada série.</p>
            </div>
            <div class="table-responsive">
                <table class="table professor-students-table class-summary-table">
                    <thead>
                        <tr>
                            <th scope="col">Série</th>
                            <th scope="col">Alunos</th>
                            <th scope="col">Alunos que jogaram</th>
                            <th scope="col">Partidas</th>
                            <th scope="col">Pontos totais</th>
                            <th scope="col">Média de pontos</th>
                            <th scope="col">Aproveitamento</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($classes as $class): ?>
                            <tr>
                                <th scope="row"><?= htmlspecialchars($class['serie'], ENT_QUOTES, 'UTF-8') ?> Ano</th>
                                <td><?= number_format($class['total_alunos'], 0, ',', '.') ?></td>
                                <td><?= number_format($class['alunos_ativos'], 0, ',', '.') ?></td>
                                <td><?= number_format($class['total_partidas'], 0, ',', '.') ?></td>
                                <td><?= number_format($class['total_pontos'], 0, ',', '.') ?></td>
                                <td><?= number_format($class['media_pontos'], 0, ',', '.') ?></td>
                                <td><?= $class['aproveitamento'] ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
