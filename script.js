// Dominant renk çıkarma fonksiyonu
function getDominantColor(imageElement, callback) {
    // Resim yüklenmediyse veya hata oluştuysa varsayılan rengi döndür
    if (!imageElement.complete || imageElement.naturalWidth === 0) {
        callback('rgb(106, 27, 154)'); // Varsayılan mor
        return;
    }
    
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const width = 50; // Küçük boyut yeterli
    const height = 50;
    
    canvas.width = width;
    canvas.height = height;
    
    try {
        // Resmi canvas'a çiz
        ctx.drawImage(imageElement, 0, 0, width, height);
        
        // Piksel verilerini al
        const imageData = ctx.getImageData(0, 0, width, height).data;
        
        // Renk sayımı için basit bir nesne
        const colorCounts = {};
        let dominantColor = null;
        let maxCount = 0;
        
        // Her piksel için
        for (let i = 0; i < imageData.length; i += 4) {
            const r = imageData[i];
            const g = imageData[i + 1];
            const b = imageData[i + 2];
            const a = imageData[i + 3];
            
            // Tamamen şeffaf pikselleri atla
            if (a < 128) continue;
            
            // Rengi bir string olarak temsil et
            const color = `rgb(${r},${g},${b})`;
            
            // Renk sayımını artır
            colorCounts[color] = (colorCounts[color] || 0) + 1;
            
            // En çok kullanılan rengi takip et
            if (colorCounts[color] > maxCount) {
                maxCount = colorCounts[color];
                dominantColor = color;
            }
        }
        
        callback(dominantColor || 'rgb(106, 27, 154)'); // Varsayılan mor
    } catch (error) {
        console.error('Resim işleme hatası:', error);
        callback('rgb(106, 27, 154)'); // Hata durumunda varsayılan mor
    }
}

// Renk kontrastını kontrol et
function isColorDark(color) {
    // rgb(r,g,b) formatından r, g, b değerlerini çıkar
    const match = color.match(/rgb\((\d+),\s*(\d+),\s*(\d+)\)/);
    if (!match) return false;
    
    const r = parseInt(match[1]);
    const g = parseInt(match[2]);
    const b = parseInt(match[3]);
    
    // Parlaklık hesapla (YIQ formülü)
    const brightness = (r * 299 + g * 587 + b * 114) / 1000;
    
    // 128'den küçükse koyu renk
    return brightness < 128;
}

// DOM yüklendikten sonra çalış
document.addEventListener('DOMContentLoaded', function() {
    // Tüm kartları seç
    const cards = document.querySelectorAll('.card');
    
    // Her kart için
    cards.forEach(card => {
        const imgElement = card.querySelector('img');
        const titleElement = card.querySelector('.card-title');
        
        if (imgElement) {
            // Resim yükleme hatası durumunda
            imgElement.onerror = function() {
                console.warn('Resim yüklenemedi:', imgElement.src);
                if (titleElement) {
                    titleElement.style.color = 'rgb(106, 27, 154)'; // Varsayılan mor
                }
            };
            
            // Resim yüklendikten sonra
            if (imgElement.complete) {
                processCardDominantColor(imgElement, card, titleElement);
            } else {
                imgElement.onload = function() {
                    processCardDominantColor(imgElement, card, titleElement);
                };
            }
        }
        
        // Kart tıklama olayı
        card.addEventListener('click', function() {
            card.classList.add('card-clicked');
        });
    });
    
    // Detay sayfasındayız
    const detailCard = document.querySelector('.detail-card');
    if (detailCard) {
        const imgElement = detailCard.querySelector('.detail-image img');
        const titleElement = detailCard.querySelector('.detail-title h1');
        const appLinks = detailCard.querySelectorAll('.app-link');
        
        if (imgElement) {
            // Resim yükleme hatası durumunda
            imgElement.onerror = function() {
                console.warn('Detay resmi yüklenemedi:', imgElement.src);
                if (titleElement) {
                    titleElement.style.color = 'rgb(106, 27, 154)'; // Varsayılan mor
                }
                if (appLinks) {
                    appLinks.forEach(link => {
                        link.style.color = 'rgb(106, 27, 154)'; // Varsayılan mor
                    });
                }
            };
            
            // Resim yüklendikten sonra
            if (imgElement.complete) {
                processDominantColor(imgElement, titleElement, appLinks);
            } else {
                imgElement.onload = function() {
                    processDominantColor(imgElement, titleElement, appLinks);
                };
            }
        }
    }
});

// Kartın dominant rengini işle ve uygula
function processCardDominantColor(imgElement, cardElement, titleElement) {
    getDominantColor(imgElement, function(color) {
        // Kartın kenarını dominant renge göre ayarla
        if (cardElement) {
            cardElement.style.borderColor = color;
            // Hafif bir gölge efekti ekle
            cardElement.style.boxShadow = `0 4px 8px ${color}40, 0 0 0 1px ${color}`;
        }
        
        // Başlık rengini ayarla
        if (titleElement) {
            titleElement.style.color = color;
            
            // Koyu renk ise metin rengini beyaz yap
            if (isColorDark(color)) {
                titleElement.style.textShadow = '1px 1px 2px rgba(0,0,0,0.7)';
            }
        }
    });
}

// Dominant rengi işle ve uygula
function processDominantColor(imgElement, titleElement, appLinks = null) {
    getDominantColor(imgElement, function(color) {
        // Başlık rengini ayarla
        if (titleElement) {
            titleElement.style.color = color;
            
            // Koyu renk ise metin rengini beyaz yap
            if (isColorDark(color) && titleElement.tagName === 'H1') {
                titleElement.style.textShadow = '1px 1px 2px rgba(0,0,0,0.7)';
            }
        }
        
        // Linklerin rengini ayarla
        if (appLinks) {
            appLinks.forEach(link => {
                link.style.color = color;
            });
        }
    });
} 