<?php
/**
 * Klasa PDOController zarządza połączeniem z bazą danych oraz wykonaniem zapytań SQL.
 * Umożliwia także obsługę transakcji bazodanowych, co zapewnia spójność danych.
 *
 * @package Simply\Connect\Controllers
 */

namespace Simply\Connect\Controllers;

use PDO;
use Simply\Connect\Interfaces\PDOInterface;
use Simply\Connect\Exceptions\DatabaseException;


class PDOController implements PDOInterface
{
    /**
     * @var PDO|null Obiekt połączenia z bazą danych lub null, jeśli połączenie nie zostało ustawione.
     */
    private ?PDO $pdo = null;

    /**
     * Ustawia obiekt połączenia PDO.
     *
     * @param PDO $pdo Obiekt połączenia PDO.
     */
    public function setConnection(PDO $pdo): void
    {
        $this->pdo = $pdo;
    }

    /**
     * Rozpoczyna transakcję bazodanową.
     *
     * @throws DatabaseException Gdy wystąpi błąd podczas rozpoczynania transakcji.
     */
    public function beginTransaction(): void
    {
        $this->ensureConnection();
        $this->pdo->beginTransaction();
    }

    /**
     * Zatwierdza bieżącą transakcję bazodanową.
     *
     * @throws DatabaseException Gdy wystąpi błąd podczas zatwierdzania transakcji.
     */
    public function commit(): void
    {
        $this->ensureConnection();
        $this->pdo->commit();
    }

    /**
     * Wycofuje bieżącą transakcję bazodanową.
     *
     * @throws DatabaseException Gdy wystąpi błąd podczas wycofywania transakcji.
     */
    public function rollBack(): void
    {
        $this->ensureConnection();
        $this->pdo->rollBack();
    }

    /**
     * Wykonuje zapytanie SELECT i zwraca wyniki.
     *
     * @param string $query Tekst zapytania SQL.
     * @param array $params Parametry zapytania.
     * @return array Wyniki zapytania.
     * @throws DatabaseException Gdy wystąpi błąd podczas zapytania.
     */
    public function select(string $query, array $params = []): array
    {
        // Metoda select wykorzystuje executeQuery do wykonania zapytania, ale nie jest w tym kontekście używana bezpośrednio.
        // Należy zaimplementować logikę zwracania wyników.
    }

    /**
     * Wykonuje operację INSERT do dodania rekordów do bazy danych.
     *
     * @param string $table Nazwa tabeli.
     * @param array $data Dane do wstawienia.
     * @return bool Status wykonania operacji.
     * @throws DatabaseException Gdy wystąpi błąd podczas wstawiania.
     */
    public function insert(string $table, array $data): bool
    {
        // Implementacja metody insert zostanie dodana później.
    }

    /**
     * Wykonuje operację UPDATE do aktualizacji rekordów w bazie danych.
     *
     * @param string $table Nazwa tabeli.
     * @param array $data Dane do aktualizacji.
     * @param array $conditions Warunki, które muszą być spełnione, aby aktualizacja miała miejsce.
     * @return bool Status wykonania operacji.
     * @throws DatabaseException Gdy wystąpi błąd podczas aktualizacji.
     */
    public function update(string $table, array $data, array $conditions): bool
    {
        // Implementacja metody update zostanie dodana później.
    }

    /**
     * Wykonuje operację DELETE do usunięcia rekordów z bazy danych.
     *
     * @param string $table Nazwa tabeli.
     * @param array $conditions Warunki, które muszą być spełnione, aby rekordy zostały usunięte.
     * @return bool Status wykonania operacji.
     * @throws DatabaseException Gdy wystąpi błąd podczas usuwania.
     */
    public function delete(string $table, array $conditions): bool
    {
        // Implementacja metody delete zostanie dodana później.
    }

    /**
     * Wykonuje dowolne zapytanie SQL.
     *
     * @param string $query Tekst zapytania SQL.
     * @param array $params Parametry zapytania.
     * @return bool Status wykonania operacji.
     * @throws DatabaseException Gdy wystąpi błąd podczas wykonania zapytania.
     */
    public function executeQuery(string $query, array $params = []): bool
    {
        $this->ensureConnection();
        try {
            $stmt = $this->pdo->prepare($query);
            foreach ($params as $key => &$value) {
                $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue(is_int($key) ? ++$key : $key, $value, $type);
            }
            return $stmt->execute();
        } catch (\PDOException $e) {
            throw new DatabaseException("An error occurred while executing the query: " . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Sprawdza, czy połączenie z bazą danych zostało ustawione.
     *
     * @throws DatabaseException Gdy połączenie z bazą danych nie zostało ustawione.
     */
    private function ensureConnection(): void
    {
        if (!$this->pdo) {
            throw new DatabaseException("The database connection has not been established.");
        }
    }
}