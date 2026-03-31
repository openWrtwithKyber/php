<?php
session_start();

$index = isset($_POST['index']) ? intval($_POST['index']) : -1;

if ($index >= 0 && isset($_SESSION['history'][$index])) {
    unset($_SESSION['history'][$index]);
    $_SESSION['history'] = array_values($_SESSION['history']);
}

// 삭제 후 최신 기록 반환
header('Content-Type: application/json; charset=UTF-8');
echo json_encode(['history' => $_SESSION['history']]);