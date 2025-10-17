<?php

class CommentaireController extends PublicationController implements ControllerInterface
{
    
    public function __construct()
    {
        parent::__construct();
    }

    public function afficherVue(): void
    {
        try{
            $this->logger->info("Affichage de la vue de publication");
            $communaute_id = $this->communauteDAO->existeCommunaute($_GET['nomCommu']);
            if (!$communaute_id) {
                $this->logger->warning("Tentative d'accès à une communauté inexistante: " . $_GET['nomCommu']);
                header('HTTP/1.0 404 Not Found');
                exit();
            }

            $commentaire = $this->commentaireDAO->getCommentaireById($_GET['idPublication']); // Commentaire
            if ($commentaire){
                $publication = $this->discussionDAO->getDiscussionById($commentaire->getIdDiscussion());
                if (isset($_SESSION['Pseudo'])){
                    include_once __DIR__ . '/../utils/left_sidebar_callback.php';
                    $session_user = $this->utilisateurDAO->getProfilUtilisateurById($this->utilisateurDAO->getIdByPseudo($_SESSION['Pseudo']));
                    $role = $this->roleDAO->getRole($this->utilisateurDAO->getIdByPseudo($_SESSION['Pseudo']), $communaute_id);
                    if ($role){
                        if ($role->estBanni()){
                            $this->logger->info("L'utilisateur est banni de la communauté.");
                            header('Location: ./?action=erreur');
                            exit();
                        }
                        $this->logger->info("Utilisateur avec le rôle " . $role->getRole() . " accède à la communauté " . $_GET['nomCommu']);
                        $this->callbackGererFavoris();
    
                        if($role->peutModerer()){
                            $this->logger->info(message: "L'utilisateur peut modérer la publication.");
                            $this->callbackEpinglerDiscussion();
                        }
                        
                        $this->callbackDeleteCommentaire();
                        $this->callbackCreerCommentaire();
                    }
                    else{
                        $this->logger->info("Utilisateur sans rôle accède à la communauté " . $_GET['nomCommu']);
                    }
                }

                // Par défaut, on affiche les commentaires récents
                $commentaires = $this->commentaireDAO->getCommentairesByCommunauteAndDiscussionOrderByDatesDESC($communaute_id, $_GET['idPublication']);

                // Callback pour le tri
                if (isset($_POST['tri'])){
                    $tri = $_POST['tri'];
                    if ($tri == 'recents'){
                        $commentaires = $this->commentaireDAO->getCommentairesByCommunauteAndDiscussionOrderByDatesDESC($communaute_id, $_GET['idPublication']);
                    }
                    elseif ($tri == 'anciens'){
                        $commentaires = $this->commentaireDAO->getCommentairesByCommunauteAndDiscussionOrderByDatesASC($communaute_id, $_GET['idPublication']);
                    }
                    elseif ($tri == 'upvotes'){
                        $commentaires = $this->commentaireDAO->getCommentairesByCommunauteAndDiscussionOrderByUpvotes($communaute_id, $_GET['idPublication']);
                    }
                    elseif ($tri == 'downvotes'){
                        $commentaires = $this->commentaireDAO->getCommentairesByCommunauteAndDiscussionOrderByDownvotes($communaute_id, $_GET['idPublication']);
                    }
                }
                
                // Callback pour la recherche de commentaire
                if  (isset($_POST['commentaire_mot_cle'])){
                    $mot_cle = $_POST['commentaire_mot_cle'];
                    $commentaires = $this->commentaireDAO->getCommentairesByCommunauteAndDiscussionAndMotcle($communaute_id, $_GET['idPublication'], $mot_cle);
                }
                    
                $communaute = $this->communauteDAO->getCommunauteById($communaute_id);
                $nbr_membres = $this->roleDAO->getNbrRolesByCommunaute($communaute_id);
                $proprio = [
                    'pseudo' => $this->utilisateurDAO->getPseudoById($this->roleDAO->getProprioByCommunaute($communaute_id)->getUtilisateurId()),
                    'pp' => $this->utilisateurDAO->getPpById($this->roleDAO->getProprioByCommunaute($communaute_id)->getUtilisateurId())
                ];

                $mods = [];
                foreach($this->roleDAO->getModsByCommunaute($communaute_id) as $moderateur){
                    $mods[] = [
                        'pseudo' => $this->utilisateurDAO->getPseudoById($moderateur->getUtilisateurId()),
                        'pp' => $this->utilisateurDAO->getPpById($moderateur->getUtilisateurId())
                    ];
                }

                $membres = [];
                foreach($this->roleDAO->getMembresByCommunaute($communaute_id) as $unMembre){
                    $membres[] = [
                        'pseudo' => $this->utilisateurDAO->getPseudoById($unMembre->getUtilisateurId()),
                        'pp' => $this->utilisateurDAO->getPpById($unMembre->getUtilisateurId()),
                        'admin' => $this->utilisateurDAO->getAdminById($unMembre->getUtilisateurId()),
                        'banglobal' => $this->bannissementDAO->getBannissementGlobalByIdUtilisateur($unMembre->getUtilisateurId()) !== null ? true : false
                    ];
                }
                require_once __DIR__ . '/../views/commentaire.php'; // Commentaire
            }
            else{
                $this->logger->warning("Tentative d'accès à une publication inexistante: " . $_GET['idPublication']);
                header('HTTP/1.0 404 Not Found');
                exit();
            }
        }
        catch(PDOException $e){
            $this->logger->error("Erreur lors de l'affichage de la vue de publication: " . $e->getMessage());
            header('Location: ./?action=erreur');
            exit();
        }
    }

    public function callbackEpinglerDiscussion(): void
    {
        if (isset($_POST['epinglerPublication'])){
            $idPublication = (int)$_POST['idPublication']; // Conversion en entier
            $this->logger->info("Épinglage de la discussion: " . $idPublication);
            try{
                if($idPublication == $_GET['idPublication']){
                    if($this->discussionDAO->estEpingle($idPublication)){
                        $this->discussionDAO->updateEpingle($idPublication, false);
                        $this->logger->info("Discussion désépinglée avec succès: " . $idPublication);
                    }
                    else{
                        $this->discussionDAO->updateEpingle($idPublication, true);
                        $this->logger->info("Discussion épinglée avec succès: " . $idPublication);
                    }
                }
                else{
                    if($this->commentaireDAO->estEpingle($idPublication)){
                        $this->commentaireDAO->updateEpingle($idPublication, false);
                        $this->logger->info("Commentaire désépinglée avec succès: " . $idPublication);
                    }
                    else{
                        $this->commentaireDAO->updateEpingle($idPublication, true);
                        $this->logger->info("Commentaire épinglée avec succès: " . $idPublication);
                    }
                }
                header('Location: ./?action=commentaire&nomCommu='.urlencode($_GET['nomCommu']).'&idPublication='.urlencode($_GET['idPublication']));
                exit();
            }
            catch (PDOException $e)
            {
                $this->logger->error("Erreur lors de l'épinglage de la discussion: " . $e->getMessage());
                header('Location: ./?action=erreur');
                exit();
            }
        }
    }

    public function callbackGererFavoris(): void
    {
        if (isset($_POST['Favoris'])){
            $idPublication = (int)$_POST['idPublication'];
            $this->logger->info("Gestion des favoris pour la publication: " . $idPublication);
            try{
                if($idPublication == $_GET['idPublication']){
                    $this->logger->info("Gestion des favoris pour la discussion: " . $idPublication);
                    if($this->favorisDAO->estFavoris($idPublication, $this->utilisateurDAO->getIdByPseudo($_SESSION['Pseudo']))){
                        $this->favorisDAO->deleteFavoris($idPublication, $this->utilisateurDAO->getIdByPseudo($_SESSION['Pseudo']));
                        $this->logger->info("Publication retirée des favoris: " . $idPublication);
                    }
                    else{
                        $this->favorisDAO->addFavoris($idPublication, $this->utilisateurDAO->getIdByPseudo($_SESSION['Pseudo']), 'discussion');
                        $this->logger->info("Publication ajoutée aux favoris: " . $idPublication);
                    }
                }
                else{
                    $this->logger->info("Gestion des favoris pour le commentaire: " . $idPublication);
                    if($this->favorisDAO->estFavoris($idPublication, $this->utilisateurDAO->getIdByPseudo($_SESSION['Pseudo']))){
                        $this->favorisDAO->deleteFavoris($idPublication, $this->utilisateurDAO->getIdByPseudo($_SESSION['Pseudo']));
                        $this->logger->info("Commentaire retiré des favoris: " . $idPublication);
                    }
                    else{
                        $this->favorisDAO->addFavoris($idPublication, $this->utilisateurDAO->getIdByPseudo($_SESSION['Pseudo']), 'commentaire');
                        $this->logger->info("Commentaire ajouté aux favoris: " . $idPublication);
                    }
                }
                header('Location: ./?action=commentaire&nomCommu='.urlencode($_GET['nomCommu']).'&idPublication='.urlencode($_GET['idPublication']));
                exit();
            }
            catch (PDOException $e)
            {
                $this->logger->error("Erreur lors de la gestion des favoris: " . $e->getMessage());
                header('Location: ./?action=erreur');
                exit();
            }
        }
    }

    public function callbackCreerCommentaire(): void
    {
        if (isset($_POST['CreerCommentaire'])){
            $this->logger->info("Création d'un nouveau commentaire dans la discussion: " . $_GET['idPublication']);
            $contenu = $_POST['contenuCommentaire'];

            if(strlen($contenu) > 2048){
                $contenu = substr($contenu, 0, 2048);
                $this->logger->info("Contenu du commentaire trop long et tronqué.");
            }
            
            try{
                $this->commentaireDAO->addCommentaire(
                    $_GET['idPublication'],
                    $this->communauteDAO->getIdByNom($_GET['nomCommu']),
                    $this->utilisateurDAO->getIdByPseudo($_SESSION['Pseudo']),
                    $contenu
                );
                header('Location: ./?action=commentaire&nomCommu='.urlencode(string: $_GET['nomCommu']).'&idPublication='.urlencode($_GET['idPublication']));
                exit();
            }
            catch (PDOException $e)
            {
                $this->logger->error("Erreur lors de la création de la discussion: " . $e->getMessage());
                header('Location: ./?action=erreur');
                exit();
            }
        }
    }

    public function callbackDeleteCommentaire(): void
    {
        if (isset($_POST['deleteCommentaire'])){
            $this->logger->info("Suppression d'un commentaire: " . $_POST['deleteCommentaire']);
            try{
                $this->commentaireDAO->deleteCommentaire($_POST['deleteCommentaire']);
                header('Location: ./?action=commentaire&nomCommu='.urlencode($_GET['nomCommu']).'&idPublication='.urlencode($_GET['idPublication']));
                exit();
            }
            catch (PDOException $e)
            {
                $this->logger->error("Erreur lors de la suppression du commentaire: " . $e->getMessage());
                header('Location: ./?action=erreur');
                exit();
            }
        }
    }
}