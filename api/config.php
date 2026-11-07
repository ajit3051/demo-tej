<?php
/**
 * Tejas ERP - Standalone API Configuration & Core Library
 * 
 * Self-contained module: All DB connection, routing, authentication, 
 * and request/response helpers are defined internally without external dependencies.
 */

// Error handling & environment
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', '0');

// Set timezone
date_default_timezone_set("Asia/Kolkata");

// CORS & JSON Headers
if (!headers_sent()) {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-Auth-Token");
    header("Content-Type: application/json; charset=UTF-8");
}

// Handle preflight OPTIONS request
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Session init if needed
if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

// Database Credentials
if (!defined('_API_DB_HOST_')) define('_API_DB_HOST_', 'localhost');
if (!defined('_API_DB_USER_')) define('_API_DB_USER_', 'admin');
if (!defined('_API_DB_PASS_')) define('_API_DB_PASS_', 'admin');
if (!defined('_API_DB_NAME_')) define('_API_DB_NAME_', 'u488042670_demoerp');
if (!defined('_API_JWT_SECRET_')) define('_API_JWT_SECRET_', 'TejasERP_2026_Secure_Api_Secret_Key!#');

/**
 * Self-contained PDO Database Wrapper
 */
class ApiDB
{
    private static ?PDO $instance = null;

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $dsn = "mysql:host=" . _API_DB_HOST_ . ";dbname=" . _API_DB_NAME_ . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                self::$instance = new PDO($dsn, _API_DB_USER_, _API_DB_PASS_, $options);
            } catch (PDOException $e) {
                Response::error("Database connection failure: " . $e->getMessage(), 500);
                exit;
            }
        }
        return self::$instance;
    }

    public static function query(string $sql, array $params = []): PDOStatement
    {
        $pdo = self::getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $res = self::query($sql, $params)->fetch();
        return $res ?: null;
    }

    public static function fetchColumn(string $sql, array $params = [], int $col = 0)
    {
        return self::query($sql, $params)->fetchColumn($col);
    }

    public static function insert(string $table, array $data): int
    {
        $pdo = self::getConnection();
        $columns = array_keys($data);
        $fields = implode(", ", array_map(fn($c) => "`$c`", $columns));
        $placeholders = implode(", ", array_map(fn($c) => ":$c", $columns));

        $sql = "INSERT INTO `$table` ($fields) VALUES ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
        return (int)$pdo->lastInsertId();
    }

    public static function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $pdo = self::getConnection();
        $setParts = [];
        $params = [];
        foreach ($data as $col => $val) {
            $setParts[] = "`$col` = :set_$col";
            $params["set_$col"] = $val;
        }
        $setSql = implode(", ", $setParts);
        $sql = "UPDATE `$table` SET $setSql WHERE $where";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_merge($params, $whereParams));
        return $stmt->rowCount();
    }

    public static function delete(string $table, string $where, array $whereParams = []): int
    {
        $pdo = self::getConnection();
        $sql = "DELETE FROM `$table` WHERE $where";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($whereParams);
        return $stmt->rowCount();
    }

    public static function lastInsertId(): int
    {
        return (int)self::getConnection()->lastInsertId();
    }

    public static function beginTransaction(): bool
    {
        return self::getConnection()->beginTransaction();
    }

    public static function commit(): bool
    {
        return self::getConnection()->commit();
    }

    public static function rollBack(): bool
    {
        return self::getConnection()->rollBack();
    }
}

/**
 * HTTP Request Handler
 */
class Request
{
    private static ?array $parsedBody = null;

    public static function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public static function json(): array
    {
        if (self::$parsedBody === null) {
            $input = file_get_contents('php://input');
            $decoded = json_decode($input, true);
            self::$parsedBody = is_array($decoded) ? $decoded : [];
        }
        return self::$parsedBody;
    }

    public static function get(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }
        return $_GET[$key] ?? $default;
    }

    public static function post(?string $key = null, $default = null)
    {
        if ($key === null) {
            return array_merge($_POST, self::json());
        }
        return $_POST[$key] ?? self::json()[$key] ?? $default;
    }

    public static function input(?string $key = null, $default = null)
    {
        $all = self::all();
        if ($key === null) {
            return $all;
        }
        return $all[$key] ?? $default;
    }

    public static function all(): array
    {
        $json = self::json();
        return array_merge($_GET, $_POST, $json);
    }

    public static function header(string $name): ?string
    {
        $normalized = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        if (isset($_SERVER[$normalized])) {
            return $_SERVER[$normalized];
        }
        if (isset($_SERVER['REDIRECT_' . $normalized])) {
            return $_SERVER['REDIRECT_' . $normalized];
        }
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            if (is_array($headers)) {
                foreach ($headers as $k => $v) {
                    if (strcasecmp($k, $name) === 0) {
                        return $v;
                    }
                }
            }
        }
        return null;
    }

    public static function bearerToken(): ?string
    {
        $header = self::header('Authorization');
        if ($header && preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return trim($matches[1]);
        }
        // Also support X-Auth-Token, query parameter, or request body token
        return $_GET['token'] ?? $_POST['token'] ?? self::json()['token'] ?? self::header('X-Auth-Token') ?? null;
    }
}

/**
 * Standardized JSON API Response Helper
 */
class Response
{
    public static function json($data = null, int $statusCode = 200, string $message = '', array $meta = []): void
    {
        http_response_code($statusCode);
        $payload = [
            'success' => ($statusCode >= 200 && $statusCode < 300),
            'message' => $message,
            'data'    => $data,
        ];
        if (!empty($meta)) {
            $payload['meta'] = $meta;
        }
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit();
    }

    public static function success($data = null, string $message = 'Success', array $meta = [], int $statusCode = 200): void
    {
        self::json($data, $statusCode, $message, $meta);
    }

    public static function error(string $message = 'An error occurred', int $statusCode = 400, $errors = null): void
    {
        $payload = [
            'success' => false,
            'message' => $message,
        ];
        if ($errors !== null) {
            $payload['errors'] = $errors;
        }
        http_response_code($statusCode);
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit();
    }

    public static function notFound(string $message = 'Resource not found'): void
    {
        self::error($message, 404);
    }

    public static function unauthorized(string $message = 'Unauthorized'): void
    {
        self::error($message, 401);
    }

    public static function forbidden(string $message = 'Access forbidden'): void
    {
        self::error($message, 403);
    }
}

/**
 * Authentication and Token Manager
 */
class Auth
{
    public static function generateToken(array $user, int $expiryDays = 7): string
    {
        $header = base64_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload = base64_encode(json_encode([
            'id'         => $user['id'] ?? null,
            'user_name'  => $user['user_name'] ?? '',
            'user_code'  => $user['user_code'] ?? '',
            'user_type'  => $user['user_type'] ?? '',
            'store_name' => $user['store_name'] ?? '',
            'exp'        => time() + ($expiryDays * 86400)
        ]));
        $signature = hash_hmac('sha256', "$header.$payload", _API_JWT_SECRET_);
        return "$header.$payload.$signature";
    }

    public static function validateToken(?string $token): ?array
    {
        if (empty($token)) {
            return null;
        }
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }
        [$header, $payload, $signature] = $parts;
        $expected = hash_hmac('sha256', "$header.$payload", _API_JWT_SECRET_);
        if (!hash_equals($expected, $signature)) {
            return null;
        }
        $data = json_decode(base64_decode($payload), true);
        if (!is_array($data) || !isset($data['exp']) || $data['exp'] < time()) {
            return null;
        }
        return $data;
    }

    public static function user(): ?array
    {
        $token = Request::bearerToken();
        if ($token) {
            $decoded = self::validateToken($token);
            if ($decoded) {
                return $decoded;
            }
        }
        // Fallback to active PHP session
        if (!empty($_SESSION['IS_LOGIN']) && $_SESSION['IS_LOGIN'] === 'yes') {
            return [
                'user_name'  => $_SESSION['login_user'] ?? '',
                'user_code'  => $_SESSION['user_code'] ?? '',
                'user_type'  => $_SESSION['user_type'] ?? '',
                'store_name' => $_SESSION['store_name'] ?? '',
            ];
        }
        return null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function requireAuth(): array
    {
        $u = self::user();
        if (!$u) {
            Response::unauthorized("Authentication required. Provide a valid Bearer token or login session.");
        }
        return $u;
    }
}

/**
 * Core Helper Functions
 */
function apiDateInSQLFormat($date): string
{
    if (empty($date)) return date('Y-m-d');
    $time = strtotime(str_replace('/', '-', $date));
    return $time ? date('Y-m-d', $time) : date('Y-m-d');
}

function apiDateFormat($date, bool $includeTime = false): string
{
    if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
        return '';
    }
    $time = strtotime($date);
    if (!$time) return '';
    return $includeTime ? date('d-m-Y h:i:s A', $time) : date('d-m-Y', $time);
}

function apiGenerateBillNo(string $table, string $column, string $prefix): string
{
    $sql = "SELECT `$column` AS bill FROM `$table` WHERE `$column` LIKE :prefix ORDER BY id DESC LIMIT 1";
    $row = ApiDB::fetchOne($sql, [':prefix' => $prefix . '-%']);
    $nextNo = 1;
    if ($row && !empty($row['bill'])) {
        $parts = explode('-', $row['bill']);
        if (isset($parts[1]) && is_numeric($parts[1])) {
            $nextNo = (int)$parts[1] + 1;
        }
    } else {
        // Fallback if no matching prefix
        $row2 = ApiDB::fetchOne("SELECT `$column` AS bill FROM `$table` ORDER BY id DESC LIMIT 1");
        if ($row2 && !empty($row2['bill'])) {
            $parts = explode('-', $row2['bill']);
            if (isset($parts[1]) && is_numeric($parts[1])) {
                $nextNo = (int)$parts[1] + 1;
            }
        }
    }
    return strtoupper($prefix) . '-' . sprintf('%04d', $nextNo);
}

function apiGenerateAutoBarcode(): string
{
    $maxVal = 10000000;
    $r1 = ApiDB::fetchOne("SELECT MAX(CAST(barcode_number AS UNSIGNED)) AS max_bc FROM tbl_purchase WHERE barcode_number REGEXP '^[0-9]{8,12}$'");
    if (!empty($r1['max_bc']) && (int)$r1['max_bc'] > $maxVal) {
        $maxVal = (int)$r1['max_bc'];
    }
    $r2 = ApiDB::fetchOne("SELECT MAX(CAST(barcode_no AS UNSIGNED)) AS max_bc FROM tbl_item_master WHERE barcode_no REGEXP '^[0-9]{8,12}$'");
    if (!empty($r2['max_bc']) && (int)$r2['max_bc'] > $maxVal) {
        $maxVal = (int)$r2['max_bc'];
    }
    $candidate = (int)$maxVal + 1;
    while (true) {
        $candidateStr = (string)$candidate;
        $c1 = ApiDB::fetchColumn("SELECT COUNT(*) FROM tbl_purchase WHERE barcode_number = ?", [$candidateStr]);
        $c2 = ApiDB::fetchColumn("SELECT COUNT(*) FROM tbl_item_master WHERE barcode_no = ?", [$candidateStr]);
        if ($c1 == 0 && $c2 == 0) {
            return $candidateStr;
        }
        $candidate++;
    }
}

function apiPaginate(int $totalRecords, int $page, int $perPage): array
{
    $totalPages = (int)ceil($totalRecords / max($perPage, 1));
    return [
        'total_records' => $totalRecords,
        'current_page'  => $page,
        'per_page'      => $perPage,
        'total_pages'   => $totalPages,
        'has_next'      => $page < $totalPages,
        'has_prev'      => $page > 1,
    ];
}
