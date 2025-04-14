<?php

require_once 'test_autoloader.php';
session_start();

if (!isset($_SESSION['Pseudo']) || !isset($_POST['action']) || !isset($_POST['utilisateur'])) {
    echo json_encode(['error' => 'Erreur: Données manquantes']);
    exit;
}

$abonne_dao = new AbonneDAO();
$utilisateur_dao = new UtilisateurDAO();

$utilisateurCible = $utilisateur_dao->getIdByPseudo($_POST['utilisateur']);
$utilisateurActuel = $utilisateur_dao->getIdByPseudo($_SESSION['Pseudo']);

if ($_POST['action'] == 'ajouterabonnement') {
    $abonne_dao->addAbonnement($utilisateurActuel, $utilisateurCible);
}
elseif ($_POST['action'] == 'supprimerabonnement') {
    $abonne_dao->deleteAbonnement($utilisateurActuel, $utilisateurCible);
}
else {
    echo json_encode(['error' => 'Erreur: Action invalide']);
    exit;
}

echo json_encode([
    'nbAbonnes' => $abonne_dao->getNbrAbonnesById($utilisateurCible)
]);