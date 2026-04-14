<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {

    $file = "users.json";
    $users = json_decode(file_get_contents($file), true);

    $id = $_POST['id'];
    $pw = $_POST['pw'];

    if (isset($users[$id]) && $users[$id]['pw'] == $pw) {
        $_SESSION['user'] = $users[$id]['name'];
        setcookie("remember_id", $id, time() + (86400 * 30), "/");
        header("Location: main1.php");
        exit;
    } else {
        $error = "아이디 또는 비밀번호 틀림";
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<title>Login</title>

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

.login-box {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    width: 320px;
    text-align: center;
}

h2 {
    margin-bottom: 20px;
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
    background: #4285f4;
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

/* 회원가입 / 기타 버튼 */
.signup-btn {
    margin-top: 10px;
    background: #fff;
    color: #4285f4;
    border: 1px solid #4285f4;
}


.error {
    color: red;
    font-size: 13px;
}
</style>
</head>

<body>

<div class="login-box">
    <h2>로그인</h2>

    <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="POST">
        <input type="text" name="id" placeholder="아이디" required>
        <input type="password" name="pw" placeholder="비밀번호" required>
        <button type="submit" name="login">로그인</button>
    </form>

    <div class="btn-group">

        <button class="signup-btn" onclick="location.href='signup.php'">
            회원가입
        </button>

        <button class="signup-btn" onclick="location.href='find_pw.php'">
            비밀번호 찾기
        </button>
        <button class="signup-btn" onclick="location.href='drawal.php'">
    회원탈퇴
        <button class="signup-btn back-btn" onclick="history.back()">
            뒤로가기
        
        </button>

    </div>
</div>

</body>
</html>