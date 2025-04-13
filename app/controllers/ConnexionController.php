<?php
// TODO : Modifier la doc de vérifierUtilisateur

/**
 * ConnexionController - Gère l'authentification des utilisateurs
 *
 * Ce contrôleur gère le processus de connexion et déconnexion des utilisateurs.
 * Il implémente les interfaces ControllerInterface et AuthControllerInterface
 * pour assurer la cohérence des fonctionnalités d'authentification.
 */
class ConnexionController implements ControllerInterface, AuthControllerInterface
{
    /**
     * @var ConnexionValidator Instance du validateur de connexion
     */
    private ConnexionValidator $validateur;

    /**
     * @var array Tableau contenant les messages d'erreurs de validation
     */
    private array $erreurs;

    /**
     * @var Logger Instance du logger pour tracer les actions
     */
    private Logger $logger;

    /**
     * Constructeur du ConnexionController
     * Initialise le validateur et le tableau d'erreurs
     */
    public function __construct()
    {
        $this->validateur = new ConnexionValidator();
        $this->erreurs = [];
        $this->logger = new Logger();
    }

    /**
     * Affiche la vue de connexion
     *
     * Vérifie l'utilisateur et affiche le formulaire de connexion
     * avec les éventuelles erreurs de validation
     *
     * @throws PDOException En cas d'erreur de connexion à la base de données
     * @return void
     */
    public function afficherVue():void
    {
        try{
            $this->logger->info("Affichage de la page de connexion");
            $this->verifierUtilisateur();
            $erreurs = $this->erreurs;
            require_once __DIR__ . '/../views/connexion.php';
        } catch (PDOException $e) {
            $this->logger->error("Erreur PDO lors de l'affichage de la page de connexion: " . $e->getMessage());
            header('HTTP/1.0 404 Not Found');
            exit();
        }
    }

    /**
     * Vérifie les informations d'authentification de l'utilisateur
     *
     * Traite les données POST du formulaire de connexion,
     * valide l'identifiant (email ou pseudo) et le mot de passe,
     * et redirige vers l'accueil en cas de succès
     *
     * @return void
     */
    public function verifierUtilisateur():void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST"){
            $this->logger->info("Tentative de connexion");
            
            if (str_contains($_POST['identifiant'], '@')) {
                $identifiant = trim(filter_input(INPUT_POST, 'identifiant', FILTER_SANITIZE_EMAIL));
                $this->logger->debug("Connexion avec email: " . $identifiant);
            }
            else{
                $identifiant = trim(filter_input(INPUT_POST, 'identifiant', FILTER_SANITIZE_SPECIAL_CHARS));
                $this->logger->debug("Connexion avec pseudo: " . $identifiant);
            }
            $mdp = trim(filter_input(INPUT_POST, 'mdp', FILTER_SANITIZE_SPECIAL_CHARS));

            if ($this->validateur->valider($identifiant, $identifiant, $mdp) == 1) {
                $_SESSION['Pseudo'] = $this->validateur->getUtilisateurDAO()->getPseudoByEmail($identifiant);
                $this->logger->info("Connexion réussie avec email: " . $identifiant . " (Pseudo: " . $_SESSION['Pseudo'] . ")");
                header("Location: ./?action=accueil");
                exit();
            }
            elseif($this->validateur->valider($identifiant, $identifiant, $mdp) == 2){
                $_SESSION['Pseudo'] = $identifiant;
                $this->logger->info("Connexion réussie avec pseudo: " . $identifiant);
                header("Location: ./?action=accueil");
                exit();
            }
            else {
                $this->erreurs = $this->validateur->getErreurs();
                $this->logger->warning("Échec de connexion pour: " . $identifiant . " - Erreurs: " . implode(", ", $this->erreurs));
            }
        }
    }

    /**
     * Gère la déconnexion de l'utilisateur
     *
     * Détruit la session courante et redirige vers la page de connexion
     *
     * @return void
     */
    public function deconnexion():void
    {
        if (isset($_SESSION['Pseudo'])){
            $this->logger->info("Déconnexion de l'utilisateur: " . $_SESSION['Pseudo']);
            session_unset();
            session_destroy();
            header("Location: ./?action=connexion");
            exit();
        }
    }
}
