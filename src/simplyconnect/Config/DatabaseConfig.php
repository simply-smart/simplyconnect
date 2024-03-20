<?php
/**
 * Zarządzanie konfiguracją połączenia z bazą danych.
 *
 * Ta klasa umożliwia centralne zarządzanie ustawieniami połączenia z bazą danych,
 * w tym informacjami o sterowniku, hoście, nazwie bazy danych, użytkowniku, haśle
 * oraz zestawie znaków. Dzięki temu ułatwia konfigurację i dostęp do połączenia z bazą danych
 * w aplikacji. Pozwala również na dynamiczne generowanie konfiguracji na podstawie
 * zmiennych środowiskowych, co zapewnia elastyczność w konfiguracji środowisk uruchomieniowych.
 *
 * @package Simply\Connect\Config
 */

namespace Simply\Connect\Config;

use Simply\Connect\Exceptions\DatabaseException;

/**
 * Klasa DatabaseConfig przechowuje konfigurację połączenia z bazą danych.
 * Umożliwia łatwe zarządzanie ustawieniami połączenia, takimi jak sterownik, host, nazwa bazy danych,
 * użytkownik, hasło, zestaw znaków oraz opcjonalne dodatkowe opcje dla połączenia PDO.
 */
class DatabaseConfig
{
    /**
     * Sterownik bazy danych.
     * @var string
     */
    public string $driver;
    
    /**
     * Host bazy danych.
     * @var string
     */
    public string $host;
    
    /**
     * Nazwa bazy danych.
     * @var string
     */
    public string $dbname;
    
    /**
     * Nazwa użytkownika bazy danych.
     * @var string
     */    
    public string $user;
    
    /**
     * Hasło użytkownika bazy danych.
     * @var string
     */
    public string $pass;
    
    /**
     * Zestaw znaków używany w połączeniu z bazą danych.
     * @var string
     */
    public string $charset;
    
    /**
     * Dodatkowe opcje dla połączenia PDO.
     * @var array|null
     */    
    public ?array $options;

    /**
     * Konstruktor klasy DatabaseConfig. Przypisuje wartości do właściwości klasy
     * na podstawie dostarczonych parametrów. Rzuca wyjątek, jeśli wymagane dane są niekompletne.
     *
     * @param string $driver Sterownik bazy danych.
     * @param string $host Host bazy danych.
     * @param string $dbname Nazwa bazy danych.
     * @param string $user Nazwa użytkownika bazy danych.
     * @param string $pass Hasło użytkownika bazy danych.
     * @param string $charset Zestaw znaków.
     * @param array|null $options Dodatkowe opcje dla połączenia PDO.
     * @throws DatabaseException Gdy sterownik lub nazwa bazy danych są puste.
     */
    public function __construct(
        string $driver,
        string $host,
        string $dbname,
        string $user,
        string $pass,
        string $charset = 'utf8',
        ?array $options = null
    ) {
        if (empty($driver) || empty($dbname)) {
            throw new DatabaseException("Driver and Database name are required.");
        }
        
        $this->driver = $driver;
        $this->host = $host;
        $this->dbname = $dbname;
        $this->user = $user;
        $this->pass = $pass;
        $this->charset = $charset;
        $this->options = $options;
    }

    /**
     * Tworzy nową instancję DatabaseConfig na podstawie zmiennych środowiskowych.
     * Umożliwia dynamiczne tworzenie konfiguracji połączenia na podstawie zmiennych środowiskowych.
     *
     * @param string $name Nazwa konfiguracji, która ma zostać użyta do odczytania zmiennych środowiskowych.
     * @return self Instancja DatabaseConfig z konfiguracją załadowaną na podstawie zmiennych środowiskowych.
     */
    public static function fromConfigName(string $name = 'DEFAULT'): self
    {
        $prefix = 'DB_'.strtoupper($name).'_';

        $options = null;
        if (!empty($_ENV[$prefix . 'OPTIONS'])) {
            $optionsDecoded = json_decode($_ENV[$prefix . 'OPTIONS'], true);
            $options = is_array($optionsDecoded) ? $optionsDecoded : null;
        }

        return new self(
            $_ENV[$prefix . 'DRIVER'] ?? 'mysql',
            $_ENV[$prefix . 'HOST'] ?? 'localhost',
            $_ENV[$prefix . 'NAME'] ?? 'my_database',
            $_ENV[$prefix . 'USER'] ?? 'root',
            $_ENV[$prefix . 'PASS'] ?? '',
            $_ENV[$prefix . 'CHARSET'] ?? 'utf8',
            $options
        );
    }
}