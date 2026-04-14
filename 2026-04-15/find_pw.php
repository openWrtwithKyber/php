<?php
$result = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $file = "users.json";
    $users = json_decode(file_get_contents($file), true);

    $id = $_POST['id'];

    if (isset($users[$id])) {
        $result = "비밀번호: " . $users[$id]['pw'];
    } else {
        $result = "존재하지 않는 아이디";
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<title>비밀번호 찾기</title>

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

/* 🔥 입력칸 + 버튼 간격 완전 동일 */
input, button {
    width: 100%;
    max-width: 260px;
    padding: 10px;
    margin-top: 10px;
    border-radius: 6px;
    border: 1px solid #ddd;
}

/* 찾기 버튼 */
button[type="submit"] {
    background: #fbbc05;
    cursor: pointer;
}

/* 뒤로가기 버튼 */
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
    <h2>비밀번호 찾기</h2>

    <form method="POST">
        <input type="text" name="id" placeholder="아이디 입력" required>

        <button type="submit">찾기</button>

        <!-- form 안으로 넣어서 간격 통일 -->
        <button type="button" class="back-btn" onclick="history.back()">
            뒤로가기
        </button>
    </form>

    <div class="result"><?php echo $result; ?></div>
</div>

</body>
</html>