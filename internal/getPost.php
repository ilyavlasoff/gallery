<?php

use App\db\DBConnector;

$reqAuth = false;
if ($sid = isset($_POST['sessionId'])) {
    session_id($sid);
    session_start();
    if (!isset($_SESSION['logged']) || !isset($_SESSION['username'])) {
        $reqAuth = true;
    }
}
else {
    $reqAuth = true;
}

if ($reqAuth) {
    sendResp(['error' => 'Authorization required'], 401);
}

$photoId = $_POST['phId'] ?? sendResp(['error' => 'Arguments error'], 400);

try {
    $val = DBExecutor::GetPhotoFullData($photoId);
    sendResp($val, 200);
}
catch (Exception $ex) {
    sendResp(['error' => 'Arguments error'], 400);
}

function sendResp(array $param, int $status) {
    http_response_code($status);
    echo json_encode($param);
}