<?php

final class AdhesionDAO
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = PostgreSQLDB::getConnexion();
    }
    
    public function getAdhesionById(int $idUtilisateur, int $idCommunaute): ?Adhesion
    {
        $query = $this->pdo->prepare("SELECT * FROM adhesion WHERE idUtilisateur = ? AND idCommunaute = ?");
        $query->execute([$idUtilisateur, $idCommunaute]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return null;
        }

        return new Adhesion(
            $idUtilisateur,
            $idCommunaute,
            $result['statut']
        );
    }

    public function getAdhesionByCommunaute(int $idCommunaute): array
    {
        $query = $this->pdo->prepare("SELECT * FROM adhesion WHERE idCommunaute = ?");
        $query->execute([$idCommunaute]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return [];
        }

        $adhesions = [];
        foreach ($result as $ligne){
            $adhesions[] = new Adhesion(
                $ligne['idUtilisateur'],
                $idCommunaute,
                $ligne['statut']
            );
        }

        return $adhesions;
    }

    public function addAdhesion(int $idUtilisateur, int $idCommunaute): bool
    {
        $query = $this->pdo->prepare("INSERT INTO adhesion (idUtilisateur, idCommunaute) VALUES (?, ?)");
        return $query->execute([$idUtilisateur, $idCommunaute]);
    }

    public function deleteAdhesion(int $idUtilisateur, int $idCommunaute): bool
    {
        $query = $this->pdo->prepare("DELETE FROM adhesion WHERE idUtilisateur = ? AND idCommunaute = ?");
        return $query->execute([$idUtilisateur, $idCommunaute]);
    }

    public function acceptAdhesion(int $idUtilisateur, int $idCommunaute): bool
    {
        $query = $this->pdo->prepare("UPDATE adhesion SET statut = 'accepté' WHERE idUtilisateur = ? AND idCommunaute = ?");
        return $query->execute([$idUtilisateur, $idCommunaute]);
    }

    public function rejectAdhesion(int $idUtilisateur, int $idCommunaute): bool
    {
        $query = $this->pdo->prepare("UPDATE adhesion SET statut = 'rejeté' WHERE idUtilisateur = ? AND idCommunaute = ?");
        return $query->execute([$idUtilisateur, $idCommunaute]);
    }
}