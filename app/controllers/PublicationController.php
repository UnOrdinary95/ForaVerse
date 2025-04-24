<?php

class PublicationController implements ControllerInterface
{
    private Logger $logger;
    private CommunauteDAO $communauteDAO;
    private DiscussionDAO $discussionDAO;
    private RoleDAO $roleDAO;
    private UtilisateurDAO $utilisateurDAO;
    private BannissementDAO $bannissementDAO;
    private FavorisDAO $favorisDAO;

    public function __construct()
    {
        $this->logger = new Logger();
        $this->communauteDAO = new CommunauteDAO();
        $this->discussionDAO = new DiscussionDAO();
        $this->roleDAO = new RoleDAO();
        $this->utilisateurDAO = new UtilisateurDAO();
        $this->bannissementDAO = new BannissementDAO();
        $this->favorisDAO = new FavorisDAO();
    }

    public function afficherVue(): void
    {
        try{
            $this->logger->info("Affichage de la vue de publication");
            $communaute_id = $this->communauteDAO->existeCommunaute($_GET['nomCommu']);
            if (!$communaute_id) {
                $this->logger->warning("Tentative d'accès à une communauté inexistante: " . $_GET['nomCommu']);
                header('HTTP/1.0 404 Not Found');
                exit();
            }
            
            $publication = $this->discussionDAO->getDiscussionById($_GET['idPublication']);
            if ($publication){
                // TODO : Permissions
            }
            else{
                $this->logger->warning("Tentative d'accès à une publication inexistante: " . $_GET['idPublication']);
                header('HTTP/1.0 404 Not Found');
                exit();
            }

            if (isset($_SESSION['Pseudo'])){
                $role = $this->roleDAO->getRole($this->utilisateurDAO->getIdByPseudo($_SESSION['Pseudo']), $communaute_id);
                if ($role){
                    $this->logger->info("Utilisateur avec le rôle " . $role->getRole() . " accède à la communauté " . $_GET['nomCommu']);
                    $this->callbackGererFavoris();

                    if($role->peutModerer()){
                        $this->logger->info(message: "L'utilisateur peut modérer la publication.");
                        $this->callbackEpinglerDiscussion();
                    }
                }
                else{
                    $this->logger->info("Utilisateur sans rôle accède à la communauté " . $_GET['nomCommu']);
                }
            }

            

            $communaute = $this->communauteDAO->getCommunauteById($communaute_id);
                $nbr_membres = $this->roleDAO->getNbrRolesByCommunaute($communaute_id);
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
                require_once __DIR__ . '/../views/publication.php';
        }
        catch(PDOException $e){
            $this->logger->error("Erreur lors de l'affichage de la vue de publication: " . $e->getMessage());
            header('Location: ./?action=erreur');
            exit();
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
                header('Location: ./?action=publication&nomCommu='.urlencode(string: $_GET['nomCommu']).'&idPublication='.urlencode($idPublication));
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
                    $this->favorisDAO->addFavoris($idPublication, $this->utilisateurDAO->getIdByPseudo($_SESSION['Pseudo']));
                    $this->logger->info("Publication ajoutée aux favoris: " . $idPublication);
                }
                header('Location: ./?action=publication&nomCommu='.urlencode(string: $_GET['nomCommu']).'&idPublication='.urlencode($idPublication));
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


}