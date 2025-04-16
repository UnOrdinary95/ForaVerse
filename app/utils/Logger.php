<?php
/**
 * Logger - Classe pour gérer les logs de l'application
 *
 * Cette classe permet d'écrire facilement des messages de log dans un fichier
 * avec différents niveaux de sévérité (INFO, WARNING, ERROR, DEBUG).
 */
class Logger 
{
    private string $logFile;
    private bool $logEnabled;
    
    /**
     * Constructeur de Logger
     * 
     * @param string $logFile Chemin vers le fichier de log (relatif ou absolu)
     * @param bool $logEnabled Active ou désactive les logs
     */
    public function __construct(string $logFile = null, bool $logEnabled = true) 
    {
        // Si aucun fichier n'est spécifié, utiliser le fichier par défaut
        if ($logFile === null) {
            // Créer le dossier logs dans public/ s'il n'existe pas
            $logDir = __DIR__ . '/../../public/logs';
            if (!is_dir($logDir) && !@mkdir($logDir, 0777, true)) {
                // Si impossible de créer le dossier, utiliser le dossier temporaire du système
                $logDir = sys_get_temp_dir();
                error_log("Logger: Impossible de créer le dossier logs, utilisation du dossier temporaire ($logDir)");
            }
            $logFile = $logDir . '/app.log';
        }
        
        $this->logFile = $logFile;
        $this->logEnabled = $logEnabled;
    }
    
    /**
     * Écrit un message de log avec le niveau INFO
     * 
     * @param string $message Le message à logger
     * @return bool True si le log a été écrit, False sinon
     */
    public function info(string $message): bool
    {
        return $this->log('INFO', $message);
    }
    
    /**
     * Écrit un message de log avec le niveau WARNING
     * 
     * @param string $message Le message à logger
     * @return bool True si le log a été écrit, False sinon
     */
    public function warning(string $message): bool
    {
        return $this->log('WARNING', $message);
    }
    
    /**
     * Écrit un message de log avec le niveau ERROR
     * 
     * @param string $message Le message à logger
     * @return bool True si le log a été écrit, False sinon
     */
    public function error(string $message): bool
    {
        return $this->log('ERROR', $message);
    }
    
    /**
     * Écrit un message de log avec le niveau DEBUG
     * 
     * @param string $message Le message à logger
     * @return bool True si le log a été écrit, False sinon
     */
    public function debug(string $message): bool
    {
        return $this->log('DEBUG', $message);
    }
    
    /**
     * Écrit un message dans le fichier de log
     * 
     * @param string $level Le niveau de sévérité du message
     * @param string $message Le message à logger
     * @return bool True si le log a été écrit, False sinon
     */
    private function log(string $level, string $message): bool
    {
        if (!$this->logEnabled) {
            return false;
        }
        
        // Définir le fuseau horaire sur Europe/Paris
        date_default_timezone_set('Europe/Paris');
        $date = date('Y-m-d H:i:s');
        $logLine = "[$date][$level] $message" . PHP_EOL;
        
        // Essayer d'écrire dans le fichier avec une gestion d'erreurs améliorée
        $result = @file_put_contents($this->logFile, $logLine, FILE_APPEND);
        
        if ($result === false) {
            // Si l'écriture échoue, enregistrer une erreur dans les logs système
            error_log("Logger: Impossible d'écrire dans le fichier {$this->logFile}");
            return false;
        }
        
        return true;
    }
    
    /**
     * Active ou désactive les logs
     * 
     * @param bool $enabled True pour activer les logs, False pour les désactiver
     */
    public function setEnabled(bool $enabled): void
    {
        $this->logEnabled = $enabled;
    }
    
    /**
     * Vide le fichier de log
     * 
     * @return bool True si le fichier a été vidé, False sinon
     */
    public function clear(): bool
    {
        return (bool) @file_put_contents($this->logFile, '');
    }
    
    /**
     * Retourne le chemin du fichier de log
     * 
     * @return string Le chemin du fichier de log
     */
    public function getLogFilePath(): string
    {
        return $this->logFile;
    }
}