<?php

/**
 * Fasada DatabaseFacade do inicjalizacji połączenia z bazą danych.
 *
 * Umożliwia uproszczone zarządzanie połączeniem z bazą danych poprzez abstrakcję
 * konfiguracji i procesu nawiązywania połączenia.
 * 
 * @package Simply\Connect\Facades
 */

namespace Simply\Connect\Facades;

use Simply\Connect\Config\DatabaseConfig;
use Simply\Connect\Controllers\DatabaseController;
use Simply\Connect\Controllers\PDOController;
use Simply\Connect\Exceptions\DatabaseException;
use Simply\Connect\Exceptions\SimplyException;

class DatabaseFacade
{
    /**
     * Inicjuje połączenie z bazą danych przy użyciu dostarczonej konfiguracji
     * i przekazuje to połączenie do PDOController.
     *
     * @param DatabaseConfig $dbConfig Konfiguracja bazy danych.
     * @return void
     * @throws DatabaseException Gdy połączenie z bazą danych nie może być nawiązane.
     * @throws SimplyException Gdy wystąpi nieoczekiwany błąd.
     */
    public static function init(DatabaseConfig $dbConfig): void
    {
        $dbController = new DatabaseController();
        $pdoController = new PDOController();

        try {
            // Przekazanie połączenia PDO do PDOController
            $dbController->provideConnection($pdoController, $dbConfig);
            echo "Connection to the database has been successfully established and passed to PDOController.";
        } catch (DatabaseException $e) {
            echo "Database error: " . $e->getMessage();
            throw $e; // Rzuca wyjątek dalej
        } catch (\PDOException $e) {
            echo "PDO error: " . $e->getMessage();
            throw new DatabaseException("Unable to connect to the database using PDO.", 0, $e);
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
            throw new SimplyException("An unexpected error occurred during database connection initialization.", 0, $e);
        }
    }
}