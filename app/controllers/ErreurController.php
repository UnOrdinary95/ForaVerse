<?php

class ErreurController implements ControllerInterface
{
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
            require_once __DIR__ . '/../views/erreur.php';
        } catch (Exception $e) {
            header('HTTP/1.0 404 Not Found');
            exit();
        }
    }
}