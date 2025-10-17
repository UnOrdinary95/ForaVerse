<?php

require_once 'test_autoloader.php';
session_start();

if (!isset($_SESSION['Pseudo']) || !isset($_POST['action']) || !isset($_POST['communaute_id'])) {
    echo json_encode(['error' => 'Erreur: Données manquantes']);
    exit;
}

$utilisateur_dao = new UtilisateurDAO();
$role_dao = new RoleDAO();
$communaute_dao = new CommunauteDAO();
$adhesion_dao = new AdhesionDAO();

$utilisateur = $utilisateur_dao->getIdByPseudo($_SESSION['Pseudo']);

if ($_POST['action'] == 'rejoindrecommu') {
    // Communauté publique
    if ($communaute_dao->getCommunauteById($_POST['communaute_id'])->getVisibilite()){
    $role_dao->addUtilisateurRole($utilisateur, $_POST['communaute_id'], Role::MEMBRE);
    }
    else{
        // La communauté est privée, on ne peut pas rejoindre directement
        $adhesion_dao->addAdhesion($utilisateur, $_POST['communaute_id']);
    }
}
elseif ($_POST['action'] == 'quittercommu') {
    if (!$communaute_dao->getCommunauteById($_POST['communaute_id'])->getVisibilite()){
        $adhesion_dao->deleteAdhesion($utilisateur, $_POST['communaute_id']);
    }
    $role_dao->deleteRole($utilisateur, $_POST['communaute_id']);
}
else {
    echo json_encode(['error' => 'Erreur: Action invalide']);
    exit;
}

echo json_encode([
    'nbMembres' => $role_dao->getNbrRolesByCommunaute($_POST['communaute_id'])
]);