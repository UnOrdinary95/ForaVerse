<?php

class CommunauteController implements ControllerInterface
{
    // private UtilisateurDAO $utilisateurDAO;
    private CommunauteDAO $communauteDAO;
    // private CommunauteValidator $validateur;
    private Logger $logger;

    public function __construct()
    {
        // $this->utilisateurDAO = new UtilisateurDAO();
        $this->communauteDAO = new CommunauteDAO();
        // $this->validateur = new CommunauteValidator();
        $this->logger = new Logger();
    }

    public function afficherVue(): void
    {
        try{
            $this->logger->info("Affichage de la page communauté pour: " . ($_GET['nomCommu'] ?? 'non spécifié'));
            $communaute_id = $this->estuneCommunaute($_GET['nomCommu']);
            if ($communaute_id){
                $communaute = $this->communauteDAO->getCommunauteById($communaute_id);
                $this->logger->info("Communauté trouvée: " . $_GET['nomCommu'] . " (ID: $communaute_id)");
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

    public function estuneCommunaute($nom):bool | int
    {
        $this->logger->debug("Vérification de l'existence de la communauté: $nom");
        $communautes = $this->communauteDAO->getNomsCommunautes();

        if (in_array($nom, $communautes)) {
            $id = $this->communauteDAO->getIdByNom($nom);
            $this->logger->debug("Communauté trouvée: $nom (ID: $id)");
            return $id;
        }
        else{
            $this->logger->debug("Communauté non trouvée: $nom");
            return false;
        }
    }
}