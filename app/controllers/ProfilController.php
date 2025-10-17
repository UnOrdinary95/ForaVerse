<?php

class ProfilController implements ControllerInterface
{
    private UtilisateurDAO $utilisateurDAO;
    private InscriptionValidator $validateur;
    private Logger $logger;

    public function __construct(){
        $this->utilisateurDAO = new UtilisateurDAO();
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
                $liste_commu_moderation = [];
                if(isset($_SESSION['Pseudo'])){
                    include_once __DIR__ . '/../utils/left_sidebar_callback.php';
                    $session_user = $this->utilisateurDAO->getProfilUtilisateurById($this->utilisateurDAO->getIdByPseudo($_SESSION['Pseudo']));
                    $liste_commu_moderation = $utilisateur->getCommuCommunModeration($session_user);
                    
                    if($session_user->estAdministrateur()){
                        $this->logger->info("L'utilisateur " . $_SESSION['Pseudo'] . " est administrateur et peut supprimer le compte de " . $_GET['utilisateur']);
                        $this->callbackSupprimerCompte($profil_id);
                    }

                    $this->callbackAvertirCompte($utilisateur, $session_user->getId());
                    $this->callbackBannirCompte($utilisateur, $session_user->getId());
                    $this->callbackAnnulerBanGlobal($utilisateur);
                }

                $est_banni_global = (new BannissementDAO)->getBannissementGlobalByIdUtilisateur($profil_id);
                $this->logger->info("Profil trouvé: " . $_GET['utilisateur'] . " (ID: $profil_id)");
                
                // Si c'est le profil de l'utilisateur connecté, on traite les formulaires de modification
                if (isset($_SESSION['Pseudo']) && $_SESSION['Pseudo'] === $_GET['utilisateur']) {
                    $this->logger->debug("Traitement des formulaires de modification pour: " . $_SESSION['Pseudo']);
                    $this->callbackModifierPseudo();
                    $this->callbackModifierEmail();
                    $this->callbackModifierBio();
                    $this->callbackModifierMdp();
                }

                if (isset($_SESSION['Pseudo']) && $_SESSION['Pseudo'] != $_GET['utilisateur'] && count($liste_commu_moderation) > 0){
                    $this->logger->info("L'utilisateur " . $_SESSION['Pseudo'] . " est modérateur dans les communautés de " . $_GET['utilisateur']);
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
                try{
                    $this->utilisateurDAO->updateBioByPseudo($_SESSION['Pseudo'], $bio);
                    unset($_SESSION['erreurs']);
                    $this->logger->info("Bio modifiée avec succès pour: " . $_SESSION['Pseudo']);
                    header("Location: ./?action=profil&utilisateur={$_SESSION['Pseudo']}#paramContainer");
                    exit();
                }
                catch(Exception $e){
                    $this->logger->error("Erreur lors de la modification de la bio: " . $e->getMessage());
                    header("Location: ./?action=erreur");
                    exit();
                }
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

    public function callbackSupprimerCompte(int $utilisateur_id): void
    {
        if (isset($_POST['Suppr'])){
            try{
                $this->logger->info("Tentative de suppression de compte par l'utilisateur: " . $_SESSION['Pseudo']);
                $this->utilisateurDAO->deleteUtilisateurById($utilisateur_id);
                $this->logger->info("Compte supprimé avec succès pour: " . $_SESSION['Pseudo']);
                header('Location: ./?action=accueil');
                exit();
            }
            catch (PDOException $e){
                $this->logger->error("Erreur lors de la suppression du compte: " . $e->getMessage());
                header('Location: ./?action=erreur');
                exit();
            }
        }
    }

    public function callbackAvertirCompte(Utilisateur $utilisateur, int $moderateur_id): void
    {
        if(isset($_POST['liste_commu']) && isset($_POST['raisonWarn'])){
            $this->logger->info("Tentative d'avertissement de compte par l'utilisateur: " . $_SESSION['Pseudo']);
            $commu = htmlspecialchars($_POST['liste_commu']);
            $raison = $_POST['raisonWarn'];
            $this->logger->debug("Communauté sélectionnée: $commu, Raison: $raison");

            if(strlen($raison) > 256){
                $this->logger->warning("Raison d'avertissement trop longue pour: " . $_SESSION['Pseudo'] . " (" . strlen($raison) . " caractères)");
                $_SESSION['erreurs']['raisonWarn'] = "Le motif ne doit pas dépasser 256 caractères.";
            }
            else{
                $this->logger->info("Avertissement de compte pour: " . $_SESSION['Pseudo'] . " dans la communauté: $commu");
                (new AvertissementDAO)->addAvertissement($moderateur_id, $utilisateur->getId(), $commu, $raison);
                unset($_SESSION['erreurs']);
                $this->logger->info("Avertissement ajouté avec succès pour: " . $_SESSION['Pseudo'] . " dans la communauté: $commu");
                header("Location: ./?action=profil&utilisateur={$utilisateur->getPseudo()}#profilmodContainer");
                exit();
            }
        }
    }

    public function callbackBannirCompte(Utilisateur $utilisateur, int $moderateur_id): void
    {
        if(isset($_POST['liste_commu']) && isset($_POST['dureeban']) && isset($_POST['raisonBan'])){
            $this->logger->info("Tentative de bannissement de compte par l'utilisateur: " . $_SESSION['Pseudo']);
            $commu = htmlspecialchars($_POST['liste_commu']);
            $duree = $_POST['dureeban'];
            $raison = $_POST['raisonBan'];
            $this->logger->debug("Communauté sélectionnée: $commu, Raison: $raison, Durée: $duree");

            if ($commu != "global" && (new BannissementDAO)->getBannissementByIdUtilisateurAndCommunaute($utilisateur->getId(), $commu)
            || $commu == "global" && (new BannissementDAO)->getBannissementGlobalByIdUtilisateur($utilisateur->getId())){
                $this->logger->warning("Échec de bannissement: l'utilisateur " . $_SESSION['Pseudo'] . " est déjà banni dans la communauté $commu");
                $_SESSION['erreurs']['raisonBan'] = "Cet utilisateur est déjà banni.";
            }
            elseif(strlen($raison) > 256){
                $this->logger->warning("Raison de bannissement trop longue pour: " . $_SESSION['Pseudo'] . " (" . strlen($raison) . " caractères)");
                $_SESSION['erreurs']['raisonBan'] = "Le motif ne doit pas dépasser 256 caractères.";
            }
            else{
                $this->logger->info("Bannissement de compte pour: " . $_SESSION['Pseudo'] . " dans la communauté: $commu");
                if ($commu == "global"){
                    if ($duree == "1m"){
                        (new BannissementDAO)->addBannissementGlobalOneMonth($moderateur_id, $utilisateur->getId(), $raison);
                    }
                    else{
                        (new BannissementDAO)->addBannissementGlobalPermanent($moderateur_id, $utilisateur->getId(), $raison);
                    }
                }
                elseif ($duree == "1m"){
                    (new BannissementDAO)->addBannissementCommuOneMonth($moderateur_id, $utilisateur->getId(), $commu, $raison);
                }
                else{
                    (new BannissementDAO)->addBannissementCommuPermanent($moderateur_id, $utilisateur->getId(), $commu, $raison);
                }
                unset($_SESSION['erreurs']);
                $this->logger->info("Bannissement ajouté avec succès pour: " . $_SESSION['Pseudo'] . " dans la communauté: $commu");
                header("Location: ./?action=profil&utilisateur={$utilisateur->getPseudo()}#profilmodContainer");
                exit();
            }
        }
    }

    public function callbackAnnulerBanGlobal(Utilisateur $utilisateur){
        if (isset($_POST['AnnulerBanGlobal'])){
            $this->logger->info("Tentative de suppression de bannissement global par l'utilisateur: " . $_SESSION['Pseudo']);
            try{
                (new BannissementDAO)->deleteBannissementById((new BannissementDAO)->getBannissementGlobalByIdUtilisateur($utilisateur->getId())->getId());
                $this->logger->info("Bannissement global supprimé avec succès pour: " . $_SESSION['Pseudo']);
                header("Location: ./?action=profil&utilisateur={$utilisateur->getPseudo()}#profilmodContainer");
                exit();
            }
            catch (PDOException $e){
                $this->logger->error("Erreur lors de la suppression du bannissement global: " . $e->getMessage());
                header('Location: ./?action=erreur');
                exit();
            }
        }
    }
}
