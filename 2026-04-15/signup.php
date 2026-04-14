<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $file = "users.json";
    $users = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

    $id = $_POST['id'];
    $pw = $_POST['pw'];
    $name = $_POST['name'];

    if (isset($users[$id])) {
        $error = "이미 존재하는 아이디";
    } else {
        $users[$id] = [
            "pw" => $pw,
            "name" => $name
        ];

        file_put_contents($file, json_encode($users, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<title>Signup</title>

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

.signup-box {
    background: white;
    padding: 30px;
    border-radius: 12px;
    width: 320px;
    text-align: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

form {
    display: flex;
    flex-direction: column;
    align-items: center;
}

input {
    width: 100%;
    max-width: 260px;
    padding: 10px;
    margin: 8px 0;
    border: 1px solid #ddd;
    border-radius: 6px;
}

button {
    width: 100%;
    max-width: 260px;
    padding: 10px;
    margin-top: 10px;
    background: #34a853;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

/* 버튼 그룹 */
.btn-group {
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* 뒤로가기 버튼 스타일 */
.back-btn {
    width: 100%;
    max-width: 260px;
    padding: 10px;
    margin-top: 10px;
    background: #fff;
    color: #4285f4;
    border: 1px solid #4285f4;
    border-radius: 6px;
    cursor: pointer;
}

.error {
    color: red;
    font-size: 13px;
}
</style>
</head>

<body>

<div class="signup-box">
    <h2>회원가입</h2>

    <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="POST">
        <input type="text" name="name" placeholder="이름" required>
        <input type="text" name="id" placeholder="아이디" required>
        <input type="password" name="pw" placeholder="비밀번호" required>
        <button type="submit">가입하기</button>
    </form>

    <!-- 뒤로가기 추가 -->
    <div class="btn-group">
        <button class="back-btn" onclick="history.back()">
            뒤로가기
        </button>
    </div>

</div>

</body>
</html>