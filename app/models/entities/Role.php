<?php

final class Role
{
    private int $utilisateur_id;
    private int $communaute_id;
    private string $role;
    private UtilisateurDAO $utilisateur_dao;

    const PROPRIETAIRE = 'propriétaire';
    const MODERATEUR = 'modérateur';
    const MEMBRE = 'membre';

    public function __construct(int $utilisateur_id, int $communaute_id, string $role)
    {
        $this->utilisateur_id = $utilisateur_id;
        $this->communaute_id = $communaute_id;
        $this->role = $role;
        $this->utilisateur_dao = new UtilisateurDAO();
    }

    public function getUtilisateurId(): int
    {
        return $this->utilisateur_id;
    }

    public function getCommunauteId(): int
    {
        return $this->communaute_id;
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
        return $this->estProprietaire() || $this->estModerateur() || $this->utilisateur_dao->getProfilUtilisateurById($this->utilisateur_id)->estAdministrateur();
    }

    public function peutGererCommunaute(): bool
    {
        return $this->estProprietaire() || $this->utilisateur_dao->getProfilUtilisateurById($this->utilisateur_id)->estAdministrateur();
    }

    public function estMembreOuModerateur(): bool
    {
        return $this->estModerateur() || $this->estMembre();
    }

}