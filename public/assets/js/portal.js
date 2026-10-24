document.querySelectorAll('select[data-submit-on-change]').forEach((select) => {
    select.addEventListener('change', () => {
        if (select.form) {
            select.form.requestSubmit();
        }
    });
});
