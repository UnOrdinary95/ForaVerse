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

    public function getNomById($id): string
    {
        $query = $this->pdo->prepare("SELECT nom FROM communaute WHERE idcommunaute = ?");
        $query->execute([$id]);
        
        return $query->fetchColumn();
    }

    public function getCommunauteById(int $id): ?Communaute
    {
        $query = $this->pdo->prepare("SELECT * FROM communaute WHERE idCommunaute = ?");
        $query->execute([$id]);
        
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return null;
        }
        
        return new Communaute(
            $result['idcommunaute'],
            $result['nom'],
            $result['description'],
            $result['chemin_photo'],
            $result['visibilite']
        );
    }

    public function getCommunautesByIdUtilisateur(int $idUtilisateur): array
    {
        $query = $this->pdo->prepare("SELECT * FROM communaute WHERE idcommunaute IN (SELECT idcommunaute FROM abonnement WHERE idutilisateur = ?)");
        $query->execute([$idUtilisateur]);
        
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!$result) {
            return [];
        }
        
        $communautes = [];
        foreach ($result as $ligne) {
            $communautes[] = new Communaute(
                $ligne['idcommunaute'],
                $ligne['nom'],
                $ligne['description'],
                $ligne['chemin_photo'],
                $ligne['visibilite']
            );
        }
        
        return $communautes;
    }

    public function getCommunautes(): array
    {
        $query = $this->pdo->prepare("SELECT * FROM communaute");
        $query->execute();
        
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!$result) {
            return [];
        }
        
        $communautes = [];
        foreach ($result as $ligne) {
            $communautes[] = new Communaute(
                $ligne['idcommunaute'],
                $ligne['nom'],
                $ligne['description'],
                $ligne['chemin_photo'],
                $ligne['visibilite']
            );
        }
        
        return $communautes;
    }
    
    public function updatePhotoProfil(int $id, string $chemin_photo): bool
    {
        $query = $this->pdo->prepare("UPDATE communaute SET chemin_photo = ? WHERE idcommunaute = ?");
        return $query->execute([$chemin_photo, $id]);
    }

    public function updateNom(int $id, string $nom): bool
    {
        $query = $this->pdo->prepare("UPDATE communaute SET nom = ? WHERE idcommunaute = ?");
        return $query->execute([$nom, $id]);
    }

    public function existeCommunaute(string $nom): bool | int
    {
        $query = $this->pdo->prepare("SELECT idcommunaute FROM communaute WHERE nom = ?");
        $query->execute([$nom]);
        return $query->fetchColumn();
    }

    public function deleteCommunaute(int $id): bool
    {
        $query = $this->pdo->prepare("DELETE FROM communaute WHERE idcommunaute = ?");
        return $query->execute([$id]);
    }
}