<?php

class FavorisDAO
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = PostgreSQLDB::getConnexion();
    }

    public function getFavorisByIdUtilisateur(int $idUtilisateur): array
    {
        $query = $this->pdo->prepare("SELECT * FROM favoris WHERE idUtilisateur = ?");
        $query->execute([$idUtilisateur]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!$result) {
            return [];
        }

        $favoris = [];
        foreach ($result as $ligne) {
            $favoris[] = new Favoris(
                $ligne['idpublication'],
                $ligne['idutilisateur'],
                $ligne['datetime_ajout'],
                $ligne['type_publication']
            );
        }
        return $favoris;
    }

    public function estFavoris(int $idPublication, int $idUtilisateur): bool
    {
        $query = $this->pdo->prepare("SELECT * FROM favoris WHERE idPublication = ? AND idUtilisateur = ?");
        $query->execute([$idPublication, $idUtilisateur]);
        return (bool)$query->fetchColumn();
    }

    public function addFavoris(int $idPublication, int $idUtilisateur, string $type_publication): bool
    {
        $query = $this->pdo->prepare("INSERT INTO favoris (idPublication, idUtilisateur, type_publication) VALUES (?, ?, ?)");
        return $query->execute([$idPublication, $idUtilisateur, $type_publication]);
    }

    public function deleteFavoris(int $idPublication, int $idUtilisateur): bool
    {
        $query = $this->pdo->prepare("DELETE FROM favoris WHERE idPublication = ? AND idUtilisateur = ?");
        return $query->execute([$idPublication, $idUtilisateur]);
    }
}