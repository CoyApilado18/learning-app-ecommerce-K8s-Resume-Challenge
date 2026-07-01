<?php
$mysqli = @new mysqli("mysql-service.dev.svc.cluster.local", "ecomuser", "ecompassword", "ecomdb");

if ($mysqli->connect_error) {
    http_response_code(503);
    echo "not ready";
    exit;
}

http_response_code(200);
echo "ready";