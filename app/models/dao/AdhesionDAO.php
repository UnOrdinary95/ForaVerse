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
        $query = $this->pdo->prepare("SELECT * FROM DemandeAdhesion WHERE idUtilisateur = ? AND idCommunaute = ?");
        $query->execute([$idUtilisateur, $idCommunaute]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return null;
        }

        return new Adhesion(
            $idUtilisateur,
            $idCommunaute,
            $result['statut'],
            $result['datetime_demande']
        );
    }

    public function getRefusByCommunaute(int $idCommunaute): array
    {
        $query = $this->pdo->prepare("SELECT * FROM DemandeAdhesion WHERE idCommunaute = ? AND statut = ?");
        $query->execute([$idCommunaute, Adhesion::REFUSEE]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return [];
        }

        $adhesions = [];
        foreach ($result as $ligne){
            $adhesions[] = new Adhesion(
                $ligne['idutilisateur'],
                $idCommunaute,
                $ligne['statut'],
                $ligne['datetime_demande']
            );
        }

        return $adhesions;
    }

    public function getAttentesByCommunaute(int $idCommunaute): array
    {
        $query = $this->pdo->prepare("SELECT * FROM DemandeAdhesion WHERE idCommunaute = ? AND statut = 'en attente'");
        $query->execute([$idCommunaute]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return [];
        }

        $adhesions = [];
        foreach ($result as $ligne){
            $adhesions[] = new Adhesion(
                $ligne['idutilisateur'],
                $idCommunaute,
                $ligne['statut'],
                $ligne['datetime_demande']
            );
        }

        return $adhesions;
    }


    public function addAdhesion(int $idUtilisateur, int $idCommunaute): bool
    {
        $query = $this->pdo->prepare("INSERT INTO DemandeAdhesion (idUtilisateur, idCommunaute) VALUES (?, ?)");
        return $query->execute([$idUtilisateur, $idCommunaute]);
    }

    public function deleteAdhesion(int $idUtilisateur, int $idCommunaute): bool
    {
        $query = $this->pdo->prepare("DELETE FROM DemandeAdhesion WHERE idUtilisateur = ? AND idCommunaute = ?");
        return $query->execute([$idUtilisateur, $idCommunaute]);
    }

    public function acceptAdhesion(int $idUtilisateur, int $idCommunaute): bool
    {
        $query = $this->pdo->prepare("UPDATE DemandeAdhesion SET statut = 'acceptée' WHERE idUtilisateur = ? AND idCommunaute = ?");
        return $query->execute([$idUtilisateur, $idCommunaute]);
    }

    public function rejectAdhesion(int $idUtilisateur, int $idCommunaute): bool
    {
        $query = $this->pdo->prepare("UPDATE DemandeAdhesion SET statut = 'refusée' WHERE idUtilisateur = ? AND idCommunaute = ?");
        return $query->execute([$idUtilisateur, $idCommunaute]);
    }
}