<?php
/**
 * Klasa DatabaseController służy do zarządzania połączeniami z bazą danych.
 * Umożliwia nawiązywanie i zamykanie połączenia z bazą danych na podstawie konfiguracji.
 *
 * @package Simply\Connect\Controllers
 */
namespace Simply\Connect\Controllers;

use PDO;
use PDOException;
use Simply\Connect\Interfaces\PDOInterface;
use Simply\Connect\Interfaces\DatabaseInterface;
use Simply\Connect\Config\DatabaseConfig;
use Simply\Connect\Exceptions\DatabaseException;


class DatabaseController implements DatabaseInterface
{

    /**
     * Przekazuje połączenie PDO do obiektu implementującego PDOInterface.
     *
     * @param PDOInterface $pdoController Obiekt, który ma otrzymać połączenie.
     * @param DatabaseConfig $config Konfiguracja bazy danych dla połączenia.
     */
    public function provideConnection(PDOInterface $pdoController, DatabaseConfig $config): void
    {
        $pdo = $this->connect($config);
        $pdoController->setConnection($pdo);
    }

    /**
     * Tablica przechowująca połączenia z bazami danych.
     * @var array<string, PDO|null>
     */
    private array $connections = [];

    /**
     * Nawiązuje połączenie z bazą danych lub zwraca istniejące połączenie.
     *
     * @param DatabaseConfig $config Konfiguracja bazy danych.
     * @return PDO|null Połączenie z bazą danych.
     */
    public function connect(DatabaseConfig $config): PDO
    {
        $key = $this->getConnectionKey($config);

        if (!isset($this->connections[$key])) {
            $this->connections[$key] = $this->createConnection($config);
        }

        return $this->connections[$key];
    }

    /**
     * Tworzy nowe połączenie z bazą danych na podstawie konfiguracji.
     * Metoda próbuje nawiązać połączenie z bazą danych używając parametrów dostarczonych
     * przez obiekt konfiguracji. Ustawia opcje PDO, aby zapewnić bezpieczne i efektywne
     * połączenie. W przypadku niepowodzenia, metoda rejestruje szczegóły błędu i rzuca
     * wyjątek, uniemożliwiając dalsze wykonanie kodu bez poprawnego połączenia z bazą danych.
     *
     * @param DatabaseConfig $config Konfiguracja bazy danych.
     * @return PDO Połączenie z bazą danych.
     * @throws DatabaseException Gdy nie można nawiązać połączenia z bazą danych. Zwraca ogólny komunikat o błędzie,
     *         szczegółowe informacje są zapisywane do logu błędów.
     */
    private function createConnection(DatabaseConfig $config): PDO
    {
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        $dsn = $this->buildDsn($config);

        try {
            return new PDO($dsn, $config->user, $config->pass, $options);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw new DatabaseException("Unable to connect to the database.");
        }
    }


    /**
     * Buduje ciąg DSN na podstawie konfiguracji bazy danych.
     *
     * @param DatabaseConfig $config Konfiguracja bazy danych.
     * @return string Ciąg DSN.
     */
    private function buildDsn(DatabaseConfig $config): string
    {
        return match ($config->driver) {
            'mysql', 'pgsql' => "{$config->driver}:host={$config->host};dbname={$config->dbname};charset={$config->charset}",
            'sqlite' => "sqlite:{$config->dbname}",
            'odbc' => "odbc:Driver={Microsoft Access Driver (*.mdb)};Dbq={$config->dbname};",
            default => throw new DatabaseException("Unsupported database type: {$config->driver}"),
        };
    }

    /**
     * Generuje unikalny klucz dla połączenia na podstawie konfiguracji bazy danych.
     *
     * @param DatabaseConfig $config Konfiguracja bazy danych.
     * @return string Unikalny klucz połączenia.
     */
    private function getConnectionKey(DatabaseConfig $config): string
    {
        return md5($config->driver . $config->host . $config->dbname . $config->user);
    }

    /**
     * Zamyka wszystkie aktywne połączenia.
     */
    public function closeConnections(): void
    {
        foreach ($this->connections as &$connection) {
            $connection = null;
        }
        $this->connections = [];
    }

    /**
     * Zamyka wybrane połączenie z bazą danych.
     *
     * @param DatabaseConfig $config Konfiguracja bazy danych, dla której ma zostać zamknięte połączenie.
     */
    public function closeConnection(DatabaseConfig $config): void
    {
        $key = $this->getConnectionKey($config);

        if (isset($this->connections[$key])) {
            $this->connections[$key] = null;
            unset($this->connections[$key]);
        }
    }
}