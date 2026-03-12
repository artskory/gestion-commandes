<?php
/**
 * Security - Headers de sécurité et système de logs
 * Gestion des Commandes
 */
class Security {

    // =====================================================
    // HEADERS DE SÉCURITÉ
    // =====================================================

    /**
     * Applique tous les headers de sécurité HTTP
     * À appeler avant tout output HTML
     */
    public static function applyHeaders() {
        // Empêche le clickjacking (inclusion dans une iframe)
        header('X-Frame-Options: DENY');

        // Empêche le MIME sniffing (exécution de fichiers mal typés)
        header('X-Content-Type-Options: nosniff');

        // Active la protection XSS du navigateur (anciens navigateurs)
        header('X-XSS-Protection: 1; mode=block');

        // Contrôle les informations envoyées dans le Referer
        header('Referrer-Policy: strict-origin-when-cross-origin');

        // Content Security Policy : limite les sources de contenu autorisées
        // Adapté aux CDN Bootstrap, FontAwesome et jQuery utilisés dans l'app
        header("Content-Security-Policy: " . implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' https://code.jquery.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com",
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com",
            "font-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com",
            "img-src 'self' data:",
            "connect-src 'self'",
            "frame-ancestors 'none'",
        ]));

        // Permissions Policy : désactive les APIs navigateur non utilisées
        header("Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()");
    }

    // =====================================================
    // SYSTÈME DE LOGS
    // =====================================================

    private static $logDir  = null;
    private static $logFile = null;

    /**
     * Initialise le répertoire de logs
     */
    private static function init() {
        if (self::$logDir !== null) return;
        self::$logDir  = __DIR__ . '/../logs';
        self::$logFile = self::$logDir . '/app-' . date('Y-m') . '.log';
        if (!is_dir(self::$logDir)) {
            mkdir(self::$logDir, 0755, true);
            // Protéger le dossier logs contre l'accès direct
            file_put_contents(self::$logDir . '/.htaccess', "Require all denied\n");
        }
    }

    /**
     * Écrit une ligne dans le fichier de log
     */
    private static function write($level, $message, array $context = []) {
        self::init();
        $ip      = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $uri     = $_SERVER['REQUEST_URI'] ?? '';
        $ctx     = empty($context) ? '' : ' ' . json_encode($context, JSON_UNESCAPED_UNICODE);
        $line    = sprintf(
            "[%s] [%s] [%s] %s%s\n",
            date('Y-m-d H:i:s'),
            strtoupper($level),
            $ip,
            $message,
            $ctx
        );
        @file_put_contents(self::$logFile, $line, FILE_APPEND | LOCK_EX);
    }

    /**
     * Log d'information (action utilisateur normale)
     */
    public static function logInfo($message, array $context = []) {
        self::write('info', $message, $context);
    }

    /**
     * Log d'avertissement (situation anormale mais non bloquante)
     */
    public static function logWarning($message, array $context = []) {
        self::write('warning', $message, $context);
    }

    /**
     * Log d'erreur (erreur applicative)
     */
    public static function logError($message, array $context = []) {
        self::write('error', $message, $context);
    }

    /**
     * Log de sécurité (tentative suspecte)
     */
    public static function logSecurity($message, array $context = []) {
        self::write('security', $message, $context);
    }

    // =====================================================
    // UTILITAIRES
    // =====================================================

    /**
     * Échappe une valeur pour affichage HTML sécurisé
     */
    public static function escape($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Valide et nettoie un entier (ex: ID en GET)
     * Logue une alerte si la valeur n'est pas un entier valide
     */
    public static function sanitizeInt($value, $paramName = 'param') {
        $int = filter_var($value, FILTER_VALIDATE_INT);
        if ($int === false) {
            self::logSecurity("Valeur entière invalide reçue", [
                'param'  => $paramName,
                'value'  => substr((string)$value, 0, 100),
                'uri'    => $_SERVER['REQUEST_URI'] ?? '',
            ]);
            return null;
        }
        return $int;
    }
}
?>
