<?php

final class Role
{
    private int $utilisateur_id;
    private int $communaute_id;
    private string $role;
    private UtilisateurDAO $utilisateur_dao;
    private CommunauteDAO $communaute_dao;
    private Utilisateur $utilisateur;
    private Communaute $communaute;

    const PROPRIETAIRE = 'propriétaire';
    const MODERATEUR = 'modérateur';
    const MEMBRE = 'membre';

    public function __construct(int $utilisateur_id, int $communaute_id, string $role)
    {
        $this->utilisateur_id = $utilisateur_id;
        $this->communaute_id = $communaute_id;
        $this->role = $role;
        $this->utilisateur_dao = new UtilisateurDAO();
        $this->communaute_dao = new CommunauteDAO();
        $this->utilisateur = $this->utilisateur_dao->getProfilUtilisateurById($utilisateur_id);
        $this->communaute = $this->communaute_dao->getCommunauteById($communaute_id);
    }

    public function getUtilisateurId(): int
    {
        return $this->utilisateur_id;
    }

    public function getCommunauteId(): int
    {
        return $this->communaute_id;
    }

    public function getUtilisateur(): Utilisateur
    {
        return $this->utilisateur;
    }

    public function getCommunaute(): Communaute
    {
        return $this->communaute;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function estProprietaire(): bool
    {
        return $this->role === self::PROPRIETAIRE; // Vérifie si l'utilisateur est le propriétaire de la communauté ('===' : Vérification de l'égalité stricte)
    }

    public function estModerateur(): bool
    {
        return $this->role === self::MODERATEUR;
    }

    public function estMembre(): bool
    {
        return $this->role === self::MEMBRE;
    }

    public function peutModerer(): bool
    {
        return $this->estProprietaire() || $this->estModerateur() || $this->utilisateur->estAdministrateur();
    }

    public function peutGererCommunaute(): bool
    {
        return $this->estProprietaire() || $this->utilisateur->estAdministrateur();
    }

    public function estMembreOuModerateur(): bool
    {
        return $this->estModerateur() || $this->estMembre();
    }

}