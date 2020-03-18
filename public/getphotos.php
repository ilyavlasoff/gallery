<?php
if (isset($_POST['sessionId'])) {
    session_id($_POST['sessionId']);
    session_start();
}
else {
    SendError('Authorization required', 401);
}

require_once ('../db/DBExecutor.php');

$sessionId = $_POST['sessionId'] ?? SendError('Arguments error');
$pageId = $_POST['pageId'] ?? SendError('Arguments error');
$quan = $_POST['quan'] ?? SendError('Arguments error');
$offset = $_POST['offset'] ?? SendError('Arguments error');
$mode = $_POST['mode'] ?? SendError('Arguments error');

if (isset($_SESSION['logged']) && isset($_SESSION['username'])) {
    $elements = DBExecutor::GetPosts($pageId, $quan, $offset, $mode);
    if (count($elements) != 0) {
        $content = "";
        foreach ($elements as $element) {
            $content .=  "<a href=\"{$element->path}\" alt=\"{$element->phid}\"></a>";
        }
        $msg = ['status' => 'ok', 'loaded' => count($elements), 'message' => $content];
        echo json_encode($msg);
    }
    else {
        SendError('Profile is empty');
    }
}
else {
    SendError('Authorization required', 401);
}

function SendError(string $message, int $code = 404) {
    //http_response_code($code);
    $errMsg = ['status' => 'error', 'message' => $message];
    echo json_encode($errMsg);
    exit();
}

