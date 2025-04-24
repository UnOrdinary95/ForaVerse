<?php

require_once 'test_autoloader.php';
session_start();

if (!isset($_SESSION['Pseudo']) || !isset($_POST['action']) || !isset($_POST['publication_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Erreur: Données manquantes']);
    exit;
}

$utilisateur_dao = new UtilisateurDAO();
$vote_dao = new VoteDAO();
$discussion_dao = new DiscussionDAO();
$logger = new Logger();

$utilisateur_id = $utilisateur_dao->getIdByPseudo($_SESSION['Pseudo']);
$publication_id = $_POST['publication_id'];
$valeur_vote = isset($_POST['valeur']) ? intval($_POST['valeur']) : 0; // On utilise intval pour s'assurer que c'est un entier car $_POST => string
$vote_existant = $vote_dao->getVote($publication_id, $utilisateur_id);
$logger->debug("Vote existant: " . $vote_existant);
$logger->debug("Valeur du vote: " . $valeur_vote);	
$resultat = false;

if ($_POST['action'] == 'voter') {
    if ($vote_existant === false) {
        $resultat = $vote_dao->addVote($publication_id, $utilisateur_id, $valeur_vote, 'discussion');
    } else {
        $resultat = $vote_dao->updateVote($publication_id, $utilisateur_id, $valeur_vote);
    }
    
    if (!$resultat) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Erreur lors de l\'enregistrement du vote']);
        exit;
    }
    
    $valeur_vote_actuel = $vote_dao->getVote($publication_id, $utilisateur_id);
    
    if ($valeur_vote_actuel === null || $valeur_vote_actuel === false) {
        $valeur_vote_actuel = 0;
    }
    
    $score_total = $discussion_dao->getScoreById($publication_id);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'vote' => $valeur_vote_actuel,
        'score' => $score_total
    ]);
    exit;
} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Erreur: Action invalide']);
    exit;
}
