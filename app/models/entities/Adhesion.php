<?php

final class Adhesion
{
    private int $idUtilisateur;
    private int $idCommunaute;
    private string $statut;

    private string $date_demande;

    const EN_ATTENTE = 'en attente';
    const ACCEPTEE = 'acceptée';
    const REFUSEE = 'refusée';

    public function __construct(int $idUtilisateur, int $idCommunaute, string $statut, string $date_demande)
    {
        $this->idUtilisateur = $idUtilisateur;
        $this->idCommunaute = $idCommunaute;
        $this->statut = $statut;
        $this->date_demande = $date_demande;
    }

    public function getIdUtilisateur(): int
    {
        return $this->idUtilisateur;
    }

    public function getIdCommunaute(): int
    {
        return $this->idCommunaute;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function getDateDemande(): string
    {
        return $this->date_demande;
    }
}