<?php
require_once 'data.php';
require_once 'db_config.php';
require_once 'functions.php';

// Get the application ID from the URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Find the application with the matching ID
$app = null;
foreach ($applications as $application) {
    if ($application['id'] === $id) {
        $app = $application;
        break;
    }
}

// If no application is found, redirect to the home page
if ($app === null) {
    header('Location: index.php');
    exit;
}

// Erişim verilerini veritabanından çek
$access_data = [];
if ($db !== null) {
    try {
        $stmt = $db->prepare("SELECT id, name, url FROM access_data WHERE app_id = :app_id ORDER BY id");
        $stmt->bindParam(':app_id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $access_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Hata durumunda sessizce devam et
    }
}
?>

<div class="container">
    <div class="back-button">
        <a href="index.php"><i class="fas fa-arrow-left"></i> Ana Sayfaya Dön</a>
    </div>
    
    <div class="detail-card">
        <div class="detail-header">
            <div class="detail-title"><?php echo htmlspecialchars($app['name']); ?></div>
            <div class="detail-category"><?php echo htmlspecialchars($app['category']); ?></div>
        </div>
        <div class="detail-content">
            <div class="detail-image">
                <img src="<?php echo getAppImageUrl($app); ?>" alt="<?php echo htmlspecialchars($app['name']); ?>">
            </div>
            <div class="detail-info">
                <div class="detail-description">
                    <h3>Açıklama</h3>
                    <p><?php echo htmlspecialchars($app['description']); ?></p>
                </div>
                <div class="detail-purpose">
                    <h3>Amaç</h3>
                    <p><?php echo htmlspecialchars($app['purpose']); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="detail-section">
        <h2>Erişim Bilgileri</h2>
        <?php if (empty($access_data)): ?>
            <p class="no-data">Erişim bilgisi bulunmuyor.</p>
        <?php else: ?>
            <table class="access-table">
                <thead>
                    <tr>
                        <th>İsim</th>
                        <th>URL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($access_data as $access): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($access['name']); ?></td>
                        <td>
                            <a href="<?php echo htmlspecialchars($access['url']); ?>" target="_blank" class="app-link">
                                <i class="fas fa-external-link-alt"></i> <?php echo htmlspecialchars($access['url']); ?>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<script src="script.js"></script> 