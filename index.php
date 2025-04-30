<?php
// Veritabanı bağlantısı
include 'db_config.php';

// Bugünkü etkinlikleri al
$todayEvents = [];
try {
    if (isset($db)) {
        $today = date('Y-m-d');
        $stmt = $db->prepare("SELECT * FROM events WHERE event_date = ? ORDER BY priority DESC");
        $stmt->execute([$today]);
        $todayEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $todayEvents = [];
}

// Uygulama verilerini al
require_once 'data.php';
require_once 'functions.php';
$applications = getApplications();

// Ana içeriği gösteren fonksiyon
function displayMainContent() {
    global $applications;
    ?>
    <div class="container">
        <h1 class="section-title">DevOps</h1>
        <div class="card-container">
            <?php
            // Display DevOps tools
            foreach ($applications as $app) {
                if ($app['category'] === 'DevOps') {
                    $imageUrl = getAppImageUrl($app);
                    $imageColor = '#000000'; // Varsayılan renk
                    
                    // Görsel dosyası varsa baskın rengi al
                    $imagePath = $imageUrl;
                    if (file_exists($imagePath)) {
                        $imageColor = getDominantColor($imagePath);
                    }
                    
                    echo '<div class="card" onclick="window.location.href=\'index.php?page=details&id=' . $app['id'] . '\'" data-id="' . $app['id'] . '" style="border-color: ' . $imageColor . ';">';
                    echo '<div class="card-image"><img src="' . $imageUrl . '" alt="' . $app['name'] . '"></div>';
                    echo '<div class="card-title">' . $app['name'] . '</div>';
                    echo '<div class="card-description">' . $app['description'] . '</div>';
                    echo '</div>';
                }
            }
            ?>
        </div>
        
        <h1 class="section-title">Monitoring</h1>
        <div class="card-container">
            <?php
            // Display Monitoring tools
            foreach ($applications as $app) {
                if ($app['category'] === 'Monitoring') {
                    $imageUrl = getAppImageUrl($app);
                    $imageColor = '#000000'; // Varsayılan renk
                    
                    // Görsel dosyası varsa baskın rengi al
                    $imagePath = $imageUrl;
                    if (file_exists($imagePath)) {
                        $imageColor = getDominantColor($imagePath);
                    }
                    
                    echo '<div class="card" onclick="window.location.href=\'index.php?page=details&id=' . $app['id'] . '\'" data-id="' . $app['id'] . '" style="border-color: ' . $imageColor . ';">';
                    echo '<div class="card-image"><img src="' . $imageUrl . '" alt="' . $app['name'] . '"></div>';
                    echo '<div class="card-title">' . $app['name'] . '</div>';
                    echo '<div class="card-description">' . $app['description'] . '</div>';
                    echo '</div>';
                }
            }
            ?>
        </div>
        
        <h1 class="section-title">Security</h1>
        <div class="card-container">
            <?php
            // Display Security tools
            foreach ($applications as $app) {
                if ($app['category'] === 'Security') {
                    $imageUrl = getAppImageUrl($app);
                    $imageColor = '#000000'; // Varsayılan renk
                    
                    // Görsel dosyası varsa baskın rengi al
                    $imagePath = $imageUrl;
                    if (file_exists($imagePath)) {
                        $imageColor = getDominantColor($imagePath);
                    }
                    
                    echo '<div class="card" onclick="window.location.href=\'index.php?page=details&id=' . $app['id'] . '\'" data-id="' . $app['id'] . '" style="border-color: ' . $imageColor . ';">';
                    echo '<div class="card-image"><img src="' . $imageUrl . '" alt="' . $app['name'] . '"></div>';
                    echo '<div class="card-title">' . $app['name'] . '</div>';
                    echo '<div class="card-description">' . $app['description'] . '</div>';
                    echo '</div>';
                }
            }
            ?>
        </div>
        
        <h1 class="section-title">AI</h1>
        <div class="card-container">
            <?php
            // Display AI tools
            foreach ($applications as $app) {
                if ($app['category'] === 'AI') {
                    $imageUrl = getAppImageUrl($app);
                    $imageColor = '#000000'; // Varsayılan renk
                    
                    // Görsel dosyası varsa baskın rengi al
                    $imagePath = $imageUrl;
                    if (file_exists($imagePath)) {
                        $imageColor = getDominantColor($imagePath);
                    }
                    
                    echo '<div class="card" onclick="window.location.href=\'index.php?page=details&id=' . $app['id'] . '\'" data-id="' . $app['id'] . '" style="border-color: ' . $imageColor . ';">';
                    echo '<div class="card-image"><img src="' . $imageUrl . '" alt="' . $app['name'] . '"></div>';
                    echo '<div class="card-title">' . $app['name'] . '</div>';
                    echo '<div class="card-description">' . $app['description'] . '</div>';
                    echo '</div>';
                }
            }
            ?>
        </div>
        
        <h1 class="section-title">Middleware</h1>
        <div class="card-container">
            <?php
            // Display Middleware tools
            foreach ($applications as $app) {
                if ($app['category'] === 'Middleware') {
                    $imageUrl = getAppImageUrl($app);
                    $imageColor = '#000000'; // Varsayılan renk
                    
                    // Görsel dosyası varsa baskın rengi al
                    $imagePath = $imageUrl;
                    if (file_exists($imagePath)) {
                        $imageColor = getDominantColor($imagePath);
                    }
                    
                    echo '<div class="card" onclick="window.location.href=\'index.php?page=details&id=' . $app['id'] . '\'" data-id="' . $app['id'] . '" style="border-color: ' . $imageColor . ';">';
                    echo '<div class="card-image"><img src="' . $imageUrl . '" alt="' . $app['name'] . '"></div>';
                    echo '<div class="card-title">' . $app['name'] . '</div>';
                    echo '<div class="card-description">' . $app['description'] . '</div>';
                    echo '</div>';
                }
            }
            ?>
        </div>
        
        <h1 class="section-title">Automation</h1>
        <div class="card-container">
            <?php
            // Display Automation tools
            foreach ($applications as $app) {
                if ($app['category'] === 'Automation') {
                    $imageUrl = getAppImageUrl($app);
                    $imageColor = '#000000'; // Varsayılan renk
                    
                    // Görsel dosyası varsa baskın rengi al
                    $imagePath = $imageUrl;
                    if (file_exists($imagePath)) {
                        $imageColor = getDominantColor($imagePath);
                    }
                    
                    echo '<div class="card" onclick="window.location.href=\'index.php?page=details&id=' . $app['id'] . '\'" data-id="' . $app['id'] . '" style="border-color: ' . $imageColor . ';">';
                    echo '<div class="card-image"><img src="' . $imageUrl . '" alt="' . $app['name'] . '"></div>';
                    echo '<div class="card-title">' . $app['name'] . '</div>';
                    echo '<div class="card-description">' . $app['description'] . '</div>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>
    <?php
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevSecOps Platform</title>
    <link rel="icon" type="image/png" href="images/devsecops-cycle.png">
    <link rel="stylesheet" href="/style.css">
    <link rel="stylesheet" href="/css/ajanda.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Prosedürler Sayfası Stilleri */
        .procedures-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .procedures-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .procedures-header h1 {
            color: #2c3e50;
            font-size: 2em;
            margin-bottom: 10px;
        }
        
        .procedures-header p {
            color: #7f8c8d;
            font-size: 1.1em;
        }
        
        .procedures-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 20px;
        }
        
        .procedure-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .procedure-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }
        
        .procedure-title {
            color: #34495e;
            font-size: 1.4em;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .procedure-summary {
            color: #7f8c8d;
            font-size: 1em;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        
        .procedure-period {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.9em;
            margin-bottom: 15px;
        }
        
        .procedure-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }
        
        .procedure-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .view-btn {
            background-color: #2ecc71;
            color: white;
            flex: 1;
            margin-right: 10px;
        }
        
        .view-btn:hover {
            background-color: #27ae60;
        }
        
        .download-btn {
            background-color: #3498db;
            color: white;
            flex: 1;
        }
        
        .download-btn:hover {
            background-color: #2980b9;
        }
        
        /* PDF Popup Stilleri */
        .pdf-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            overflow: auto;
        }
        
        .pdf-modal-content {
            position: relative;
            width: 90%;
            height: 90%;
            margin: 2% auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        }
        
        .pdf-close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 28px;
            font-weight: bold;
            color: #333;
            cursor: pointer;
            z-index: 10001;
        }
        
        .pdf-iframe {
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 8px;
        }
        
        .no-procedures {
            text-align: center;
            padding: 50px;
            color: #7f8c8d;
            font-size: 1.2em;
        }
        
        /* Container styles */
        .container {
            width: 96%;
            max-width: 1800px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
        }
        
        .card-container {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="banner">
        <?php 
        // Sayfa başlığını belirle
        $page_title = 'DevSecOps Tools';
        
        if (isset($_GET['page'])) {
            switch ($_GET['page']) {
                case 'ajanda':
                    $page_title = 'DevSecOps Yıllık Planlı Süreçler';
                    break;
                case 'procedures':
                    $page_title = 'DevSecOps Prosedürler';
                    break;
                case 'details':
                    $page_title = 'DevSecOps Tool Detayları';
                    break;
            }
        }
        ?>
        <h1><?php echo $page_title; ?></h1>
        <div class="navbar">
            <a href="index.php" class="<?php echo !isset($_GET['page']) || $_GET['page'] === 'home' ? 'active' : ''; ?>"><i class="fas fa-home"></i> Ana Sayfa</a>
            <a href="index.php?page=ajanda" class="<?php echo isset($_GET['page']) && $_GET['page'] === 'ajanda' ? 'active' : ''; ?>"><i class="fas fa-calendar-alt"></i> Ajanda</a>
            <a href="index.php?page=procedures" class="<?php echo isset($_GET['page']) && $_GET['page'] === 'procedures' ? 'active' : ''; ?>"><i class="fas fa-file-alt"></i> Prosedürler</a>
            <a href="admin.php" class="login-button"><i class="fas fa-user-shield"></i> Admin</a>
        </div>
    </div>
    
    <?php if (!empty($todayEvents) && (!isset($_GET['page']) || $_GET['page'] !== 'ajanda')): ?>
    <!-- Bildirim Kutusu -->
    <div class="notification-container" id="notification">
        <div class="notification-header">
            <div class="notification-title">
                Bugün Planlı Süreçler (<?= date('d.m.Y') ?>)
            </div>
            <button class="notification-close" id="close-notification">×</button>
        </div>
        <div class="notification-body">
            <?php foreach ($todayEvents as $event): 
                $priorityClass = strtolower($event['priority']);
                // Kategori için CSS sınıfını oluştur
                $categoryClass = strtolower(str_replace(' ', '-', $event['category']));
                $categoryClass = str_replace(['ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç'], 
                                           ['i', 'g', 'u', 's', 'o', 'c', 'i', 'g', 'u', 's', 'o', 'c'], $categoryClass);
                
                $formattedTime = !empty($event['event_time']) ? substr($event['event_time'], 0, 5) : '-';
            ?>
            <div class="notification-event">
                <div class="notification-date"><strong><?= $formattedTime ?></strong></div>
                <div class="event-title"><?= htmlspecialchars($event['title']) ?></div>
                <div class="event-description"><?= htmlspecialchars($event['description']) ?></div>
                <div class="event-tags">
                    <span class="event-tag <?= $priorityClass ?>"><?= $event['priority'] ?></span>
                    <span class="event-tag <?= $categoryClass ?>"><?= $event['category'] ?></span>
                    <?php if (!empty($event['team'])): ?>
                    <span class="event-tag <?= strtolower($event['team']) ?>"><?= $event['team'] ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php
    // Sayfa içeriğini belirle
    if (isset($_GET['page'])) {
        switch ($_GET['page']) {
            case 'details':
                include('details.php');
                break;
            case 'ajanda':
                include('ajanda.php');
                break;
            case 'procedures':
                include('procedures.php');
                break;
            default:
                displayMainContent();
        }
    } else {
        displayMainContent();
    }
    ?>
    
    <footer class="footer">
        <p>APPs Catalogs Kariyer.net DevSecOps ürünüdür &copy; 2025</p>
    </footer>
    
    <script>
        // Bildirim kapatma ve yönlendirme
        const notification = document.getElementById('notification');
        const closeNotification = document.getElementById('close-notification');
        
        if (notification) {
            notification.addEventListener('click', function() {
                window.location.href = 'index.php?page=ajanda';
            });
        }
        
        if (closeNotification) {
            closeNotification.addEventListener('click', function(e) {
                e.stopPropagation(); // Tıklamanın notification'a yayılmasını engelle
                notification.style.display = 'none';
            });
        }

        function filterApps() {
            const searchTerm = document.getElementById('search').value.toLowerCase();
            const categoryFilter = document.getElementById('category-filter').value;
            const appCards = document.querySelectorAll('.app-card');
            
            appCards.forEach(card => {
                const category = card.getAttribute('data-category');
                const appName = card.querySelector('h3').textContent.toLowerCase();
                const appDescription = card.querySelector('p').textContent.toLowerCase();
                
                const matchesSearch = appName.includes(searchTerm) || appDescription.includes(searchTerm);
                const matchesCategory = categoryFilter === 'all' || category === categoryFilter;
                
                if (matchesSearch && matchesCategory) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html> 