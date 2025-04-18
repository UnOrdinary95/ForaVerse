<?php

final class AvertissementDAO{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = PostgreSQLDB::getConnexion();
    }

    public function addAvertissement(int $idModerateur, int $idUtilisateur, int $idCommunaute, string $raison): bool
    {
        $query = $this->pdo->prepare("INSERT INTO avertissement (idModerateur, idUtilisateur, idCommunaute, raison) VALUES (?, ?, ?, ?)");
        return $query->execute([$idModerateur, $idUtilisateur, $idCommunaute, $raison]);
    }

    public function getAvertissementsByIdUtilisateurAndCommunaute(int $idUtilisateur, int $idCommunaute): array
    {
        $query = $this->pdo->prepare("SELECT * FROM avertissement WHERE idUtilisateur = ? AND idCommunaute = ?");
        $query->execute([$idUtilisateur, $idCommunaute]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!$result) {
            return [];
        }

        $avertissements = [];	
        foreach ($result as $ligne) {
            $avertissements[] = new Avertissement(
                $ligne['idModeration'],
                $ligne['idModerateur'],
                $ligne['idUtilisateur'],
                $ligne['idCommunaute'],
                $ligne['raison']
            );
        }

        return $avertissements;
    }

    public function deleteAvertissementById(int $idModeration): bool
    {
        $query = $this->pdo->prepare("DELETE FROM avertissement WHERE idModeration = ?");
        return $query->execute([$idModeration]);
    }
}