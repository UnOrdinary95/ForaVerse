<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    // Vérifier le chemin correct pour l'autoloader
    require_once 'test_autoloader.php';

    session_start();

    if (!isset($_SESSION['Pseudo'])) {
        throw new Exception('Utilisateur non connecté');
    }

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Erreur lors de l\'upload: ' . $_FILES['image']['error']);
    }

    $utilisateur_dao = new UtilisateurDAO();
    $id = $utilisateur_dao->getIdByPseudo($_SESSION['Pseudo']);

    $upload_dir = __DIR__ . '/../../public/images/pp_user/';
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            throw new Exception('Impossible de créer le dossier upload');
        }
    }

    $filename = $id . '_' . time() . '.jpg';
    $filepath = $upload_dir . $filename;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
        throw new Exception('Erreur lors du déplacement du fichier uploadé');
    }

    $chemin_relatif = 'images/pp_user/' . $filename;
    if (!$utilisateur_dao->updatePhotoProfil($id, $chemin_relatif)) {
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