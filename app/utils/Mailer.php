<?php

use MailerSend\MailerSend;
use MailerSend\Helpers\Builder\Recipient;
use MailerSend\Helpers\Builder\EmailParams;
require_once 'test_autoloader.php';

class Mailer
{
    private MailerSend $instance;
    private string $email_sender;
    private string $nom_sender;
   
    public function __construct() {
        $this->instance = new MailerSend(['api_key' => getenv("MAILERSEND_TOKEN")]);
        $this->email_sender = getenv("MAILERSEND_EMAIL");
        $this->nom_sender = getenv("MAILERSEND_NAME");
    }

    
    public function envoyer($destinataire, $sujet, $corps_html, $corps_txt): bool
    {
        // Destinataire de l'email
        $recipients = [
            new Recipient($destinataire, null), // Email, Nom (optionnel)
        ];

        // Configurer les paramètres de l'email
        $emailParams = (new EmailParams())
            ->setFrom($this->email_sender)         // Email de l'expéditeur
            ->setFromName($this->nom_sender)      // Nom de l'expéditeur
            ->setRecipients($recipients)          // Destinataire(s)
            ->setSubject($sujet)                  // Sujet de l'email
            ->setHtml($corps_html)                // Version HTML
            ->setText($corps_txt);                // Version texte

        // Envoyer l'email
        try {
            $this->instance->email->send($emailParams);
            return true;
        } catch (Exception $e) {
            print("Erreur lors de l'envoi de l'email: " . $e->getMessage() . "\n");
            return false;
        }
    }
}
