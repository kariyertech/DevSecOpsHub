<?php
/**
 * Bir uygulamanın görsel URL'sini döndürür
 * 
 * @param array $app Uygulama dizisi
 * @return string Görsel URL'si
 */
function getAppImageUrl($app) {
    // Veritabanındaki URL'yi kontrol et
    if (!empty($app['image'])) {
        // Eski S3 URL'lerini yerel yola dönüştür
        if (strpos($app['image'], 'https://ist1.s3.turkcellbulut.com/') !== false) {
            // URL'den dosya adını çıkar
            $fileName = basename($app['image']);
            return 'images/' . $fileName;
        }
        return $app['image'];
    }
    
    // Özel isim düzeltmeleri
    $nameMap = [
        'Hashicorp Vault' => 'vault',
        'OWASP ZAP' => 'owaspzap',
        'Apache Tomcat' => 'apachetomcat',
        'Apache Kafka' => 'apachekafka',
        'Azure DevOps' => 'azuredevops',
        'Burp Suite' => 'burpsuite',
        'Redis-UI' => 'redis-ui',
        'Elasticsearch' => 'elastic'
    ];
    
    // İsim haritasını kontrol et, varsa onu kullan
    if (array_key_exists($app['name'], $nameMap)) {
        return 'images/' . $nameMap[$app['name']] . '.png';
    }
    
    // Normal isimlerde sadece küçük harfe çevirip boşlukları kaldır
    $cleanName = strtolower(str_replace(' ', '', $app['name']));
    
    // Yerel dosya yolunu döndür
    return 'images/' . $cleanName . '.png';
}

/**
 * Bir resmin baskın rengini HEX formatında döndürür
 * 
 * @param string $imagePath Resim dosyasının yolu
 * @return string Baskın renk (HEX formatında)
 */
function getDominantColor($imagePath) {
    // GD kütüphanesinin yüklü olup olmadığını kontrol et
    if (!function_exists('imagecreatefrompng')) {
        return '#3498db'; // GD kütüphanesi yoksa varsayılan mavi renk döndür
    }

    // Dosyanın varlığını kontrol et
    if (!file_exists($imagePath)) {
        return '#000000'; // Varsayılan siyah
    }
    
    // Dosya uzantısını al
    $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
    
    // Resim türüne göre uygun fonksiyonu kullan
    $image = null;
    if ($extension == 'jpg' || $extension == 'jpeg') {
        $image = @imagecreatefromjpeg($imagePath);
    } elseif ($extension == 'png') {
        $image = @imagecreatefrompng($imagePath);
    } elseif ($extension == 'gif') {
        $image = @imagecreatefromgif($imagePath);
    }
    
    // Resim oluşturulamadıysa varsayılan rengi döndür
    if (!$image) {
        return '#4a69bd';
    }
    
    // Resmi küçült (daha hızlı analiz için)
    $width = imagesx($image);
    $height = imagesy($image);
    $resized = imagecreatetruecolor(50, 50);
    
    // Saydam PNG desteği için
    if ($extension == 'png') {
        // Saydam renkler için ayarlar
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
    }
    
    // Resmi yeniden boyutlandır
    imagecopyresampled($resized, $image, 0, 0, 0, 0, 50, 50, $width, $height);
    
    // Renkleri analiz et
    $colors = [];
    for ($x = 0; $x < 50; $x++) {
        for ($y = 0; $y < 50; $y++) {
            $rgb = imagecolorat($resized, $x, $y);
            
            // Saydam bölgeleri atla (PNG için)
            if ($extension == 'png') {
                $alpha = ($rgb >> 24) & 0x7F;
                if ($alpha == 127) { // Tamamen saydam
                    continue;
                }
            }
            
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            
            // Beyaz ve siyaha yakın renkler genellikle arka plan veya metin olabilir
            if (($r > 240 && $g > 240 && $b > 240) || // Beyaza yakın
                ($r < 15 && $g < 15 && $b < 15)) {    // Siyaha yakın
                continue;
            }
            
            $index = sprintf('%02x%02x%02x', $r, $g, $b);
            if (isset($colors[$index])) {
                $colors[$index]++;
            } else {
                $colors[$index] = 1;
            }
        }
    }
    
    // Bellekten temizle
    imagedestroy($image);
    imagedestroy($resized);
    
    // En çok bulunan rengi bul
    if (empty($colors)) {
        return '#000000'; // Renk bulunamadıysa siyah döndür
    }
    
    arsort($colors);
    $dominantColor = key($colors);
    
    return '#' . $dominantColor;
}
?> 