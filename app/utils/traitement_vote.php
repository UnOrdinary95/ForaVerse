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
$commentaire_dao = new CommentaireDAO();
$logger = new Logger();

$utilisateur_id = $utilisateur_dao->getIdByPseudo($_SESSION['Pseudo']);
$publication_id = $_POST['publication_id'];
$valeur_vote = isset($_POST['valeur']) ? intval($_POST['valeur']) : 0; // On utilise intval pour s'assurer que c'est un entier car $_POST => string
$vote_existant = $vote_dao->getVote($publication_id, $utilisateur_id);
$logger->debug("Vote existant: " . $vote_existant);
$logger->debug("Valeur du vote: " . $valeur_vote);	
$resultat = false;

if ($_POST['action'] == 'voter') {
    if ($discussion_dao->getDiscussionById($publication_id)){
        $type_publication = 'discussion';
    } elseif ($commentaire_dao->getCommentaireById($publication_id)) {
        $type_publication = 'commentaire';
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Erreur: Publication introuvable']);
        exit;
    }

    if ($vote_existant === false) {
        $resultat = $vote_dao->addVote($publication_id, $utilisateur_id, $valeur_vote, $type_publication);  
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
    
    if ($type_publication == 'discussion') {
        $score_total = $discussion_dao->getScoreById($publication_id);
    } elseif ($type_publication == 'commentaire') {
        $score_total = $commentaire_dao->getScoreById($publication_id);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Erreur: Type de publication invalide']);
        exit;
    }
    // TODO : L'enlever
    $logger->debug("Score total: " . $score_total);
    $logger->debug("Valeur du vote actuel: " . $valeur_vote_actuel);

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
