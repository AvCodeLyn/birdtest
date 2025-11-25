<?php

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "quiz_db";

function getPdoConnection(): PDO
{
    global $host, $user, $pass, $dbname;

    $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (PDOException $e) {
        throw new RuntimeException('Błąd połączenia z bazą danych (PDO): ' . $e->getMessage(), 0, $e);
    }

    return $pdo;
}

function getMysqliConnection(): mysqli
{
    global $host, $user, $pass, $dbname;

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $conn = new mysqli($host, $user, $pass, $dbname);
        $conn->set_charset('utf8mb4');
    } catch (mysqli_sql_exception $e) {
        throw new RuntimeException('Błąd połączenia z bazą danych (MySQLi): ' . $e->getMessage(), 0, $e);
    }

    return $conn;
}
