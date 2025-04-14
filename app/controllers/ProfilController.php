<?php

class ProfilController implements ControllerInterface
{
    private UtilisateurDAO $utilisateurDAO;
    private AbonneDAO $abonneDAO;
    private InscriptionValidator $validateur;
    private Logger $logger;

    public function __construct(){
        $this->utilisateurDAO = new UtilisateurDAO();
        $this->abonneDAO = new AbonneDAO();
        $this->validateur = new InscriptionValidator();
        $this->logger = new Logger();
    }

    public function afficherVue(): void
    {
        try{
            $this->logger->info("Affichage de la page profil pour: " . ($_GET['utilisateur'] ?? 'non spécifié'));
            $profil_id = $this->utilisateurDAO->existeUtilisateur($_GET['utilisateur']);
            if ($profil_id) {
                $utilisateur = $this->utilisateurDAO->getProfilUtilisateurById($profil_id);
                $abonne = $this->abonneDAO->getNbrAbonnesById($profil_id) ?? 0;
                $abonnement = $this->abonneDAO->getNbrAbonnementsById($profil_id) ?? 0;
                $this->logger->info("Profil trouvé: " . $_GET['utilisateur'] . " (ID: $profil_id)");
                
                // Si c'est le profil de l'utilisateur connecté, on traite les formulaires de modification
                if (isset($_SESSION['Pseudo']) && $_SESSION['Pseudo'] === $_GET['utilisateur']) {
                    $this->logger->debug("Traitement des formulaires de modification pour: " . $_SESSION['Pseudo']);
                    $this->callbackModifierPseudo();
                    $this->callbackModifierEmail();
                    $this->callbackModifierBio();
                    $this->callbackModifierMdp();
                }
                
                require_once __DIR__ . '/../views/profil.php';
            }
            else{
                $this->logger->warning("Tentative d'accès à un profil inexistant: " . $_GET['utilisateur']);
                header('HTTP/1.0 404 Not Found');
                exit();
            }
        }
        catch (PDOException $e)
        {
            $this->logger->error("Erreur PDO lors de l'affichage du profil: " . $e->getMessage());
            header('Location: ./?action=erreur');
            exit(); 
        }
    }

    public function callbackModifierPseudo(): void
    {
        if (isset($_POST['modalPseudo'])) {
            $this->logger->info("Tentative de modification de pseudo par l'utilisateur: " . $_SESSION['Pseudo']);
            
            $this->validateur->clearErreurs();
            $pseudo = trim(filter_input(INPUT_POST, 'modalPseudo', FILTER_SANITIZE_SPECIAL_CHARS));
            $this->logger->debug("Nouveau pseudo demandé: $pseudo");
            
            $this->validateur->validerPseudo($pseudo);

            if ($this->utilisateurDAO->existeUtilisateur($pseudo)){
                $this->logger->warning("Échec de modification de pseudo: le pseudo '$pseudo' existe déjà");
                $_SESSION['erreurs']['pseudo'] = "Ce pseudo existe déjà.";
            }
            elseif(empty($this->validateur->getErreurs()['pseudo'])){
                try{
                    $ancienpseudo = $_SESSION['Pseudo'];
                    $this->utilisateurDAO->updatePseudoByPseudo($_SESSION['Pseudo'], $pseudo);
                    $_SESSION['Pseudo'] = $pseudo;
                    unset($_SESSION['erreurs']);
                    $this->logger->info("Pseudo modifié avec succès: $ancienpseudo → $pseudo");
                    header("Location: ./?action=profil&utilisateur=$pseudo#paramContainer");
                    exit();
                }
                catch (Exception $e){
                    $this->logger->error("Erreur lors de la modification du pseudo: " . $e->getMessage());
                    header("Location: ./?action=erreur");
                    exit();
                }
            }
            else{
                $this->logger->warning("Validation échouée pour la modification de pseudo: " . implode(", ", $this->validateur->getErreurs()));
                $_SESSION['erreurs'] = $this->validateur->getErreurs();
            }
        }
    }

    public function callbackModifierEmail(): void
    {
        if (isset($_POST['modalEmail'])){
            $this->logger->info("Tentative de modification d'email par l'utilisateur: " . $_SESSION['Pseudo']);
            
            $this->validateur->clearErreurs();
            $email = trim(filter_input(INPUT_POST, 'modalEmail', FILTER_SANITIZE_EMAIL));
            $this->logger->debug("Nouvel email demandé: $email");
            
            $this->validateur->validerEmail($email);

            if ($this->utilisateurDAO->existeEmail($email)){
                $this->logger->warning("Échec de modification d'email: l'email '$email' existe déjà");
                $_SESSION['erreurs']['email'] = "Cette adresse email existe déjà.";
            }
            elseif(empty($this->validateur->getErreurs()['email'])){
                try{
                    $this->utilisateurDAO->updateEmailByPseudo($_SESSION['Pseudo'], $email);
                    unset($_SESSION['erreurs']);
                    $this->logger->info("Email modifié avec succès pour: " . $_SESSION['Pseudo'] . " → $email");
                    header("Location: ./?action=profil&utilisateur={$_SESSION['Pseudo']}#paramContainer");
                    exit();
                }
                catch (Exception $e){
                    $this->logger->error("Erreur lors de la modification de l'email: " . $e->getMessage());
                    header("Location: ./?action=erreur");
                    exit();
                }
            }
            else{
                $this->logger->warning("Validation échouée pour la modification d'email: " . implode(", ", $this->validateur->getErreurs()));
                $_SESSION['erreurs'] = $this->validateur->getErreurs();
            }	
        }
    }

    public function callbackModifierBio():void
    {
        if (isset($_POST['modalBio'])){
            $this->logger->info("Tentative de modification de bio par l'utilisateur: " . $_SESSION['Pseudo']);
            
            // On récupère la bio en brut car on se protège des injections XSS avec htmlspecialchars() dans la vue
            $bio = trim($_POST['modalBio']);
            $this->logger->debug("Nouvelle bio demandée (longueur: " . strlen($bio) . " caractères)");
            
            if (strlen($bio) > 256){
                $this->logger->warning("Bio trop longue pour: " . $_SESSION['Pseudo'] . " (" . strlen($bio) . " caractères)");
                $_SESSION['erreurs']['bio'] = "La bio ne doit pas dépasser 256 caractères.";
            }
            else{
                $this->utilisateurDAO->updateBioByPseudo($_SESSION['Pseudo'], $bio);
                unset($_SESSION['erreurs']);
                $this->logger->info("Bio modifiée avec succès pour: " . $_SESSION['Pseudo']);
                header("Location: ./?action=profil&utilisateur={$_SESSION['Pseudo']}#paramContainer");
                exit();
            }
        }
    }

    public function callbackModifierMdp():void
    {
        if (isset($_POST['ancienMdp'], $_POST['nouveauMdp'], $_POST['confirmationMdp'])){
            $this->logger->info("Tentative de modification de mot de passe par l'utilisateur: " . $_SESSION['Pseudo']);
            
            $this->validateur->clearErreurs();
            $ancienMdp = trim($_POST['ancienMdp']);
            $nouveauMdp = trim($_POST['nouveauMdp']);
            $confirmationMdp = trim($_POST['confirmationMdp']);
            
            $this->validateur->validerMdp($nouveauMdp);
            if (empty($ancienMdp)){
                $this->logger->warning("Échec de modification de mot de passe: ancien mot de passe non fourni");
                $_SESSION['erreurs']['ancienMdp'] = "Veuillez entrer votre ancien mot de passe.";
            }
            elseif (!password_verify($ancienMdp, $this->utilisateurDAO->getMdpByPseudo($_SESSION['Pseudo']))){
                $this->logger->warning("Échec de modification de mot de passe: ancien mot de passe incorrect");
                $_SESSION['erreurs']['ancienMdp'] = "Le mot de passe est incorrect.";
            }
            elseif (!empty($this->validateur->getErreurs()['mdp'])){
                $this->logger->warning("Validation échouée pour le nouveau mot de passe: " . $this->validateur->getErreurs()['mdp']);
                $_SESSION['erreurs']['nouveauMdp'] = $this->validateur->getErreurs()['mdp'];
            }
            elseif(empty($confirmationMdp)){
                $this->logger->warning("Échec de modification de mot de passe: confirmation non fournie");
                $_SESSION['erreurs']['confirmationMdp'] = "Veuillez entrer votre mot de passe de confirmation.";
            }
            elseif ($nouveauMdp !== $confirmationMdp){
                $this->logger->warning("Échec de modification de mot de passe: les mots de passe ne correspondent pas");
                $_SESSION['erreurs']['confirmationMdp'] = "Les mots de passe ne correspondent pas.";
            }
            else{
                try{
                    $this->utilisateurDAO->updateMdpByPseudo($_SESSION['Pseudo'], $nouveauMdp);
                    $this->logger->info("Mot de passe modifié avec succès pour: " . $_SESSION['Pseudo'] . " (déconnexion automatique)");
                    // On déconnecte l'utilisateur après la modification de son mot de passe
                    session_unset();
                    session_destroy();
                    header("Location: ./?action=connexion");
                    exit();
                }
                catch (Exception $e){
                    $this->logger->error("Erreur lors de la modification du mot de passe: " . $e->getMessage());
                    header("Location: ./?action=erreur");
                    exit();
                }
            }
        }
    }
    

}
