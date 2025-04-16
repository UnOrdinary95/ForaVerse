<?php

class CommunauteValidator
{
    private CommunauteDAO $communaute_dao;
    private array $erreurs;

    public function __construct()
    {
        $this->communaute_dao = new CommunauteDAO();
        $this->erreurs = [
            'nomCommu' => "",
            'descriptionCommu' => ""
        ];
    }

    public function valider(string $nom, ?string $description): bool
    {
        return $this->validerNomCommu($nom) &&
               $this->validerDescriptionCommu($description);
    }

    public function validerNomCommu(string $nom):bool
    {
        $validation = false;

        if(empty($nom)){
            $this->erreurs['nomCommu'] = "Veuillez entrer un nom de communauté.";
        }
        elseif(strlen($nom) < 3 || strlen($nom) > 50){
            $this->erreurs['nomCommu'] = "Le nom de la communauté doit contenir entre 3 et 50 caractères.";
        }
        elseif (!preg_match('/^[a-zA-Z0-9_-]+$/', $nom)){
            $this->erreurs['nomCommu'] = "Le nom de la communauté ne doit contenir que des lettres, des chiffres, des underscores ou des tirets";
        }
        elseif(!preg_match('/[a-z]{3,}/', $nom)){
            $this->erreurs['nomCommu'] = "Le nom de la communauté doit contenir au moins 3 lettres minuscules";
        }
        elseif(in_array($nom, $this->communaute_dao->getNomsCommunautes())){
            $this->erreurs['nomCommu'] = "Le nom de la communauté existe déjà";
        }
        else{
            $validation = true;
        }
        return $validation;           
    }

    public function validerDescriptionCommu(?string $description):bool
    {
        if ($description == null){
            return true;
        }
        $validation = false;

        if(strlen($description) > 1024){
            $this->erreurs['descriptionCommu'] = "La description de la communauté doit contenir moins de 1024 caractères.";
        }
        else{
            $validation = true;
        }
        return $validation;
    }

    public function getErreurs():array
    {
        return $this->erreurs;
    }
    
    public function clearErreurs():void
    {
        $this->erreurs = [
            'nomCommu' => "",
            'descriptionCommu' => ""
        ];
    }
}