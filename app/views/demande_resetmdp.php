<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe</title>
    <link rel="icon" href="../../public/images/favicon/favicon_foraverse.png"/>
    <link rel="stylesheet" href="../../public/styles/style.css?<?php echo time(); ?>">
</head>

<body class="flex justify-center items-center h92">
    <main class="card main_auth">
        <form action="?action=demande_resetmdp" method="POST" novalidate>
            <div class="margin3">
                <h1>Réinitialiser votre mot de passe</h1>
                <p>Saississez votre adresse électronique et nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>
            </div>
            
            <input type="email" name="email" placeholder="Email" required><br><br>
            <input type="submit" name="envoyer" value="Réinitialiser votre mot de passe">
        </form>
        <script src="../../public/scripts/emailcustomvalidity.js"></script>
    </main>
</body>
</html>