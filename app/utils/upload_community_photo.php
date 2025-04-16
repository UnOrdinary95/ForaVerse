<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    require_once 'test_autoloader.php';

    session_start();

    if (!isset($_SESSION['Pseudo'])) {
        throw new Exception('Utilisateur non connecté');
    }

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Erreur lors de l\'upload: ' . $_FILES['image']['error']);
    }

    // Récupérer l'ID de la communauté à partir de l'URL
    $communaute_dao = new CommunauteDAO();
    $nom_commu = isset($_GET['nomCommu']) ? $_GET['nomCommu'] : null;
    
    if (!$nom_commu) {
        throw new Exception('Nom de communauté manquant');
    }
    
    $id_commu = $communaute_dao->getIdByNom($nom_commu);
    
    if (!$id_commu) {
        throw new Exception('Communauté introuvable');
    }

    // Créer le répertoire de destination s'il n'existe pas
    $upload_dir = __DIR__ . '/../../public/images/pp_commu/';
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            throw new Exception('Impossible de créer le dossier upload');
        }
        // Appliquer les permissions sur le dossier nouvellement créé
        chmod($upload_dir, 0755);
    }

    $filename = $id_commu . '_' . time() . '.jpg';
    $filepath = $upload_dir . $filename;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
        throw new Exception('Erreur lors du déplacement du fichier uploadé');
    }

    // S'assurer que les permissions sont correctes pour le fichier
    if (!chmod($filepath, 0644)) {
        throw new Exception('Impossible de modifier les permissions du fichier');
    }

    $chemin_relatif = 'images/pp_commu/' . $filename;
    
    // Mettre à jour le chemin de la photo dans la base de données
    if (!$communaute_dao->updatePhotoProfil($id_commu, $chemin_relatif)) {
        throw new Exception('Erreur lors de la mise à jour en base de données');
    }

    echo json_encode([
        'success' => true,
        'path' => $chemin_relatif
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    exit;
}