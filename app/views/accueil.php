<?php
/**
 * @var Utilisateur $utilisateur
 * @var array $communautes
 * @var array $erreurs
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="icon" href="../../public/images/favicon/favicon_foraverse.png"/>
    <link rel="stylesheet" href="../../public/styles/style.css?<?php echo time(); ?>">
</head>
<body>
    <?php include_once 'components/header.php'; ?>   

    <main class="flex">
        <?php include_once 'components/left_sidebar.php'; ?>

        <h1>Liste des communautés :</h1>
        <?php 
        foreach($communautes as $communaute){
            echo "<a style='text-decoration: none;'  href=\"./?action=communaute&nomCommu={$communaute->getNom()}\">
                    <h3>{$communaute->getNom()}</h3>
                </a>";
        } 
        ?>
    </main>
    <script src="../../public/scripts/creer_commu.js"></script>
</body>
</html>