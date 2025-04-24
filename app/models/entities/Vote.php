<?php

class Vote
{
    private int $idPublication;
    private int $idUtilisateur;
    private int $resultat;
    private string $typePublication;	

    public function __construct(int $idPublication, int $idUtilisateur, int $resultat, string $typePublication)
    {
        $this->idPublication = $idPublication;
        $this->idUtilisateur = $idUtilisateur;
        $this->resultat = $resultat;
        $this->typePublication = $typePublication;
    }

    public function getIdPublication(): int
    {
        return $this->idPublication;
    }

    public function getIdUtilisateur(): int
    {
        return $this->idUtilisateur;
    }

    public function getResultat(): int
    {
        return $this->resultat;
    }

    public function getTypePublication(): string
    {
        return $this->typePublication;
    }
}