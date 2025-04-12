<?php

class ProfilController implements ControllerInterface
{
    private UtilisateurDAO $utilisateurDAO;
    private AbonneDAO $abonneDAO;
    private InscriptionValidator $validateur;

    public function __construct(){
        $this->utilisateurDAO = new UtilisateurDAO();
        $this->abonneDAO = new AbonneDAO();
        $this->validateur = new InscriptionValidator();
    }

    public function afficherVue(): void
    {
        try{
            $profil_id = $this->estunProfil($_GET['utilisateur'] ?? null);
            if ($profil_id) {
                $utilisateur = $this->utilisateurDAO->getProfilUtilisateurById($profil_id);
                $abonne = $this->abonneDAO->getNbrAbonnesById($profil_id) ?? 0;
                $abonnement = $this->abonneDAO->getNbrAbonnementsById($profil_id) ?? 0;
                $this->callbackModifierPseudo();
                $this->callbackModifierEmail();
                $this->callbackModifierBio();
                $this->callbackModifierMdp();
                require_once __DIR__ . '/../views/profil.php';
            }
            else{
                header('HTTP/1.0 404 Not Found');
                exit();
            }
        }
        catch (PDOException $e)
        {
            header('HTTP/1.0 404 Not Found');
            exit();
        }
    }

    public function estunProfil($pseudo):bool | int
    {
        $utilisateurs = $this->utilisateurDAO->getPseudos();

        if (in_array($pseudo, $utilisateurs)) {
            return $this->utilisateurDAO->getIdByPseudo($pseudo);
        }
        else{
            return false;
        }
    }

    public function estunEmail($email):bool | int
    {
        $emails = $this->utilisateurDAO->getEmails();

        if (in_array($email, $emails)) {
            return $this->utilisateurDAO->getIdByEmail($email);
        }
        else{
            return false;
        }
    }

    public function callbackModifierPseudo(): void
    {
        if (isset($_POST['modalPseudo'])) {
            $this->validateur->clearErreurs();
            $pseudo = trim(filter_input(INPUT_POST, 'modalPseudo', FILTER_SANITIZE_SPECIAL_CHARS));
            $this->validateur->validerPseudo($pseudo);

            if ($this->estunProfil($pseudo)){
                $_SESSION['erreurs']['pseudo'] = "Ce pseudo existe déjà.";
            }
            elseif(empty($this->validateur->getErreurs()['pseudo'])){
                try{
                    $this->utilisateurDAO->updatePseudoByPseudo($_SESSION['Pseudo'], $pseudo);
                    $_SESSION['Pseudo'] = $pseudo;
                    unset($_SESSION['erreurs']);
                    header("Location: ./?action=profil&utilisateur=$pseudo#paramContainer");
                    exit();
                }
                catch (Exception $e){
                    header("Location: ./?action=erreur");
                    exit();
                }
            }
            else{
                $_SESSION['erreurs'] = $this->validateur->getErreurs();
            }
        }
    }

    public function callbackModifierEmail(): void
    {
        if (isset($_POST['modalEmail'])){
            $this->validateur->clearErreurs();
            $email = trim(filter_input(INPUT_POST, 'modalEmail', FILTER_SANITIZE_EMAIL));
            $this->validateur->validerEmail($email);

            if ($this->estunEmail($email)){
                $_SESSION['erreurs']['email'] = "Cette adresse email existe déjà.";
            }
            elseif(empty($this->validateur->getErreurs()['email'])){
                try{
                    $this->utilisateurDAO->updateEmailByPseudo($_SESSION['Pseudo'], $email);
                    unset($_SESSION['erreurs']);
                    header("Location: ./?action=profil&utilisateur={$_SESSION['Pseudo']}#paramContainer");
                    exit();
                }
                catch (Exception $e){
                    header("Location: ./?action=erreur");
                    exit();
                }
            }
            else{
                $_SESSION['erreurs'] = $this->validateur->getErreurs();
            }	
        }
    }

    public function callbackModifierBio():void
    {
        if (isset($_POST['modalBio'])){
            // On récupère la bio en brut car on se protège des injections XSS avec htmlspecialchars() dans la vue
            $bio = trim($_POST['modalBio']);
            
            if (strlen($bio) > 256){
                $_SESSION['erreurs']['bio'] = "La bio ne doit pas dépasser 256 caractères.";
            }
            else{
                $this->utilisateurDAO->updateBioByPseudo($_SESSION['Pseudo'], $bio);
                unset($_SESSION['erreurs']);
                header("Location: ./?action=profil&utilisateur={$_SESSION['Pseudo']}#paramContainer");
                exit();
            }
        }
    }

    public function callbackModifierMdp():void
    {
        if (isset($_POST['ancienMdp'], $_POST['nouveauMdp'], $_POST['confirmationMdp'])){
            $this->validateur->clearErreurs();
            $ancienMdp = trim($_POST['ancienMdp']);
            $nouveauMdp = trim($_POST['nouveauMdp']);
            $confirmationMdp = trim($_POST['confirmationMdp']);
            
            $this->validateur->validerMdp($nouveauMdp);
            if (empty($ancienMdp)){
                $_SESSION['erreurs']['ancienMdp'] = "Veuillez entrer votre ancien mot de passe.";
            }
            elseif (!password_verify($ancienMdp, $this->utilisateurDAO->getMdpByPseudo($_SESSION['Pseudo']))){
                $_SESSION['erreurs']['ancienMdp'] = "Le mot de passe est incorrect.";
            }
            elseif (!empty($this->validateur->getErreurs()['mdp'])){
                $_SESSION['erreurs']['nouveauMdp'] = $this->validateur->getErreurs()['mdp'];
            }
            elseif(empty($confirmationMdp)){
                $_SESSION['erreurs']['confirmationMdp'] = "Veuillez entrer votre mot de passe de confirmation.";
            }
            elseif ($nouveauMdp !== $confirmationMdp){
                $_SESSION['erreurs']['confirmationMdp'] = "Les mots de passe ne correspondent pas.";
            }
            else{
                try{
                    $this->utilisateurDAO->updateMdpByPseudo($_SESSION['Pseudo'], $nouveauMdp);
                    // On déconnecte l'utilisateur après la modification de son mot de passe
                    session_unset();
                    session_destroy();
                    header("Location: ./?action=connexion");
                    exit();
                }
                catch (Exception $e){
                    header("Location: ./?action=erreur");
                    exit();
                }
            }
        }
    }
    

}
