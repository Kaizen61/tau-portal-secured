document.querySelectorAll('select[data-submit-on-change]').forEach((select) => {
    select.addEventListener('change', () => {
        if (select.form) {
            select.form.requestSubmit();
        }
    });
});

document.querySelectorAll('input[data-numeric-identifier]').forEach((input) => {
    input.value += input.dataset.identifierSuffix;
});
