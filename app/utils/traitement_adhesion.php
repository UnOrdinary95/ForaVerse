<?php

require_once 'test_autoloader.php';
session_start();

if (!isset($_SESSION['Pseudo']) || !isset($_POST['action']) || !isset($_POST['communaute_id'])) {
    echo json_encode(['error' => 'Erreur: Données manquantes']);
    exit;
}

$utilisateur_dao = new UtilisateurDAO();
$role_dao = new RoleDAO();

$utilisateur = $utilisateur_dao->getIdByPseudo($_SESSION['Pseudo']);

if ($_POST['action'] == 'rejoindrecommu') {
    $role_dao->addUtilisateurRole($utilisateur, $_POST['communaute_id'], Role::MEMBRE);
}
elseif ($_POST['action'] == 'quittercommu') {
    $role_dao->deleteRole($utilisateur, $_POST['communaute_id']);
}
else {
    echo json_encode(['error' => 'Erreur: Action invalide']);
    exit;
}

echo json_encode([
    'nbMembres' => $role_dao->getNbrRolesByCommunaute($_POST['communaute_id'])
]);