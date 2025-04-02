<?php

//require_once "../../utils/test_autoloader.php";

final class UtilisateurDAO
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = PostgreSQLDB::getConnexion();
    }

    public function getPseudos():array
    {
        $query = $this->pdo->prepare("SELECT pseudo FROM utilisateur");
        $query->execute();

        return array_column($query->fetchAll(PDO::FETCH_NUM), 0);
    }

    public function getEmails():array
    {
        $query = $this->pdo->prepare("SELECT email FROM utilisateur");
        $query->execute();

        return array_column($query->fetchAll(PDO::FETCH_NUM), 0);
    }

    public function getMdpByPseudo(string $pseudo): ?string
    {
        $query = $this->pdo->prepare("SELECT motdepasse FROM utilisateur WHERE pseudo = ?");
        $query->execute([$pseudo]);

        return $query->fetchColumn();
    }

    public function getMdpByEmail(string $email): ?string
    {
        $query = $this->pdo->prepare("SELECT motdepasse FROM utilisateur WHERE email = ?");
        $query->execute([$email]);

        return $query->fetchColumn();
    }

    public function getIdByPseudo(string $pseudo):?int
    {
        $query = $this->pdo->prepare("SELECT idUtilisateur FROM utilisateur WHERE pseudo = ?");
        $query->execute([$pseudo]);

        return $query->fetchColumn();
    }

    public function getIdByEmail(string $email):?int
    {
        $query = $this->pdo->prepare("SELECT idUtilisateur FROM utilisateur WHERE email = ?");
        $query->execute([$email]);

        return $query->fetchColumn();
    }

    public function addUtilisateur(string $pseudo, string $email, string $mdp): bool
    {
        $mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);

        $query = $this->pdo->prepare("INSERT INTO utilisateur (pseudo, email, motdepasse) VALUES (?, ?, ?)");
        return $query->execute([$pseudo, $email, $mdp_hash]);
    }

}

if (php_sapi_name() == 'cli') {
    print("Test de la connexion à la base de données PostgreSQL\n");
    echo "getPseudos() : \n";
    $utilisateurDAO = new UtilisateurDAO();
    $tab = $utilisateurDAO->getPseudos();
    print("Tableau des pseudos : \n");
    foreach ($tab as $pseudo) {
        print($pseudo . "\n");
    }

    echo "getEmails() : \n";
    $tab = $utilisateurDAO->getEmails();
    print("Tableau des emails : \n");
    foreach ($tab as $email) {
        print($email . "\n");
    }
}
