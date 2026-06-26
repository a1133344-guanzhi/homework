<?php
require_once 'config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 撈取最新照片，數量調高到 8 張讓直向長度足夠
try {
    // 💡 修正：首頁只撈取公開(public)的照片，避免隱私外洩
    $stmt = $pdo->query("SELECT photo_path FROM memories WHERE status = 'public' ORDER BY id DESC LIMIT 8");
    $db_photos = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
    $db_photos = [];
}

// 備用預設照片（若資料庫沒照片時展現）
$default_photos = [
    "https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?q=80&w=400&auto=format&fit=crop",
    "https://images.unsplash.com/photo-1452421822248-d4c2b47f0c81?q=80&w=400&auto=format&fit=crop",
    "https://images.unsplash.com/photo-1501854140801-50d01698950b?q=80&w=400&auto=format&fit=crop",
    "https://images.unsplash.com/photo-1440635535359-2c700dfa4f00?q=80&w=400&auto=format&fit=crop"
];

$display_photos = !empty($db_photos) ? $db_photos : $default_photos;

include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>回憶照片館 - 數位歷史長卷</title>
    <style>
        :root {
            --bg-color: #FFF8EE; 
            --main-brown: #8B5E3C; 
            --accent-gold: #D4AF37; 
            --text-dark: #2D2926;
            --card-bg: #FFFFFF;
        }
        body {
            background-color: var(--bg-color);
            color: var(--text-dark);
            font-family: "Georgia", "Noto Serif TC", "Microsoft JhengHei", serif;
            margin: 0;
            padding: 0;
            line-height: 1.8;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        /* 特展序言卡片 */
        .museum-intro-card {
            background: var(--card-bg);
            padding: 45px 50px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(139, 94, 60, 0.06);
            border-top: 5px solid var(--main-brown);
            margin-bottom: 50px;
            text-align: center;
            position: relative;
        }
        
        .museum-title {
            font-size: 2.2em;
            color: var(--main-brown);
            margin-top: 0;
            margin-bottom: 10px;
            font-weight: bold;
            letter-spacing: 2px;
        }
        
        .museum-subtitle {
            font-size: 1.1em;
            color: var(--accent-gold);
            font-weight: 600;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 30px;
        }

        .museum-divider {
            width: 80px;
            height: 2px;
            background-color: var(--accent-gold);
            margin: 0 auto 30px auto;
        }

        .intro-text {
            font-size: 1.15em;
            color: #4A4A4A;
            max-width: 850px;
            margin: 0 auto 25px auto;
            text-align: justify;
            text-justify: inter-ideographic;
        }

        .intro-emphasis {
            font-size: 1.2em;
            color: var(--main-brown);
            font-weight: bold;
            background: #FDF6EC;
            padding: 15px 25px;
            border-left: 4px solid var(--accent-gold);
            border-radius: 0 12px 12px 0;
            margin: 35px auto;
            max-width: 850px;
            text-align: left;
        }

        /* 💡 修正：引導按鈕區塊，僅保留唯一的訪客觀展功能 */
        .cta-group {
            display: flex;
            justify-content: center;
            margin-top: 35px;
        }
        .btn-cta-visitor {
            background-color: var(--main-brown);
            color: white;
            text-decoration: none;
            padding: 14px 45px;
            border-radius: 30px;
            font-weight: bold;
            font-size: 1.1em;
            letter-spacing: 1px;
            transition: all 0.3s;
            box-shadow: 0 4px 18px rgba(139, 94, 60, 0.25);
        }
        .btn-cta-visitor:hover {
            background-color: #724c2f;
            transform: translateY(-2px);
            box-shadow: 0 6px 22px rgba(139, 94, 60, 0.35);
        }

        /* 大標題樣式 */
        .section-title {
            font-size: 1.6em;
            color: var(--main-brown);
            font-weight: bold;
            margin: 60px 0 25px 0;
            position: relative;
            padding-left: 15px;
            border-left: 5px solid var(--main-brown);
        }

        /* 系統功能三欄網格 */
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }
        .feature-item {
            background: var(--card-bg);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.02);
            border: 1px solid #F3EADE;
            transition: all 0.3s ease;
            display: flex;
            gap: 20px;
        }
        .feature-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(139, 94, 60, 0.08);
            border-color: var(--accent-gold);
        }
        .feature-icon {
            font-size: 2.2em;
            line-height: 1;
            margin-top: 5px;
        }
        .feature-text h4 {
            margin: 0 0 10px 0;
            font-size: 1.2em;
            color: var(--main-brown);
            font-weight: bold;
        }
        .feature-text p {
            margin: 0;
            font-size: 0.95em;
            color: #666;
            line-height: 1.6;
        }

        /* 縮圖膠捲橫向牆 */
        .photo-gallery-wall {
            display: flex;
            gap: 15px;
            overflow-x: auto;
            padding: 15px 5px;
            scroll-behavior: smooth;
            margin-bottom: 20px;
        }
        .photo-gallery-wall::-webkit-scrollbar {
            height: 6px;
        }
        .photo-gallery-wall::-webkit-scrollbar-thumb {
            background: #E0D5C1;
            border-radius: 10px;
        }
        .gallery-photo-card {
            min-width: 240px;
            width: 240px;
            height: 160px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border: 4px solid white;
            transition: transform 0.3s;
        }
        .gallery-photo-card:hover {
            transform: scale(1.03) rotate(1deg);
        }
        .gallery-photo-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>
<body>

    <div class="container">
        
        <div class="museum-intro-card">
            <h1 class="museum-title">🏛️ 時光長卷</h1>
            <div class="museum-subtitle">數位歷史回憶博物館</div>
            <div class="museum-divider"></div>
            
            <p class="intro-text">
                歷史不僅僅記載於厚重的教科書中，更流淌在每個人家中的老相簿、泛黃的照片，以及照片背後那段不經意卻無比珍貴的生命點滴。然而，傳統的電子相簿多流於單純的資料夾分類，往往缺乏了「歷史推移」的厚重厚實感。
            </p>
            
            <div class="intro-emphasis">
                💡 本系統的核心目標，是建構一個具有人文溫度的「動態歷史長卷」。透過多使用者獨立的策展機制，將破碎的個人回憶重組，並在同一條時間軸上交織並蓄，進而拼湊出屬於大眾的集體回憶與時代縮影。
            </div>

            <div class="cta-group">
                <a href="timeline.php" class="btn-cta-visitor">✨ 以訪客身分進入觀展</a>
            </div>
        </div>

        <div class="section-title">🖼️ 最新館藏微光快顯</div>
        <div class="photo-gallery-wall">
            <?php foreach($display_photos as $path): ?>
                <div class="gallery-photo-card">
                    <img src="<?php echo htmlspecialchars($path); ?>" alt="館藏回憶照片">
                </div>
            <?php endforeach; ?>
        </div>

        <div class="section-title">✨ 展覽館核心機制</div>
        <div class="feature-grid">
            
            <div class="feature-item">
                <div class="feature-icon">⏳</div>
                <div class="feature-text">
                    <h4>智慧時間軸排序</h4>
                    <p>上傳照片時僅需輸入西元年月日，系統後端會自動依時間序列精準遞增排序，流暢呈現歷史推移的軌跡。</p>
                </div>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">👥</div>
                <div class="feature-text">
                    <h4>多策展人獨立空間</h4>
                    <p>全站照片依策展人進行大歸類標示。登入後即可擁有專屬獨立展位、發布照片，並保有隨時設定公開或私密的彈性權限。</p>
                </div>
            </div>

            <div class="feature-item">
                <div class="feature-icon">💬</div>
                <div class="feature-text">
                    <h4>時光共鳴互動</h4>
                    <p>不論是策展人或一般訪客，皆能在各項珍貴展品下方留下一筆溫暖的留言，透過跨越時空的對話，找回大眾共同的集體記憶。</p>
                </div>
            </div>
            
        </div>

    </div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
