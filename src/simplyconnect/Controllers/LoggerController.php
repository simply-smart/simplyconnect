<?php
/**
 * LoggerController
 *
 * Ta klasa wykorzystuje bibliotekę `Monolog` do logowania różnych poziomów informacji,
 * ostrzeżeń i błędów, które mogą wystąpić podczas działania aplikacji. Umożliwia
 * konfigurację poziomu logowania oraz przechowywanie logów w określonym katalogu.
 * Dodatkowo oferuje zintegrowane metody do obsługi błędów i wyjątków.
 *
 * @package Simply\Connect\Controllers
 */

namespace Simply\Connect\Controllers;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

/**
 * Klasa LoggerController służy do logowania różnych poziomów komunikatów.
 * Wykorzystuje bibliotekę Monolog do logowania.
 */
class LoggerController {
    /**
     * @var Logger Instancja loggera Monolog.
     */
    private $logger;

    /**
     * @var array Mapowanie nazw poziomów logowania na ich wartości w Monolog.
     */
    private $logLevels = [
        'DEBUG'     => Logger::DEBUG,
        'INFO'      => Logger::INFO,
        'NOTICE'    => Logger::NOTICE,
        'WARNING'   => Logger::WARNING,
        'ERROR'     => Logger::ERROR,
        'CRITICAL'  => Logger::CRITICAL,
        'ALERT'     => Logger::ALERT,
        'EMERGENCY' => Logger::EMERGENCY
    ];

    /**
     * Konstruktor klasy LoggerController.
     * 
     * @param string $channelName Nazwa kanału logowania.
     * @param array|null $params Parametry konfiguracyjne, w tym katalog logowania i poziom logowania.
     */
    public function __construct($channelName = 'simplyApp', ?array $params = []) {
        $this->logger = new Logger($channelName);

        $logDirectory = $params['logDirectory'] ?? PATH_ROOT . 'storage/logs/';
        $loggerCurrentData = date('Y-m-d');
        $logFile = $logDirectory . "{$loggerCurrentData}.log";
        $logLevel = $this->logLevels[strtoupper($params['logLevel'] ?? 'DEBUG')];

        $this->logger->pushHandler(new StreamHandler($logFile, $logLevel));
        $this->logger->pushHandler(new FirePHPHandler());
    }

    /**
     * Inicjalizuje obsługę błędów i wyjątków.
     */
    public function init() {
        $this->registerErrorHandler();
        $this->registerExceptionHandler();
        $this->registerShutdownFunction();
    }

    /**
     * Loguje błąd.
     * 
     * @param string $message Komunikat błędu.
     */
    public function logError($message) {
        $this->logger->error($message);
    }

    /**
     * Loguje ostrzeżenie.
     * 
     * @param string $message Komunikat ostrzeżenia.
     */
    public function logWarning($message) {
        $this->logger->warning($message);
    }

    /**
     * Rejestruje globalną obsługę błędów PHP.
     */
    private function registerErrorHandler() {
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            $this->logError("Error ({$errno}): {$errstr} in {$errfile} on line {$errline}");
        });
    }

    /**
     * Rejestruje globalną obsługę nieuchwyconych wyjątków.
     */
    private function registerExceptionHandler() {
        set_exception_handler(function ($exception) {
            $this->logError("Uncaught Exception: " . $exception->getMessage());
        });
    }

    /**
     * Rejestruje funkcję, która zostanie wykonana przy zamykaniu skryptu.
     */
    private function registerShutdownFunction() {
        register_shutdown_function(function () {
            $error = error_get_last();
            if (!empty($error)) {
                $this->logError("Fatal Error: {$error['message']} in {$error['file']} on line {$error['line']}");
            }
        });
    }
}