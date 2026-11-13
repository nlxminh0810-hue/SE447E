<?php include 'View/layout/header.php'; ?>

<div class="auth-page auth-page-register">
    <div class="auth-card auth-card-register">
        <section class="auth-form-panel">
            <div class="auth-form-shell">
                <h2>Create an account</h2>
                <p class="auth-subtitle">Let's get started!</p>

                <?php if (isset($error) && $error): ?>
                    <div class="auth-alert auth-alert-error"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if (isset($success) && $success): ?>
                    <div class="auth-alert auth-alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <form method="POST" action="?controller=auth&action=register" class="auth-form">
                    <div class="form-group form-group-modern">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Choose a username" required>
                    </div>

                    <div class="form-group form-group-modern">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>

                    <div class="form-group form-group-modern">
                        <label for="password">Password</label>
                        <div class="password-field">
                            <input type="password" id="password" name="password" placeholder="Create a password" required>
                            <button type="button" class="password-toggle" aria-label="Toggle password visibility" data-password-toggle>
                                <span>◔</span>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="auth-submit">Sign up</button>
                    <button type="button" class="auth-secondary-button">Continue with SDT</button>
                </form>

                <p class="auth-switch auth-switch-light">
                    Already have an account? <a href="?controller=auth&action=login">Log in</a>
                </p>
            </div>
        </section>

        <section class="auth-visual">
            <div class="auth-visual-copy">
                <span class="auth-eyebrow">ENGLISHAI</span>
                <h1>Start your journey</h1>
                <p>Đăng ký tài khoản ngay để bắt đầu lộ trình học tập và theo dõi tiến độ, làm bài test và học với AI.</p>
            </div>

            <div class="auth-illustration auth-illustration-register" aria-hidden="true">
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
