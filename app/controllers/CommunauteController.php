<?php

class CommunauteController implements ControllerInterface
{
    private CommunauteDAO $communauteDAO;
    private Logger $logger;
    private RoleDAO $roleDAO;
    private UtilisateurDAO $utilisateurDAO;

    public function __construct()
    {
        $this->communauteDAO = new CommunauteDAO();
        $this->logger = new Logger();
        $this->roleDAO = new RoleDAO();
        $this->utilisateurDAO = new UtilisateurDAO();
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
                if (isset($_SESSION['Pseudo'])){
                    $role = $this->roleDAO->getRole($this->utilisateurDAO->getIdByPseudo($_SESSION['Pseudo']), $communaute_id);
                    if ($role){
                        $this->logger->info("Rôle de l'utilisateur dans la communauté: " . $role->getRole());
                    }
                    else{
                        $this->logger->info("L'utilisateur n'a pas de rôle dans la communauté.");
                    }
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
}
