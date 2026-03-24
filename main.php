<?php
session_start();

// 배열 초기화
if (!isset($_SESSION['history'])) {
    $_SESSION['history'] = [];
}

//  드롭다운 유지 여부
$keepOpen = false;

// 삭제 처리
$isDeleting = false;
if (isset($_POST['delete_index'])) {
    $isDeleting = true;
    $keepOpen = true; //  삭제 시 드롭다운 유지

    $index = (int)$_POST['delete_index'];

    if (isset($_SESSION['history'][$index])) {
        unset($_SESSION['history'][$index]);
        $_SESSION['history'] = array_values($_SESSION['history']);
    }
}

// 검색 처리
if (!$isDeleting && isset($_GET['q'])) {
    $query = trim($_GET['q']);

    if ($query !== "") {
        if (!isset($_SESSION['history'][0]) || $_SESSION['history'][0] !== $query) {
            array_unshift($_SESSION['history'], $query);
            $_SESSION['history'] = array_unique($_SESSION['history']);
            $_SESSION['history'] = array_values($_SESSION['history']);
        }
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
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: #fff;
}

.center-box {
    width: 90%;
    max-width: 700px;
}

.search-wrapper {
    position: relative;
}

.search-box {
    border: 1px solid #ddd;
    border-radius: 30px;
    padding: 14px 50px 14px 20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    position: relative;
}

.search-box input {
    width: 100%;
    border: none;
    outline: none;
    font-size: 18px;
}

/*  CSS 돋보기 */
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
    position: absolute;
    width: 12px;
    height: 12px;
    border: 2px solid #9e9e9e;
    border-radius: 50%;
    top: 0;
    left: 0;
}

.search-icon::after {
    content: "";
    position: absolute;
    width: 7px;
    height: 2px;
    background: #9e9e9e;
    transform: rotate(45deg);
    bottom: 0;
    right: 0;
}

/* 드롭다운 */
.dropdown {
    position: absolute;
    top: 60px;
    width: 100%;
    background: #fff;
    border: 1px solid #eee;
    border-radius: 12px;
    display: none;
    box-shadow: 0 6px 15px rgba(0,0,0,0.08);
    overflow: hidden;
}

.item {
    display: flex;
    justify-content: space-between;
    padding: 12px 15px;
    cursor: pointer;
    font-size: 14px;
}

.item:hover {
    background: #f7f7f7;
}

.delete-btn {
    color: #bbb;
    font-size: 12px;
    cursor: pointer;
}

.delete-btn:hover {
    color: #888;
}

@media (max-width: 600px) {
    .search-box input {
        font-size: 16px;
    }
}
</style>
</head>

<body>

<div class="center-box">

    <!-- 검색 -->
    <form method="GET" id="searchForm">
        <div class="search-wrapper">

            <div class="search-box">
                <input type="text" name="q" id="searchInput"
                       value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>"
                       placeholder="검색어 입력" autocomplete="off">

                <!--  CSS 돋보기 -->
                <span class="search-icon" onclick="submitSearch()"></span>
            </div>

            <!-- 드롭다운 -->
            <div class="dropdown" id="dropdown">
                <?php
                if (!empty($_SESSION['history'])) {
                    foreach ($_SESSION['history'] as $index => $item) {

                        $safeJS = htmlspecialchars(json_encode($item), ENT_QUOTES);

                        echo "
                        <div class='item'>
                            <span onclick=\"selectItem({$safeJS})\">"
                            .htmlspecialchars($item).
                            "</span>
                            <span class='delete-btn' onclick=\"deleteItem(event, {$index})\">✕</span>
                        </div>";
                    }
                } else {
                    echo "<div class='item'>기록 없음</div>";
                }
                ?>
            </div>

        </div>
    </form>

    <!-- 삭제용 -->
    <form method="POST" id="deleteForm"></form>

</div>

<script>
const input = document.getElementById('searchInput');
const dropdown = document.getElementById('dropdown');
const form = document.getElementById('searchForm');

// 드롭다운 열기
input.addEventListener('focus', () => {
    dropdown.style.display = 'block';
});

// 바깥 클릭 시 닫기
document.addEventListener('click', (e) => {
    if (!e.target.closest('.search-wrapper')) {
        dropdown.style.display = 'none';
    }
});

// 검색어 클릭
function selectItem(value) {
    input.value = value;
}

//  돋보기 클릭 검색
function submitSearch() {
    form.submit();
}

//  삭제 (드롭다운 유지)
function deleteItem(e, index) {
    e.stopPropagation();

    const form = document.getElementById('deleteForm');
    form.innerHTML = `
        <input type="hidden" name="delete_index" value="${index}">
    `;
    form.submit();
}

//  삭제 후 드롭다운 다시 열기
<?php if ($keepOpen): ?>
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('dropdown').style.display = 'block';
});
<?php endif; ?>
</script>

</body>
</html>