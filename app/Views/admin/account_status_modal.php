<div class="modal fade" id="accountStatusModal" tabindex="-1" aria-labelledby="accountStatusModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="accountStatusForm" method="POST" action="">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="accountStatusModalTitle">Confirm account status change</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="accountStatusModalMessage" class="mb-0"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="accountStatusConfirm" class="btn">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script {csp-script-nonce}>
(() => {
    const modal = document.getElementById('accountStatusModal');
    const form = document.getElementById('accountStatusForm');
    const message = document.getElementById('accountStatusModalMessage');
    const confirmButton = document.getElementById('accountStatusConfirm');

    modal.addEventListener('show.bs.modal', (event) => {
        const trigger = event.relatedTarget;
        const nextStatus = trigger.dataset.nextStatus;
        const isDeactivation = nextStatus === 'deactivate';
        const accountName = trigger.dataset.userName;

        form.action = trigger.dataset.action;
        message.textContent = isDeactivation
            ? `Deactivate ${accountName}? They will no longer be able to sign in.`
            : `Reactivate ${accountName}? They will be able to sign in again.`;
        confirmButton.textContent = isDeactivation ? 'Deactivate' : 'Reactivate';
        confirmButton.className = `btn ${isDeactivation ? 'btn-danger' : 'btn-success'}`;
    });
})();
</script>
