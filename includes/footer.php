    </main>
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5><i class="bi bi-mortarboard-fill"></i> <?= APP_NAME ?></h5>
                    <p class="text-muted">Empowering students through accessible education funding opportunities in Kenya.</p>
                </div>
                <div class="col-md-2 mb-3">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?= APP_URL ?>scholarships.php" class="text-muted text-decoration-none">Scholarships</a></li>
                        <li><a href="<?= APP_URL ?>about.php" class="text-muted text-decoration-none">About Us</a></li>
                        <li><a href="<?= APP_URL ?>contact.php" class="text-muted text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-3">
                    <h6>For Students</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?= APP_URL ?>register.php" class="text-muted text-decoration-none">Register</a></li>
                        <li><a href="<?= APP_URL ?>login.php" class="text-muted text-decoration-none">Login</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h6>Connect</h6>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-muted"><i class="bi bi-facebook fs-4"></i></a>
                        <a href="#" class="text-muted"><i class="bi bi-twitter fs-4"></i></a>
                        <a href="#" class="text-muted"><i class="bi bi-linkedin fs-4"></i></a>
                    </div>
                </div>
            </div>
            <hr class="border-secondary">
            <div class="text-center text-muted">
                <small>&copy; <?= date('Y') ?> <?= APP_NAME ?>. All Rights Reserved.</small>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="<?= APP_URL ?>assets/js/main.js"></script>
</body>
</html>