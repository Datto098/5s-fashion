<?php
/**
 * Database Connection Class
 * 5S Fashion E-commerce Platform
 */

class Database
{
    private static $instance = null;
    private $connection;
    private $config;

    private function __construct()
    {
        // Ensure APP_PATH is defined
        if (!defined('APP_PATH')) {
            define('APP_PATH', dirname(__DIR__));
        }

        $configPath = APP_PATH . '/config/database.php';
        if (file_exists($configPath)) {
            $this->config = require $configPath;
        } else {
            // Fallback configuration if file doesn't exist
            $this->config = [
                'connections' => [
                    'mysql' => [
                        'host' => 'localhost',
                        'port' => '3306',
                        'database' => '5s_fashion',
                        'username' => 'root',
                        'password' => '',
                        'charset' => 'utf8',
                        'options' => [
                            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                            PDO::ATTR_EMULATE_PREPARES => false,
                        ]
                    ]
                ]
            ];
        }

        $this->connect();
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function connect()
    {
        try {
            // Check if config is properly loaded
            if (!$this->config || !isset($this->config['connections']['mysql'])) {
                throw new PDOException('Database configuration not found');
            }

            $config = $this->config['connections']['mysql'];

            // Validate required config values
            $host = $config['host'] ?? 'localhost';
            $port = $config['port'] ?? '3306';
            $database = $config['database'] ?? '5s_fashion';
            $username = $config['username'] ?? 'root';
            $password = $config['password'] ?? '';
            $charset = $config['charset'] ?? 'utf8';

            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset={$charset}";

            $options = $config['options'] ?? [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->connection = new PDO($dsn, $username, $password, $options);

        } catch (PDOException $e) {
            die('Database connection failed: ' . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            if (APP_DEBUG) {
                die('Query failed: ' . $e->getMessage() . '<br>SQL: ' . $sql);
            } else {
                error_log('Database query failed: ' . $e->getMessage() . ' SQL: ' . $sql);
                return false;
            }
        }
    }

    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetchAll() : [];
    }

    public function fetchOne($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->fetch() : null;
    }

    public function execute($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt ? $stmt->rowCount() : false;
    }

    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }

    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    public function commit()
    {
        // Only commit if there's an active transaction
        if ($this->connection->inTransaction()) {
            return $this->connection->commit();
        }
        return true;
    }

    public function rollback()
    {
        // Only rollback if there's an active transaction
        if ($this->connection->inTransaction()) {
            return $this->connection->rollback();
        }
        return true;
    }

    public function escape($value)
    {
        return $this->connection->quote($value);
    }

    public function __destruct()
    {
        $this->connection = null;
    }
}
