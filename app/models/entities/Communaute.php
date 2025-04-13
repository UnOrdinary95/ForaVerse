<?php

final class Communaute{
    private ?int $id;
    private ?string $nom;
    private ?string $description;
    private ?bool $visibilite;
    private ?string $chemin_photo;

    public function __construct(
        ?int $unId = null,
        ?string $unNom = null,
        ?string $uneDescription = 'Pas de description.',
        ?string $unCheminPhoto = null,
        ?bool $uneVisibilite = true
    ){
        $this->id = $unId;
        $this->nom = $unNom;
        $this->description = $uneDescription;
        $this->visibilite = $uneVisibilite;
        $this->chemin_photo = $unCheminPhoto;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getVisibilite(): ?bool
    {
        return $this->visibilite;
    }

    public function getCheminPhoto(): ?string
    {
        return $this->chemin_photo;
    }
}