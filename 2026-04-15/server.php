<?php
session_start();

// JSON 헤더
header('Content-Type: application/json; charset=UTF-8');

// 세션 초기화 (중요)
if (!isset($_SESSION['history'])) {
    $_SESSION['history'] = [];
}

// POST 값 받기
$query = isset($_POST['query']) ? trim($_POST['query']) : '';

$result = "";

// 검색어 저장 (중복 방지 + 최신순)
if ($query !== "") {

    // 이미 존재하면 제거 (중복 방지)
    if (($key = array_search($query, $_SESSION['history'])) !== false) {
        unset($_SESSION['history'][$key]);
    }

    // 맨 앞에 추가
    array_unshift($_SESSION['history'], $query);

    // 최대 개수 제한 (예: 10개)
    $_SESSION['history'] = array_slice($_SESSION['history'], 0, 10);
}

// JSON 응답
echo json_encode([
    "query" => $query,
    "result" => $result,
    "history" => $_SESSION['history']
], JSON_UNESCAPED_UNICODE);
?>