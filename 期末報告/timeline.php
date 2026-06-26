<?php
require_once 'config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>時光長卷 - 回憶照片館</title>
    <style>
        :root {
            --bg-color: #FFF8EE; 
            --main-brown: #8B5E3C; 
            --accent-gold: #D4AF37; 
            --text-dark: #2D2926;
        }
        body {
            background-color: var(--bg-color);
            color: var(--text-dark);
            font-family: "Microsoft JhengHei", sans-serif;
            margin: 0;
        }
        
        .timeline-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        /* 工具列（搜尋框與返回按鈕） */
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            gap: 15px;
            flex-wrap: wrap;
        }
        .search-box {
            display: flex;
            gap: 8px;
            flex: 1;
            max-width: 500px;
        }
        .search-input {
            width: 100%;
            padding: 8px 16px;
            border: 1px solid #ddd;
            border-radius: 20px;
            outline: none;
            font-size: 0.95em;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
        }
        .search-input:focus {
            border-color: var(--main-brown);
        }
        .btn-search {
            background-color: var(--main-brown);
            color: white;
            border: none;
            padding: 8px 22px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.2s;
        }
        .btn-search:hover {
            background-color: #724c2f;
        }
        
        /* 💡 修正：返回完整時間軸的按鈕樣式 */
        .btn-clear-search {
            background-color: transparent;
            color: var(--main-brown);
            border: 2px solid var(--main-brown);
            padding: 8px 18px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
            display: none; /* 預設隱藏，有搜尋時才顯示 */
        }
        .btn-clear-search:hover {
            background-color: var(--main-brown);
            color: white;
        }

        /* 策展人獨立大區塊 */
        .curator-group-section {
            background: white;
            margin: 30px 0;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        }
        .curator-title {
            font-size: 1.3em;
            color: var(--main-brown);
            font-weight: bold;
            margin-bottom: 20px;
            border-bottom: 2px solid #f3eade;
            padding-bottom: 10px;
        }

        /* 回憶相簿卡片 */
        .memory-card {
            display: flex;
            background: #fff;
            border: 1px solid #eee;
            border-radius: 12px;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        }
        .memory-img-wrapper {
            width: 260px;
            min-width: 260px;
            height: 200px;
            position: relative;
        }
        .memory-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .memory-info {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .memory-title {
            font-size: 1.25em;
            margin: 0 0 6px 0;
            color: var(--text-dark);
        }
        .memory-meta {
            font-size: 0.88em;
            color: #666;
            margin-bottom: 12px;
        }
        .memory-content {
            font-size: 0.95em;
            color: #444;
            line-height: 1.6;
            margin-bottom: 15px;
            white-space: pre-line;
        }

        /* 互動留言區 */
        .comment-section {
            border-top: 1px dashed #eee;
            padding-top: 12px;
        }
        .comment-list {
            list-style: none;
            padding: 0;
            margin: 0 0 10px 0;
            max-height: 120px;
            overflow-y: auto;
            font-size: 0.88em;
        }
        .comment-item {
            margin-bottom: 5px;
            color: #555;
            line-height: 1.4;
        }
        .comment-form {
            display: flex;
            gap: 8px;
        }
        .comment-input {
            flex: 1;
            padding: 7px 14px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 0.85em;
        }
        .btn-comment {
            background: var(--main-brown);
            color: white;
            border: none;
            padding: 7px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85em;
        }
        
        .no-data {
            text-align: center;
            padding: 50px;
            color: #888;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        }
    </style>
</head>
<body>

    <div class="timeline-container">
        <div class="toolbar">
            <button type="button" id="btn-clear" class="btn-clear-search" onclick="resetSearch()">⬅ 返回完整長卷</button>
            
            <div class="search-box">
                <input type="text" id="search-input" class="search-input" placeholder="搜尋關鍵字（標題、年份、地點、故事）...">
                <button type="button" class="btn-search" onclick="loadTimeline()">搜尋</button>
            </div>
        </div>

        <div id="timeline-content"></div>
    </div>

<script>
        // 💡 1. 取得當前使用者的角色（判斷是否為 admin）
        const currentUserRole = "<?php echo isset($_SESSION['role']) ? $_SESSION['role'] : 'visitor'; ?>";

        document.addEventListener("DOMContentLoaded", function() {
            loadTimeline();
            
            document.getElementById('search-input').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    loadTimeline();
                }
            });
        });

        // 清空搜尋並返回初始狀態
        function resetSearch() {
            document.getElementById('search-input').value = ''; 
            loadTimeline(); 
        }

        // 載入與渲染時光長卷
        function loadTimeline() {
            const keyword = document.getElementById('search-input').value.trim();
            const timelineContent = document.getElementById('timeline-content');
            const clearBtn = document.getElementById('btn-clear');
            
            if (keyword !== '') {
                clearBtn.style.display = 'inline-block';
            } else {
                clearBtn.style.display = 'none';
            }
            
            timelineContent.innerHTML = '<div class="no-data">⏳ 正在載入時光長卷軸...</div>';

            // ⚠️ 檢查點：確保你的 get_timeline.php 真的放在 api 資料夾裡面喔！
            const url = `api/get_timeline.php?search=${encodeURIComponent(keyword)}`;

            fetch(url)
                .then(res => res.json())
                .then(response => {
                    // 💡 1. 關鍵修正：把真正的陣列從 response.data 裡面抽出來
                    const memories = response.data || response; 

                    if (!memories || memories.length === 0) {
                        timelineContent.innerHTML = '<div class="no-data">🔍 找不到任何符合條件的回憶物件。</div>';
                        return;
                    }

                    const groups = {};
                    // 💡 2. 對著正確的陣列 (memories) 執行 forEach
                    memories.forEach(item => {
                        if (!groups[item.username]) {
                            groups[item.username] = [];
                        }
                        groups[item.username].push(item);
                    });

                    let html = '';
                    
                    for (const curator in groups) {
                        const memories = groups[curator];
                        const isCurrentUser = <?php echo isset($_SESSION['username']) ? json_encode($_SESSION['username']) : 'null'; ?>;
                        const titleTag = (isCurrentUser && curator === isCurrentUser) ? '👑 我的專屬時光展位' : '🏛️ 策展人';

                        html += `
                            <div class="curator-group-section">
                                <div class="curator-title">${titleTag}：${curator}</div>
                        `;

                        memories.forEach(memory => {
                            let metaStr = `⏳ 西元 ${memory.year} 年`;
                            if(memory.month) metaStr += ` ${memory.month} 月`;
                            if(memory.day) metaStr += ` ${memory.day} 日`;
                            if(memory.location) metaStr += ` 📍 ${memory.location}`;

                            let commentsHtml = '';
                            if (memory.comments && memory.comments.length > 0) {
                                memory.comments.forEach(c => {
                                    commentsHtml += `<li class="comment-item"><strong>${c.visitor_name}</strong>: ${c.comment_text}</li>`;
                                });
                            } else {
                                commentsHtml = '<li class="comment-item" style="color:#bbb; font-style:italic;">尚無觀展留言...</li>';
                            }

                            html += `
                                <div class="memory-card">
                                    <div class="memory-img-wrapper">
                                        <img src="${memory.photo_path}" class="memory-img" alt="${memory.title}">
                                    </div>
                                    <div class="memory-info">
                                        <div>
                                            <h3 class="memory-title">${memory.title}</h3>
                                            <div class="memory-meta">${metaStr}</div>
                                            <div class="memory-content">${memory.content}</div>
                                            
                                            ${currentUserRole === 'admin' ? `
                                                <div style="text-align: right; margin-bottom: 10px;">
                                                    <button onclick="deleteMemory(${memory.id})" style="background-color: #E53E3E; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 0.85em; transition: background 0.2s;" onmouseover="this.style.backgroundColor='#C53030'" onmouseout="this.style.backgroundColor='#E53E3E'">
                                                        🗑️ 管理員強制下架
                                                    </button>
                                                </div>
                                            ` : ''}
                                        </div>
                                        
                                        <div class="comment-section">
                                            <ul class="comment-list" id="comments-of-${memory.id}">
                                                ${commentsHtml}
                                            </ul>
                                            <div class="comment-form">
                                                <input type="text" id="text-input-${memory.id}" class="comment-input" placeholder="留下一筆溫暖的共鳴回應...">
                                                <button type="button" class="btn-comment" onclick="addComment(${memory.id})">發送</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });

                        html += `</div>`;
                    }

                    timelineContent.innerHTML = html;
                })
                .catch(err => {
                    console.error("Error:", err);
                    timelineContent.innerHTML = '<div class="no-data">❌ 讀取時光長卷失敗，請打開 F12 確認 API 路徑是否正確。</div>';
                });
        }

        function addComment(memoryId) {
            const textInput = document.getElementById(`text-input-${memoryId}`);
            const commentList = document.getElementById(`comments-of-${memoryId}`);

            if(!textInput.value.trim()) {
                alert('請輸入留言內容！');
                return;
            }

            const formData = new FormData();
            formData.append('memory_id', memoryId);
            formData.append('comment_text', textInput.value);

            // ⚠️ 檢查點：確保 add_comment.php 也在 api 資料夾內
            fetch('api/add_comment.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    if(commentList.innerText.includes("尚無")) {
                        commentList.innerHTML = '';
                    }
                    const li = document.createElement('li');
                    li.className = 'comment-item';
                    li.innerHTML = `<strong>${data.visitor_name}</strong>: ${data.comment_text}`;
                    commentList.appendChild(li);
                    textInput.value = '';
                    commentList.scrollTop = commentList.scrollHeight;
                } else {
                    alert('留言失敗：' + data.message);
                }
            })
            .catch(err => {
                alert('留言連線失敗！請確認路徑。');
            });
        }

        // 💡 3. 新增管理員刪除的 API 呼叫函式
        function deleteMemory(memoryId) {
            if(!confirm('🚨 警告：管理員您好，您確定要強制下架並徹底清除此違規展品嗎？此操作不可逆！')) return;
            
            const formData = new FormData();
            formData.append('memory_id', memoryId);

            // ⚠️ 檢查點：確保 delete_memory.php 也在 api 資料夾內
            fetch('api/delete_memory.php', { 
                method: 'POST', 
                body: formData 
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    alert('🎉 成功下架！');
                    loadTimeline(); // 重新整理畫面
                } else {
                    alert('❌ 刪除失敗：' + data.message);
                }
            })
            .catch(err => {
                alert('連線失敗，找不到 api/delete_memory.php！');
            });
        }
    </script>

<?php include 'includes/footer.php'; ?>
</body>
</html>