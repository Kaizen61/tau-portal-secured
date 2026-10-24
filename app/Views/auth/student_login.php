<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Sign In - TAU Student Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700;900&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/css/portal.css') ?>" rel="stylesheet">
</head>
<body>
<div class="login-shell">
    <div class="login-side">
        <div class="seal-wrap">
            <svg width="96" height="96" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="47" fill="var(--uni-green-dark)" stroke="var(--uni-amber)" stroke-width="2"/>
                <circle cx="50" cy="50" r="39" fill="none" stroke="var(--uni-amber)" stroke-width="0.8" stroke-dasharray="1.6 2.2"/>
                <text x="50" y="49" text-anchor="middle" font-family="Merriweather, Georgia, serif" font-weight="700" font-size="19" fill="var(--uni-cream)">TAU</text>
                <text x="50" y="64" text-anchor="middle" font-family="Source Sans 3, sans-serif" font-size="5.5" letter-spacing="1.5" fill="var(--uni-amber-soft)">PORTAL</text>
            </svg>
        </div>
        <h1>Tarlac Agricultural University</h1>
        <p>Student Portal for grades, class records, and academic advising.</p>
    </div>

    <div class="login-form-side">
        <div class="login-form-card">
            <div class="login-eyebrow">Student Sign In</div>
            <h2 class="mb-4" style="color:var(--uni-green-dark);">Access your grades</h2>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2"><?= esc($error) ?></div>
            <?php endif; ?>

            <form action="<?= site_url('login/student') ?>?debug=1" method="POST">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Student Number</label>
                    <input type="text" name="student_number" class="form-control" placeholder="Enter your 10-digit student number" maxlength="10">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <button type="submit" class="btn btn-university w-100 py-2">Log In</button>
            </form>
            <p class="text-center small text-muted mt-3">
                Faculty or Admin? <a href="<?= site_url('staff-login') ?>">Log in here</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>