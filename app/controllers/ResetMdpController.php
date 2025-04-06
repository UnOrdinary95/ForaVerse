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

    public function __construct()
    {
        $this->email = '';
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
                $token = $_GET['token'];
                if ($this->verifierToken($token)) {
                    print("Token valide");
                    $_SESSION['token_reset'] = $token;
                    $pseudo = $this->utilisateur_dao->getPseudoByEmail($this->email);
//                    print("<br> Bonjour $pseudo");
//                    $this->callbackResetMdp();
//                    print("<br> Vous pouvez maintenant réinitialiser votre mot de passe");
                    // TODO : Finir le dimanche
                    print("Hi ! Pour l'instant ça marche pas cette merde ! :D");
                }
            }
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
        $message = "
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
        $this->mailer->envoyer($this->email, $sujet, $message);
    }

    public function verifierToken($token):bool
    {
        try{
            $secret_key = getenv("SECRET_KEY");
            $decodeur = JWT::decode($token, new Key($secret_key, 'HS256'));
            $this->email = $decodeur->email;
            return true;
        } catch (ExpiredException) {
            echo 'Le token a expiré.';
            unset($_SESSION['token_reset']);
            return false;
        } catch (Exception $e) {
            echo 'Erreur lors de la vérification du token: ',  $e->getMessage(), "\n";
            return false;
        }
    }

    public function callbackVue():void
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if ($this->est_enregistre($_POST['email'])){
                $this->email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
                $this->envoyerMailReset();
            }
            require_once __DIR__ . '/../views/confirmdemande_resetmdp.php';
            exit();
        }
    }

//    public function callbackResetMdp():void
//    {
//        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//            $mdp = trim(filter_input(INPUT_POST, 'mdp', FILTER_SANITIZE_SPECIAL_CHARS));
//            $this->validateur->validerMdp($mdp);
//            if (empty($this->validateur->getErreurs())) {
//                try {
//                    $this->utilisateur_dao->updateMdpByEmail($this->email, $_POST['mdp']);
//                    header("Location: ./?action=connexion");
//                    exit();
//                } catch (PDOException $e) {
//                    require_once __DIR__ . '/../views/erreur.php';
//                    exit();
//                }
//            }
//            else{
//                $_SESSION['erreurs'] = $this->validateur->getErreurs();
//                if (!isset($_SESSION['reloaded'])) {
//                    $_SESSION['reloaded'] = true;
//                    header("Location: ./?action=resetmdp&token=" . urlencode($_GET['token']));
//                    exit();
//                } else {
//                    unset($_SESSION['reloaded']);
//                }
//            }
//        }
//    }

//    public function callbackResetMdp(): void
//    {
//        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//            $mdp = trim(filter_input(INPUT_POST, 'mdp', FILTER_SANITIZE_SPECIAL_CHARS));
//            $this->validateur->validerMdp($mdp);
//
//            if (empty($this->validateur->getErreurs())) {
//                try {
//                    $this->utilisateur_dao->updateMdpByEmail($this->email, $mdp);
//                    unset($_SESSION['token_reset']); // ⬅️ On nettoie après succès
//                    header("Location: ./?action=connexion");
//                    exit();
//                } catch (PDOException $e) {
//                    require_once __DIR__ . '/../views/erreur.php';
//                    exit();
//                }
//            } else {
//                $_SESSION['erreurs'] = $this->validateur->getErreurs();
//                if (!isset($_SESSION['reloaded'])) {
//                    $_SESSION['reloaded'] = true;
//                    $token = $_SESSION['token_reset'] ?? ''; // ⬅️ Récupère le token pour rediriger
//                    header("Location: ./?action=resetmdp&token=" . urlencode($token));
//                    exit();
//                } else {
//                    unset($_SESSION['reloaded']);
//                }
//            }
//        }
//    }

    public function callbackResetMdp(): void
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Récupérer le token envoyé via POST (champ caché)
            $token = $_POST['token'] ?? '';

            // Vérification de la validité du token
            if (!$this->verifierToken($token)) {
                // Si le token est invalide, afficher une erreur
                $_SESSION['erreurs'] = "Token invalide ou expiré.";
                header("Location: ./?action=resetmdp");
                exit();
            }

            // Récupérer le mot de passe
            $mdp = trim(filter_input(INPUT_POST, 'mdp', FILTER_SANITIZE_SPECIAL_CHARS));
            $this->validateur->validerMdp($mdp);

            if (empty($this->validateur->getErreurs())) {
                try {
                    // Utilisation de l'email stocké dans la session
                    $email = $_SESSION['email_reset'] ?? null;
                    if (!$email) {
                        throw new Exception("Email non trouvé en session.");
                    }

                    // Mise à jour du mot de passe dans la base de données
                    $this->utilisateur_dao->updateMdpByEmail($email, $mdp);

                    // Nettoyage de la session après succès
                    unset($_SESSION['token_reset'], $_SESSION['email_reset']);

                    // Redirection vers la page de connexion après la réinitialisation
                    header("Location: ./?action=connexion");
                    exit();
                } catch (Exception $e) {
                    // Gestion des erreurs
                    $_SESSION['erreurs'] = "Erreur lors de la mise à jour du mot de passe.";
                    require_once __DIR__ . '/../views/erreur.php';
                    exit();
                }
            } else {
                // Si des erreurs de validation existent, les renvoyer au formulaire
                $_SESSION['erreurs'] = $this->validateur->getErreurs();
                require_once __DIR__ . '/../views/erreur.php';
                exit();
            }
        }
    }


    private function est_enregistre(string $email):bool
    {
        return in_array($email, $this->utilisateur_dao->getEmails());
    }
}