<?php
$logger = new Logger();
$role_dao = new RoleDAO();
$utilisateur_dao = new UtilisateurDAO();
$session_user = $utilisateur_dao->getProfilUtilisateurById($utilisateur_dao->getIdByPseudo($_SESSION['Pseudo']));

if (isset($_POST['reseau'])) {
    $reseau = $_POST['reseau'];
    try{
        if ($reseau == 'communautes') {
            $roles = $role_dao->getRolesByUtilisateur($session_user->getId());
            $logger->info("Récupération des communautés de l'utilisateur : " . $session_user->getPseudo());
            $communautes_joined = [];
            foreach ($roles as $role) {
                $communautes_joined[] = $role->getCommunaute();
            }
        } elseif ($reseau == 'abonnements') {
            $abonnements_id = $session_user->getSystemeAbonnement()->getAbonnements();
            $abonnements = [];
            foreach ($abonnements_id as $id) {
                $abonnements[] = $utilisateur_dao->getProfilUtilisateurById($id);
            }
        }
        else{
            // Communauté par défaut
            $communautes_joined = $role_dao->getRolesByUtilisateur($session_user->getId());
        }
    }
    catch (PDOException $e)
    {
        $logger->warning("Erreur lors de la récupération des communautés ou abonnements : " . $e->getMessage());
        header('Location: ./?action=erreur');
        exit();
    }   
}
else{
    // Par défaut
    if(isset($_SESSION['Pseudo'])){
        $logger->info("Aucun réseau sélectionné, affichage des communautés par défaut");
        $roles = $role_dao->getRolesByUtilisateur($session_user->getId());
        $communautes_joined = [];
        foreach ($roles as $role) {
            $communautes_joined[] = $role->getCommunaute();
        }
    }
}
?>