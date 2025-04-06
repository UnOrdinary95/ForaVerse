<?php

class ProfilController implements ControllerInterface
{
    private UtilisateurDAO $utilisateurDAO;
    private AbonneDAO $abonneDAO;

    public function __construct(){
        $this->utilisateurDAO = new UtilisateurDAO();
        $this->abonneDAO = new AbonneDAO();
    }
    public function afficherVue(): void
    {
        try{
            $profil_id = $this->estunProfil();
            if ($profil_id) {
                $abonne_dao = new AbonneDAO();
                $utilisateur_dao = new UtilisateurDAO();
                $utilisateur = $this->utilisateurDAO->getProfilUtilisateurById($profil_id) ?? null;
                $abonne = $this->abonneDAO->getNbrAbonnesById($profil_id) ?? 0;
                $abonnement = $this->abonneDAO->getNbrAbonnementsById($profil_id) ?? 0;

                if (isset($abonne_dao , $utilisateur, $abonne, $abonnement)) {
                    require_once __DIR__ . '/../views/profil.php';
                }
            }
        }
        catch (PDOException $e)
        {
            require_once __DIR__ . '/../views/erreur.php';
            exit();
        }
    }

    public function estunProfil():bool | int
    {
        $profil = $_GET['utilisateur'];
        $utilisateurs = $this->utilisateurDAO->getPseudos();

        if (in_array($profil, $utilisateurs)) {
            return $this->utilisateurDAO->getIdByPseudo($profil);
        }
        else{
            header("HTTP/1.0 404 Not Found");
            echo "404 Not Found";
            return false;
        }
    }

}