<?php

/**
 * Interface AuthControllerInterface - Interface pour l'authentification
 *
 * Cette interface définit le contrat pour les contrôleurs gérant
 * l'authentification des utilisateurs dans l'application.
 */
interface AuthControllerInterface
{
    /**
     * Vérifie l'authentification d'un utilisateur
     *
     * Cette méthode doit être implémentée pour gérer la vérification
     * des informations d'identification de l'utilisateur.
     *
     * @return void
     */
    public function verifierUtilisateur():void;
}