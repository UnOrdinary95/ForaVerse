<?php
// TODO : Modifier la doc de valider

/**
 * AuthValidatorAbstract - Classe abstraite pour la validation d'authentification
 *
 * Cette classe définit la structure de base pour les validateurs d'authentification.
 * Elle fournit les propriétés et méthodes communes pour la validation des données
 * utilisateur lors de l'inscription et de la connexion.
 */
abstract class AuthValidatorAbstract
{
    /**
     * @var UtilisateurDAO Instance du DAO pour l'accès aux données utilisateur
     */
    protected UtilisateurDAO $utilisateur_dao;
    /**
     * @var array Tableau associatif contenant les messages d'erreurs de validation
     */
    protected array $erreurs;

    /**
     * Valide les données d'authentification
     *
     * @param string $pseudo Le pseudo de l'utilisateur
     * @param string $email L'adresse email de l'utilisateur
     * @param string $mdp Le mot de passe de l'utilisateur
     * @return bool | int True si la validation est réussie, False sinon
     */
    abstract public function valider(string $pseudo, string $email, string $mdp): bool | int;

    /**
     * Retourne l'instance du DAO utilisateur
     *
     * @return UtilisateurDAO L'instance du DAO utilisateur
     */
    public function getUtilisateurDAO(): UtilisateurDAO
    {
        return $this->utilisateur_dao;
    }

    /**
     * Retourne le tableau des erreurs de validation
     *
     * @return array Les messages d'erreurs de validation
     */
    public function getErreurs(): array
    {
        return $this->erreurs;
    }
}
