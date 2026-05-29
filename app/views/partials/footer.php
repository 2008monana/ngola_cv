</main>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <h4><i class="fas fa-file-alt"></i> Ngola CV</h4>
                <p>Plataforma angolana para criação de currículos profissionais de alto impacto.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
            <div class="footer-col">
                <h4><i class="fas fa-link"></i> Links Rápidos</h4>
                <ul>
                    <li><a href="index.php?page=sobre"><i class="fas fa-info-circle"></i> Sobre</a></li>
                    <li><a href="index.php?page=templates"><i class="fas fa-layer-group"></i> Templates</a></li>
                    <li><a href="index.php?page=planos"><i class="fas fa-tags"></i> Planos</a></li>
                    <li><a href="index.php?page=faq"><i class="fas fa-question-circle"></i> FAQ</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4><i class="fas fa-gavel"></i> Legal</h4>
                <ul>
                    <li><a href="index.php?page=termos"><i class="fas fa-file-contract"></i> Termos de Uso</a></li>
                    <li><a href="index.php?page=privacidade"><i class="fas fa-shield-alt"></i> Privacidade</a></li>
                    <li><a href="index.php?page=cookies"><i class="fas fa-cookie-bite"></i> Cookies</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4><i class="fas fa-envelope"></i> Contato</h4>
                <ul>
                    <li><i class="fas fa-envelope"></i> suporte@ngolacv.ao</li>
                    <li><i class="fas fa-phone-alt"></i> +244 923 456 789</li>
                    <li><i class="fas fa-map-marker-alt"></i> Luanda, Angola</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Ngola CV. Todos os direitos reservados.</p>
            <p class="made-in-angola"><i class="fas fa-heart"></i> Feito em Angola</p>
        </div>
    </div>
</footer>

<!-- JavaScript Principal -->
<script src="assets/js/main.js"></script>
<script src="assets/js/mobile-menu.js"></script>

<?php if (isset($page_specific_js)): ?>
    <script src="assets/js/<?php echo $page_specific_js; ?>"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/mobile-menu.js"></script>
<?php endif; ?>

</body>
</html>