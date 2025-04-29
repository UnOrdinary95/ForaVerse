<?php

final class CommentaireDAO
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = PostgreSQLDB::getConnexion();
    }

    public function addCommentaire(int $idDiscussion, int $idCommunaute, int $idUtilisateur, string $contenu): bool
    {
        $query = $this->pdo->prepare("INSERT INTO commentaire (idDiscussion, idCommunaute, idUtilisateur, contenu) VALUES (?, ?, ?, ?)");
        return $query->execute([$idDiscussion, $idCommunaute, $idUtilisateur, $contenu]);
    }

    public function getCommentairesByCommunauteAndDiscussionAndMotcle(int $idCommunaute, int $idDiscussion, string $motcle): array
    {
        $query = $this->pdo->prepare("SELECT * FROM commentaire WHERE idCommunaute = ? AND idDiscussion = ? AND contenu LIKE ?");
        $query->execute([$idCommunaute, $idDiscussion, "%$motcle%"]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!$result){
            return [];
        }

        $commentaires = [];
        foreach ($result as $ligne){
            $commentaires[] = new Commentaire(
                $ligne['iddiscussion'],
                $ligne['idpublication'],
                $ligne['idcommunaute'],
                $ligne['idutilisateur'],
                $ligne['contenu'],
                $ligne['datetime_creation'],
                $ligne['score'],
                $ligne['est_epingle'],
            );
        }
        return $commentaires;
    }

    // Récents
    public function getCommentairesByCommunauteAndDiscussionOrderByDatesDESC(int $idCommunaute, int $idDiscussion): array
    {
        $query = $this->pdo->prepare("SELECT * FROM commentaire WHERE idCommunaute = ? AND idDiscussion = ? ORDER BY est_epingle DESC, datetime_creation DESC");
        $query->execute([$idCommunaute, $idDiscussion]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!$result){
            return [];
        }

        $commentaires = [];
        foreach ($result as $ligne){
            $commentaires[] = new Commentaire(
                $ligne['iddiscussion'],
                $ligne['idpublication'],
                $ligne['idcommunaute'],
                $ligne['idutilisateur'],
                $ligne['contenu'],
                $ligne['datetime_creation'],
                $ligne['score'],
                $ligne['est_epingle'],
            );
        }
        return $commentaires;
    }

    // Anciens
    public function getCommentairesByCommunauteAndDiscussionOrderByDatesASC(int $idCommunaute, int $idDiscussion): array
    {
        $query = $this->pdo->prepare("SELECT * FROM commentaire WHERE idCommunaute = ? AND idDiscussion = ? ORDER BY est_epingle DESC, datetime_creation ASC");
        $query->execute([$idCommunaute, $idDiscussion]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!$result){
            return [];
        }

        $commentaires = [];
        foreach ($result as $ligne){
            $commentaires[] = new Commentaire(
                $ligne['iddiscussion'],
                $ligne['idpublication'],
                $ligne['idcommunaute'],
                $ligne['idutilisateur'],
                $ligne['contenu'],
                $ligne['datetime_creation'],
                $ligne['score'],
                $ligne['est_epingle'],
            );
        }
        return $commentaires;
    }

    // Upvotes
    public function getCommentairesByCommunauteAndDiscussionOrderByUpvotes(int $idCommunaute, int $idDiscussion): array
    {
        $req = "SELECT c.*, COUNT(CASE WHEN v.resultat = 1 THEN 1 ELSE NULL END) AS total_upvotes
                FROM commentaire c
                LEFT JOIN vote v ON c.idPublication = v.idPublication
                WHERE c.idCommunaute = ? AND c.idDiscussion = ?
                GROUP BY c.idPublication
                ORDER BY c.est_epingle DESC, total_upvotes DESC;";

        $query = $this->pdo->prepare($req);
        $query->execute([$idCommunaute, $idDiscussion]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!$result){
            return [];
        }

        $commentaires = [];
        foreach ($result as $ligne){
            $commentaires[] = new Commentaire(
                $ligne['iddiscussion'],
                $ligne['idpublication'],
                $ligne['idcommunaute'],
                $ligne['idutilisateur'],
                $ligne['contenu'],
                $ligne['datetime_creation'],
                $ligne['score'],
                $ligne['est_epingle'],
            );
        }
        return $commentaires;
    }

    // Downvotes
    public function getCommentairesByCommunauteAndDiscussionOrderByDownvotes(int $idCommunaute, int $idDiscussion): array
    {    
        $req = "SELECT c.*, COUNT(CASE WHEN v.resultat = -1 THEN 1 ELSE NULL END) AS total_downvotes
                FROM commentaire c
                LEFT JOIN vote v ON c.idPublication = v.idPublication
                WHERE c.idCommunaute = ? and c.idDiscussion = ?
                GROUP BY c.idPublication
                ORDER BY c.est_epingle DESC, total_downvotes DESC;";

        $query = $this->pdo->prepare($req);
        $query->execute([$idCommunaute, $idDiscussion]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!$result){
            return [];
        }

        $commentaires = [];
        foreach ($result as $ligne){
            $commentaires[] = new Commentaire(
                $ligne['iddiscussion'],
                $ligne['idpublication'],
                $ligne['idcommunaute'],
                $ligne['idutilisateur'],
                $ligne['contenu'],
                $ligne['datetime_creation'],
                $ligne['score'],
                $ligne['est_epingle'],
            );
        }
        return $commentaires;
    }

    

    public function getScoreById(int $idPublication): int
    {
        $query = $this->pdo->prepare("SELECT score FROM commentaire WHERE idPublication = ?");
        $query->execute([$idPublication]);
        return $query->fetchColumn();
    }

    public function estEpingle(int $idPublication): bool
    {
        $query = $this->pdo->prepare("SELECT est_epingle FROM commentaire WHERE idPublication = ?");
        $query->execute([$idPublication]);
        return (bool)$query->fetchColumn();
    }

    public function updateEpingle(int $idPublication, bool $estEpingle): bool
    {
        // PDO ne supporte pas les booléens, donc on les convertit en string
        $estEpingle = $estEpingle ? 'true' : 'false';

        $query = $this->pdo->prepare("UPDATE commentaire SET est_epingle = ? WHERE idPublication = ?");
        return $query->execute([$estEpingle, $idPublication]);
    }

    public function getCommentaireById(int $idPublication): ?Commentaire
    {
        $query = $this->pdo->prepare("SELECT * FROM commentaire WHERE idPublication = ?");
        $query->execute([$idPublication]);
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if (!$result){
            return null;
        }

        return new Commentaire(
            $result['iddiscussion'],
            $result['idpublication'],
            $result['idcommunaute'],
            $result['idutilisateur'],
            $result['contenu'],
            $result['datetime_creation'],
            $result['score'],
            $result['est_epingle'],
        );
    }

    public function deleteCommentaire(int $idPublication): bool
    {
        $query = $this->pdo->prepare("DELETE FROM commentaire WHERE idPublication = ?");
        return $query->execute([$idPublication]);
    }
}