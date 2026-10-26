<?php
$inputId = (string) ($inputId ?? '');
$identifier = (string) ($identifier ?? '');
$isNumericIdentifier = preg_match('/^\d{10}$/D', $identifier) === 1;
?>
<input
    type="text"
    id="<?= esc($inputId, 'attr') ?>"
    class="form-control"
    value="<?= esc($isNumericIdentifier ? substr($identifier, 0, 4) : $identifier, 'attr') ?>"
    <?php if ($isNumericIdentifier): ?>data-numeric-identifier data-identifier-suffix="<?= esc(substr($identifier, 4), 'attr') ?>"<?php endif; ?>
    readonly
>
