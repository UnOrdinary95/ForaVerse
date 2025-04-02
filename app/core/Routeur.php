<?php

/**
 * Class Routeur
 * Gère le routage des requêtes HTTP dans l'application
 *
 * Cette classe permet de :
 * - Définir des routes et leurs fonctions de rappel associées
 * - Exécuter la fonction correspondant à la route demandée via le paramètre 'action'
 *
 */
final class Routeur
{
    /** @var array<string,callable> Tableau associatif des routes et leurs fonctions */
    private array $routes;

    /**
     * Initialise un nouveau routeur avec un tableau de routes vide
     */
    public function __construct()
    {
        $this->routes = [];
    }

    /**
     * Ajoute une nouvelle route et sa fonction de rappel associée
     *
     * @param string $route Nom de la route (utilisé dans le paramètre 'action')
     * @param callable $fonction Fonction à exécuter pour cette route
     */
    public function ajouterRoute(string $route, callable $fonction):void
    {
        $this->routes[$route] = $fonction;
    }

    /**
     * Exécute la fonction associée à la route demandée
     * Si aucune route n'est spécifiée, exécute la route 'accueil'
     * Si la route spécifiée n'existe pas, renvoie une erreur 404
     */
    public function executerRoute():void
    {
        $action = $_GET['action'] ?? 'accueil';

        if (array_key_exists($action, $this->routes)){
            call_user_func($this->routes[$action]);
        }
        else{
            header("HTTP/1.0 404 Not Found");
            echo "404 Not Found";
        }
    }
}
