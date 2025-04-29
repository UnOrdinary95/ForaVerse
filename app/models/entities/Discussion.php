<?php

class Discussion
{
    private int $idPublication;
    private int $idCommunaute;
    private int $idUtilisateur;
    private string $contenu;
    private string $dateCreation;
    private int $score;
    private bool $estEpingle;
    private string $titre;

    private ?int $vote_utilisateur_courant;

    public function __construct(
        int $idPublication,
        int $idCommunaute,
        int $idUtilisateur,
        string $contenu,
        string $dateCreation,
        int $score,
        bool $estEpingle,
        string $titre
    ) {
        $this->idPublication = $idPublication;
        $this->idCommunaute = $idCommunaute;
        $this->idUtilisateur = $idUtilisateur;
        $this->contenu = $contenu;
        $this->dateCreation = $dateCreation;
        $this->score = $score;
        $this->estEpingle = $estEpingle;
        $this->titre = $titre;
        if (isset($_SESSION['Pseudo'])) {
            $this->vote_utilisateur_courant = (new VoteDAO())->getVote($idPublication, (new UtilisateurDAO())->getIdByPseudo($_SESSION['Pseudo']));
        } else {
            $this->vote_utilisateur_courant = null;
        }
    }

    public function getIdPublication(): int
    {
        return $this->idPublication;
    }

    public function getIdCommunaute(): int
    {
        return $this->idCommunaute;
    }

    public function getIdUtilisateur(): int
    {
        return $this->idUtilisateur;
    }

    public function getContenu(): string
    {
        return $this->contenu;
    }

    public function getDateCreation(): string
    {
        return $this->dateCreation;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function estEpingle(): bool
    {
        return $this->estEpingle;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function getUtilisateur(): Utilisateur
    {
        $utilisateurDAO = new UtilisateurDAO();
        return (new UtilisateurDAO())->getProfilUtilisateurById($this->idUtilisateur);
    }

    public function getRoleUtilisateur(): Role
    {
        return (new RoleDAO())->getRole($this->idUtilisateur, $this->idCommunaute);
    }

    public function getVoteUtilisateurCourant(): ?int
    {
        return $this->vote_utilisateur_courant;
    }

    public function getCommunaute(): ?Communaute
    {
        return (new CommunauteDAO())->getCommunauteById($this->idCommunaute);
    }

    public function estFavoris(): bool
    {
        if (isset($_SESSION['Pseudo'])) {
            $favorisDAO = new FavorisDAO();
            return $favorisDAO->estFavoris($this->idPublication, (new UtilisateurDAO())->getIdByPseudo($_SESSION['Pseudo']));
        }
        return false;
    }
}