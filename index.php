<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

require_once __DIR__ . '/vendor/autoload.php';

$routeur = new Routeur();

$accueilController = new AccueilController();
$inscriptionController = new InscriptionController();
$connexionController = new ConnexionController();

$routeur->ajouterRoute('accueil', [$accueilController, 'afficherVue']);
$routeur->ajouterRoute('inscription', [$inscriptionController, 'afficherVue']);
$routeur->ajouterRoute('connexion', [$connexionController, 'afficherVue']);
$routeur->ajouterRoute('deconnexion', [$connexionController, 'deconnexion']);

$routeur->executerRoute();
