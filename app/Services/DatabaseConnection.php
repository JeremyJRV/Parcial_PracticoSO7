<?php

namespace App\Services;

use PDO;
use PDOException;
use PDOStatement;

/**
 * DatabaseConnection
 * -------------------
 * Punto único de conexión a la base de datos (patrón Singleton).
 * Provee métodos helper para ejecutar consultas de forma segura
 * usando PDO con prepared statements (protección contra SQL Injection).
 */
class DatabaseConnection
{
    private static ?DatabaseConnection $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $config = require __DIR__ . '/../Config/database.php';

        $dsn = "{$config['driver']}:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";

        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false, // fuerza prepared statements reales
            ]);
        } catch (PDOException $e) {
            // No exponemos detalles sensibles del error en producción
            die('Error de conexión a la base de datos: ' . $e->getMessage());
        }
    }

    // Evita clonación y (de)serialización del singleton
    private function __clone() {}
    public function __wakeup() {}

    public static function getInstance(): DatabaseConnection
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Ejecuta un SELECT y devuelve todas las filas.
     */
    public function select(string $sql, array $params = []): array
    {
        $stmt = $this->run($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Ejecuta un SELECT y devuelve una sola fila (o null).
     */
    public function selectOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->run($sql, $params);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /**
     * Ejecuta un INSERT y devuelve el último ID insertado.
     */
    public function insert(string $sql, array $params = []): string
    {
        $this->run($sql, $params);
        return $this->pdo->lastInsertId();
    }

    /**
     * Ejecuta un UPDATE o DELETE y devuelve el número de filas afectadas.
     */
    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this->run($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Ejecución interna centralizada (prepared statement).
     */
    private function run(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }
}
