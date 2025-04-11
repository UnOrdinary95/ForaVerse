<?php

final class Utilisateur
{
    private ?int $id;
    private ?string $pseudo;
    private ?string $email;
    private ?string $mdp;
    private ?string $chemin_photo;
    private ?string $bio;
    private ?string $date_inscription;
    private bool $est_admin;

    public function __construct(
        ?string $unPseudo = null,
        ?string $unEmail = null,
        ?string $unMdp = null,
        ?int $unId = null,
        ?string $unCheminPhoto = null,
        string $uneBio = 'Pas de bio.',
        ?string $uneDateInscription = null,
        bool $unEstAdmin = false
    ){
        $this->id = $unId;
        $this->pseudo = $unPseudo;
        $this->email = $unEmail;
        $this->mdp = $unMdp;
        $this->chemin_photo = $unCheminPhoto;
        $this->bio = $uneBio;
        $this->date_inscription = $uneDateInscription;
        $this->est_admin = $unEstAdmin;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function getCheminPhoto(): ?string
    {
        return $this->chemin_photo;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function getDateInscription(): ?string
    {
        return $this->date_inscription;
    }

    public function getEstAdmin(): bool
    {
        return $this->est_admin;
    }
}
