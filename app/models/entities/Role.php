<?php

final class Role
{
    private int $utilisateur_id;
    private int $communaute_id;
    private string $role;

    const PROPRIETAIRE = 'propriétaire';
    const MODERATEUR = 'modérateur';
    const MEMBRE = 'membre';

    public function __construct(int $utilisateur_id, int $communaute_id, string $role)
    {
        $this->utilisateur_id = $utilisateur_id;
        $this->communaute_id = $communaute_id;
        $this->role = $role;
    }


    public function getUtilisateurId(): int
    {
        return $this->utilisateur_id;
    }

    public function getCommunauteId(): int
    {
        return $this->communaute_id;
    }

    public function estAdmin(): bool
    {
        return (new UtilisateurDAO())->getAdminById($this->utilisateur_id);
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
        return $this->estProprietaire() || $this->estModerateur() || $this->estAdmin();
    }

    public function peutGererCommunaute(): bool
    {
        return $this->estProprietaire() || $this->estAdmin();
    }

    public function estMembreOuModerateur(): bool
    {
        return $this->estModerateur() || $this->estMembre() || $this->estAdmin();
    }

}