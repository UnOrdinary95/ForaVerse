<?php

class Favoris
{
    private int $idPublication;
    private int $idUtilisateur;
    private string $dateAjout;
    private string $type_publication;

    public function __construct(int $idPublication, int $idUtilisateur, string $dateAjout, string $type_publication)
    {
        $this->idPublication = $idPublication;
        $this->idUtilisateur = $idUtilisateur;
        $this->dateAjout = $dateAjout;
        $this->type_publication = $type_publication;
    }

    public function getIdPublication(): int
    {
        return $this->idPublication;
    }

    public function getIdUtilisateur(): int
    {
        return $this->idUtilisateur;
    }

    public function getDateAjout(): string
    {
        return $this->dateAjout;
    }

    public function getTypePublication(): string
    {
        return $this->type_publication;
    }
}