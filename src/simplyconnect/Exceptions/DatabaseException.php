<?php
/**
 * Klasa wyjątku dla błędów bazy danych.
 *
 * Reprezentuje wyjątki specyficzne dla błędów napotkanych podczas operacji na bazie danych.
 * Umożliwia precyzyjne śledzenie i obsługę błędów związanych z bazą danych,
 * zapewniając lepszą diagnostykę i reakcję na problemy.
 *
 * @package Simply\Connect\Exceptions
 */
namespace Simply\Connect\Exceptions;

/**
 * Wyjątek DatabaseException.
 *
 * Specjalizowany wyjątek przeznaczony do zgłaszania błędów związanych z bazą danych.
 * Służy do oznaczania wyjątkowych sytuacji napotkanych podczas operacji na bazie danych,
 * takich jak problemy z połączeniem, błędy w zapytaniach SQL, itp.
 */
class DatabaseException extends \PDOException {}