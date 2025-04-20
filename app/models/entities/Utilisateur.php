<?php

final class Utilisateur
{
    private ?int $id;
    private ?string $pseudo;
    private ?string $email;
    private ?string $mdp;
    private string $chemin_photo;
    private string $bio;
    private ?string $date_inscription;
    private bool $est_admin;

    public function __construct(
        ?string $unPseudo = null,
        ?string $unEmail = null,
        ?string $unMdp = null,
        ?int $unId = null,
        ?string $uneDateInscription = null,
        string $unCheminPhoto = 'images/pp_user/default.jpeg',
        string $uneBio = 'Pas de bio.',
        bool $unEstAdmin = false
    ){
        $this->id = $unId;
        $this->pseudo = $unPseudo;
        $this->email = $unEmail;
        $this->mdp = $unMdp;
        $this->chemin_photo = $unCheminPhoto;
        $this->bio = $uneBio;
        $this->date_inscription = $uneDateInscription;
        $this->est_admin = $unEstAdmin;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function getCheminPhoto(): ?string
    {
        return $this->chemin_photo;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function getDateInscription(): ?string
    {
        return $this->date_inscription;
    }

    public function estAdministrateur(): bool
    {
        return $this->est_admin;
    }

    public function getSystemeAbonnement(): Abonne
    {
        return new Abonne($this->id);
    }

    public function getRoles(): array
    {
        return (new RoleDAO())->getRolesByUtilisateur($this->id);
    }

    public function getCommuCommunModeration(Utilisateur $utilisateur): array
    {
        
        $communaute_dao = new CommunauteDAO();
        $bannissement_dao = new BannissementDAO();
        $avertissement_dao = new AvertissementDAO();
        $communautes = [];         
        $mesRoles = $this->getRoles();
        $rolesUtilisateur = $utilisateur->getRoles();

        if ($utilisateur->estAdministrateur()) {
            foreach($mesRoles as $role){
                if ($role->estMembre()){
                    $communautes[$role->getCommunauteId()] = [
                        'nom' => $communaute_dao->getNomById($role->getCommunauteId()),
                        'estbanni' => $bannissement_dao->getBannissementByIdUtilisateurAndCommunaute($utilisateur->getId(), $role->getCommunauteId()) !== null,
                        'aete_averti' => $avertissement_dao->getAvertissementsByIdUtilisateurAndCommunaute($utilisateur->getId(), $role->getCommunauteId()) !== null
                    ];
                }
            }

            return $communautes;
        }

        $commu_moderation = [];
        foreach ($rolesUtilisateur as $role){
            if ($role->peutModerer()) {
                $commu_moderation[$role->getCommunauteId()] = $communaute_dao->getNomById($role->getCommunauteId());
            }
        }

        foreach ($mesRoles as $role) {
            if (array_key_exists($role->getCommunauteId(), $commu_moderation)) {
                if ($role->estMembre()){
                    $communautes[$role->getCommunauteId()] = [
                        'nom' => $commu_moderation[$role->getCommunauteId()],
                        'estbanni' => $bannissement_dao->getBannissementByIdUtilisateurAndCommunaute($utilisateur->getId(), $role->getCommunauteId()) !== null,
                        'aete_averti' => $avertissement_dao->getAvertissementsByIdUtilisateurAndCommunaute($utilisateur->getId(), $role->getCommunauteId()) !== null
                    ];
                } 
            }
        }

        return $communautes;
    }

    public function getBannissementByIdUtilisateurAndCommunaute(int $id_communaute): ?Bannissement
    {
        return (new BannissementDAO())->getBannissementByIdUtilisateurAndCommunaute($this->id, $id_communaute);
    }

    public function getAllBannissementsByIdUtilisateur(): array
    {
        return (new BannissementDAO())->getAllBannissementsByIdUtilisateur($this->id);
    }

    public function getAvertissementsByIdUtilisateurAndCommunaute(int $idCommunaute): array
    {
        return (new AvertissementDAO())->getAvertissementsByIdUtilisateurAndCommunaute($this->id, $idCommunaute);
    }

}
