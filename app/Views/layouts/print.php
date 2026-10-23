<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($printTitle ?? 'Print') ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700;900&family=Source+Sans+3:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/css/print.css') ?>" rel="stylesheet">
</head>
<body>
    <div class="print-toolbar no-print">
        <button type="button" onclick="window.print()">Print</button>
        <button type="button" class="secondary" onclick="window.close()">Close</button>
    </div>

    <div class="print-sheet">
        <header class="print-letterhead">
            <svg width="64" height="64" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <circle cx="50" cy="50" r="47" fill="#0F332A" stroke="#A6883C" stroke-width="2"/>
                <circle cx="50" cy="50" r="39" fill="none" stroke="#D9C48F" stroke-width="0.8" stroke-dasharray="1.6 2.2"/>
                <text x="50" y="49" text-anchor="middle" font-family="Merriweather, Georgia, serif" font-weight="700" font-size="19" fill="#FAF9F7">TAU</text>
                <text x="50" y="64" text-anchor="middle" font-family="Source Sans 3, sans-serif" font-size="5.5" letter-spacing="1.5" fill="#D9C48F">PORTAL</text>
            </svg>
            <div>
                <div class="univ">Tarlac Agricultural University</div>
                <div class="campus">Camiling, Tarlac &middot; College of Information Technology</div>
                <div class="doc-title"><?= esc($documentTitle ?? '') ?></div>
            </div>
        </header>

        <?= $this->renderSection('content') ?>

        <footer class="print-footer">
            Printed <?= esc($printedAt ?? date('F j, Y g:i A')) ?> &middot; TAU Student Portal &middot; For official records use
        </footer>
    </div>

    <?php if (!empty($autoPrint)): ?>
    <script>window.addEventListener('load', function () { window.print(); });</script>
    <?php endif; ?>
</body>
</html>
