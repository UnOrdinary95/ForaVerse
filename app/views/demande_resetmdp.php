<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe</title>
    <link rel="icon" href="../../public/images/favicon/favicon_foraverse.png"/>
</head>

<body>
<form action="?action=demande_resetmdp" method="POST" novalidate>
    <h1><a href="./" style="text-decoration: none">⬅️</a>Réinitialiser votre mot de passe</h1>
    <p>Saississez votre adresse électronique et nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>
    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="submit" name="envoyer" value="Réinitialiser votre mot de passe">
</form>

<script>
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
</script>


</body>
</html>