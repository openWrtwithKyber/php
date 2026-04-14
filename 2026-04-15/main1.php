<?php
session_start();

if (isset($_GET['logout'])) {
    unset($_SESSION['user']);
    setcookie("remember_id", "", time() - 3600, "/");
    header("Location: main1.php");
    exit;
}

// users 먼저 로드 (필수)
$file = "users.json";
$users = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

// 쿠키 → 세션 복구
if (!isset($_SESSION['user']) && isset($_COOKIE['remember_id'])) {
    $id = $_COOKIE['remember_id'];

    if (isset($users[$id])) {
        $_SESSION['user'] = $id;
    }
}

// 로그인 상태
$loggedIn = isset($_SESSION['user']);

// 이름 처리
$userName = "";

if ($loggedIn) {
    $id = $_SESSION['user'];

    if (isset($users[$id])) {
        $name = $users[$id]['name'];

        // 성 제거 + 2글자
        $userName = mb_substr($name, 1, 2);
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<title>Search</title>

<style>
body {
    font-family: Arial, sans-serif;
    margin: 0;
}

/* 🔹 상단 */
.top-bar {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    padding: 15px 20px;
    gap: 15px;
    font-size: 14px;
}

.top-bar a {
    text-decoration: none;
    color: black;
}

/* 🔹 앱 버튼 */
.apps-btn {
    width: 24px;
    height: 24px;
    display: grid;
    grid-template-columns: repeat(3, 6px);
    grid-gap: 3px;
    cursor: pointer;
}

.apps-btn div {
    width: 6px;
    height: 6px;
    background: #555;
    border-radius: 50%;
}

/* 🔹 앱 메뉴 */
.apps-menu {
    position: absolute;
    top: 60px;
    right: 20px;
    width: 260px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 12px;
    display: none;
    padding: 15px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.apps-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    text-align: center;
}

.app-item {
    cursor: pointer;
    font-size: 12px;

    display: flex;
    flex-direction: column;   /* 세로 정렬 */
    align-items: center;      /* 가운데 정렬 */
    justify-content: center;
}

.app-icon {
    width: 40px;
    height: 40px;
    object-fit: contain;
    margin-bottom: 5px;
}

/* 🔹 프로필 */
.profile {
    width: 32px;
    height: 32px;
    background: #bbb;
    border-radius: 50%;
}

/* 🔹 중앙 */
.center {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-top: 150px;
}

.logo img {
    width: 270px;
    margin-bottom: 30px;
}

/* 🔹 검색창 */
.search-wrapper {
    width: 90%;
    max-width: 600px;
    position: relative;
}

.search-box {
    border: 1px solid #ddd;
    border-radius: 30px;
    padding: 14px 50px 14px 20px;
}

.search-box input {
    width: 100%;
    border: none;
    outline: none;
    font-size: 18px;
}

.search-icon {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.search-icon::before {
    content: "";
    width: 12px;
    height: 12px;
    border: 2px solid #9e9e9e;
    border-radius: 50%;
    position: absolute;
}

.search-icon::after {
    content: "";
    width: 7px;
    height: 2px;
    background: #9e9e9e;
    position: absolute;
    transform: rotate(45deg);
    bottom: 0;
    right: 0;
}

/* 🔹 드롭다운 */
.dropdown {
    position: absolute;
    top: 60px;
    width: 100%;
    background: #fff;
    border: 1px solid #eee;
    border-radius: 12px;
    display: none;
    max-height: 250px;
    overflow-y: auto;
}

.item {
    display: flex;
    justify-content: space-between;
    padding: 12px;
    cursor: pointer;
}

.item:hover { background: #f7f7f7; }

.delete-btn {
    color: #bbb;
    cursor: pointer;
}
.profile-wrap {
    position: relative;
    display: inline-block;
}

.logout-text {
    position: absolute;
    top: 40px;
    right: 0;
    background: black;
    color: white;
    font-size: 12px;
    padding: 5px 8px;
    border-radius: 6px;
    display: none;
    white-space: nowrap;
}

.user-name {
    position: absolute;
    bottom: -10px;
    right: 50%;
    transform: translateX(50%);
    font-size: 11px;
    color: #333;
    font-weight: bold;
}
</style>
</head>

<body>

<!-- 🔹 상단 -->
<div class="top-bar">
    <a href="https://mail.google.com" target="_blank">Gmail</a>
    <a href="https://www.google.com/imghp" target="_blank">이미지</a>

    <div class="apps-btn" onclick="toggleApps()">
        <div></div><div></div><div></div>
        <div></div><div></div><div></div>
        <div></div><div></div><div></div>
    </div>

    <div class="profile-wrap"
     onmouseenter="showLogout()"
     onmouseleave="hideLogout()"
     onclick="handleProfileClick()">

    <img class="profile"
     src="https://upload.wikimedia.org/wikipedia/commons/7/7c/Profile_avatar_placeholder_large.png"
     style="cursor:pointer;">

     <div class="user-name">
        <?= $userName ?>
    </div>

    <div class="logout-text" id="logoutText">로그아웃</div>
</div>
</div>

<!-- 🔹 앱 메뉴 -->
<div class="apps-menu" id="appsMenu">
    <div class="apps-grid">

        <div class="app-item" onclick="go('https://www.google.com')">
            <img class="app-icon" src="https://cdn-icons-png.flaticon.com/512/300/300221.png">
            Google
        </div>

        <div class="app-item" onclick="go('https://www.youtube.com')">
            <img class="app-icon" src="https://cdn-icons-png.flaticon.com/512/1384/1384060.png">
            YouTube
        </div>

        <div class="app-item" onclick="go('https://mail.google.com')">
            <img class="app-icon" src="https://cdn-icons-png.flaticon.com/512/732/732200.png">
            Gmail
        </div>

     <div class="app-item" onclick="go('https://drive.google.com')">
    <img class="app-icon" src="https://upload.wikimedia.org/wikipedia/commons/d/da/Google_Drive_logo.png">
    Drive
</div>

        <div class="app-item" onclick="go('https://maps.google.com')">
            <img class="app-icon" src="https://cdn-icons-png.flaticon.com/512/684/684908.png">
            Maps
        </div>

<div class="app-item" onclick="go('https://translate.google.com')">
    <img class="app-icon" src="https://upload.wikimedia.org/wikipedia/commons/d/db/Google_Translate_Icon.png">
    Translate
</div>

    </div>
</div>

<!-- 🔹 중앙 -->
<div class="center">

    <div class="logo">
        <img src="https://www.google.com/images/branding/googlelogo/2x/googlelogo_color_272x92dp.png">
    </div>

    <div class="search-wrapper">
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="검색어 입력">
            <span class="search-icon" onclick="submitSearch()"></span>
        </div>

        <div class="dropdown" id="dropdown"></div>
    </div>

</div>

<script>
const appsMenu = document.getElementById('appsMenu');

// 앱 토글
function toggleApps() {
    appsMenu.style.display =
        appsMenu.style.display === 'block' ? 'none' : 'block';
}

// 바깥 클릭 닫기
document.addEventListener('click', e => {
    if (!e.target.closest('.apps-btn') && !e.target.closest('.apps-menu')) {
        appsMenu.style.display = 'none';
    }
});

function goLogin() {
    window.location.href = "login.php";
}

// 이동
function go(url) {
    window.open(url, '_blank');
}

// 검색
function submitSearch() {
    const value = document.getElementById('searchInput').value.trim();
    if (!value) return;

    window.location.href =
        "https://www.google.com/search?q=" + encodeURIComponent(value);
}

// 엔터 검색
document.getElementById('searchInput').addEventListener('keydown', e => {
    if (e.key === 'Enter') submitSearch();
});
const loggedIn = <?= $loggedIn ? 'true' : 'false' ?>;

function showLogout() {
    if (!loggedIn) return;
    document.getElementById('logoutText').style.display = 'block';
}

function hideLogout() {
    document.getElementById('logoutText').style.display = 'none';
}

function handleProfileClick() {
    if (loggedIn) {
        window.location.href = "?logout=1";
    } else {
        window.location.href = "login.php";
    }
}
</script>

</body>
</html>