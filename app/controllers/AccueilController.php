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

    public function __construct()
    {
        $this->utilisateurDAO = new UtilisateurDAO();
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
            if (isset($_SESSION['Pseudo'])){
                $utilisateur_dao = new UtilisateurDAO();
                $utilisateur = $this->utilisateurDAO->getProfilUtilisateurById($this->utilisateurDAO->getIdByPseudo($_SESSION['Pseudo'])) ?? null;
            }
            require_once __DIR__ . '/../views/accueil.php';
        } catch (PDOException $e) {
            require_once __DIR__ . '/../views/erreur.php';
        }
    }
}