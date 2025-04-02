<?php

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
     * Constructeur du ConnexionController
     * Initialise le validateur et le tableau d'erreurs
     */
    public function __construct()
    {
        $this->validateur = new ConnexionValidator();
        $this->erreurs = [];
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
            $this->verifierUtilisateur();
            $erreurs = $this->erreurs;
            require_once __DIR__ . '/../views/connexion.php';
        } catch (PDOException $e) {
            require_once __DIR__ . '/../views/erreur.php';
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
            if (str_contains($_POST['identifiant'], '@')) {
                $identifiant = trim(filter_input(INPUT_POST, 'identifiant', FILTER_SANITIZE_EMAIL));
            }
            else{
                $identifiant = trim(filter_input(INPUT_POST, 'identifiant', FILTER_SANITIZE_SPECIAL_CHARS));
            }
            $mdp = trim(filter_input(INPUT_POST, 'mdp', FILTER_SANITIZE_SPECIAL_CHARS));

            if ($this->validateur->valider($identifiant, $identifiant, $mdp)){
                if($this->validateur->validerIdentifiant($identifiant) == 1){
                    $_SESSION['UserID'] = $this->validateur->getUtilisateurDAO()->getIdByEmail($identifiant);
                }
                else{
                    $_SESSION['UserID'] = $this->validateur->getUtilisateurDAO()->getIdByPseudo($identifiant);
                }
                header("Location: ./?action=accueil");
                exit();
            }
            else {
                $this->erreurs = $this->validateur->getErreurs();
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
        if (isset($_SESSION['UserID'])){
            session_unset();
            session_destroy();
            header("Location: ./?action=connexion");
            exit();
        }
    }
}
