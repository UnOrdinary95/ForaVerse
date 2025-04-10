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

    public function __construct()
    {
        $this->email = '';
        $this->token = '';
        $this->mailer = new Mailer();
        $this->utilisateur_dao = new UtilisateurDAO();
        $this->validateur = new InscriptionValidator();
    }

    public function afficherVue(): void
    {
        try{
            $this->callbackVue();
            require_once __DIR__ . "/../views/demande_resetmdp.php";
        } catch (Exception $e) {
            require_once __DIR__ . "/../views/erreur.php";
        }
    }

    public function afficherVueResetMdp(): void
    {
        try {
            if (isset($_GET['token'])) {
                $this->token = $_GET['token'];
                if ($this->verifierToken($this->token)) {
                    // if ($_SERVER['REQUEST_METHOD'] === 'POST')
                    // {
                        $this->callbackVueResetMdp();
                    // }
                    $token = $this->token;
                    $pseudo = $this->utilisateur_dao->getPseudoByEmail($this->email);
                    require_once __DIR__ . "/../views/resetmdp.php";
                }
            }
        } catch (Exception $e) {
            require_once __DIR__ . "/../views/erreur.php";
        }
    }

    public function afficherVueDemandeResetMdp(): void
    {
        try{
            require_once __DIR__ . "/../views/confirmdemande_resetmdp.php";
        } catch (Exception $e) {
            require_once __DIR__ . "/../views/erreur.php";
        } 
    }

    public function envoyerMailReset(): void
    {
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
        $this->mailer->envoyer($this->email, $sujet, $message_html, $message_txt);
    }

    public function verifierToken($token):bool
    {
        try{
            $secret_key = getenv("SECRET_KEY");
            $decodeur = JWT::decode($token, new Key($secret_key, 'HS256'));
            $this->email = $decodeur->email;
            return true;
        } catch (ExpiredException) {
            return false;
        } catch (Exception $e) {
            echo 'Erreur lors de la vérification du token: ',  $e->getMessage(), "\n";
            return false;
        }
    }

    public function callbackVue():void
    {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if ($this->est_enregistre($_POST['email'])){
                $this->email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
                $this->envoyerMailReset();
            }
            header("Location: ./?action=confirmdemande_resetmdp");
            exit();
        }
    }

    public function callbackVueResetMdp(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            if (!$this->verifierToken($this->token)) {
                $_SESSION['erreurs'] = "Token invalide ou expiré.";
            }
            else{
                $mdp = trim(filter_input(INPUT_POST, 'mdp', FILTER_SANITIZE_SPECIAL_CHARS));
                $this->validateur->validerMdp($mdp);
                
                if (empty($this->validateur->getErreurs())) {
                    try {
                        print("CONNEXION OK");
                        if (isset($this->email)){
                            $this->utilisateur_dao->updateMdpByEmail($this->email, $mdp);
                            unset($_SESSION['erreurs']);
                        }
                        header("Location: ./?action=connexion");
                        exit();
                    } catch (Exception $e) {
                        print("PAS OK");
                        $_SESSION['erreurs'] = "Erreur lors de la mise à jour du mot de passe.";
                        require_once __DIR__ . '/../views/erreur.php';
                    }
                }
                else {
                    print("PAS OK");
                    $_SESSION['erreurs'] = $this->validateur->getErreurs();
                }
            }
        }
    }

    private function est_enregistre(string $email):bool
    {
        return in_array($email, $this->utilisateur_dao->getEmails());
    }
}
