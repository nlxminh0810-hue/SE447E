<?php include 'View/layout/header.php'; ?>

<div class="auth-page auth-page-login">
    <div class="auth-card auth-card-login">
        <section class="auth-visual">
            <div class="auth-visual-copy">
                <span class="auth-eyebrow">ENGLISHAI</span>
                <h1>Welcome Back!</h1>
                <p>Học tiếng Anh, luyện phản xạ và chat với AI trong một không gian hiện đại.</p>
            </div>

            <div class="auth-illustration auth-illustration-login" aria-hidden="true">
                <div class="illust-phone">
                    <div class="illust-screen"></div>
                    <div class="illust-play"></div>
                    <div class="illust-chip chip-top"></div>
                    <div class="illust-chip chip-bottom"></div>
                </div>
                <div class="illust-person person-top"></div>
                <div class="illust-person person-left"></div>
                <div class="illust-person person-right"></div>
                <div class="illust-note note-left"></div>
                <div class="illust-note note-right"></div>
                <div class="illust-plant plant-left"></div>
                <div class="illust-plant plant-right"></div>
            </div>
        </section>

        <section class="auth-form-panel">
            <div class="auth-form-shell">
                <h2>Login</h2>
                <p class="auth-subtitle">Đăng nhập để tiếp tục học với ENGLISHAI.</p>

                <?php if (isset($error) && $error): ?>
                    <div class="auth-alert auth-alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST" action="?controller=auth&action=login" class="auth-form">
                    <div class="form-group form-group-modern">
                        <label for="login">Username</label>
                        <input type="text" id="login" name="login" placeholder="Enter your username" required>
                    </div>

                    <div class="form-group form-group-modern">
                        <label for="password">Password</label>
                        <div class="password-field">
                            <input type="password" id="password" name="password" placeholder="Enter your password" required>
                            <button type="button" class="password-toggle" aria-label="Toggle password visibility" data-password-toggle>
                                <span>◔</span>
                            </button>
                        </div>
                    </div>

                    <div class="auth-actions auth-actions-login">
                        <label class="auth-remember">
                            <input type="checkbox" name="remember_me">
                            <span>Keep me logged in</span>
                        </label>
                        <a href="#" class="auth-forgot">Forgot Password?</a>
                    </div>

                    <button type="submit" class="auth-submit">Login</button>
                </form>

                <p class="auth-switch">
                    Don't have an account yet? <a href="?controller=auth&action=register">Sign Up</a>
                </p>
            </div>
        </section>
    </div>
</div>

<script>
document.querySelectorAll('[data-password-toggle]').forEach(function(button) {
    button.addEventListener('click', function() {
        const input = document.getElementById('password');
        if (!input) return;
        input.type = input.type === 'password' ? 'text' : 'password';
    });
});
</script>

<?php include 'View/layout/footer.php'; ?>
