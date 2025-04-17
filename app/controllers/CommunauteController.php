<?php

class CommunauteController implements ControllerInterface
{
    private CommunauteDAO $communauteDAO;
    private Logger $logger;
    private RoleDAO $roleDAO;
    private UtilisateurDAO $utilisateurDAO;
    private CommunauteValidator $validateur;
    private AdhesionDAO $adhesionDAO;
    private array $erreurs;

    public function __construct()
    {
        $this->communauteDAO = new CommunauteDAO();
        $this->logger = new Logger();
        $this->roleDAO = new RoleDAO();
        $this->utilisateurDAO = new UtilisateurDAO();
        $this->adhesionDAO = new AdhesionDAO();
        $this->validateur = new CommunauteValidator();
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
                
                if (isset($_SESSION['Pseudo'])){
                    $role = $this->roleDAO->getRole($this->utilisateurDAO->getIdByPseudo($_SESSION['Pseudo']), $communaute_id);
                    if ($role){
                        $this->logger->info("Rôle de l'utilisateur dans la communauté: " . $role->getRole());
                        if($role->peutModerer()){
                            $this->logger->info("L'utilisateur peut modérer la communauté.");
                            foreach($this->adhesionDAO->getRefusByCommunaute($communaute_id) as $refus){
                                $liste_refus[$this->utilisateurDAO->getPseudoById($refus->getIdUtilisateur())] = $refus->getIdUtilisateur();
                            }

                            foreach($this->adhesionDAO->getAttentesByCommunaute($communaute_id) as $attente){
                                $liste_attentes[$this->utilisateurDAO->getPseudoById($attente->getIdUtilisateur())] = [
                                    'id' => $attente->getIdUtilisateur(),
                                    'datedemande' => $attente->getDateDemande()
                                ];
                            }

                            $this->callbackAnnulerAdhesion();
                            $this->callbackGestionAdhesion();
                            $this->logger->info("Initialisation des tableaux de gestion des adhésions en attente et refusées.");
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
                        'pp' => $this->utilisateurDAO->getPpById($unMembre->getUtilisateurId())
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
}