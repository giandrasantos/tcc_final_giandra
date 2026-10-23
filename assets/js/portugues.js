/**
 * MathPlay Solutions — Jogo de Português Logic
 * Studio Game Over
 */

(function() {
    'use strict';

    const gameState = {
        jogo: 'portugues',
        dificuldade: 'facil',
        questions: [],
        currentIndex: 0,
        selectedOption: null,
        answered: false,
        score: 0,
        acertos: 0,
        erros: 0,
        streak: 0,
        maxStreak: 0,
        xpGained: 0,
        token: '',
        pointsPerCorrect: 10
    };

    const pointsMap = { facil: 10, medio: 20, dificil: 30 };

    const frasesCerto = [
        "🌟 Excelente! Vocabulário em dia!",
        "🔥 Incrível! Você domina a língua portuguesa!",
        "💪 Muito bem! Concordância perfeita!",
        "🚀 Arrasou! Próximo desafio das palavras!",
        "🏆 Mestre da Língua Portuguesa!"
    ];

    const frasesErro = [
        "💪 Errar faz parte do aprendizado! Veja a dica com atenção.",
        "🧠 Não desista! Língua Portuguesa exige prática constante!",
        "💡 Leia a explicação para fixar a regra gramatical!",
        "📚 O conhecimento se constrói a cada tentativa!"
    ];

    document.addEventListener('DOMContentLoaded', () => {
        const config = document.getElementById('game-config');
        if (!config) return;

        gameState.jogo = config.dataset.jogo || 'portugues';
        gameState.dificuldade = config.dataset.dificuldade || 'facil';
        gameState.pointsPerCorrect = pointsMap[gameState.dificuldade] || 10;

        initGame();
    });

    async function initGame() {
        showLoading(true);
        try {
            const res = await fetch(`../api/get-questions.php?jogo=${gameState.jogo}&dificuldade=${gameState.dificuldade}&limit=10`);
            const data = await res.json();

            if (data.success && data.questions && data.questions.length > 0) {
                gameState.questions = data.questions;
                gameState.token = data.token;
                showLoading(false);
                renderQuestion();
            } else {
                showError(data.message || 'Não foi possível carregar as perguntas.');
            }
        } catch (err) {
            console.error(err);
            showError('Erro ao conectar com o servidor. Verifique o XAMPP.');
        }
    }

    function renderQuestion() {
        const q = gameState.questions[gameState.currentIndex];
        gameState.selectedOption = null;
        gameState.answered = false;

        // Reset UI
        document.getElementById('q-current').textContent = gameState.currentIndex + 1;
        document.getElementById('q-text').textContent = q.enunciado || q.pergunta;
        document.getElementById('opt-a-text').textContent = q.alternativa_a;
        document.getElementById('opt-b-text').textContent = q.alternativa_b;
        document.getElementById('opt-c-text').textContent = q.alternativa_c;
        document.getElementById('opt-d-text').textContent = q.alternativa_d;

        const categoryEl = document.getElementById('q-category');
        if (categoryEl) categoryEl.textContent = q.categoria || 'Português';

        // Update Progress Bar
        const progressPct = ((gameState.currentIndex) / gameState.questions.length) * 100;
        document.getElementById('game-progress-bar').style.width = progressPct + '%';

        // Reset option buttons
        const options = document.querySelectorAll('.answer-option');
        options.forEach(opt => {
            opt.classList.remove('btn-accent', 'btn-success', 'btn-danger', 'btn-selected');
            opt.classList.add('btn-outline');
            opt.disabled = false;
        });

        // Hide feedback
        document.getElementById('feedback-container').style.display = 'none';
        document.getElementById('tip-box').style.display = 'none';
        document.getElementById('explanation-box').style.display = 'none';
        document.getElementById('btn-responder').disabled = true;

        document.getElementById('question-card').style.display = 'block';

        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    // Handle Option Click
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.answer-option');
        if (!btn || gameState.answered) return;

        document.querySelectorAll('.answer-option').forEach(opt => {
            opt.classList.remove('btn-accent', 'btn-selected');
            opt.classList.add('btn-outline');
        });

        btn.classList.remove('btn-outline');
        btn.classList.add('btn-accent', 'btn-selected');

        gameState.selectedOption = btn.dataset.option;
        document.getElementById('btn-responder').disabled = false;
    });

    // Submit Answer
    document.getElementById('btn-responder')?.addEventListener('click', async () => {
        if (!gameState.selectedOption || gameState.answered) return;

        gameState.answered = true;
        const q = gameState.questions[gameState.currentIndex];

        // Disable options & button
        document.querySelectorAll('.answer-option').forEach(opt => opt.disabled = true);
        document.getElementById('btn-responder').disabled = true;

        try {
            const res = await fetch('../api/verify-answer.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    token: gameState.token,
                    question_id: q.id,
                    resposta_dada: gameState.selectedOption
                })
            });

            const result = await res.json();
            handleAnswerFeedback(result);
        } catch (err) {
            console.error('Error verifying answer:', err);
        }
    });

    function handleAnswerFeedback(result) {
        const isCorrect = result.correct;
        const correctOpt = result.resposta_correta || result.correct_answer;

        // Highlight options
        document.querySelectorAll('.answer-option').forEach(opt => {
            const optVal = opt.dataset.option;
            if (optVal === correctOpt) {
                opt.classList.remove('btn-outline', 'btn-accent');
                opt.classList.add('btn-success');
            } else if (optVal === gameState.selectedOption && !isCorrect) {
                opt.classList.remove('btn-outline', 'btn-accent');
                opt.classList.add('btn-danger');
            }
        });

        // Update stats
        if (isCorrect) {
            let pts = gameState.pointsPerCorrect;
            gameState.streak++;
            if (gameState.streak > gameState.maxStreak) gameState.maxStreak = gameState.streak;

            if (gameState.streak >= 5) pts += 10;
            else if (gameState.streak >= 3) pts += 5;

            gameState.score += pts;
            gameState.acertos++;
            gameState.xpGained += pts;

            document.getElementById('score-display').textContent = gameState.score;
            document.getElementById('streak-display').textContent = '🔥 ' + gameState.streak;

            const status = document.getElementById('feedback-status');
            status.className = 'flex items-center gap-md fs-4 mb-3 text-success';
            status.innerHTML = `<i data-lucide="check-circle"></i> Resposta Correta! +${pts} pts`;

            const quote = frasesCerto[Math.floor(Math.random() * frasesCerto.length)];
            document.getElementById('motivational-banner').textContent = quote;

            if (result.explicacao) {
                document.getElementById('explanation-content').textContent = result.explicacao;
                document.getElementById('explanation-box').style.display = 'block';
            }
        } else {
            gameState.streak = 0;
            gameState.erros++;
            document.getElementById('streak-display').textContent = '🔥 0';

            const status = document.getElementById('feedback-status');
            status.className = 'flex items-center gap-md fs-4 mb-3 text-danger';
            status.innerHTML = `<i data-lucide="x-circle"></i> Resposta Incorreta!`;

            if (result.dica) {
                document.getElementById('tip-content').textContent = result.dica;
                document.getElementById('tip-box').style.display = 'block';
            }

            if (result.explicacao) {
                document.getElementById('explanation-content').textContent = result.explicacao;
                document.getElementById('explanation-box').style.display = 'block';
            }

            const quote = frasesErro[Math.floor(Math.random() * frasesErro.length)];
            document.getElementById('motivational-banner').textContent = quote;
        }

        document.getElementById('feedback-container').style.display = 'block';
        if (typeof lucide !== 'undefined') lucide.createIcons();

        if (gameState.currentIndex >= gameState.questions.length - 1) {
            document.getElementById('btn-proxima').innerHTML = 'FINALIZAR PARTIDA <i data-lucide="award"></i>';
        }
    }

    // Next Question Button
    document.getElementById('btn-proxima')?.addEventListener('click', () => {
        gameState.currentIndex++;
        if (gameState.currentIndex >= gameState.questions.length) {
            finishGame();
        } else {
            renderQuestion();
        }
    });

    async function finishGame() {
        document.getElementById('question-card').style.display = 'none';
        document.getElementById('result-screen').style.display = 'block';

        document.getElementById('res-score').textContent = gameState.score;
        document.getElementById('res-acertos').textContent = gameState.acertos;
        document.getElementById('res-erros').textContent = gameState.erros;
        document.getElementById('res-xp').textContent = '+' + gameState.xpGained + ' XP';

        const saveStatus = document.getElementById('save-status');
        let saveData;
        try {
            const saveRes = await fetch('../api/salvar-partida.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    jogo: gameState.jogo,
                    dificuldade: gameState.dificuldade,
                    pontuacao: gameState.score,
                    acertos: gameState.acertos,
                    erros: gameState.erros,
                    xp_ganho: gameState.xpGained
                })
            });
            saveData = await saveRes.json();
            if (!saveRes.ok || !saveData.success) {
                throw new Error(saveData.message || 'Não foi possível salvar a partida.');
            }
            saveStatus.textContent = 'Partida salva! Seu painel e histórico foram atualizados.';
            saveStatus.className = 'text-success';
        } catch (err) {
            console.error('Error saving completed game:', err);
            saveStatus.textContent = err instanceof Error
                ? err.message
                : 'Não foi possível salvar a partida. Tente novamente.';
            saveStatus.className = 'text-danger';
            return;
        }

        try {
            // Check Achievements
            const achRes = await fetch('../api/conquistas.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    jogo: gameState.jogo,
                    acertos: gameState.acertos,
                    erros: gameState.erros,
                    pontuacao: gameState.score,
                    sequencia_maxima: gameState.maxStreak
                })
            });
            const achData = await achRes.json();

            if (achData.novas_conquistas && achData.novas_conquistas.length > 0) {
                const box = document.getElementById('new-achievements-box');
                const list = document.getElementById('achievements-list');
                list.innerHTML = achData.novas_conquistas.map(a =>
                    `<div class="card p-2 bg-secondary flex items-center gap-md">
                        <span class="fs-3">${a.icone}</span>
                        <div>
                            <strong>${a.nome}</strong>
                            <div class="small text-secondary">${a.descricao}</div>
                        </div>
                     </div>`
                ).join('');
                box.style.display = 'block';
            }

            if (saveData.level_up && window.showToast) {
                window.showToast(`🎉 PARABÉNS! Você subiu para o Nível ${saveData.new_nivel} — ${saveData.novo_nivel_nome}!`, 'success');
            }
        } catch (err) {
            console.error('Error checking achievements:', err);
        }
    }

    function showLoading(show) {
        document.getElementById('game-loading').style.display = show ? 'block' : 'none';
    }

    function showError(msg) {
        showLoading(false);
        const container = document.getElementById('game-container');
        container.innerHTML = `
            <div class="card text-center p-5 border-danger">
                <h2 class="text-danger font-display">Ops! Ocorreu um problema</h2>
                <p class="text-secondary">${msg}</p>
                <a href="portugues.php" class="btn btn-accent mt-3">Tentar Novamente</a>
            </div>
        `;
    }
})();
