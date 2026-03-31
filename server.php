<?php
session_start();

header('Content-Type: application/json; charset=UTF-8');

$query = $_POST['query'] ?? '';

$result = "";

// 기록 저장
if ($query !== "") {

    if (!isset($_SESSION['history'][0]) || $_SESSION['history'][0] !== $query) {
        array_unshift($_SESSION['history'], $query);
        $_SESSION['history'] = array_unique($_SESSION['history']);
        $_SESSION['history'] = array_values($_SESSION['history']);
    }
}

// JSON 응답
echo json_encode([
    "query" => $query,
    "result" => $result,
    "history" => $_SESSION['history']
]);