<?php

final class CommunauteDAO
{
    private PDO $pdo;
    
    public function __construct()
    {
        $this->pdo = PostgreSQLDB::getConnexion();
    }

    public function addCommunaute(string $nom, ?string $description, ?string $cheminPhoto, ?bool $visibilite): bool
    {
        // On créer une requête dynamiquement en omettant les colonnes nulles
        $colonnes = ['nom'];
        $valeurs = ['?'];
        $args = [$nom];

        if (!is_null($description)) {
            $colonnes[] = 'description';
            $valeurs[] = '?';
            $args[] = $description;
        }

        if (!is_null($cheminPhoto)) {
            $colonnes[] = 'chemin_photo';
            $valeurs[] = '?';
            $args[] = $cheminPhoto;
        }
        
        if (!is_null($visibilite)) {
            $colonnes[] = 'visibilite';
            $valeurs[] = '?';
            $args[] = $visibilite;
        }

        $col_str = implode(', ', $colonnes);
        $val_str = implode(', ', $valeurs);

        $query = $this->pdo->prepare("INSERT INTO communaute ($col_str) VALUES ($val_str)");
        return $query->execute($args);
    }
    
}