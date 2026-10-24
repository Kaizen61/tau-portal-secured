<div class="modal fade" id="schoolYearModal" tabindex="-1" aria-labelledby="schoolYearModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="schoolYearModalTitle">Select School Year</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-grid gap-2">
                <?php foreach ($schoolYears as $schoolYear): ?>
                    <button type="button" class="btn btn-outline-university" data-school-year-option="<?= esc($schoolYear, 'attr') ?>">
                        <?= esc($schoolYear) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<script {csp-script-nonce}>
(() => {
    const input = document.querySelector('[data-school-year-input]');
    const display = document.querySelector('[data-school-year-display]');
    const modal = document.getElementById('schoolYearModal');

    modal.querySelectorAll('[data-school-year-option]').forEach((option) => {
        option.addEventListener('click', () => {
            input.value = option.dataset.schoolYearOption;
            display.textContent = option.dataset.schoolYearOption;
            bootstrap.Modal.getOrCreateInstance(modal).hide();
        });
    });
})();
</script>
