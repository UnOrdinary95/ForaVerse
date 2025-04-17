<?php

final class RoleDAO
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = PostgreSQLDB::getConnexion();
    }

    public function getRole(int $utilisateur_id, int $communaute_id): ?Role
    {
        $query = $this->pdo->prepare("SELECT * FROM role WHERE idUtilisateur = ? AND idCommunaute = ?");
        $query->execute([$utilisateur_id, $communaute_id]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return null; // Aucun rôle trouvé
        }

        return new Role(
            $utilisateur_id,
            $communaute_id,
            $result['role']
        );
    }
    
    public function getRolesByCommunaute(int $communaute_id): array
    {
        $query = $this->pdo->prepare("SELECT * FROM role WHERE idCommunaute = ?");
        $query->execute([$communaute_id]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return [];
        }

        $roles = [];
        foreach ($result as $ligne){
            $roles[] = new Role(
                $ligne['idutilisateur'], // Sensible à la casse
                $communaute_id,
                $ligne['role']
            );
        }

        return $roles;
    }

    public function getRolesByUtilisateur(int $utilisateur_id): array
    {
        $query = $this->pdo->prepare("SELECT * FROM role WHERE idUtilisateur = ?");
        $query->execute([$utilisateur_id]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return [];
        }

        $roles = [];
        foreach ($result as $ligne){
            $roles[] = new Role(
                $utilisateur_id,
                $ligne['idcommunaute'],
                $ligne['role']
            );
        }

        return $roles;
    }

    public function getModByCommunaute(int $communaute_id): array
    {
        $query = $this->pdo->prepare("SELECT * FROM role WHERE idCommunaute = ? AND role = ?");
        $query->execute([$communaute_id, Role::MODERATEUR]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return [];
        }

        $roles = [];
        foreach ($result as $ligne){
            $roles[] = new Role(
                $ligne['idutilisateur'],
                $communaute_id,
                Role::MODERATEUR
            );
        }

        return $roles;
    }

    public function getProprioByCommunaute(int $communaute_id): ?Role
    {
        $query = $this->pdo->prepare("SELECT * FROM role WHERE idCommunaute = ? AND role = ?");
        $query->execute([$communaute_id, Role::PROPRIETAIRE]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return null;
        }

        return new Role(
            $result['idutilisateur'],
            $communaute_id,
            Role::PROPRIETAIRE
        );
    }
    

    public function getNbrRolesByCommunaute(int $communaute_id): int
    {
        $query = $this->pdo->prepare("SELECT COUNT(*) FROM role WHERE idCommunaute = ?");
        $query->execute([$communaute_id]);
        return $query->fetchColumn();
    }

    public function addUtilisateurRole(int $utilisateur_id, int $communaute_id, string $role): bool
    {
        $query = $this->pdo->prepare("INSERT INTO role (idUtilisateur, idCommunaute, role) VALUES (?, ?, ?)");
        
        return $query->execute([$utilisateur_id, $communaute_id, $role]);
    }

    public function setModerateur(int $utilisateur_id, int $communaute_id): bool
    {
        $query = $this->pdo->prepare("UPDATE role SET role = ? WHERE idUtilisateur = ? AND idCommunaute = ?");
        
        return $query->execute([Role::MODERATEUR, $utilisateur_id, $communaute_id]);
    }

    public function deleteModerateur(int $utilisateur_id, int $communaute_id): bool
    {
        $query = $this->pdo->prepare("UPDATE role SET role = ? WHERE idUtilisateur = ? AND idCommunaute = ?");

        return $query->execute([Role::MEMBRE, $utilisateur_id, $communaute_id]);
    }

    public function deleteRole(int $utilisateur_id, int $communaute_id): bool
    {
        $query = $this->pdo->prepare("DELETE FROM role WHERE idUtilisateur = ? AND idCommunaute = ?");
        
        return $query->execute([$utilisateur_id, $communaute_id]);
    }
}