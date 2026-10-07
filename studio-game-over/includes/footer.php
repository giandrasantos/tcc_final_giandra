<?php
/**
 * footer.php — Global Page Footer
 * MathPlay Solutions | Studio Game Over
 *
 * Closes <main>, renders the full footer, then closes <body> and <html>.
 * Always include this file at the very bottom of every page that uses header.php.
 */

$currentYear = date('Y');
?>

</main><!-- /#main-content — opened in header.php -->

<!-- ======================================================================== -->
<!-- SITE FOOTER                                                               -->
<!-- ======================================================================== -->
<footer class="site-footer" role="contentinfo">


    <!-- ------------------------------------------------------------------ -->
    <!-- Main footer content                                                 -->
    <!-- ------------------------------------------------------------------ -->
    <div class="footer-content">
        <div class="footer-container">

            <!-- ---------------------------------------------------------- -->
            <!-- Column 1 — Brand                                            -->
            <!-- ---------------------------------------------------------- -->
            <div class="footer-col footer-col--brand">
                <div class="footer-brand">
                    <div class="footer-brand-logo" aria-hidden="true">
                        <i data-lucide="gamepad-2"></i>
                    </div>
                    <div class="footer-brand-text">
                        <span class="footer-brand-name">Studio Game Over</span>
                        <span class="footer-brand-subtitle">MathPlay Solutions</span>
                    </div>
                </div>

                <p class="footer-tagline">
                    MathPlay Solutions — Transformando o aprendizado em aventura
                </p>

                <p class="footer-description">
                    Uma plataforma gamificada para alunos do ensino fundamental,
                    tornando matemática e português divertidos e desafiadores.
                </p>

                <!-- Social / info links -->
                <nav class="footer-social" aria-label="Redes sociais e contato">
                    <a href="#" class="footer-social-link" aria-label="Instagram" title="Instagram">
                        <i data-lucide="instagram" aria-hidden="true"></i>
                    </a>
                    <a href="#" class="footer-social-link" aria-label="YouTube" title="YouTube">
                        <i data-lucide="youtube" aria-hidden="true"></i>
                    </a>
                    <a href="#" class="footer-social-link" aria-label="E-mail de contato" title="E-mail">
                        <i data-lucide="mail" aria-hidden="true"></i>
                    </a>
                    <a href="#" class="footer-social-link" aria-label="LinkedIn" title="LinkedIn">
                        <i data-lucide="linkedin" aria-hidden="true"></i>
                    </a>
                </nav>
            </div><!-- /.footer-col--brand -->

            <!-- ---------------------------------------------------------- -->
            <!-- Column 2 — Sobre                                            -->
            <!-- ---------------------------------------------------------- -->
            <div class="footer-col">
                <h3 class="footer-col-title">
                    <i data-lucide="info" aria-hidden="true"></i>
                    Sobre
                </h3>
                <ul class="footer-links" role="list">
                    <li>
                        <a href="#" class="footer-link">
                            <i data-lucide="chevron-right" aria-hidden="true"></i>
                            Nossa Missão
                        </a>
                    </li>
                    <li>
                        <a href="#" class="footer-link">
                            <i data-lucide="chevron-right" aria-hidden="true"></i>
                            Equipe
                        </a>
                    </li>
                    <li>
                        <a href="#" class="footer-link">
                            <i data-lucide="chevron-right" aria-hidden="true"></i>
                            Metodologia
                        </a>
                    </li>
                    <li>
                        <a href="#" class="footer-link">
                            <i data-lucide="chevron-right" aria-hidden="true"></i>
                            Blog Educacional
                        </a>
                    </li>
                    <li>
                        <a href="#" class="footer-link">
                            <i data-lucide="chevron-right" aria-hidden="true"></i>
                            Política de Privacidade
                        </a>
                    </li>
                </ul>
            </div><!-- /.footer-col -->

            <!-- ---------------------------------------------------------- -->
            <!-- Column 3 — Plataforma                                       -->
            <!-- ---------------------------------------------------------- -->
            <div class="footer-col">
                <h3 class="footer-col-title">
                    <i data-lucide="layout-grid" aria-hidden="true"></i>
                    Plataforma
                </h3>
                <ul class="footer-links" role="list">
                    <li>
                        <a href="/studio-game-over/dashboard.php" class="footer-link">
                            <i data-lucide="chevron-right" aria-hidden="true"></i>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="/studio-game-over/jogos/matematica.php" class="footer-link">
                            <i data-lucide="chevron-right" aria-hidden="true"></i>
                            Jogos de Matemática
                        </a>
                    </li>
                    <li>
                        <a href="/studio-game-over/jogos/portugues.php" class="footer-link">
                            <i data-lucide="chevron-right" aria-hidden="true"></i>
                            Jogos de Português
                        </a>
                    </li>
                    <li>
                        <a href="/studio-game-over/ranking.php" class="footer-link">
                            <i data-lucide="chevron-right" aria-hidden="true"></i>
                            Ranking Global
                        </a>
                    </li>
                    <li>
                        <a href="/studio-game-over/historico.php" class="footer-link">
                            <i data-lucide="chevron-right" aria-hidden="true"></i>
                            Histórico de Partidas
                        </a>
                    </li>
                    <li>
                        <a href="/studio-game-over/perfil.php" class="footer-link">
                            <i data-lucide="chevron-right" aria-hidden="true"></i>
                            Meu Perfil
                        </a>
                    </li>
                </ul>
            </div><!-- /.footer-col -->

            <!-- ---------------------------------------------------------- -->
            <!-- Column 4 — Contato                                          -->
            <!-- ---------------------------------------------------------- -->
            <div class="footer-col">
                <h3 class="footer-col-title">
                    <i data-lucide="headphones" aria-hidden="true"></i>
                    Contato
                </h3>
                <ul class="footer-links footer-links--contact" role="list">
                    <li>
                        <a href="mailto:suporte@studiogameover.com.br" class="footer-link">
                            <i data-lucide="mail" aria-hidden="true"></i>
                            suporte@studiogameover.com.br
                        </a>
                    </li>
                    <li>
                        <a href="#" class="footer-link">
                            <i data-lucide="message-circle" aria-hidden="true"></i>
                            Chat de Suporte
                        </a>
                    </li>
                    <li>
                        <a href="#" class="footer-link">
                            <i data-lucide="book-marked" aria-hidden="true"></i>
                            Central de Ajuda
                        </a>
                    </li>
                    <li>
                        <a href="#" class="footer-link">
                            <i data-lucide="bug" aria-hidden="true"></i>
                            Reportar Problema
                        </a>
                    </li>
                </ul>

                <!-- Gamification badge -->
                <div class="footer-badge" role="img" aria-label="Aprenda jogando">
                    <i data-lucide="award" aria-hidden="true"></i>
                    <div>
                        <strong>Aprenda Jogando</strong>
                        <span>Gamificação educacional</span>
                    </div>
                </div>
            </div><!-- /.footer-col -->

        </div><!-- /.footer-container -->
    </div><!-- /.footer-content -->

    <!-- ------------------------------------------------------------------ -->
    <!-- Footer bottom bar — copyright                                       -->
    <!-- ------------------------------------------------------------------ -->
    <div class="footer-bottom">
        <div class="footer-bottom-inner">
            <p class="footer-copyright">
                &copy; <?= $currentYear ?> Studio Game Over. Todos os direitos reservados.
            </p>
            <p class="footer-made-with">
                Feito com
                <i data-lucide="heart" class="footer-heart" aria-label="amor" aria-hidden="true"></i>
                e muito código para a educação brasileira.
            </p>
        </div>
    </div><!-- /.footer-bottom -->

</footer><!-- /.site-footer -->

<!-- ======================================================================== -->
<!-- Reinitialise Lucide for any icons injected after DOMContentLoaded        -->
<!-- ======================================================================== -->
<script>
    (function waitForLucide() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        } else {
            // Lucide script may still be loading — retry after a short delay
            setTimeout(waitForLucide, 50);
        }
    }());
</script>

</body>
</html>
