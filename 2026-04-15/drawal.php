<?php
$result = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $file = "users.json";
    $users = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

    $id = $_POST['id'];
    $pw = $_POST['pw'];

    if (isset($users[$id]) && $users[$id]['pw'] == $pw) {

        unset($users[$id]);

        file_put_contents($file, json_encode($users, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $result = "회원탈퇴 완료";

    } else {
        $result = "아이디 또는 비밀번호 틀림";
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<title>회원탈퇴</title>

<style>
body {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    font-family: Arial;
    background: #f5f5f5;
    margin: 0;
}

.box {
    background: white;
    padding: 30px;
    border-radius: 12px;
    width: 320px;
    text-align: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

input, button {
    width: 100%;
    max-width: 260px;
    padding: 10px;
    margin-top: 10px;
    border-radius: 6px;
    border: 1px solid #ddd;
}

button {
    background: #ea4335;
    color: white;
    cursor: pointer;
}

.back-btn {
    background: #fff;
    color: #4285f4;
    border: 1px solid #4285f4;
}

.result {
    margin-top: 15px;
    font-weight: bold;
}
</style>
</head>

<body>

<div class="box">
    <h2>회원탈퇴</h2>

    <form method="POST">
        <input type="text" name="id" placeholder="아이디" required>
        <input type="password" name="pw" placeholder="비밀번호" required>

        <button type="submit">회원탈퇴</button>

        <button type="button" class="back-btn" onclick="history.back()">
            뒤로가기
        </button>
    </form>

    <div class="result"><?php echo $result; ?></div>
</div>

</body>
</html>