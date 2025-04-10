<?php

/**
 * InscriptionValidator - Validateur pour l'inscription des utilisateurs
 *
 * Cette classe gère la validation des informations d'inscription des utilisateurs.
 * Elle hérite de AuthValidatorAbstract et implémente les règles spécifiques
 * pour valider le pseudo, l'email et le mot de passe lors de l'inscription.
 */
class InscriptionValidator extends AuthValidatorAbstract
{
    /**
     * Constructeur de InscriptionValidator
     * Initialise le DAO utilisateur et le tableau des erreurs de validation
     */
    public function __construct()
    {
        $this->utilisateur_dao = new UtilisateurDAO();
        $this->erreurs = [
            'pseudo' => "",
            'email' => "",
            'mdp' => ""
        ];
    }

    /**
     * Valide les informations d'inscription de l'utilisateur
     *
     * @param string $pseudo Le pseudo choisi par l'utilisateur
     * @param string $email L'adresse email de l'utilisateur
     * @param string $mdp Le mot de passe choisi
     * @return bool True si toutes les informations sont valides, False sinon
     */
    public function valider(string $pseudo, string $email, string $mdp): bool
    {
        return $this->validerPseudo($pseudo) &&
               $this->validerEmail($email) &&
               $this->validerMdp($mdp);
    }

    /**
     * Valide le pseudo selon les critères définis
     * - Entre 3 et 20 caractères
     * - Uniquement lettres, chiffres, underscores et tirets
     * - Au moins 3 lettres minuscules
     * - Doit être unique
     *
     * @param string $pseudo Le pseudo à valider
     * @return bool True si le pseudo est valide, False sinon
     */
    private function validerPseudo(string $pseudo):bool
    {
        $validation = false;

        if(empty($pseudo)){
            $this->erreurs['pseudo'] = "Veuillez entrer un pseudo.";
        }
        elseif(strlen($pseudo) < 3 || strlen($pseudo) > 20){
            $this->erreurs['pseudo'] = "Le pseudo doit contenir entre 3 et 20 caractères.";
        }
        elseif (!preg_match('/^[a-zA-Z0-9_-]+$/', $pseudo)){
            $this->erreurs['pseudo'] = "Le pseudo ne doit contenir que des lettres, des chiffres, des underscores ou des tirets";
        }
        elseif (!preg_match('/[a-z]{3,}/', $pseudo)){
            $this->erreurs['pseudo'] = "Le pseudo doit contenir au moins 3 lettres minuscules";
        }
        elseif (in_array($pseudo, $this->utilisateur_dao->getPseudos())){
            $this->erreurs['pseudo'] = "Le pseudo existe déjà";
        }
        else{
            $validation = true;
        }
        return $validation;
    }

    /**
     * Valide l'adresse email selon les critères définis
     * - Format email valide
     * - Entre 5 et 50 caractères
     * - Doit être unique
     *
     * @param string $email L'email à valider
     * @return bool True si l'email est valide, False sinon
     */
    private function validerEmail(string $email):bool
    {
        $validation = false;

        if(empty($email)){
            $this->erreurs['email'] = "Veuillez entrer une adresse email.";
        }
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $this->erreurs['email'] = "L'adresse email n'est pas valide.";
        }
        elseif (strlen($email) < 5 || strlen($email) > 50){
            $this->erreurs['email'] = "L'adresse email doit contenir entre 5 et 50 caractères.";
        }
        elseif (in_array($email, $this->utilisateur_dao->getEmails())){
            $this->erreurs['email'] = "L'adresse email existe déjà";
        }
        else{
            $validation = true;
        }
        return $validation;
    }

    /**
     * Valide le mot de passe selon les critères définis
     * - Entre 8 et 15 caractères
     * - Au moins une majuscule, une minuscule et un chiffre
     *
     * @param string $mdp Le mot de passe à valider
     * @return bool True si le mot de passe est valide, False sinon
     */
    public function validerMdp(string $mdp):bool
    {
        $validation = false;

        if(empty($mdp)){
            $this->erreurs['mdp'] = "Veuillez entrer un mot de passe.";
        }
        elseif (strlen($mdp) < 8 || strlen($mdp) > 15){
            $this->erreurs['mdp'] = "Le mot de passe doit contenir entre 8 et 15 caractères.";
        }
        elseif (!preg_match('/[A-Z]/', $mdp) || !preg_match('/[a-z]/', $mdp) || !preg_match('/[0-9]/', $mdp)){
            $this->erreurs['mdp'] = "Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre.";
        }
        else{
            $validation = true;
        }
        return $validation;
    }

    public function clearErreurs():void
    {
        $this->erreurs = [
            'pseudo' => "",
            'email' => "",
            'mdp' => ""
        ];
    }
}
