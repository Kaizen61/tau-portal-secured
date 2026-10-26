        </main>
        <footer class="site-footer text-center">
            <small>&copy; <?= date('Y') ?> Tarlac Agricultural University &middot; CET &middot; Secured (CI4) Demo Portal</small>
        </footer>
    </div>
</div>
<?php if (session()->get('user_id')): ?>
<div class="modal fade" id="logoutConfirmationModal" tabindex="-1" aria-labelledby="logoutConfirmationModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutConfirmationModalTitle">Confirm Logout</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">Are you sure you want to log out?</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="<?= site_url('logout') ?>">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-university">Log out</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
<script src="<?= base_url('assets/js/portal.js') ?>"></script>
<script src="<?= base_url('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
