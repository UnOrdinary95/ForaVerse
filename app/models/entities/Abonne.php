<?php

final class Abonne
{
    private int $utilisateur_id;
    private array $abonnes;
    private array $abonnements;
    
    public function __construct(int $utilisateur_id)
    {
        $this->utilisateur_id = $utilisateur_id;
        $abonne_dao = new AbonneDAO();
        $this->abonnes = $abonne_dao->getAbonnesByUtilisateur($utilisateur_id);
        $this->abonnements = $abonne_dao->getAbonnementsByUtilisateur($utilisateur_id);
    }

    public function getUtilisateurId(): int
    {
        return $this->utilisateur_id;
    }

    public function getAbonnes(): array
    {
        return $this->abonnes;
    }

    public function getAbonnements(): array
    {
        return $this->abonnements;
    }
    public function estAbonne(int $idAbonne): bool
    {
        return in_array($idAbonne, $this->abonnes);
    }
}