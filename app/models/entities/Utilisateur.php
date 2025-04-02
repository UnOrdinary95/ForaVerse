<?php

final class Utilisateur
{
    private ?int $id;
    private ?string $pseudo;
    private ?string $email;
    private ?string $mdp;
    private ?string $chemin_photo;
    private ?string $bio;
    private ?DateTime $date_inscription;
    private bool $est_admin;

    public function __construct(
        string $unPseudo,
        string $unEmail,
        string $unMdp,
        int $unId = null,
        string $unCheminPhoto = null,
        string $uneBio = 'Pas de bio.',
        DateTime $uneDateInscription = null,
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



}