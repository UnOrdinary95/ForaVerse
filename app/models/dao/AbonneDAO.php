<?php

final class AbonneDAO
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = PostgreSQLDB::getConnexion();
    }

    public function getNbrAbonnesById(int $id): int
    {
        $query = $this->pdo->prepare("SELECT COUNT(*) FROM abonne WHERE idutilisateur = ?");
        $query->execute([$id]);

        return $query->fetchColumn();
    }

    public function getNbrAbonnementsById(int $id): int
    {
        $query = $this->pdo->prepare("SELECT COUNT(*) FROM abonne WHERE idAbonne = ?");
        $query->execute([$id]);

        return $query->fetchColumn();
    }

    public function getAbonnesByUtilisateur(int $id): array
    {
        $query = $this->pdo->prepare("SELECT idAbonne FROM abonne WHERE idUtilisateur = ?");
        $query->execute([$id]);

        return array_column($query->fetchAll(PDO::FETCH_NUM), 0);
    }

    public function getAbonnementsByUtilisateur(int $id): array
    {
        $query = $this->pdo->prepare("SELECT idUtilisateur FROM abonne WHERE idAbonne = ?");
        $query->execute([$id]);

        return array_column($query->fetchAll(PDO::FETCH_NUM), 0);
    }

    
    public function estAbonne(int $idAbonne, int $idUtilisateur): bool
    {
        $query = $this->pdo->prepare("SELECT COUNT(*) FROM abonne WHERE idAbonne = ? AND idUtilisateur = ?");
        $query->execute([$idAbonne, $idUtilisateur]);

        return $query->fetchColumn() > 0;
    }

    public function addAbonnement(int $idAbonne, int $idUtilisateur): void
    {
        $query = $this->pdo->prepare("INSERT INTO abonne (idAbonne, idUtilisateur) VALUES (?, ?)");
        $query->execute([$idAbonne, $idUtilisateur]);
    }

    public function deleteAbonnement(int $idAbonne, int $idUtilisateur): void
    {
        $query = $this->pdo->prepare("DELETE FROM abonne WHERE idAbonne = ? AND idUtilisateur = ?");
        $query->execute([$idAbonne, $idUtilisateur]);
    }
}
