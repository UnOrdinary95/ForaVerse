<?php

final class BannissementDAO
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = PostgreSQLDB::getConnexion();
    }

    public function addBannissementCommuOneMonth(int $idModerateur, int $idUtilisateur, int $idCommunaute, string $raison): bool
    {
        $curdate = new DateTime();
        $curdate->modify('+1 month');
        $date_fin = $curdate->format('Y-m-d H:i:s.u');

        $query = $this->pdo->prepare("INSERT INTO bannissement (idModerateur, idUtilisateur, idCommunaute, raison, date_fin) VALUES (?, ?, ?, ?, ?)");
        return $query->execute([$idModerateur, $idUtilisateur, $idCommunaute, $raison, $date_fin]);
    }

    public function addBannissementCommuPermanent(int $idModerateur, int $idUtilisateur, int $idCommunaute, string $raison): bool
    {
        $query = $this->pdo->prepare("INSERT INTO bannissement (idModerateur, idUtilisateur, idCommunaute, raison, date_fin) VALUES (?, ?, ?, ?, ?)");
        return $query->execute([$idModerateur, $idUtilisateur, $idCommunaute, $raison, null]);
    }

    public function addBannissementGlobalOneMonth(int $idModerateur, int $idUtilisateur, string $raison): bool
    {
        $curdate = new DateTime();
        $curdate->modify('+1 month');
        $date_fin = $curdate->format('Y-m-d H:i:s.u');

        $query = $this->pdo->prepare("INSERT INTO bannissement (idModerateur, idUtilisateur, idCommunaute, raison, date_fin, est_global) VALUES (?, ?, ?, ?, ?, ?)");
        return $query->execute([$idModerateur, $idUtilisateur, null, $raison, $date_fin, true]);
    }

    public function addBannissementGlobalPermanent(int $idModerateur, int $idUtilisateur, string $raison): bool
    {
        $query = $this->pdo->prepare("INSERT INTO bannissement (idModerateur, idUtilisateur, idCommunaute, raison, date_fin, est_global) VALUES (?, ?, ?, ?, ?, ?)");
        return $query->execute([$idModerateur, $idUtilisateur, null, $raison, null, true]);
    }

    public function deleteBannissementById(int $idModeration): bool
    {
        $query = $this->pdo->prepare("DELETE FROM bannissement WHERE idModeration = ?");
        return $query->execute([$idModeration]);
    }

    public function getBannissementByIdUtilisateurAndCommunaute(int $idUtilisateur, int $idCommunaute): ?Bannissement
    {
        $query = $this->pdo->prepare("SELECT * FROM bannissement WHERE idUtilisateur = ? AND idCommunaute = ?");
        $query->execute([$idUtilisateur, $idCommunaute]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return null;
        }

        return new Bannissement(
            $result['idmoderation'],
            $result['idmoderateur'],
            $result['idutilisateur'],
            $result['date_debut'],
            $result['date_fin'],
            $result['est_global'],
            $result['idcommunaute'],
            $result['raison']
        );
    }

    public function getBannissementGlobalByIdUtilisateur(int $idUtilisateur): ?Bannissement
    {
        $query = $this->pdo->prepare("SELECT * FROM bannissement WHERE idUtilisateur = ? AND idCommunaute IS NULL AND est_global = true");
        $query->execute([$idUtilisateur]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return null;
        }

        return new Bannissement(
            $result['idmoderation'],
            $result['idmoderateur'],
            $result['idutilisateur'],
            $result['date_debut'],
            $result['date_fin'],
            true,
            null,

            $result['raison']
        );
    }

    public function getAllBannissementsByIdUtilisateur(int $idUtilisateur): array
    {
        $query = $this->pdo->prepare("SELECT * FROM bannissement WHERE idUtilisateur = ? ORDER BY idCommunaute");
        $query->execute([$idUtilisateur]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return [];
        }

        $bannissements = [];
        foreach ($result as $ligne) {
            $bannissements[] = new Bannissement(
                $ligne['idmoderation'],
                $ligne['idmoderateur'],
                $ligne['idutilisateur'],
                $ligne['date_debut'],
                $ligne['date_fin'],
                $ligne['est_global'],
                $ligne['idcommunaute'],

                $ligne['raison']
            );
        }

        return $bannissements;
    }

    public function getAllBannissementsByIdCommunaute(int $idCommunaute): array
    {
        $query = $this->pdo->prepare("SELECT * FROM bannissement WHERE idCommunaute = ? ORDER BY idUtilisateur, date_debut");
        $query->execute([$idCommunaute]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return [];
        }

        $bannissements = [];
        foreach ($result as $ligne) {
            $bannissements[] = new Bannissement(
                $ligne['idmoderation'],
                $ligne['idmoderateur'],
                $ligne['idutilisateur'],
                $ligne['date_debut'],
                $ligne['date_fin'],
                $ligne['est_global'],
                $ligne['idcommunaute'],
                $ligne['raison']
            );
        }

        return $bannissements;
    }
}