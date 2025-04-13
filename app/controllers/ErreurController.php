<?php

class ErreurController implements ControllerInterface
{
    /**
     * @var Logger Instance du logger pour tracer les actions
     */
    private Logger $logger;
    
    /**
     * Constructeur du ErreurController
     * Initialise le logger
     */
    public function __construct()
    {
        $this->logger = new Logger();
    }
    
    /**
     * Affiche la vue d'erreur
     *
     * Tente d'inclure et d'afficher le template de la vue d'erreur.
     * En cas d'exception, affiche un message d'erreur générique.
     *
     * @throws Exception En cas d'erreur lors de l'affichage de la vue
     * @return void
     */
    public function afficherVue(): void
    {
        try {
            $this->logger->error("Affichage de la page d'erreur");
            require_once __DIR__ . '/../views/erreur.php';
        } catch (Exception $e) {
            $this->logger->error("Erreur lors de l'affichage de la page d'erreur: " . $e->getMessage());
            header('HTTP/1.0 404 Not Found');
            exit();
        }
    }
}