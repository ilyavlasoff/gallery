<?php
if (isset($_POST['sessionId'])) {
    session_id($_POST['sessionId']);
    session_start();
}
else {
    SendResponse(['errno' => 'Authorization required'], 401);
}


require_once ('../db/DBExecutor.php');

$to = $_POST['to'] ?? SendResponse(['errno' => 'Arguments error'], 400);
$deny = $_POST['deny'] ?? SendResponse(['errno' => 'Arguments error'], 400);
if (!isset($_SESSION['logged']) || !isset($_SESSION['username']) || $to === $_SESSION['username']) {
    SendResponse(['errno' => 'Access denied'], 401);
}
try {
    $inserted = DBExecutor::Subscribe($_SESSION['username'], $to, $deny);
    if ($inserted !== 1) throw new Exception();
    else {
        SendResponse(['message' => 'Ok'], 200);
    }
}
catch (Exception $ex) {
    SendResponse(['message' => 'Subscription error'], 404);
}

function SendResponse(array $resp, int $code) {
    http_response_code($code);
    echo json_encode($resp);
    exit();
}