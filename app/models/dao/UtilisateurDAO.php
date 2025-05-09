<?php

final class UtilisateurDAO
{
    public PDO $pdo;

    public function __construct()
    {
        $this->pdo = PostgreSQLDB::getConnexion();
    }
    
    public function getProfilUtilisateurById(int $id): ?Utilisateur
    {
        $query = $this->pdo->prepare("SELECT * FROM utilisateur WHERE idUtilisateur = ?");
        $query->execute([$id]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return null;
        }   
        
        return new Utilisateur(
            $result['pseudo'], 
            $result['email'], 
            $result['motdepasse'], 
            $result['idutilisateur'],  
            $result['date_inscription'], 
            $result['chemin_photo'], 
            $result['bio'], 
            $result['est_admin']
        );
    }

    public function getPseudos():array
    {
        $query = $this->pdo->prepare("SELECT pseudo FROM utilisateur");
        $query->execute();

        // 0 => Chaque élément possède la même clé, c'est une façon de transformer le tableau associatif en tableau numérique sans clé.
        return array_column($query->fetchAll(PDO::FETCH_NUM), 0);
    }

    public function getEmails():array
    {
        $query = $this->pdo->prepare("SELECT email FROM utilisateur");
        $query->execute();

        return array_column($query->fetchAll(PDO::FETCH_NUM), 0);
    }

    public function getMdpByPseudo(string $pseudo):string
    {
        $query = $this->pdo->prepare("SELECT motdepasse FROM utilisateur WHERE pseudo = ?");
        $query->execute([$pseudo]);

        return $query->fetchColumn();
    }

    public function getMdpByEmail(string $email):string
    {
        $query = $this->pdo->prepare("SELECT motdepasse FROM utilisateur WHERE email = ?");
        $query->execute([$email]);

        return $query->fetchColumn();
    }

    public function getIdByPseudo(string $pseudo):int
    {
        $query = $this->pdo->prepare("SELECT idUtilisateur FROM utilisateur WHERE pseudo = ?");
        $query->execute([$pseudo]);

        return $query->fetchColumn();
    }

    public function getPseudoById(int $id):string
    {
        $query = $this->pdo->prepare("SELECT pseudo FROM utilisateur WHERE idUtilisateur = ?");
        $query->execute([$id]);

        return $query->fetchColumn();
    }

    public function getPseudoByEmail(string $email):string
    {
        $query = $this->pdo->prepare("SELECT pseudo FROM utilisateur WHERE email = ?");
        $query->execute([$email]);

        return $query->fetchColumn();
    }

    public function getAdminById(int $id):bool
    {
        $query = $this->pdo->prepare("SELECT est_admin FROM utilisateur WHERE idutilisateur = ?");
        $query->execute([$id]);
        return (bool)$query->fetchColumn();
    }

    public function getPpById(int $id):string
    {
        $query = $this->pdo->prepare("SELECT chemin_photo FROM utilisateur WHERE idutilisateur = ?");
        $query->execute([$id]);
        return $query->fetchColumn();
    }

    public function addUtilisateur(string $pseudo, string $email, string $mdp):bool
    {
        $mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);

        $query = $this->pdo->prepare("INSERT INTO utilisateur (pseudo, email, motdepasse) VALUES (?, ?, ?)");
        return $query->execute([$pseudo, $email, $mdp_hash]);
    }

    public function updatePhotoProfil(int $id, string $chemin_photo):bool
    {
        $query = $this->pdo->prepare("UPDATE utilisateur SET chemin_photo = ? WHERE idutilisateur = ?");
        return $query->execute([$chemin_photo, $id]);
    }

    public function updateMdpByEmail(string $email, string $mdp):bool
    {
        $mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);
        $query = $this->pdo->prepare("UPDATE utilisateur SET motdepasse = ? WHERE email = ?");
        return $query->execute([$mdp_hash, $email]);
    }

    public function updatePseudoByPseudo(string $pseudo, string $newPseudo):bool
    {
        $query = $this->pdo->prepare("UPDATE utilisateur SET pseudo = ? WHERE pseudo = ?");
        return $query->execute([$newPseudo, $pseudo]);
    }

    public function updateEmailByPseudo(string $pseudo, string $newEmail):bool
    {
        $query = $this->pdo->prepare("UPDATE utilisateur SET email = ? WHERE pseudo = ?");
        return $query->execute([$newEmail, $pseudo]);
    }

    public function updateBioByPseudo(string $pseudo, string $bio):bool
    {
        $query = $this->pdo->prepare("UPDATE utilisateur SET bio = ? WHERE pseudo = ?");
        return $query->execute([$bio, $pseudo]);
    }

    public function updateMdpByPseudo(string $pseudo, string $mdp):bool
    {
        $mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);
        $query = $this->pdo->prepare("UPDATE utilisateur SET motdepasse = ? WHERE pseudo = ?");
        return $query->execute([$mdp_hash, $pseudo]);
    }

    public function existeUtilisateur(string $pseudo): bool|int
    {
        $query = $this->pdo->prepare("SELECT idUtilisateur FROM utilisateur WHERE pseudo = ?");
        $query->execute([$pseudo]);
        return $query->fetchColumn();
    }

    public function existeEmail(string $email): bool|int
    {
        $query = $this->pdo->prepare("SELECT idUtilisateur FROM utilisateur WHERE email = ?");
        $query->execute([$email]);
        return $query->fetchColumn();
    }

    public function deleteUtilisateurById(int $id):bool
    {
        $query = $this->pdo->prepare("DELETE FROM utilisateur WHERE idUtilisateur = ?");
        return $query->execute([$id]);
    }

    public function getUtilisateursByMotcle(string $motcle):array
    {
        $query = $this->pdo->prepare("SELECT * FROM utilisateur WHERE pseudo LIKE ?");
        $query->execute(["%$motcle%"]);
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        if (!$result) {
            return [];
        }

        $utilisateurs = [];
        foreach ($result as $ligne) {
            $utilisateurs[] = new Utilisateur(
                $ligne['pseudo'], 
                $ligne['email'], 
                $ligne['motdepasse'], 
                $ligne['idutilisateur'],  
                $ligne['date_inscription'], 
                $ligne['chemin_photo'], 
                $ligne['bio'], 
                $ligne['est_admin']
            );
        }
        
        return $utilisateurs;
    }
}

