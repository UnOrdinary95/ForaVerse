<?php

class Commentaire
{
    private int $idDiscussion;
    private int $idPublication;
    private int $idCommunaute;
    private int $idUtilisateur;
    private string $contenu;
    private string $dateCreation;
    private int $score;
    private bool $estEpingle;
    private ?int $vote_utilisateur_courant;

    public function __construct(
        int $idDiscussion,
        int $idPublication,
        int $idCommunaute,
        int $idUtilisateur,
        string $contenu,
        string $dateCreation,
        int $score,
        bool $estEpingle
    ) {
        $this->idDiscussion = $idDiscussion;
        $this->idPublication = $idPublication;
        $this->idCommunaute = $idCommunaute;
        $this->idUtilisateur = $idUtilisateur;
        $this->contenu = $contenu;
        $this->dateCreation = $dateCreation;
        $this->score = $score;
        $this->estEpingle = $estEpingle;
        if (isset($_SESSION['Pseudo'])) {
            $this->vote_utilisateur_courant = (new VoteDAO())->getVote($idPublication, (new UtilisateurDAO())->getIdByPseudo($_SESSION['Pseudo']));
        } else {
            $this->vote_utilisateur_courant = null;
        }
    }

    public function getIdDiscussion(): int
    {
        return $this->idDiscussion;
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

    public function getDateCreationFormatee(): string
    {
        $datetime = new DateTime($this->dateCreation);
        $datetime->modify('+2 hours');
        return $datetime->format('d/m/Y');
    }

    public function getHeureCreationFormatee(): string
    {
        $datetime = new DateTime($this->dateCreation);
        $datetime->modify('+2 hours');
        return $datetime->format('H:i');
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function estEpingle(): bool
    {
        return $this->estEpingle;
    }

    public function estuneDiscussion(): ?Discussion
    {
        return (new DiscussionDAO())->getDiscussionById($this->idDiscussion);
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

    public function estFavoris(): bool
    {
        if (isset($_SESSION['Pseudo'])) {
            $favorisDAO = new FavorisDAO();
            return $favorisDAO->estFavoris($this->idPublication, (new UtilisateurDAO())->getIdByPseudo($_SESSION['Pseudo']));
        }
        return false;
    }
}