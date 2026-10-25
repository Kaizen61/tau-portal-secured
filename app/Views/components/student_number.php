<?php $studentNumber = (string) ($studentNumber ?? ''); ?>
<?php if (preg_match('/^\d{10}$/D', $studentNumber) === 1): ?>
    <span class="text-nowrap"><span><?= esc(substr($studentNumber, 0, 4)) ?></span><span><?= esc(substr($studentNumber, 4)) ?></span></span>
<?php else: ?>
    <?= esc($studentNumber) ?>
<?php endif; ?>
