<?php
session_start();

// 검색 기록 초기화
if (!isset($_SESSION['history'])) {
    $_SESSION['history'] = [];
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<title>Main Search</title>
<style>
body {
    font-family: Arial;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0;
}

.center-box { width: 90%; max-width: 700px; }
.search-wrapper { position: relative; }

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
    width: 18px; height: 18px;
    cursor: pointer;
}

.search-icon::before {
    content: "";
    width: 12px; height: 12px;
    border: 2px solid #9e9e9e;
    border-radius: 50%;
    position: absolute;
}

.search-icon::after {
    content: "";
    width: 7px; height: 2px;
    background: #9e9e9e;
    position: absolute;
    transform: rotate(45deg);
    bottom: 0; right: 0;
}

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
    z-index: 10;
}

.item {
    display: flex;
    justify-content: space-between;
    padding: 12px;
    cursor: pointer;
}

.item:hover { background: #f7f7f7; }
.item.active { background: #e8e8e8; }

.delete-btn {
    width: 20px; height: 20px;
    border-radius: 50%;
    text-align: center;
    line-height: 20px;
    color: #bbb;
    cursor: pointer;
}

.delete-btn:hover { background: #eee; color: #555; }

#statusBox {
    position: fixed;
    top: 10px;
    right: 15px;
    font-size: 12px;
    color: black;
}
</style>
</head>

<body>

<div id="statusBox">연결안됨</div>

<div class="center-box">
    <div class="search-wrapper">

        <div class="search-box">
            <input type="text" id="searchInput" placeholder="검색어 입력" autocomplete="off">
            <span class="search-icon" onclick="submitSearch()"></span>
        </div>

        <div class="dropdown" id="dropdown">
            <?php
            foreach ($_SESSION['history'] as $index => $item) {
                echo "<div class='item'>
                        <span onclick=\"selectItem(this.parentElement)\">{$item}</span>
                        <span class='delete-btn' onclick=\"deleteItem(event, {$index})\">✕</span>
                      </div>";
            }
            ?>
        </div>

    </div>
</div>

<script>
const input = document.getElementById('searchInput');
const dropdown = document.getElementById('dropdown');
const statusBox = document.getElementById('statusBox');
let currentIndex = -1;

// 드롭다운 열기
input.addEventListener('focus', () => {
    if (dropdown.children.length > 0) dropdown.style.display = 'block';
});

// 바깥 클릭 시 닫기
document.addEventListener('click', e => {
    if (!e.target.closest('.search-wrapper')) {
        dropdown.style.display = 'none';
        currentIndex = -1;
    }
});

// 검색 실행
function submitSearch() {
    const value = input.value.trim();
    if (!value) return;

    statusBox.innerText = "연결중";

    fetch('server.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'query=' + encodeURIComponent(value)
    })
    .then(res => res.ok ? res.json() : Promise.reject())
    .then(data => {
        statusBox.innerText = "연결됨";
        renderHistory(data.history);
        dropdown.style.display = 'block';
        currentIndex = -1;
    })
    .catch(() => statusBox.innerText = "연결안됨");
}

// 기록 렌더링
function renderHistory(history) {
    let html = '';
    history.forEach((item, index) => {
        html += `<div class="item">
            <span onclick="selectItem(this.parentElement)">${item}</span>
            <span class="delete-btn" onclick="deleteItem(event, ${index})">✕</span>
        </div>`;
    });
    dropdown.innerHTML = html;
}

// 검색어 선택
function selectItem(el) {
    input.value = el.querySelector('span').innerText.trim();
}

// 삭제
function deleteItem(e, index) {
    e.stopPropagation();
    fetch('delete.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'index=' + index
    })
    .then(res => res.ok ? res.json() : Promise.reject())
    .then(data => {
        renderHistory(data.history); // 삭제 후 최신 기록 갱신
    })
    .catch(() => console.error('삭제 실패'));
}

// 키보드 이동 + 엔터
input.addEventListener('keydown', e => {
    const items = document.querySelectorAll('.item');
    if (items.length > 0) {
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            currentIndex = (currentIndex + 1) % items.length;
        }
        if (e.key === 'ArrowUp') {
            e.preventDefault();
            currentIndex = (currentIndex - 1 + items.length) % items.length;
        }
        items.forEach(i => i.classList.remove('active'));
        if (items[currentIndex]) {
            items[currentIndex].classList.add('active');
            input.value = items[currentIndex].querySelector('span').innerText.trim();
        }
    }

    if (e.key === 'Enter') {
        e.preventDefault();
        if (currentIndex >= 0 && items[currentIndex]) {
            input.value = items[currentIndex].querySelector('span').innerText.trim();
        }
        submitSearch();
        dropdown.style.display = 'none';
        currentIndex = -1;
    }
});
</script>

</body>
</html>