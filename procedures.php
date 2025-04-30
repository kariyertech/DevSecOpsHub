<?php
// Prosedür verilerini al
$procedures = [];
try {
    if (isset($db)) {
        $stmt = $db->query("SELECT * FROM procedures ORDER BY title");
        $procedures = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    // Hata durumunda sessiz kal
}
?>

<div class="container">
    <div class="procedures-container">
        <div class="procedures-header">
            <h1><i class="fas fa-file-alt"></i> DevSecOps Prosedürleri</h1>
            <p>Bu bölümde DevSecOps ekibi tarafından oluşturulan tüm prosedürleri inceleyebilirsiniz.</p>
        </div>
        
        <div class="procedures-list">
            <?php if (!empty($procedures)): ?>
                <?php foreach ($procedures as $procedure): ?>
                    <div class="procedure-card">
                        <div class="procedure-title"><i class="fas fa-file-pdf"></i> <?php echo htmlspecialchars($procedure['title']); ?></div>
                        <div class="procedure-summary"><?php echo htmlspecialchars($procedure['summary']); ?></div>
                        <div class="procedure-period"><i class="fas fa-clock"></i> Kontrol Süresi: <?php echo htmlspecialchars($procedure['period']); ?></div>
                        <div class="procedure-actions">
                            <button class="procedure-btn view-btn" onclick="openPdfModal('<?php echo htmlspecialchars($procedure['document_url']); ?>')"><i class="fas fa-eye"></i> Dökümanı Oku</button>
                            <a href="<?php echo htmlspecialchars($procedure['document_url']); ?>" download class="procedure-btn download-btn"><i class="fas fa-download"></i> İndir</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-procedures">
                    <i class="fas fa-exclamation-circle"></i>
                    <p>Henüz prosedür eklenmemiş.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- PDF Görüntüleme Modal -->
<div id="pdfModal" class="pdf-modal">
    <div class="pdf-modal-content">
        <span class="pdf-close" onclick="closePdfModal()">&times;</span>
        <iframe id="pdfFrame" class="pdf-iframe" src=""></iframe>
    </div>
</div>

<script>
    // PDF Modal Fonksiyonları
    function openPdfModal(pdfUrl) {
        document.getElementById('pdfFrame').src = pdfUrl;
        document.getElementById('pdfModal').style.display = 'block';
        document.body.style.overflow = 'hidden'; // Scrollu kapat
    }
    
    function closePdfModal() {
        document.getElementById('pdfModal').style.display = 'none';
        document.getElementById('pdfFrame').src = '';
        document.body.style.overflow = 'auto'; // Scrollu geri aç
    }
    
    // Modal dışına tıklanınca kapat
    window.onclick = function(event) {
        const modal = document.getElementById('pdfModal');
        if (event.target === modal) {
            closePdfModal();
        }
    }
    
    // ESC tuşuna basılınca kapat
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closePdfModal();
        }
    });
</script> 