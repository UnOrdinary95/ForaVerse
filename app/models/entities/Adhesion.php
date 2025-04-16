<?php

final class Adhesion
{
    private int $idUtilisateur;
    private int $idCommunaute;
    private string $statut;

    public function __construct(int $idUtilisateur, int $idCommunaute, string $statut)
    {
        $this->idUtilisateur = $idUtilisateur;
        $this->idCommunaute = $idCommunaute;
        $this->statut = $statut;
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
}