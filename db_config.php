<?php
// PostgreSQL veritabanı bağlantı bilgileri
// Ortam değişkenlerinden veritabanı bilgilerini al, yoksa varsayılan değerleri kullan
$db_host = getenv('DB_HOST') ?: 'db';
$db_port = getenv('DB_PORT') ?: '5432';
$db_name = getenv('DB_NAME') ?: 'devopstool';
$db_user = getenv('DB_USER') ?: 'admin';
$db_pass = getenv('DB_PASSWORD') ?: 'admin123';

// PDO bağlantısı
try {
    $db = new PDO("pgsql:host=$db_host;port=$db_port;dbname=$db_name", $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Tablo yapıları
    $tables = [
        "CREATE TABLE IF NOT EXISTS applications (
            id SERIAL PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            category VARCHAR(50) NOT NULL,
            url VARCHAR(255) NOT NULL,
            purpose TEXT,
            image VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS access_data (
            id SERIAL PRIMARY KEY,
            app_id INTEGER NOT NULL,
            name VARCHAR(255) NOT NULL,
            url VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS events (
            id SERIAL PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            event_date DATE NOT NULL,
            event_time TIME,
            priority VARCHAR(50) NOT NULL DEFAULT 'Orta',
            category VARCHAR(50) NOT NULL,
            team VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS procedures (
            id SERIAL PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            summary TEXT NOT NULL,
            period VARCHAR(50) NOT NULL,
            document_url VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"
    ];
    
    // Tabloları oluştur
    foreach ($tables as $sql) {
        $db->exec($sql);
    }
    
    // Sequence değerini sıfırlamak için kontrol yap
    $sql = "SELECT MAX(id) as max_id FROM applications";
    $stmt = $db->query($sql);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $max_id = $result['max_id'];
    
    if ($max_id) {
        // Eğer tabloda kayıt varsa, sequence değerini en yüksek ID'den sonra ayarla
        $sql = "SELECT setval('applications_id_seq', $max_id, true)";
        $db->exec($sql);
    }
    
} catch (PDOException $e) {
    // Hata durumunda sessizce devam et, kullanıcıya hata gösterme
    // echo "Veritabanı bağlantı hatası: " . $e->getMessage();
    $db = null;
} 