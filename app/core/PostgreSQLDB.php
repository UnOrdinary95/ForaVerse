<?php

/**
 * Class PostgreSQLDB
 * Gère la connexion à la base de données PostgreSQL en utilisant le pattern Singleton
 *
 * Cette classe fournit un point d'accès unique à la connexion PDO.
 * Elle utilise les variables d'environnement pour la configuration.
 */
final class PostgreSQLDB
{
    /** @var ?PDO Instance unique de connexion PDO */
    private static ?PDO $connexion = null;

    /**
     * Établit une connexion à la base de données PostgreSQL
     * Utilise les variables d'environnement :
     * - DB_HOST : hôte du serveur
     * - DB_PORT : port du serveur
     * - DB_TEST : nom de la base de données
     * - DB_USERNAME : nom d'utilisateur
     * - DB_PASSWORD : mot de passe
     *
     * @throws PDOException Si la connexion échoue
     */
    private static function connexionDB():void
    {
        try {
            self::$connexion = new PDO(
                sprintf(
                    "pgsql:host=%s;port=%s;dbname=%s;user=%s;password=%s",
                    getenv("DB_HOST"),
                    getenv("DB_PORT"),
                    getenv("DB_TEST"),
                    getenv("DB_USERNAME"),
                    getenv("DB_PASSWORD"))
            );
            self::$connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            throw new PDOException("Erreur de connexion PDO : " . $e->getMessage());
        }
    }

    /**
     * Retourne l'instance unique de connexion PDO
     * Si la connexion n'existe pas, elle est créée
     *
     * @return PDO Instance de connexion PDO
     * @throws PDOException Si la connexion échoue
     */
    public static function getConnexion(): PDO
    {
        if (self::$connexion === null) {
            self::connexionDB();
        }
        return self::$connexion;
    }

}

// Script de test : vérifie la connexion à la base de données
// Exécuté uniquement en ligne de commande (CLI - Command Line Interface)
if (php_sapi_name() == 'cli') {
    print("Test de la connexion à la base de données PostgreSQL\n");
    echo "connexionDB() : \n";
    print_r(PostgreSQLDB::getConnexion());
}
