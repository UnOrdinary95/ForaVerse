<?php

final class Communaute{
    private ?int $id;
    private ?string $nom;
    private string $description;
    private bool $visibilite;
    private string $chemin_photo;

    private array $adhesions;
    
    public function __construct(
        ?int $unId = null,
        ?string $unNom = null,
        string $uneDescription = 'Pas de description.',
        string $unCheminPhoto = 'images/pp_commu/default.png',
        bool $uneVisibilite = true
    ){
        $this->id = $unId;
        $this->nom = $unNom;
        $this->description = $uneDescription;
        $this->visibilite = $uneVisibilite;
        $this->chemin_photo = $unCheminPhoto;
        if (!$this->visibilite){
            $adhesion_dao = new AdhesionDAO();
            $this->adhesions = $adhesion_dao->getAdhesionByCommunaute($this->id);
        }
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
    
    public function getAdhesions(): array
    {
        return $this->adhesions;
    }

}