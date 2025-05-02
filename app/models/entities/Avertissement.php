<?php

final class Avertissement
{
    private int $id;
    private int $idModerateur;
    private int $idUtilisateur;
    private int $idCommunaute;
    private string $dateDebut;
    private string $raison;

    public function __construct(
        int $unId,
        int $unIdModerateur,
        int $unIdUtilisateur,
        int $unIdCommunaute,
        string $uneDateDebut,
        string $uneRaison = '',
    ) {
        $this->id = $unId;
        $this->idModerateur = $unIdModerateur;
        $this->idUtilisateur = $unIdUtilisateur;
        $this->idCommunaute = $unIdCommunaute;
        $this->raison = $uneRaison;
        $this->dateDebut = $uneDateDebut;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdModerateur(): ?int
    {
        return $this->idModerateur;
    }

    public function getIdUtilisateur(): ?int
    {
        return $this->idUtilisateur;
    }

    public function getIdCommunaute(): ?int
    {
        return $this->idCommunaute;
    }

    public function getRaison(): string
    {
        return $this->raison;
    }

    public function getDateDebut(): string
    {
        $datetime = new DateTime($this->dateDebut);
        $datetime->modify('+2 hours');
        return $datetime->format('d/m/Y');
    }

    public function getUtilisateur(): Utilisateur
    {
        return (new UtilisateurDAO())->getProfilUtilisateurById($this->idUtilisateur);
    }
}