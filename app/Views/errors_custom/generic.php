<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TAU Student Portal - Something Went Wrong</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="<?= base_url('assets/css/portal.css') ?>" rel="stylesheet">
    <style>body{display:flex;align-items:center;justify-content:center;min-height:100vh;} .box{max-width:420px;text-align:center;padding:2rem;}</style>
</head>
<body>
    <div class="box">
        <h1 style="font-family:Georgia,serif;color:var(--uni-green-dark);">We're sorry</h1>
        <p><?= esc($message) ?></p>
        <p><a href="<?= site_url('/') ?>">Return to login</a></p>
    </div>
</body>
</html>
