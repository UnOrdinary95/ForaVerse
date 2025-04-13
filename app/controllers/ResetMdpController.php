<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

class ResetMdpController implements ControllerInterface
{
    private UtilisateurDAO $utilisateur_dao;
    private InscriptionValidator $validateur;
    private Mailer $mailer;
    private string $email;
    private string $token;
    private Logger $logger;

    public function __construct()
    {
        $this->email = '';
        $this->token = '';
        $this->mailer = new Mailer();
        $this->utilisateur_dao = new UtilisateurDAO();
        $this->validateur = new InscriptionValidator();
        $this->logger = new Logger();
    }

    public function afficherVue(): void
    {
        try{
            $this->logger->info("Affichage de la page de demande de réinitialisation de mot de passe");
            $this->callbackVue();
            require_once __DIR__ . "/../views/demande_resetmdp.php";
        } catch (Exception $e) {
            $this->logger->error("Erreur lors de l'affichage de la page de demande de réinitialisation: " . $e->getMessage());
            header('HTTP/1.0 404 Not Found');
            exit();
        }
    }

    public function afficherVueResetMdp(): void
    {
        try {
            if (isset($_GET['token'])) {
                $this->token = $_GET['token'];
                $this->logger->info("Tentative de réinitialisation de mot de passe avec token");
                $this->logger->debug("Token reçu: " . substr($this->token, 0, 10) . "...");
                
                if ($this->verifierToken($this->token)) {
                    $this->logger->info("Token valide pour l'email: " . $this->email);
                    $this->callbackVueResetMdp();
                    $token = $this->token;
                    $pseudo = $this->utilisateur_dao->getPseudoByEmail($this->email);
                    require_once __DIR__ . "/../views/resetmdp.php";
                }
                else{
                    $this->logger->warning("Token invalide ou expiré");
                    header('HTTP/1.0 404 Not Found');
                    exit();
                }
            } else {
                $this->logger->warning("Tentative d'accès à la page de réinitialisation sans token");
                header('HTTP/1.0 404 Not Found');
                exit();
            }
        } catch (Exception $e) {
            $this->logger->error("Erreur lors de l'affichage de la page de réinitialisation: " . $e->getMessage());
            header('HTTP/1.0 404 Not Found');
            exit();
        }
    }

    public function afficherVueDemandeResetMdp(): void
    {
        try{
            $this->logger->info("Affichage de la page de confirmation de demande de réinitialisation");
            require_once __DIR__ . "/../views/confirmdemande_resetmdp.php";
        } catch (Exception $e) {
            $this->logger->error("Erreur lors de l'affichage de la page de confirmation: " . $e->getMessage());
            header('HTTP/1.0 404 Not Found');
            exit();
        } 
    }

    public function envoyerMailReset(): void
    {
        $this->logger->info("Envoi d'email de réinitialisation à: " . $this->email);
        $secret_key = getenv("SECRET_KEY");
        $payload = ['email' => $this->email, 'exp' => time() + 5 * 60];
        $token = JWT::encode($payload, $secret_key, 'HS256');
        $lien = 'https://foraverse.unordinary-things.tech/index.php?action=resetmdp&token=' . urlencode($token);
        $sujet = "Réinitialisation de votre mot de passe ForaVerse";
        $message_html = "
        <h1>Réinitialisation de votre mot de passe</h1>
        <p>Bonjour,</p>
        <p>Nous avons reçu une demande de réinitialisation de votre mot de passe. Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe (vous avez 5 min) :</p>
        <p><a href='$lien'>Réinitialiser mon mot de passe</a></p>
        <p>Si vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet email.</p>
        <p>Merci,</p>
        <p>L'équipe ForaVerse</p>
        <p><small>Cette notification a été envoyée à l'adresse email associée à votre compte ForaVerse. Ce mail est auto-généré. Merci de ne pas y répondre, si vous voulez de l'aide supplémentaire, merci de vous adresser à unordinary. (discord)</small></p>
        <p><small><a href='https://foraverse.unordinary-things.tech'>foraverse.unordinary-things.tech</a></small></p>
        ";

        $message_txt = "Bonjour,\n\nNous avons reçu une demande de réinitialisation de votre mot de passe. Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe (vous avez 5 min) :\n\n$lien\n\nSi vous n'avez pas demandé cette réinitialisation, veuillez ignorer cet email.\n\nMerci,\nL'équipe ForaVerse";
        
        try {
            $this->mailer->envoyer($this->email, $sujet, $message_html, $message_txt);
            $this->logger->info("Email de réinitialisation envoyé avec succès à: " . $this->email);
        } catch (Exception $e) {
            $this->logger->error("Échec de l'envoi de l'email de réinitialisation à: " . $this->email . " - Erreur: " . $e->getMessage());
        }
    }

    public function verifierToken($token):bool
    {
        try{
            $this->logger->debug("Vérification du token de réinitialisation");
            $secret_key = getenv("SECRET_KEY");
            $decodeur = JWT::decode($token, new Key($secret_key, 'HS256'));
            $this->email = $decodeur->email;
            $this->logger->info("Token de réinitialisation valide pour: " . $this->email);
            return true;
        } catch (ExpiredException) {
            $this->logger->warning("Token de réinitialisation expiré");
            return false;
        } catch (Exception $e) {
            $this->logger->error("Erreur lors de la vérification du token de réinitialisation: " . $e->getMessage());
            echo 'Erreur lors de la vérification du token: ',  $e->getMessage(), "\n";
            return false;
        }
    }

    public function callbackVue():void
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $email = isset($_POST['email']) ? $_POST['email'] : '';
            $this->logger->info("Demande de réinitialisation de mot de passe pour l'email: " . $email);
            
            if ($this->est_enregistre($email)){
                $this->email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
                $this->logger->info("Email valide trouvé dans la base de données: " . $this->email);
                $this->envoyerMailReset();
            } else {
                $this->logger->warning("Tentative de réinitialisation pour un email non enregistré: " . $email);
            }
            
            header("Location: ./?action=confirmdemande_resetmdp");
            exit();
        }
    }

    public function callbackVueResetMdp(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            $this->logger->info("Traitement de la demande de réinitialisation de mot de passe pour: " . $this->email);
            
            $this->validateur->clearErreurs();
            $mdp = trim(filter_input(INPUT_POST, 'mdp', FILTER_SANITIZE_SPECIAL_CHARS));
            $this->validateur->validerMdp($mdp);
            
            if (empty($this->validateur->getErreurs()['mdp'])) {
                try {
                    if (empty($this->email)) {
                        $this->logger->error("Email manquant lors de la réinitialisation du mot de passe");
                        throw new Exception("Email manquant");
                    }

                    $this->utilisateur_dao->updateMdpByEmail($this->email, $mdp);
                    unset($_SESSION['erreurs']);
                    $this->logger->info("Mot de passe réinitialisé avec succès pour: " . $this->email);
                    header("Location: ./?action=connexion");
                    exit();
                } catch (Exception $e) {
                    $this->logger->error("Erreur lors de la réinitialisation du mot de passe: " . $e->getMessage());
                    error_log("Erreur lors de la réinitialisation du mot de passe: " . $e->getMessage());
                    header("Location: ./?action=erreur");
                    exit();
                }
            } else {
                $this->logger->warning("Validation du nouveau mot de passe échouée: " . implode(", ", $this->validateur->getErreurs()));
                $_SESSION['erreurs'] = $this->validateur->getErreurs();
            }
        }
    }

    private function est_enregistre(string $email):bool
    {
        $this->logger->debug("Vérification si l'email est enregistré: " . $email);
        $result = in_array($email, $this->utilisateur_dao->getEmails());
        
        if ($result) {
            $this->logger->debug("Email trouvé dans la base de données: " . $email);
        } else {
            $this->logger->debug("Email non trouvé dans la base de données: " . $email);
        }
        
        return $result;
    }
}
