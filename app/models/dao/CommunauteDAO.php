<?php

final class CommunauteDAO
{
    private PDO $pdo;
    
    public function __construct()
    {
        $this->pdo = PostgreSQLDB::getConnexion();
    }

    public function addCommunaute(string $nom, ?string $description, bool $visibilite): bool
    {
        // On crée une requête dynamiquement en omettant les colonnes nulles
        $colonnes = ['nom', 'visibilite'];
        $valeurs = ['?', '?'];
        
        // Convertir le booléen $visibilite en une valeur que PostgreSQL peut accepter
        // PostgreSQL accepte 'true'/'false' (chaines) ou 1/0 (entiers) pour les booléens
        $args = [$nom, $visibilite ? 'true' : 'false'];

        if (!is_null($description)) {
            $colonnes[] = 'description';
            $valeurs[] = '?';
            $args[] = $description;
        }
        
        $col_str = implode(', ', $colonnes);
        $val_str = implode(', ', $valeurs);

        $query = $this->pdo->prepare("INSERT INTO communaute ($col_str) VALUES ($val_str)");
        return $query->execute($args);
    }
    
    public function getNomsCommunautes(): array
    {
        $query = $this->pdo->prepare("SELECT nom FROM communaute");
        $query->execute();
        
        return array_column($query->fetchAll(PDO::FETCH_NUM), 0);
    }

    public function getIdByNom(string $nom): int
    {
        $query = $this->pdo->prepare("SELECT idcommunaute FROM communaute WHERE nom = ?");
        $query->execute([$nom]);
        
        return $query->fetchColumn();
    }

    public function getCommunauteById(int $id): Communaute
    {
        $query = $this->pdo->prepare("SELECT * FROM communaute WHERE idCommunaute = ?");
        $query->execute([$id]);
        
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            throw new Exception("Communauté introuvable");
        }
        
        return new Communaute(
            $result['idcommunaute'],
            $result['nom'],
            $result['description'],
            $result['chemin_photo'],
            $result['visibilite']
        );
    }
    
    public function updatePhotoProfil(int $id, string $chemin_photo): bool
    {
        $query = $this->pdo->prepare("UPDATE communaute SET chemin_photo = ? WHERE idcommunaute = ?");
        return $query->execute([$chemin_photo, $id]);
    }
}