<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TAU Student Portal - Something Went Wrong</title>
    <link href="<?= base_url('assets/vendor/bootstrap/bootstrap.min.css') ?>" rel="stylesheet">
    <style {csp-style-nonce}>
        body{display:flex;align-items:center;justify-content:center;min-height:100vh;}
        .error-box{max-width:420px;padding:2rem;text-align:center;}
        .error-heading{color:#0F332A;font-family:Georgia,serif;}
    </style>
</head>
<body>
    <div class="box error-box">
        <h1 class="error-heading">We're sorry</h1>
        <p><?= esc($message) ?></p>
        <p><a href="<?= site_url('/') ?>">Return to login</a></p>
    </div>
</body>
</html>
