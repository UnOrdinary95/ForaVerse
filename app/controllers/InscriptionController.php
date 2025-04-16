<?php

/**
 * InscriptionController - Gère le processus d'inscription des utilisateurs
 *
 * Ce contrôleur gère l'inscription des nouveaux utilisateurs dans l'application.
 * Il implémente les interfaces ControllerInterface et AuthControllerInterface
 * pour assurer la cohérence des fonctionnalités d'inscription.
 */
class InscriptionController implements ControllerInterface, AuthControllerInterface
{
    /**
     * @var InscriptionValidator Instance du validateur d'inscription
     */
    private InscriptionValidator $validateur;
    /**
     * @var array Tableau contenant les messages d'erreurs de validation
     */
    private array $erreurs;
    /**
     * @var Logger Instance du logger pour tracer les actions
     */
    private Logger $logger;

    /**
     * Constructeur de InscriptionController
     * Initialise le validateur et le tableau d'erreurs
     */
    public function __construct()
    {
        $this->validateur = new InscriptionValidator();
        $this->erreurs = [];
        $this->logger = new Logger();
    }

    /**
     * Affiche la vue d'inscription
     *
     * Vérifie l'utilisateur et affiche le formulaire d'inscription
     * avec les éventuelles erreurs de validation
     *
     * @throws PDOException En cas d'erreur de connexion à la base de données
     * @return void
     */
    public function afficherVue():void
    {
        try{
            $this->logger->info("Affichage de la page d'inscription");
            $this->verifierUtilisateur();
            $erreurs = $this->erreurs;
            require_once __DIR__ . '/../views/inscription.php';
        } catch (PDOException $e) {
            $this->logger->error("Erreur PDO lors de l'affichage de la page d'inscription: " . $e->getMessage());
            header('HTTP/1.0 404 Not Found');
            exit();
        }
    }

    /**
     * Vérifie et traite l'inscription d'un nouvel utilisateur
     *
     * Traite les données POST du formulaire d'inscription,
     * valide le pseudo, l'email et le mot de passe,
     * et crée un nouvel utilisateur en cas de succès
     *
     * @return void
     */
    public function verifierUtilisateur(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST"){
            $this->logger->info("Tentative d'inscription d'un nouvel utilisateur");
            
            $pseudo = trim(filter_input(INPUT_POST, 'pseudo', FILTER_SANITIZE_SPECIAL_CHARS));
            $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
            $mdp = trim($_POST['mdp']);
            
            $this->logger->debug("Données d'inscription reçues - Pseudo: $pseudo, Email: $email");

            if ($this->validateur->valider($pseudo, $email, $mdp)){
                try{
                    $utilisateur_dao = new UtilisateurDAO();
                    $utilisateur_dao->addUtilisateur($pseudo, $email, $mdp);
                    $this->logger->info("Inscription réussie pour l'utilisateur: $pseudo ($email)");
                    header("Location: ./?action=connexion");
                    exit();
                } catch (PDOException $e) {
                    $this->logger->error("Erreur PDO lors de l'inscription de l'utilisateur: " . $e->getMessage());
                    header("Location: ./?action=erreur");
                    exit();
                }
            }
            else {
                $this->erreurs = $this->validateur->getErreurs();
                $this->logger->warning("Échec d'inscription pour: $pseudo - Erreurs: " . implode(", ", $this->erreurs));
            }
        }
    }
}
