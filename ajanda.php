<?php
// Veritabanı bağlantısını dahil et
include 'db_config.php';

// Ay isimleri
$months = [
    1 => 'Ocak',
    2 => 'Şubat',
    3 => 'Mart',
    4 => 'Nisan',
    5 => 'Mayıs',
    6 => 'Haziran',
    7 => 'Temmuz',
    8 => 'Ağustos',
    9 => 'Eylül',
    10 => 'Ekim',
    11 => 'Kasım',
    12 => 'Aralık'
];

// Etkinlikler için kategori renkleri
$categoryColors = [
    'toplanti' => '#4caf50',
    'rapor' => '#673ab7',
    'proje' => '#00acc1',
    'gorev' => '#ffa000',
    'finans' => '#2e7d32'
];

// Etkinlik verilerini al
$eventsData = [];
try {
    // Veritabanı bağlantısı varsa etkinlikleri çek
    if (isset($db)) {
        $currentYear = date('Y');
        $stmt = $db->prepare("SELECT * FROM events WHERE EXTRACT(YEAR FROM event_date) = ? ORDER BY event_date ASC");
        $stmt->execute([$currentYear]);
        $eventsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    // Hata durumunda boş array döndür
    $eventsData = [];
}

// Aylara göre etkinlikleri grupla
$eventsByMonth = [];
foreach ($eventsData as $event) {
    $eventDate = new DateTime($event['event_date']);
    $month = (int)$eventDate->format('n');
    if (!isset($eventsByMonth[$month])) {
        $eventsByMonth[$month] = [];
    }
    $eventsByMonth[$month][] = $event;
}

// Şu anki ayı hesapla
$currentMonth = (int)date('n');
?>

<div class="container">
    <div class="ajanda-container">
        <div class="ajanda-header">
            <div class="ajanda-title">Yıllık Ajanda</div>
            <div class="ajanda-navigation">
                <button id="view-month" class="view-btn active"><i class="fas fa-calendar-day"></i> Aylık</button>
                <button id="view-quarter" class="view-btn"><i class="fas fa-calendar-week"></i> 3 Aylık</button>
                <button id="view-year" class="view-btn"><i class="fas fa-calendar-alt"></i> Tüm Yıl</button>
                <button id="prev-btn" class="nav-btn"><i class="fas fa-chevron-left"></i> Önceki</button>
                <button id="next-btn" class="nav-btn">Sonraki <i class="fas fa-chevron-right"></i></button>
            </div>
        </div>

        <div class="ajanda-months">
            <?php
            // Tüm aylar için kartlar oluştur
            $year = date('Y');
            
            for ($monthIndex = 1; $monthIndex <= 12; $monthIndex++) {
                $monthEvents = isset($eventsByMonth[$monthIndex]) ? $eventsByMonth[$monthIndex] : [];
                $eventCount = count($monthEvents);
                ?>
                <div class="month-card" data-month="<?= $monthIndex ?>">
                    <div class="month-header">
                        <div class="month-title"><?= $months[$monthIndex] ?> <?= $year ?></div>
                        <div class="event-count"><?= $eventCount ?> görev<?= $eventCount > 1 ? 'ler' : '' ?></div>
                    </div>
                    <div class="events-list">
                        <?php if ($eventCount === 0): ?>
                            <div class="no-events">Bu ay için planlanmış görev bulunmuyor.</div>
                        <?php else: ?>
                            <?php foreach ($monthEvents as $event): 
                                $eventDate = new DateTime($event['event_date']);
                                $formattedDate = $eventDate->format('j F Y');
                                $formattedTime = !empty($event['event_time']) ? ' ' . substr($event['event_time'], 0, 5) : '';
                                
                                // Öncelik CSS sınıfını oluştur ve Türkçe öncelik adlarını koru
                                $priority = strtolower($event['priority']);
                                
                                // Kategori CSS sınıfını oluştur (boşluk ve özel karakterleri temizle)
                                $category = strtolower(str_replace(' ', '-', $event['category']));
                                // Türkçe karakter düzeltmeleri
                                $category = str_replace(['ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ş', 'Ö', 'Ç'], 
                                                      ['i', 'g', 'u', 's', 'o', 'c', 'i', 'g', 'u', 's', 'o', 'c'], $category);
                            ?>
                                <div class="event-item <?= $priority ?>">
                                    <div class="event-date"><strong><?= $formattedDate ?><?= $formattedTime ? ' - ' . $formattedTime : '' ?></strong></div>
                                    <div class="event-title"><?= htmlspecialchars($event['title']) ?></div>
                                    <div class="event-description"><?= htmlspecialchars($event['description']) ?></div>
                                    <div class="event-tags">
                                        <span class="event-tag <?= $priority ?>"><?= ucfirst($priority) ?></span>
                                        <span class="event-tag <?= $category ?>"><?= ucfirst($category) ?></span>
                                        <?php if (!empty($event['team'])): ?>
                                        <span class="event-tag <?= strtolower($event['team']) ?>"><?= $event['team'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const months = <?= json_encode($months) ?>;
        const allMonths = document.querySelectorAll('.month-card');
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');
        const viewMonthBtn = document.getElementById('view-month');
        const viewQuarterBtn = document.getElementById('view-quarter');
        const viewYearBtn = document.getElementById('view-year');
        const viewButtons = document.querySelectorAll('.view-btn');
        
        let currentDisplayMonth = <?= $currentMonth ?>;
        let displayMode = 'month'; // 'month', 'quarter', 'year'
        
        // Görünüm modunu ayarla
        function setViewMode(mode) {
            displayMode = mode;
            
            // Aktif buton sınıfını güncelle
            viewButtons.forEach(btn => btn.classList.remove('active'));
            if (mode === 'month') viewMonthBtn.classList.add('active');
            if (mode === 'quarter') viewQuarterBtn.classList.add('active');
            if (mode === 'year') viewYearBtn.classList.add('active');
            
            // Navigasyon butonlarını gerekirse gizle
            if (mode === 'year') {
                prevBtn.style.display = 'none';
                nextBtn.style.display = 'none';
            } else {
                prevBtn.style.display = 'block';
                nextBtn.style.display = 'block';
            }
            
            updateMonths();
        }
        
        // Ayları güncelle
        function updateMonths() {
            allMonths.forEach(month => {
                const monthIndex = parseInt(month.dataset.month);
                
                if (displayMode === 'year') {
                    // Tüm ayları göster
                    month.style.display = 'block';
                    return;
                }
                
                if (displayMode === 'month') {
                    // Sadece aktif ayı göster
                    month.style.display = (monthIndex === currentDisplayMonth) ? 'block' : 'none';
                    return;
                }
                
                if (displayMode === 'quarter') {
                    // Aktif ve sonraki 2 ayı göster (toplam 3 ay)
                    let show = false;
                    for (let i = 0; i < 3; i++) {
                        const targetMonth = ((currentDisplayMonth + i - 1) % 12) + 1;
                        if (monthIndex === targetMonth) {
                            show = true;
                            break;
                        }
                    }
                    month.style.display = show ? 'block' : 'none';
                }
            });
            
            // Görünüm modlarının stilini ayarla
            setTimeout(() => {
                const activeMonths = document.querySelectorAll('.month-card[style*="display: block"]');
                activeMonths.forEach(month => {
                    if (displayMode === 'year') {
                        month.classList.add('year-view');
                    } else if (displayMode === 'quarter') {
                        month.classList.add('quarter-view');
                    } else {
                        month.classList.add('month-view');
                    }
                });
            }, 50);
        }
        
        // İleri butonu
        nextBtn.addEventListener('click', function() {
            currentDisplayMonth = (currentDisplayMonth % 12) + 1;
            updateMonths();
        });
        
        // Geri butonu
        prevBtn.addEventListener('click', function() {
            currentDisplayMonth = ((currentDisplayMonth - 2 + 12) % 12) + 1;
            updateMonths();
        });
        
        // Aylık görünüm
        viewMonthBtn.addEventListener('click', function() {
            setViewMode('month');
        });
        
        // Çeyrek görünüm
        viewQuarterBtn.addEventListener('click', function() {
            setViewMode('quarter');
        });
        
        // Yıllık görünüm
        viewYearBtn.addEventListener('click', function() {
            setViewMode('year');
        });
        
        // İlk görünümü ayarla
        setViewMode('month');
    });
</script> 