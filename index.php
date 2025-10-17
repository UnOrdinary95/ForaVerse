<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

require_once __DIR__ . '/vendor/autoload.php';

$routeur = new Routeur();

$accueilController = new AccueilController();
$erreurController = new ErreurController();
$inscriptionController = new InscriptionController();
$connexionController = new ConnexionController();
$profilController = new ProfilController();
$resetMdpController = new ResetMdpController();
$communauteController = new CommunauteController();
$publicationController = new PublicationController();
$commentaireController = new CommentaireController();
$rechercheController = new RechercheController();

$routeur->ajouterRoute('accueil', [$accueilController, 'afficherVue']);
$routeur->ajouterRoute('inscription', [$inscriptionController, 'afficherVue']);
$routeur->ajouterRoute('connexion', [$connexionController, 'afficherVue']);
$routeur->ajouterRoute('deconnexion', [$connexionController, 'deconnexion']);
$routeur->ajouterRoute('profil', [$profilController, 'afficherVue']);
$routeur->ajouterRoute('demande_resetmdp', [$resetMdpController, 'afficherVue']);
$routeur->ajouterRoute('confirmdemande_resetmdp', [$resetMdpController, 'afficherVueDemandeResetMdp']);
$routeur->ajouterRoute('resetmdp', [$resetMdpController, 'afficherVueResetMdp']);
$routeur->ajouterRoute('erreur', [$erreurController, 'afficherVue']);
$routeur->ajouterRoute('communaute', [$communauteController, 'afficherVue']);
$routeur->ajouterRoute('publication', [$publicationController, 'afficherVue']);
$routeur->ajouterRoute('commentaire', [$commentaireController, 'afficherVue']);
$routeur->ajouterRoute('recherche', [$rechercheController, 'afficherVue']);


$routesPubliques = ['accueil', 'inscription', 'connexion', 'demande_resetmdp', 'confirmdemande_resetmdp', 'resetmdp', 'erreur', 'communaute', 'publication', 'profil', 'commentaire', 'recherche'];
$action = $_GET['action'] ?? 'accueil';

// On vérifie si l'utilisateur doit être connecté pour cette route
if (!in_array($action, $routesPubliques) && !isset($_SESSION['Pseudo'])) {
    header('Location: ./?action=connexion');
    exit();
}

$routeur->executerRoute();
