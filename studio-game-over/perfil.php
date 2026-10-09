<?php
/**
 * MathPlay Solutions — Perfil do Usuário
 * Studio Game Over
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
requireAuth();
refreshSessionCache();

$pageTitle = 'Meu Perfil | MathPlay Solutions';
$extraCss  = ['css/dashboard.css'];
require_once __DIR__ . '/includes/header.php';

$pdo = getDB();
$userId = (int) $_SESSION['user_id'];

$stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id');
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch();
$isStudent = ($user['tipo_usuario'] ?? 'aluno') === 'aluno';

$levelNames = [1 => 'Iniciante', 2 => 'Aprendiz', 3 => 'Avançado', 4 => 'Especialista', 5 => 'Mestre'];

if ($isStudent) {
    $stmtAch = $pdo->prepare('SELECT COUNT(*) FROM user_achievements WHERE user_id = :uid');
    $stmtAch->execute([':uid' => $userId]);
    $achCount = (int) $stmtAch->fetchColumn();

    $avatars = [
        'avatar1' => ['name' => 'Guerreiro', 'icon' => '⚔️'],
        'avatar2' => ['name' => 'Mago', 'icon' => '🧙'],
        'avatar3' => ['name' => 'Arqueiro', 'icon' => '🏹'],
        'avatar4' => ['name' => 'Ninja', 'icon' => '🥷'],
        'avatar5' => ['name' => 'Robô', 'icon' => '🤖'],
        'avatar6' => ['name' => 'Alien', 'icon' => '👾'],
        'avatar7' => ['name' => 'Cavaleiro', 'icon' => '🛡️'],
        'avatar8' => ['name' => 'Cientista', 'icon' => '🔬'],
    ];
}
?>

<div class="container py-4">

    <div class="card p-5 animate-fade-in mb-4">
        <div class="flex items-center gap-xl flex-wrap">
            <?php if ($isStudent): ?>
            <div class="avatar-large text-center">
                <div id="current-avatar-display" style="width: 120px; height: 120px; border-radius: 50%; background: var(--bg-tertiary); display: flex; align-items: center; justify-content: center; font-size: 4rem; border: 3px solid var(--primary); box-shadow: var(--glow-primary);" class="mx-auto">
                    <?= $avatars[$user['avatar']]['icon'] ?? '🎮' ?>
                </div>
                <div class="badge badge-medium mt-3">
                    Nível <?= (int) $user['nivel'] ?> — <?= $levelNames[$user['nivel']] ?? 'Iniciante' ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="profile-details flex-1">
                <h1 class="font-display text-gradient display-title m-0"><?= htmlspecialchars($user['nome']) ?></h1>
                <p class="text-secondary fs-5 mb-2">@<?= htmlspecialchars($user['username']) ?></p>
                <div class="flex gap-md text-secondary small flex-wrap">
                    <span>📧 <?= htmlspecialchars($user['email']) ?></span>
                    <?php if ($isStudent): ?>
                        <span>🎓 <?= htmlspecialchars($user['serie'] ?? '—') ?> Ano</span>
                    <?php else: ?>
                        <span>👩‍🏫 Professor</span>
                    <?php endif; ?>
                    <span>📅 Cadastrado em <?= date('d/m/Y', strtotime($user['created_at'])) ?></span>
                </div>

                <?php if ($isStudent): ?>
                    <div class="grid-3 gap-md mt-4">
                        <div class="card p-3 bg-tertiary text-center">
                            <span class="text-secondary small">XP TOTAL</span>
                            <div class="font-display text-primary fs-4"><?= number_format($user['xp']) ?></div>
                        </div>
                        <div class="card p-3 bg-tertiary text-center">
                            <span class="text-secondary small">PONTUAÇÃO</span>
                            <div class="font-display text-warning fs-4"><?= number_format($user['pontuacao']) ?></div>
                        </div>
                        <div class="card p-3 bg-tertiary text-center">
                            <span class="text-secondary small">CONQUISTAS</span>
                            <div class="font-display text-success fs-4"><?= $achCount ?> / 6 🏅</div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($isStudent): ?>
    <!-- Avatar Customization Card -->
    <div class="card p-4 animate-fade-in mb-4">
        <h2 class="font-display text-primary mb-2">Personalize seu Avatar Gamer</h2>
        <p class="text-secondary mb-4">Escolha o personagem que melhor representa seu estilo no MathPlay Solutions!</p>

        <div class="grid-4 gap-md my-3" id="avatar-selector">
            <?php foreach ($avatars as $key => $av): ?>
                <?php $isSelected = ($user['avatar'] === $key); ?>
                <div class="card card-hover text-center p-3 cursor-pointer avatar-option <?= $isSelected ? 'border-primary' : '' ?>"
                     data-avatar="<?= $key ?>"
                     style="<?= $isSelected ? 'border-width: 2px; box-shadow: var(--glow-primary);' : '' ?>">
                    <div style="font-size: 3rem;"><?= $av['icon'] ?></div>
                    <div class="font-display mt-2 fs-5"><?= $av['name'] ?></div>
                    <?php if ($isSelected): ?>
                        <span class="badge badge-easy mt-1">SELECIONADO</span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-right mt-3">
            <button id="btn-save-avatar" class="btn btn-primary btn-lg">
                <i data-lucide="save"></i> Salvar Novo Avatar
            </button>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php if ($isStudent): ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    let selectedAvatar = '<?= $user['avatar'] ?>';
    const avatarIcons = {
        'avatar1': '⚔️', 'avatar2': '🧙', 'avatar3': '🏹', 'avatar4': '🥷',
        'avatar5': '🤖', 'avatar6': '👾', 'avatar7': '🛡️', 'avatar8': '🔬'
    };

    document.querySelectorAll('.avatar-option').forEach(opt => {
        opt.addEventListener('click', () => {
            document.querySelectorAll('.avatar-option').forEach(o => {
                o.style.borderWidth = '1px';
                o.style.boxShadow = 'none';
                o.classList.remove('border-primary');
            });
            opt.style.borderWidth = '2px';
            opt.style.boxShadow = 'var(--glow-primary)';
            opt.classList.add('border-primary');

            selectedAvatar = opt.dataset.avatar;
            document.getElementById('current-avatar-display').textContent = avatarIcons[selectedAvatar] || '🎮';
        });
    });

    document.getElementById('btn-save-avatar')?.addEventListener('click', async () => {
        try {
            const res = await fetch('api/update-avatar.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ avatar: selectedAvatar })
            });
            const data = await res.json();
            if (data.success) {
                if (window.showToast) window.showToast('Avatar atualizado com sucesso!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                if (window.showToast) window.showToast(data.message || 'Erro ao atualizar avatar', 'error');
            }
        } catch (err) {
            console.error(err);
        }
    });
});
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
