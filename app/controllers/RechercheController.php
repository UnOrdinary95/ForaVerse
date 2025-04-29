<?php

class RechercheController implements ControllerInterface
{
    private UtilisateurDAO $utilisateurDAO;
    private CommunauteDAO $communauteDAO;
    private DiscussionDAO $discussionDAO;
    private RoleDAO $roleDAO;
    private CommunauteValidator $validateur;
    private array $erreurs;
    private Logger $logger;

    public function __construct()
    {
        $this->utilisateurDAO = new UtilisateurDAO();
        $this->communauteDAO = new CommunauteDAO();
        $this->discussionDAO = new DiscussionDAO();
        $this->roleDAO = new RoleDAO();
        $this->validateur = new CommunauteValidator();
        $this->erreurs = [];
        $this->logger = new Logger();
    }

    
    public function afficherVue():void
    {
        try{
            $this->logger->info("Affichage de la page d'accueil");
            if (isset($_SESSION['Pseudo'])){
                $session_user = $this->utilisateurDAO->getProfilUtilisateurById($this->utilisateurDAO->getIdByPseudo($_SESSION['Pseudo']));
                $this->logger->info("Utilisateur connecté: " . $_SESSION['Pseudo']);
                $this->callbackCreerCommu();
                $erreurs = $this->erreurs;
                include_once __DIR__ . '/../utils/left_sidebar_callback.php';
            } else {
                $this->logger->info("Accès visiteur (non connecté)");
            }

            $utilisateurs = [];
            $communautes = [];
            $discussions = [];

            if (isset($_POST['recherche_mot_cle'])){
                $this->logger->info("Recherche effectuée avec le mot clé: " . $_POST['recherche_mot_cle']);
                $motCle = trim(filter_input(INPUT_POST, 'recherche_mot_cle', FILTER_SANITIZE_SPECIAL_CHARS));
                $utilisateurs = $this->utilisateurDAO->getUtilisateursByMotcle($motCle);
                $communautes = $this->communauteDAO->getCommunautesByMotcle($motCle);
                $discussions = $this->discussionDAO->getDiscussionsByMotcle($motCle);
            }
            
            require_once __DIR__ . '/../views/recherche.php';
        } catch (PDOException $e) {
            $this->logger->error("Erreur PDO lors de l'affichage de la page d'accueil: " . $e->getMessage());
            header('HTTP/1.0 404 Not Found');
            exit();
        }
    }

    public function callbackCreerCommu():void
    {
        if (isset($_POST['nomCommu']) && isset($_POST['descriptionCommu']) && isset($_POST['visibilite'])) {
            $this->logger->info("Tentative de création de communauté");
            $this->validateur->clearErreurs();
            $nom = trim(filter_input(INPUT_POST, 'nomCommu', FILTER_SANITIZE_SPECIAL_CHARS));
            $description = trim($_POST['descriptionCommu']) == "" ? null : trim($_POST['descriptionCommu']);
            $visibilite = $_POST['visibilite'] === "publique" ? true : false;
            
            $this->logger->debug("Données reçues - Nom: $nom, Visibilité: " . ($visibilite ? "publique" : "privée"));

            if ($this->validateur->valider($nom, $description)) {
                try{
                    $this->communauteDAO->addCommunaute($nom, $description, $visibilite);
                    $this->logger->info("Communauté créée avec succès: $nom");
                    $this->roleDAO->addUtilisateurRole($this->utilisateurDAO->getIdByPseudo($_SESSION['Pseudo']), $this->communauteDAO->getIdByNom($nom), Role::PROPRIETAIRE);
                    $this->logger->info("Rôle de propriétaire attribué à l'utilisateur: " . $_SESSION['Pseudo']);
                    header("Location: ./?action=communaute&nomCommu=". urlencode($nom));
                    exit();
                } catch (PDOException $e) {
                    $this->logger->error("Erreur lors de la création de la communauté: " . $e->getMessage());
                    header("Location: ./?action=erreur");
                    exit();
                }
            }
            else{
                $this->erreurs = $this->validateur->getErreurs();
                $this->logger->warning("Validation échouée pour la création de communauté: " . implode(", ", $this->erreurs));
            }    
        }
    }
}