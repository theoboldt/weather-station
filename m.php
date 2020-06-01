<?php

require_once(__DIR__ . '/local_config.php');

if (!defined('TOKEN') || !defined('DB_USER') || !defined('DB_DB') || !defined('DB_PASSWORD')) {
    http_response_code(401);
    exit();
}

file_put_contents('access_m.log', date('U') . "\n", FILE_APPEND);

if (!isset($_GET['x'])) {
    http_response_code(400);
    exit();
}
if ($_GET['x'] !== TOKEN) {
    http_response_code(403);
    exit();
}

if ($_GET['test']) {
    http_response_code(200);
    exit();
}

http_response_code(204);
ignore_user_abort(true);
set_time_limit(0);
ob_start();
header('Connection: close');
header('Content-Length: ' . ob_get_length());
ob_end_flush();
ob_flush();
flush();

logValues(
    isset($_GET['m1']) ? (int)$_GET['m1'] : null,
    isset($_GET['m2']) ? (int)$_GET['m2'] : null,
);

function logValues(
    ?int $moisture1 = null,
    ?int $moisture2 = null
) {
    $db = mysqli_connect("localhost", DB_USER, DB_PASSWORD, DB_DB);
    // Check connection
    if ($db->connect_error) {
        file_put_contents('error.log', 'ERROR DB ' . $db->connect_error . "\n", FILE_APPEND);
    } else {
        // prepare and bind
        $stmt = $db->prepare(
            "INSERT INTO moisture (moisture01, moisture02) VALUES (?, ?);"
        );
        $stmt->bind_param($moisture1, $moisture2);
        $stmt->execute();
        $stmt->close();
        $db->close();
    }
}



