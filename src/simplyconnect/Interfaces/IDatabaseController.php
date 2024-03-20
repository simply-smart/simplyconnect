<?php
/**
 * Interfejs dla kontrolera bazy danych.
 *
 * Definiuje wymagane metody dla klas zarządzających połączeniami z bazą danych,
 * w tym nawiązywanie, zamykanie i ponowne łączenie połączeń. Zapewnia abstrakcyjną
 * warstwę na różne implementacje kontrolerów bazy danych, co ułatwia zmianę
 * implementacji bazy danych bez wpływu na pozostałą część aplikacji.
 *
 * @package Simply\Connect\Interfaces
 */
namespace Simply\Connect\Interfaces;

use PDO;
use Simply\Connect\Config\DatabaseConfig;

interface IDatabaseController
{
    /**
     * Nawiązuje połączenie z bazą danych na podstawie podanej konfiguracji.
     * 
     * @param DatabaseConfig $config Konfiguracja bazy danych.
     * @return PDO Połączenie z bazą danych.
     * @throws DatabaseException Gdy nie można nawiązać połączenia.
     */
    public function connect(DatabaseConfig $config): PDO;

    /**
     * Zamyka połączenie z bazą danych na podstawie podanej konfiguracji.
     * 
     * @param DatabaseConfig $config Konfiguracja bazy danych, dla której ma zostać zamknięte połączenie.
     * @return void
     */
    public function closeConnection(DatabaseConfig $config): void;

    /**
     * Zamyka wszystkie aktywne połączenia.
     * 
     * @return void
     */
    public function closeConnections(): void;
}