<?php

/**
 * Interface ControllerInterface - Interface de base pour les contrôleurs
 *
 * Cette interface définit le contrat de base que tous les contrôleurs
 * de l'application doivent implémenter. Elle assure une structure
 * cohérente pour l'affichage des vues.
 */
interface ControllerInterface
{
    /**
     * Affiche la vue associée au contrôleur
     *
     * Cette méthode doit être implémentée par chaque contrôleur pour
     * gérer l'affichage de sa vue spécifique.
     *
     * @return void
     */
    public function afficherVue():void;
}