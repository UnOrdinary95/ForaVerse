<?php

class CommunauteController implements ControllerInterface
{
    private CommunauteDAO $communauteDAO;
    private Logger $logger;
    private RoleDAO $roleDAO;
    private UtilisateurDAO $utilisateurDAO;
    private CommunauteValidator $validateur;
    private AdhesionDAO $adhesionDAO;
    private AvertissementDAO $avertissementDAO;
    private BannissementDAO $bannissementDAO;
    private DiscussionDAO $discussionDAO;
    private FavorisDAO $favorisDAO;
    private array $erreurs;

    public function __construct()
    {
        $this->communauteDAO = new CommunauteDAO();
        $this->logger = new Logger();
        $this->roleDAO = new RoleDAO();
        $this->utilisateurDAO = new UtilisateurDAO();
        $this->adhesionDAO = new AdhesionDAO();
        $this->avertissementDAO = new AvertissementDAO();
        $this->bannissementDAO = new BannissementDAO();
        $this->discussionDAO = new DiscussionDAO();
        $this->validateur = new CommunauteValidator();
        $this->favorisDAO = new FavorisDAO();
        $this->erreurs = [];
    }

    public function afficherVue(): void
    {
        try{
            $this->logger->info("Affichage de la page communauté pour: " . ($_GET['nomCommu'] ?? 'non spécifié'));
            $communaute_id = $this->communauteDAO->existeCommunaute($_GET['nomCommu']);
            if ($communaute_id){
                $communaute = $this->communauteDAO->getCommunauteById($communaute_id);
                $this->logger->info("Communauté trouvée: " . $_GET['nomCommu'] . " (ID: $communaute_id)");
                $nbr_membres = $this->roleDAO->getNbrRolesByCommunaute($communaute_id);
                $this->logger->info("Nombre de membres dans la communauté: " . $nbr_membres);
                
                // Initialisation des variables nécessaires à la vue
                $liste_refus = [];
                $liste_attentes = [];
                $erreurs_rename = [];
                $erreurs_addmod = [];
                $liste_warns = [];
                $liste_bans = [];

                // Callback pour le tri
                if (isset($_POST['tri'])){
                    $tri = $_POST['tri'];
                    if ($tri == 'recents'){
                        $discussions = $this->discussionDAO->getDiscussionsByCommunauteOrderByDatesDESC($communaute_id);
                    }
                    elseif ($tri == 'anciens'){
                        $discussions = $this->discussionDAO->getDiscussionsByCommunauteOrderByDatesASC($communaute_id);
                    }
                    elseif ($tri == 'upvotes'){
                        $discussions = $this->discussionDAO->getDiscussionsByCommunauteOrderByUpvotes($communaute_id);
                    }
                    elseif ($tri == 'downvotes'){
                        $discussions = $this->discussionDAO->getDiscussionsByCommunauteOrderByDownvotes($communaute_id);
                    }
                    else{
                        // Récent par défaut
                        $discussions = $this->discussionDAO->getDiscussionsByCommunauteOrderByDatesDESC($communaute_id);
                    }
                }
                else{
                    // Récent par défaut
                    $discussions = $this->discussionDAO->getDiscussionsByCommunauteOrderByDatesDESC($communaute_id);
                } 

                // Callback pour la recherche de discussions
                if  (isset($_POST['discussion_mot_cle'])){
                    $mot_cle = $_POST['discussion_mot_cle'];
                    $discussions = $this->discussionDAO->getDiscussionsByCommunauteAndMotcle($communaute_id, $mot_cle);
                }
                else{
                    // Récent par défaut
                    $discussions = $this->discussionDAO->getDiscussionsByCommunauteOrderByDatesDESC($communaute_id);
                }

                $this->logger->info("Récupération des discussions pour la communauté: " . $_GET['nomCommu']);
                
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
                        $this->logger->info("Rôle de l'utilisateur dans la communauté: " . $role->getRole());
                        $this->callbackCreerDiscussion();
                        $erreurs = $this->erreurs;
                        $this->callbackGererFavoris();
                        $this->callbackDeleteDiscussion();
                        if($role->peutModerer()){
                            $this->logger->info(message: "L'utilisateur peut modérer la communauté.");
                            foreach($this->adhesionDAO->getRefusByCommunaute($communaute_id) as $refus){
                                $liste_refus[$this->utilisateurDAO->getPseudoById($refus->getIdUtilisateur())] = $refus->getIdUtilisateur();
                            }

                            foreach($this->adhesionDAO->getAttentesByCommunaute($communaute_id) as $attente){
                                $liste_attentes[$this->utilisateurDAO->getPseudoById($attente->getIdUtilisateur())] = [
                                    'id' => $attente->getIdUtilisateur(),
                                    'datedemande' => $attente->getDateDemande()
                                ];
                            }

                            $liste_warns = $this->avertissementDAO->getAllAvertissementsByIdCommunaute($communaute_id);
                            $liste_bans = $this->bannissementDAO->getAllBannissementsByIdCommunaute($communaute_id);
                            $this->logger->info("Récupération des avertissements et bannissements pour la communauté: " . $_GET['nomCommu']);

                            $this->callbackAnnulerAdhesion();
                            $this->callbackGestionAdhesion();
                            $this->logger->info("Initialisation des tableaux de gestion des adhésions en attente et refusées.");

                            $this->callbackAnnulerAvertissement();
                            $this->callbackAnnulerBannissement();
                            $this->logger->info("Initialisation des tableaux de gestion des avertissements et bannissements.");
                            $this->callbackEpinglerDiscussion();
                        }
                        if($role->peutGererCommunaute()){
                            $this->logger->info("L'utilisateur est le propriétaire de la communauté.");
                            $this->callbackSuppressionCommunaute();
                            $this->callbackRename();
                            $erreurs_rename = $this->erreurs;
                            $this->callbackDeleteMod();
                            $this->callbackAddMod();
                            $erreurs_addmod = $this->erreurs;
                        }
                    }
                    else{
                        $adhesion = $this->adhesionDAO->getAdhesionById($this->utilisateurDAO->getIdByPseudo($_SESSION['Pseudo']), $communaute_id);
                        $this->logger->info("L'utilisateur n'a pas de rôle dans la communauté.");
                    }
                }
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

                require_once __DIR__ . '/../views/communaute.php';
            }
            else{
                $this->logger->warning("Tentative d'accès à une communauté inexistante: " . $_GET['nomCommu']);
                header('HTTP/1.0 404 Not Found');
                exit();
            }
        }
        catch (PDOException $e)
        {
            $this->logger->error("Erreur PDO lors de l'affichage de la communauté: " . $e->getMessage());
            header('Location: ./?action=erreur');
            exit();
        }
    }

    public function callbackSuppressionCommunaute(): void
    {
        if (isset($_POST['supprimerCommunaute'])) {
            try{
                $this->logger->info("Suppression de la communauté: " . $_GET['nomCommu']);
                $this->communauteDAO->deleteCommunaute($this->communauteDAO->getIdByNom($_GET['nomCommu']));
                header('Location: ./?action=accueil');
                exit();
            }
            catch (PDOException $e)
            {
                $this->logger->error("Erreur lors de la suppression de la communauté: " . $e->getMessage());
                header('Location: ./?action=erreur');
                exit();
            }
        }
    }

    public function callbackRename(): void
    {
        if (isset($_POST['nomCommu'])) {
            $this->logger->info("Renommage de la communauté: " . $_GET['nomCommu']);
            $this->validateur->clearErreurs();
            $nom = trim(filter_input(INPUT_POST, 'nomCommu', FILTER_SANITIZE_SPECIAL_CHARS));

            $this->logger->debug("Nouveau nom de la communauté: $nom");
            
            if ($this->validateur->validerNomCommu($nom)){
                try{
                    $this->communauteDAO->updateNom($this->communauteDAO->getIdByNom($_GET['nomCommu']), $nom);
                    $this->logger->info("Communauté renommée avec succès: " . $_GET['nomCommu'] . " -> $nom");
                    header('Location: ./?action=communaute&nomCommu=' . urlencode($nom). "#ParamCommuContainer");
                    exit();
                }
                catch (PDOException $e)
                {
                    $this->logger->error("Erreur lors du renommage de la communauté: " . $e->getMessage());
                    header('Location: ./?action=erreur');
                    exit();
                }
            }
            else{
                $this->erreurs = $this->validateur->getErreurs();
                $this->logger->warning("Validation échouée pour le renommage de la communauté: " . implode(", ", $this->erreurs));
            }
        }
    }

    public function callbackAddMod(): void
    {
        if (isset($_POST['pseudoMembre'])){
            $this->logger->info("Ajout d'un modérateur: " . $_POST['pseudoMembre']);
            $pseudo = trim(filter_input(INPUT_POST, 'pseudoMembre', FILTER_SANITIZE_SPECIAL_CHARS));

            $this->logger->debug("Pseudo du modérateur: $pseudo");

            try{
                $user_id = $this->utilisateurDAO->getIdByPseudo($pseudo);
                $commu_id = $this->communauteDAO->getIdByNom($_GET['nomCommu']);
                if(empty($pseudo)){
                    $this->logger->warning("Aucun pseudo fourni pour le modérateur.");
                    $this->erreurs['pseudoMembre'] = "Veuillez entrer un pseudo.";
                }
                elseif (!$this->utilisateurDAO->existeUtilisateur($pseudo)){
                    $this->logger->warning("Le membre n'existe pas: " . $pseudo);
                    $this->erreurs['pseudoMembre'] = "Ce membre n'est pas dans la communauté."; // Mesure de sécurité
                }
                elseif (!$this->roleDAO->getRole($user_id, $commu_id)){
                    $this->logger->warning("Le membre n'est pas dans la communauté: " . $pseudo);
                    $this->erreurs['pseudoMembre'] = "Ce membre n'est pas dans la communauté.";
                }               
                elseif($this->roleDAO->getRole($user_id, $commu_id)->estProprietaire()){
                    $this->logger->warning("Le membre est propriétaire (Action impossible) => " . $pseudo);
                    $this->erreurs['pseudoMembre'] = "Vous êtes le propriétaire...";
                }  
                elseif ($this->roleDAO->getRole($user_id, $commu_id)->estModerateur()){
                    $this->logger->warning("Le membre est déjà modérateur: " . $pseudo);
                    $this->erreurs['pseudoMembre'] = "Ce membre est déjà modérateur.";
                }
                else{
                    $this->roleDAO->setModerateur($user_id, $this->communauteDAO->getIdByNom($_GET['nomCommu']));
                    $this->logger->info("Modérateur ajouté avec succès: " . $pseudo);
                    header('Location: ./?action=communaute&nomCommu=' . urlencode($_GET['nomCommu']). '#modalMod');
                    exit();
                }
            }
            catch (PDOException $e)
            {
                $this->logger->error("Erreur lors de l'ajout du modérateur: " . $e->getMessage());
                header('Location: ./?action=erreur');
                exit();
            }
        }
    }

    public function callbackDeleteMod(): void
    {
        if (isset($_POST['deleteMod'])){
            $this->logger->info("Suppression du modérateur: " . $_POST['deleteMod']);
            try{
                $this->roleDAO->deleteModerateur($_POST['deleteMod'], $this->communauteDAO->getIdByNom($_GET['nomCommu']));
                $this->logger->info("Modérateur supprimé avec succès: " . $_POST['deleteMod']);
                header('Location: ./?action=communaute&nomCommu=' . urlencode($_GET['nomCommu']). "#modalMod");
                exit();
            }
            catch (PDOException $e)
            {
                $this->logger->error("Erreur lors de la suppression du modérateur: " . $e->getMessage());
                header('Location: ./?action=erreur');
                exit();
            }
        }
    }

    public function callbackAnnulerAdhesion():void
    {
        if(isset($_POST['annulerAdhesion']) && isset($_POST['idAdhesion'])){
            $this->logger->info("Annulation de l'adhésion: " . $_POST['annulerAdhesion']);
            try{
                $this->adhesionDAO->deleteAdhesion($_POST['idAdhesion'], $this->communauteDAO->getIdByNom($_GET['nomCommu']));
                $this->logger->info("Adhésion annulée avec succès: " . $_POST['annulerAdhesion']);
                header('Location: ./?action=communaute&nomCommu=' . urlencode($_GET['nomCommu']). "#gestionAdhesionContainer");
                exit();
            }
            catch (PDOException $e)
            {
                $this->logger->error("Erreur lors de l'annulation de l'adhésion: " . $e->getMessage());
                header('Location: ./?action=erreur');
                exit();
            }    
        }
    }

    public function callbackGestionAdhesion():void
    {
        if(isset($_POST['validerAdhesion']) || isset($_POST['refuserAdhesion']) && isset($_POST['idAdhesion'])){
            $this->logger->info("Gestion de l'adhésion: " . $_POST['validerAdhesion'] ?? $_POST['refuserAdhesion']);
            try{
                if(isset($_POST['validerAdhesion'])){
                    $this->adhesionDAO->acceptAdhesion($_POST['idAdhesion'], $this->communauteDAO->getIdByNom($_GET['nomCommu']));
                    $this->logger->info("Adhésion validée avec succès: " . $_POST['idAdhesion']);
                }
                elseif(isset($_POST['refuserAdhesion'])){
                    $this->adhesionDAO->rejectAdhesion($_POST['idAdhesion'], $this->communauteDAO->getIdByNom($_GET['nomCommu']));
                    $this->logger->info("Adhésion refusée avec succès: " . $_POST['idAdhesion']);
                }
                header('Location: ./?action=communaute&nomCommu=' . urlencode($_GET['nomCommu']). "#gestionAdhesionContainer");
                exit();
            }
            catch (PDOException $e)
            {
                $this->logger->error("Erreur lors de la gestion de l'adhésion: " . $e->getMessage());
                header('Location: ./?action=erreur');
                exit();
            }    
        }            
    }

    public function callbackAnnulerAvertissement(): void
    {
        if (isset($_POST['annulerAvertissement'])){
            try{
                $this->avertissementDAO->deleteAvertissementById($_POST['idAvertissement']);
                $this->logger->info("Avertissement annulé avec succès: " . $_POST['idAvertissement']);
                header('Location: ./?action=communaute&nomCommu=' . urlencode($_GET['nomCommu']). "#listeAvertiContainer");
                exit();
            }
            catch(PDOException $e)
            {
                $this->logger->error("Erreur lors de l'annulation de l'avertissement: " . $e->getMessage());
                header('Location: ./?action=erreur');
                exit();
            }
        }
    }

    public function callbackAnnulerBannissement(): void
    {
        if (isset($_POST['annulerBannissement'])){
            try{
                $this->bannissementDAO->deleteBannissementById($_POST['idBannissement']);
                $this->logger->info("Bannissement annulé avec succès: " . $_POST['idBannissement']);
                header('Location: ./?action=communaute&nomCommu=' . urlencode($_GET['nomCommu']). "#listeBanniContainer");
                exit();
            }
            catch(PDOException $e)
            {
                $this->logger->error("Erreur lors de l'annulation du bannissement: " . $e->getMessage());
                header('Location: ./?action=erreur');
                exit();
            }
        }
    }

    public function callbackCreerDiscussion(): void
    {
        if (isset($_POST['titreDiscussion'], $_POST['contenuDiscussion'])){
            $this->logger->info("Création d'une nouvelle discussion dans la communauté: " . $_GET['nomCommu']);
            $titre = $_POST['titreDiscussion'];
            $contenu = $_POST['contenuDiscussion'];

            if(empty($titre)){
                $this->logger->warning("Titre de la discussion vide.");
                $this->erreurs['titreDiscussion'] = "Veuillez entrer un titre.";
            }
            elseif(strlen($titre) > 50){
                $this->logger->warning("Titre de la discussion trop long.");
                $this->erreurs['titreDiscussion'] = "Le titre doit faire moins de 50 caractères.";
            }
            elseif(empty($contenu)){
                $this->logger->warning("Contenu de la discussion vide.");
                $this->erreurs['contenuDiscussion'] = "Veuillez entrer un contenu.";
            }
            elseif(strlen($contenu) > 2048){
                $this->logger->warning("Contenu de la discussion trop long.");   
            }
            else{
                try{
                    $idPublication = $this->discussionDAO->addDiscussion(
                        $this->communauteDAO->getIdByNom($_GET['nomCommu']),
                        $this->utilisateurDAO->getIdByPseudo($_SESSION['Pseudo']),
                        $titre,
                        $contenu
                    );
                    header("Location: ./?action=publication&nomCommu=" . urlencode($_GET['nomCommu']).'&idPublication='. $idPublication);
                    $this->logger->info("Discussion créée avec succès: " . $titre);
                    $this->logger->info("ID de la publication: " . $idPublication);
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
    }

    public function callbackEpinglerDiscussion(): void
    {
        if (isset($_POST['epinglerPublication'])){
            $idPublication = (int)$_POST['idPublication']; // Conversion en entier
            $this->logger->info("Épinglage de la discussion: " . $idPublication);
            try{
                if($this->discussionDAO->estEpingle($idPublication)){
                    $this->discussionDAO->updateEpingle($idPublication, false);
                    $this->logger->info("Discussion désépinglée avec succès: " . $idPublication);
                }
                else{
                    $this->discussionDAO->updateEpingle($idPublication, true);
                    $this->logger->info("Discussion épinglée avec succès: " . $idPublication);
                }
                header('Location: ./?action=communaute&nomCommu=' . urlencode($_GET['nomCommu']));
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
                if($this->favorisDAO->estFavoris($idPublication, $this->utilisateurDAO->getIdByPseudo($_SESSION['Pseudo']))){
                    $this->favorisDAO->deleteFavoris($idPublication, $this->utilisateurDAO->getIdByPseudo($_SESSION['Pseudo']));
                    $this->logger->info("Publication retirée des favoris: " . $idPublication);
                }
                else{
                    $this->favorisDAO->addFavoris($idPublication, $this->utilisateurDAO->getIdByPseudo($_SESSION['Pseudo']), 'discussion');
                    $this->logger->info("Publication ajoutée aux favoris: " . $idPublication);
                }
                header('Location: ./?action=communaute&nomCommu=' . urlencode($_GET['nomCommu']));
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

    public function callbackDeleteDiscussion(): void
    {
        if (isset($_POST['deleteDiscussion'])){
            $this->logger->info("Suppression de la discussion: " . $_POST['deleteDiscussion']);
            try{
                $this->discussionDAO->deleteDiscussion($_POST['deleteDiscussion']);
                $this->logger->info("Discussion supprimée avec succès: " . $_POST['deleteDiscussion']);
                header('Location: ./?action=communaute&nomCommu=' . urlencode($_GET['nomCommu']));
                exit();
            }
            catch (PDOException $e)
            {
                $this->logger->error("Erreur lors de la suppression de la discussion: " . $e->getMessage());
                header('Location: ./?action=erreur');
                exit();
            }
        }
    }

}