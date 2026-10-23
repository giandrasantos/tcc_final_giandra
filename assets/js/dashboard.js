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

    // XP Progress Fill Animation
    const xpFill = document.getElementById('xpBarFill');
    if (xpFill) {
        const progress = Number(xpFill.dataset.target);
        const targetWidth = `${Math.min(100, Math.max(0, Number.isFinite(progress) ? progress : 0))}%`;
        setTimeout(() => {
            xpFill.style.width = targetWidth;
        }, 200);
    }
});
