<?php
/**
 * Interfejs PDOInterface definiuje metodę działania dla klasy obsługującej operacje na bazie danych z użyciem PDO.
 *
 * Zapewnia abstrakcyjny interfejs dla wykonywania podstawowych operacji bazodanowych, takich jak SELECT, INSERT,
 * UPDATE, DELETE, a także zarządzania transakcjami. Umożliwia zdefiniowanie spójnego API dla różnych implementacji
 * obsługi bazy danych, co ułatwia zmianę bazy danych bez wpływu na kod aplikacji.
 *
 * @package Simply\Connect\Interfaces
 */
namespace Simply\Connect\Interfaces;

use PDO;
use Simply\Connect\Exceptions\DatabaseException;

interface PDOInterface
{
    /**
     * Ustawia połączenie PDO, które ma być używane do operacji na bazie danych.
     * @param PDO $pdo Obiekt połączenia PDO.
     */
    public function setConnection(PDO $pdo): void;

    /**
     * Wykonuje zapytanie SELECT i zwraca wyniki.
     * @param string $query Tekst zapytania SQL.
     * @param array $params Parametry zapytania.
     * @return array Wyniki zapytania.
     * @throws DatabaseException Gdy wystąpi błąd podczas zapytania.
     */
    public function select(string $query, array $params = []): array;

    /**
     * Wykonuje operację INSERT do dodania rekordów do bazy danych.
     * @param string $table Nazwa tabeli.
     * @param array $data Dane do wstawienia.
     * @return bool Status wykonania operacji.
     * @throws DatabaseException Gdy wystąpi błąd podczas wstawiania.
     */
    public function insert(string $table, array $data): bool;

    /**
     * Wykonuje operację UPDATE do aktualizacji rekordów w bazie danych.
     * @param string $table Nazwa tabeli.
     * @param array $data Dane do aktualizacji.
     * @param array $conditions Warunki, które muszą być spełnione, aby aktualizacja miała miejsce.
     * @return bool Status wykonania operacji.
     * @throws DatabaseException Gdy wystąpi błąd podczas aktualizacji.
     */
    public function update(string $table, array $data, array $conditions): bool;

    /**
     * Wykonuje operację DELETE do usunięcia rekordów z bazy danych.
     * @param string $table Nazwa tabeli.
     * @param array $conditions Warunki, które muszą być spełnione, aby rekordy zostały usunięte.
     * @return bool Status wykonania operacji.
     * @throws DatabaseException Gdy wystąpi błąd podczas usuwania.
     */
    public function delete(string $table, array $conditions): bool;

    /**
     * Wykonuje dowolne zapytanie SQL.
     * @param string $query Tekst zapytania SQL.
     * @param array $params Parametry zapytania.
     * @return bool Status wykonania operacji.
     * @throws DatabaseException Gdy wystąpi błąd podczas wykonania zapytania.
     */
    public function executeQuery(string $query, array $params = []): bool;

    /**
     * Rozpoczyna transakcję.
     * @throws DatabaseException Gdy wystąpi błąd podczas rozpoczynania transakcji.
     */
    public function beginTransaction(): void;

    /**
     * Zatwierdza transakcję.
     * @throws DatabaseException Gdy wystąpi błąd podczas zatwierdzania transakcji.
     */
    public function commit(): void;

    /**
     * Wycofuje transakcję.
     * @throws DatabaseException Gdy wystąpi błąd podczas wycofywania transakcji.
     */
    public function rollBack(): void;
}