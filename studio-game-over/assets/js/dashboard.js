/**
 * MathPlay Solutions — Dashboard JavaScript
 * Studio Game Over
 */

document.addEventListener('DOMContentLoaded', () => {
    // Motivational phrases rotation
    const phrases = [
        "🌟 \"Aprender é a única coisa de que a mente nunca se cansa!\"",
        "💪 \"Errar faz parte do aprendizado. O importante é continuar tentando!\"",
        "🔥 \"Cada questão respondida é um passo rumo à vitória!\"",
        "🚀 \"Você é capaz de grandes conquistas. Mantenha o foco!\"",
        "🧠 \"Seu cérebro é como um músculo: quanto mais você treina, mais forte ele fica!\"",
        "🏆 \"Studio Game Over aprova seu empenho nos estudos!\""
    ];

    const phraseElement = document.getElementById('motivational-quote-text');
    if (phraseElement) {
        const randomPhrase = phrases[Math.floor(Math.random() * phrases.length)];
        phraseElement.textContent = randomPhrase;
    }

    // Number counting animation for stats
    document.querySelectorAll('.stat-count').forEach(el => {
        const target = parseInt(el.getAttribute('data-target') || el.textContent, 10);
        if (isNaN(target) || target <= 0) return;

        let current = 0;
        const step = Math.max(1, Math.ceil(target / 30));
        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            el.textContent = current;
        }, 30);
    });

    // XP Progress Fill Animation
    const xpFill = document.getElementById('dashboard-xp-fill');
    if (xpFill) {
        const targetWidth = xpFill.getAttribute('data-width') || '0%';
        setTimeout(() => {
            xpFill.style.width = targetWidth;
        }, 200);
    }
});
