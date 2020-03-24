<?php
if (isset($_POST['sessionId'])) {
    session_id($_POST['sessionId']);
    session_start();
}
else {
    SendError('Authorization required', 401);
}

require_once ('../db/DBExecutor.php');

$sessionId = $_POST['sessionId'] ?? SendError('Arguments error', 400);
$pageId = $_POST['pageId'] ?? SendError('Arguments error', 400);
$quan = $_POST['quan'] ?? SendError('Arguments error', 400);
$offset = $_POST['offset'] ?? SendError('Arguments error', 400);
$mode = $_POST['mode'] ?? SendError('Arguments error', 400);

try {
    if (isset($_SESSION['logged']) && isset($_SESSION['username'])) {
        $elements = DBExecutor::GetPosts($pageId, $quan, $offset, $mode);
        http_response_code(200);
        if (count($elements) != 0) {
            $content = "";
            foreach ($elements as $element) {
                $content .= "<div style=\"background-image: url(${element['path']})\" class='ph'></div>";
            }
            $msg = ['loaded' => count($elements), 'message' => $content];
        } else {
            $msg = ['loaded' => 0, 'message' => "<p>Profile is empty</p>"];

        }
        echo json_encode($msg);
    } else {
        SendError('Authorization required', 401);
    }
}
catch (Exception $e) {
    SendError($e->getMessage(), 404);
}

function SendError(string $message, int $code) {
    http_response_code($code);
    $errMsg = ['message' => $message];
    echo json_encode($errMsg);
    exit();
}

