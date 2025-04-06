<?php
// TODO : Modifier la doc de valider et vérifierUtilisateur


/**
 * ConnexionValidator - Validateur pour la connexion des utilisateurs
 *
 * Cette classe gère la validation des informations de connexion des utilisateurs.
 * Elle hérite de AuthValidatorAbstract et implémente les règles spécifiques
 * pour valider l'identifiant (email ou pseudo) et le mot de passe.
 */
class ConnexionValidator extends AuthValidatorAbstract
{
    /**
     * Constructeur de ConnexionValidator
     * Initialise le DAO utilisateur et le tableau des erreurs de validation
     */
    public function __construct()
    {
        $this->utilisateur_dao = new UtilisateurDAO();
        $this->erreurs = [
            'identifiant' => "",
            'mdp' => "",
            'idmdp' => ""
        ];
    }

    /**
     * Valide les informations de connexion de l'utilisateur
     *
     * @param string $pseudo Le pseudo ou l'email de l'utilisateur
     * @param string $email L'email (non utilisé dans cette implémentation)
     * @param string $mdp Le mot de passe de l'utilisateur
     * @return int True si les informations sont valides, False sinon
     */
    public function valider(string $pseudo, string $email, string $mdp): int
    {
        if (str_contains($pseudo, '@') && $this->validerIdentifiant($email) && $this->validerMdp($mdp, $email)){
            return 1;
        }
        elseif($this->validerIdentifiant($pseudo) && $this->validerMdp($mdp, $pseudo)){
            return 2;
        }
        else{
            return 0;
        }
    }

    /**
     * Valide l'identifiant de connexion (pseudo ou email)
     *
     * @param string $identifiant Le pseudo ou l'email à valider
     * @return bool 1 si email valide, 2 si pseudo valide, False sinon
     */
    private function validerIdentifiant(string $identifiant):bool
    {
        $validation = false;

        if(empty($identifiant)){
            $this->erreurs['identifiant'] = "Veuillez entrer un pseudo ou une adresse email.";
        }
        elseif(!in_array($identifiant, $this->utilisateur_dao->getPseudos()) && !in_array($identifiant, $this->utilisateur_dao->getEmails())){
            $this->erreurs['idmdp'] = "Identifiant ou mot de passe incorrect.";
        }
//        elseif(str_contains($identifiant, '@')) {
//            $validation = 1;
//        }
        else{
            $validation = true;
        }

        return $validation;
    }

    /**
     * Vérifie la correspondance du mot de passe avec l'identifiant
     *
     * @param string $mdp Le mot de passe à vérifier
     * @param string $identifiant L'identifiant (pseudo ou email) associé
     * @return bool True si le mot de passe correspond, False sinon
     */
    private function validerMdp(string $mdp, string $identifiant):bool
    {
        if(empty($mdp)){
            $this->erreurs['mdp'] = "Veuillez entrer un mot de passe.";
        }
        elseif(in_array($identifiant, $this->utilisateur_dao->getPseudos()) &&
            password_verify($mdp, $this->utilisateur_dao->getMdpByPseudo($identifiant))){
            return true;
        }
        elseif(in_array($identifiant, $this->utilisateur_dao->getEmails()) &&
            password_verify($mdp, $this->utilisateur_dao->getMdpByEmail($identifiant))){
            return true;
        }
        else{
            $this->erreurs['idmdp'] = "Identifiant ou mot de passe incorrect.";
        }
        return false;
    }
}
