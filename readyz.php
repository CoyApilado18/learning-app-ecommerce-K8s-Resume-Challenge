<?php
$dbHost = getenv('DB_HOST') ?: 'mysql-service';
$dbUser = getenv('DB_USER') ?: 'ecomuser';
$dbPassword = getenv('DB_PASSWORD') ?: 'ecompassword';
$dbName = getenv('DB_NAME') ?: 'ecomdb';
$mysqli = @new mysqli($dbHost, $dbUser, $dbPassword, $dbName);

if ($mysqli->connect_error) {
    http_response_code(503);
    echo "not ready";
    exit;
}

http_response_code(200);
echo "ready";
