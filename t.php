<?php

require_once(__DIR__ . '/local_config.php');

if (!defined('TOKEN') || !defined('DB_USER') || !defined('DB_DB') || !defined('DB_PASSWORD')) {
    http_response_code(401);
    exit();
}

file_put_contents('access_t.log', date('U') . "\n", FILE_APPEND);

if (!isset($_GET['i']) || !isset($_GET['x'])) {
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
    (int)$_GET['i'],
    isset($_GET['t']) ? (float)$_GET['t'] : null,
    isset($_GET['p']) ? (float)$_GET['p'] : null,
    isset($_GET['r']) ? (int)$_GET['r'] : null,
    isset($_GET['h']) ? (float)$_GET['h'] : null
);

function logValues(
    int $id, ?float $temperature = null, ?float $pressure = null, ?float $rain = null, ?float $humidity = null
)
{
        $db = mysqli_connect("localhost", DB_USER, DB_PASSWORD, DB_DB);
    // Check connection
    if ($db->connect_error) {
        file_put_contents('error.log', 'ERROR DB ' . $db->connect_error . "\n", FILE_APPEND);
    } else {
        // prepare and bind
        $stmt = $db->prepare(
            "INSERT INTO measurements (sensor_id, temperature, pressure, rain, humidity) VALUES (?, ?, ?, ?, ?);"
        );
        $stmt->bind_param("iddid", $id, $temperature, $pressure, $rain, $humidity);
        $stmt->execute();
        $stmt->close();
        $db->close();
    }
}



