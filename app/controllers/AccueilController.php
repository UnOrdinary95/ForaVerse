<?php
/**
 * AccueilController - Gère l'affichage de la page d'accueil
 *
 * Ce contrôleur est responsable de l'affichage de la vue principale
 * de la page d'accueil de l'application. Il implémente l'interface
 * ControllerInterface pour assurer un comportement cohérent des contrôleurs.
 */
class AccueilController implements ControllerInterface
{
    /**
     * Constructeur de AccueilController
     */
    private UtilisateurDAO $utilisateurDAO;
    private CommunauteDAO $communauteDAO;
    private RoleDAO $roleDAO;
    private CommunauteValidator $validateur;
    private array $erreurs;
    private Logger $logger;

    public function __construct()
    {
        $this->utilisateurDAO = new UtilisateurDAO();
        $this->communauteDAO = new CommunauteDAO();
        $this->roleDAO = new RoleDAO();
        $this->validateur = new CommunauteValidator();
        $this->erreurs = [];
        $this->logger = new Logger();
    }

    /**
     * Affiche la vue de la page d'accueil
     *
     * Tente d'inclure et d'afficher le template de la page d'accueil.
     * En cas d'exception PDO, affiche la vue d'erreur.
     *
     * @throws PDOException En cas d'erreur de connexion à la base de données
     * @return void
     */
    public function afficherVue():void
    {
        try{
            $this->logger->info("Affichage de la page d'accueil");
            if (isset($_SESSION['Pseudo'])){
                $utilisateur = $this->utilisateurDAO->getProfilUtilisateurById($this->utilisateurDAO->getIdByPseudo($_SESSION['Pseudo'])) ?? null;
                $this->logger->info("Utilisateur connecté: " . $_SESSION['Pseudo']);
                $this->callbackCreerCommu();
                $erreurs = $this->erreurs;
            } else {
                $this->logger->info("Accès visiteur (non connecté)");
            }
            $communautes = $this->communauteDAO->getCommunautes();
            require_once __DIR__ . '/../views/accueil.php';
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