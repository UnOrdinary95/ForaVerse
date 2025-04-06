<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once 'test_autoloader.php';

class Mailer
{
    private string $host;
    private string $username;
    private string $password;
    private int $port;

    public function __construct() {
        $this->host     = getenv("SMTP_HOST"); // Serveur SMTP
        $this->username = getenv("SMTP_USER");
        $this->password = getenv("SMTP_PASS");
        $this->port     = getenv("SMTP_PORT"); // Port SMTP
    }

    public function envoyer($destinataire, $sujet, $corps): bool
    {
        $mail = new PHPMailer(true);
        $mail->CharSet = "UTF-8";

        try {
            // Configuration du serveur SMTP
            $mail->isSMTP();
            $mail->Host       = $this->host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->username;
            $mail->Password   = $this->password;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Chiffrement TLS
            $mail->Port       = $this->port;

            // Expéditeur et destinataire
            $mail->setFrom("test@test.com", "ForaVerse"); // Adresse expéditeur
            $mail->addAddress($destinataire); // Adresse destinataire

            // Contenu de l'email
            $mail->isHTML(); // Email au format HTML
            $mail->Subject = $sujet; // Sujet de l'email
            $mail->Body    = $corps;    // Corps de l'email (HTML)

            return $mail->send();
        } catch (Exception) {
            echo "Erreur lors de l'envoi de l'email: $mail->ErrorInfo";
            return false;
        }
    }
}
// Script de test
//if (php_sapi_name() == 'cli') {
//    print(getenv("SMTP_HOST")."|||\n\n");
//    print(getenv("SMTP_USER")."|||\n\n");
//    print(getenv("SMTP_PASS")."|||\n\n");
//    print(getenv("SMTP_PORT")."|||\n\n");
//    print("Test de l'envoi d'un email\n");
//    $mailer = new Mailer();
//
//    $destinataire = 'hernani.cda@gmail.com';
//    $sujet = 'Test Email - UnOrdinary';
//    $corps = '<h1>Ceci est un email de test !</h1><p>Envoyé via PHPMailer.</p>';
//
//    if ($mailer->envoyer($destinataire, $sujet, $corps)) {
//        echo "Email envoyé avec succès.\n";
//    } else {
//        echo "Échec de l'envoi de l'email.\n";
//    }
//}