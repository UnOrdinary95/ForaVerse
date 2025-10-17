<?php

class VoteDAO
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = PostgreSQLDB::getConnexion();
    }

    public function addVote(int $idPublication, int $idUtilisateur, int $resultat, string $type_publication): bool
    {
        $query = $this->pdo->prepare("INSERT INTO vote (idPublication, idUtilisateur, resultat, type_publication) VALUES (?, ?, ?, ?)");
        return $query->execute([$idPublication, $idUtilisateur, $resultat, $type_publication]);
    }

    public function updateVote(int $idPublication, int $idUtilisateur, int $resultat): bool
    {
        $query = $this->pdo->prepare("UPDATE vote SET resultat = ? WHERE idPublication = ? AND idUtilisateur = ?");
        return $query->execute([$resultat, $idPublication, $idUtilisateur]);
    }

    public function getVote(int $idPublication, int $idUtilisateur): int | false
    {
        $query = $this->pdo->prepare("SELECT resultat FROM vote WHERE idPublication = ? AND idUtilisateur = ?");
        $query->execute([$idPublication, $idUtilisateur]);
        return $query->fetchColumn();
    }
}