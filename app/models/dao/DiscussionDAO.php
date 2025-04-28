<?php

final class DiscussionDAO
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = PostgreSQLDB::getConnexion();
    }

    public function addDiscussion(int $idCommunaute, int $idUtilisateur, string $titre, string $contenu): bool
    {
        $query = $this->pdo->prepare("INSERT INTO discussion (idCommunaute, idUtilisateur, titre, contenu) VALUES (?, ?, ?, ?)");
        $query->execute([$idCommunaute, $idUtilisateur, $titre, $contenu]);
        
        return (int)$this->pdo->lastInsertId('publication_id_seq');
    }

    public function getDiscussionsByCommunauteAndMotcle(int $idCommunaute, string $motcle): array
    {
        $query = $this->pdo->prepare("SELECT * FROM discussion WHERE idCommunaute = ? AND contenu LIKE ?
        UNION ALL
        SELECT * FROM discussion WHERE idCommunaute = ? AND titre LIKE ?");
        $query->execute([$idCommunaute, "%$motcle%", $idCommunaute, "%$motcle%"]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!$result){
            return [];
        }

        $discussions = [];
        foreach ($result as $ligne){
            $discussions[] = new Discussion(
                $ligne['idpublication'],
                $ligne['idcommunaute'],
                $ligne['idutilisateur'],
                $ligne['contenu'],
                $ligne['datetime_creation'],
                $ligne['score'],
                $ligne['est_epingle'],
                $ligne['titre']
            );
        }
        return $discussions;
    }

    // Récents
    public function getDiscussionsByCommunauteOrderByDatesDESC(int $idCommunaute): array
    {
        $query = $this->pdo->prepare("SELECT * FROM discussion WHERE idCommunaute = ? ORDER BY est_epingle DESC, datetime_creation DESC");
        $query->execute([$idCommunaute]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!$result){
            return [];
        }

        $discussions = [];
        foreach ($result as $ligne){
            $discussions[] = new Discussion(
                $ligne['idpublication'],
                $ligne['idcommunaute'],
                $ligne['idutilisateur'],
                $ligne['contenu'],
                $ligne['datetime_creation'],
                $ligne['score'],
                $ligne['est_epingle'],
                $ligne['titre']
            );
        }
        return $discussions;
    }

    // Anciens
    public function getDiscussionsByCommunauteOrderByDatesASC(int $idCommunaute): array
    {
        $query = $this->pdo->prepare("SELECT * FROM discussion WHERE idCommunaute = ? ORDER BY est_epingle DESC, datetime_creation ASC");
        $query->execute([$idCommunaute]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!$result){
            return [];
        }

        $discussions = [];
        foreach ($result as $ligne){
            $discussions[] = new Discussion(
                $ligne['idpublication'],
                $ligne['idcommunaute'],
                $ligne['idutilisateur'],
                $ligne['contenu'],
                $ligne['datetime_creation'],
                $ligne['score'],
                $ligne['est_epingle'],
                $ligne['titre']
            );
        }
        return $discussions;
    }

    // Upvotes
    public function getDiscussionsByCommunauteOrderByUpvotes(int $idCommunaute): array
    {
        $req = "SELECT c.*, COUNT(CASE WHEN v.resultat = 1 THEN 1 ELSE NULL END) AS total_upvotes
                FROM discussion c
                LEFT JOIN vote v ON c.idPublication = v.idPublication
                WHERE c.idCommunaute = ?
                GROUP BY c.idPublication
                ORDER BY c.est_epingle DESC, total_upvotes DESC;";

        $query = $this->pdo->prepare($req);
        $query->execute([$idCommunaute]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!$result){
            return [];
        }

        $discussions = [];
        foreach ($result as $ligne){
            $discussions[] = new Discussion(
                $ligne['idpublication'],
                $ligne['idcommunaute'],
                $ligne['idutilisateur'],
                $ligne['contenu'],
                $ligne['datetime_creation'],
                $ligne['score'],
                $ligne['est_epingle'],
                $ligne['titre'],
            );
        }
        return $discussions;
    }

    // Downvotes
    public function getDiscussionsByCommunauteOrderByDownvotes(int $idCommunaute): array
    {    
        $req = "SELECT c.*, COUNT(CASE WHEN v.resultat = -1 THEN 1 ELSE NULL END) AS total_downvotes
                FROM discussion c
                LEFT JOIN vote v ON c.idPublication = v.idPublication
                WHERE c.idCommunaute = ?
                GROUP BY c.idPublication
                ORDER BY c.est_epingle DESC, total_downvotes DESC;";

        $query = $this->pdo->prepare($req);
        $query->execute([$idCommunaute]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!$result){
            return [];
        }

        $discussions = [];
        foreach ($result as $ligne){
            $discussions[] = new Discussion(
                $ligne['idpublication'],
                $ligne['idcommunaute'],
                $ligne['idutilisateur'],
                $ligne['contenu'],
                $ligne['datetime_creation'],
                $ligne['score'],
                $ligne['est_epingle'],
                $ligne['titre'],
            );
        }
        return $discussions;
    }



    public function getScoreById(int $idPublication): int
    {
        $query = $this->pdo->prepare("SELECT score FROM discussion WHERE idPublication = ?");
        $query->execute([$idPublication]);
        return $query->fetchColumn();
    }

    public function estEpingle(int $idPublication): bool
    {
        $query = $this->pdo->prepare("SELECT est_epingle FROM discussion WHERE idPublication = ?");
        $query->execute([$idPublication]);
        return (bool)$query->fetchColumn();
    }

    public function updateEpingle(int $idPublication, bool $estEpingle): bool
    {
        // PDO ne supporte pas les booléens, donc on les convertit en string
        $estEpingle = $estEpingle ? 'true' : 'false';

        $query = $this->pdo->prepare("UPDATE discussion SET est_epingle = ? WHERE idPublication = ?");
        return $query->execute([$estEpingle, $idPublication]);
    }

    public function getDiscussionById(int $idPublication): ?Discussion
    {
        $query = $this->pdo->prepare("SELECT * FROM discussion WHERE idPublication = ?");
        $query->execute([$idPublication]);
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if (!$result){
            return null;
        }

        return new Discussion(
            $result['idpublication'],
            $result['idcommunaute'],
            $result['idutilisateur'],
            $result['contenu'],
            $result['datetime_creation'],
            $result['score'],
            $result['est_epingle'],
            $result['titre']
        );
    }
}