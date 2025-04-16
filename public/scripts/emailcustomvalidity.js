document.querySelector('form').addEventListener('submit', function(event) {
    const emailInput = this.email;

    emailInput.setCustomValidity('');

    if (!emailInput.validity.valid) {
        event.preventDefault();

        if (emailInput.validity.valueMissing) {
            emailInput.setCustomValidity('Veuillez remplir ce champ.');
        } else if (emailInput.validity.typeMismatch) {
            emailInput.setCustomValidity('Veuillez inclure un "@" dans l\'adresse électronique.');
        }

        emailInput.reportValidity();
    }
});