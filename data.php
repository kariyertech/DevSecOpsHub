<?php
// Static uygulama verileri (Yedek olarak)
$static_applications = [
    ["id" => 1, "name" => "Azure DevOps", "description" => "Microsoft'un DevOps çözümü", "category" => "DevOps", "url" => "https://azure.microsoft.com/tr-tr/products/devops/", "purpose" => "CI/CD, versiyon kontrolü, proje yönetimi ve test otomasyonu için kullanılır.", "image" => "images/azuredevops.png"],
    ["id" => 2, "name" => "SonarQube", "description" => "Kod kalitesi ve güvenlik analizi aracı", "category" => "DevOps", "url" => "https://www.sonarqube.org/", "purpose" => "Kodun kalitesi, güvenlik açıkları ve teknik borçları tespit etmek için kullanılır.", "image" => "images/sonarqube.png"],
    ["id" => 3, "name" => "Hashicorp Consul", "description" => "Service Mesh ve Service Discovery çözümü", "category" => "DevOps", "url" => "https://www.consul.io/", "purpose" => "Mikroservis mimarisinde servis keşfi ve yapılandırma yönetimi için kullanılır.", "image" => "images/consul.png"],
    ["id" => 4, "name" => "Rancher", "description" => "Konteyner yönetim platformu", "category" => "DevOps", "url" => "https://rancher.com/", "purpose" => "Kubernetes cluster'larını yönetmek ve orkestre etmek için kullanılır.", "image" => "images/rancher.png"],
    ["id" => 5, "name" => "Grafana", "description" => "Metrik görselleştirme ve analiz aracı", "category" => "Monitoring", "url" => "https://grafana.com/", "purpose" => "Çeşitli veri kaynaklarından metrikleri görselleştirme ve analiz etme aracı.", "image" => "images/grafana.png"],
    ["id" => 6, "name" => "Prometheus", "description" => "Metrik toplama ve uyarı sistemi", "category" => "Monitoring", "url" => "https://prometheus.io/", "purpose" => "Sistem ve uygulama metriklerini toplama ve izleme için kullanılır.", "image" => "images/prometheus.png"],
    ["id" => 7, "name" => "Goldilocks", "description" => "Kubernetes kaynak önerileri aracı", "category" => "Monitoring", "url" => "https://goldilocks.docs.fairwinds.com/", "purpose" => "Kubernetes pod'ları için optimum kaynak kullanımını önerir.", "image" => "images/goldilocks.png"],
    ["id" => 8, "name" => "Sentry", "description" => "Hata izleme ve performans monitörü", "category" => "Monitoring", "url" => "https://sentry.io/", "purpose" => "Uygulama hatalarını gerçek zamanlı olarak izlemek ve raporlamak için kullanılır.", "image" => "images/sentry.png"],
    ["id" => 9, "name" => "OWASP ZAP", "description" => "Web uygulama güvenlik tarayıcısı", "category" => "Security", "url" => "https://www.zaproxy.org/", "purpose" => "Web uygulamalarındaki güvenlik açıklarını taramak ve tespit etmek için kullanılır.", "image" => "images/owaspzap.png"],
    ["id" => 10, "name" => "Dependency Track", "description" => "Yazılım tedarik zinciri güvenliği platformu", "category" => "Security", "url" => "https://dependencytrack.org/", "purpose" => "Yazılım bileşenlerinin güvenlik açıklarını ve lisans uyumluluğunu takip eder.", "image" => "images/dependencytrack.png"],
    ["id" => 11, "name" => "NGINX", "description" => "Yüksek performanslı web sunucusu", "category" => "Middleware", "url" => "https://www.nginx.com/", "purpose" => "Yüksek performanslı, düşük kaynak tüketimli web sunucusu ve ters proxy olarak kullanılır.", "image" => "images/nginx.png"],
    ["id" => 12, "name" => "Portainer", "description" => "Konteyner yönetim arayüzü", "category" => "Middleware", "url" => "https://www.portainer.io/", "purpose" => "Docker ve Kubernetes ortamlarını görsel arayüz üzerinden yönetmek için kullanılır.", "image" => "images/portainer.png"],
    ["id" => 13, "name" => "Kubernetes", "description" => "Konteyner orkestrasyon platformu", "category" => "DevOps", "url" => "https://kubernetes.io/", "purpose" => "Konteynerize uygulamaların yönetimi ve otomasyonu için açık kaynaklı platform.", "image" => "images/kubernetes.png"],
    ["id" => 14, "name" => "Docker", "description" => "Konteynerizasyon platformu", "category" => "DevOps", "url" => "https://www.docker.com/", "purpose" => "Uygulamaları konteynerlar içinde paketlemek ve dağıtmak için kullanılır.", "image" => "images/docker.png"],
    ["id" => 15, "name" => "Elasticsearch", "description" => "Dağıtık arama ve analiz motoru", "category" => "Monitoring", "url" => "https://www.elastic.co/elasticsearch/", "purpose" => "Büyük veri setlerini hızlı bir şekilde araştırmak, analiz etmek ve görselleştirmek için kullanılır.", "image" => "images/elastic.png"],
    ["id" => 16, "name" => "Kibana", "description" => "Elasticsearch için görselleştirme aracı", "category" => "Monitoring", "url" => "https://www.elastic.co/kibana/", "purpose" => "Elasticsearch verilerini görselleştirmek ve keşfetmek için kullanılır.", "image" => "images/kibana.png"],
    ["id" => 17, "name" => "Ansible", "description" => "Otomasyon aracı", "category" => "Automation", "url" => "https://www.ansible.com/", "purpose" => "BT altyapısının otomasyonu için açık kaynaklı yazılım.", "image" => "images/ansible.png"],
    ["id" => 18, "name" => "Terraform", "description" => "Altyapı yönetim aracı", "category" => "Automation", "url" => "https://www.terraform.io/", "purpose" => "Altyapıyı kod olarak yazma ve yönetme aracı.", "image" => "images/terraform.png"],
    ["id" => 19, "name" => "Burp Suite", "description" => "Web uygulama güvenlik testi aracı", "category" => "Security", "url" => "https://portswigger.net/burp", "purpose" => "Web uygulamalarının güvenlik testlerini gerçekleştirmek için kullanılır.", "image" => "images/burpsuite.png"],
    ["id" => 20, "name" => "RabbitMQ", "description" => "Mesaj kuyruk yazılımı", "category" => "Middleware", "url" => "https://www.rabbitmq.com/", "purpose" => "Dağıtık sistemler arasında mesajlaşma için kullanılan açık kaynaklı bir mesaj aracısıdır.", "image" => "images/rabbitmq.png"],
    ["id" => 21, "name" => "Apache Kafka", "description" => "Dağıtık akış platformu", "category" => "Middleware", "url" => "https://kafka.apache.org/", "purpose" => "Gerçek zamanlı veri beslemelerini işlemek için dağıtık bir akış platformudur.", "image" => "images/apachekafka.png"],
    ["id" => 22, "name" => "Redis", "description" => "In-memory veri yapısı deposu", "category" => "Middleware", "url" => "https://redis.io/", "purpose" => "Yüksek performanslı anahtar-değer saklama ve önbellek sistemi olarak kullanılır.", "image" => "images/redis.png"],
    ["id" => 23, "name" => "TensorFlow", "description" => "Makine öğrenmesi kütüphanesi", "category" => "AI", "url" => "https://www.tensorflow.org/", "purpose" => "Derin öğrenme uygulamaları geliştirmek için kullanılan açık kaynaklı yazılım kütüphanesi.", "image" => "images/tensorflow.png"],
    ["id" => 24, "name" => "PyTorch", "description" => "Derin öğrenme çerçevesi", "category" => "AI", "url" => "https://pytorch.org/", "purpose" => "Derin öğrenme ve bilgisayarlı görü uygulamaları için kullanılan açık kaynaklı kütüphane.", "image" => "images/pytorch.png"],
    ["id" => 25, "name" => "LangChain", "description" => "Dil modeli uygulamaları geliştirme çerçevesi", "category" => "AI", "url" => "https://www.langchain.com/", "purpose" => "Büyük dil modelleri ile uygulama geliştirmek için kullanılan açık kaynaklı çerçeve.", "image" => "images/langchain.png"],
    ["id" => 26, "name" => "LangFlow", "description" => "LangChain için görsel geliştirme aracı", "category" => "AI", "url" => "https://github.com/logspace-ai/langflow", "purpose" => "LangChain uygulamalarını görsel olarak geliştirmek için kullanılan araç.", "image" => "images/langflow.png"],
    ["id" => 27, "name" => "Ollama", "description" => "Yerel dil modeli çalıştırma aracı", "category" => "AI", "url" => "https://ollama.ai/", "purpose" => "Yerel ortamda büyük dil modellerini çalıştırmak için kullanılan araç.", "image" => "images/ollama.png"],
    ["id" => 28, "name" => "OpenWebUI", "description" => "Web tabanlı AI arayüzü", "category" => "AI", "url" => "https://github.com/open-webui/open-webui", "purpose" => "Yerel dil modelleri için web tabanlı kullanıcı arayüzü sağlar.", "image" => "images/openwebui.png"],
    ["id" => 29, "name" => "Hashicorp Vault", "description" => "Sır yönetimi yazılımı", "category" => "Security", "url" => "https://www.vaultproject.io/", "purpose" => "Hassas verileri güvenli bir şekilde saklamak ve yönetmek için kullanılır.", "image" => "images/vault.png"]
];

// Veritabanı yapılandırması
require_once 'db_config.php';

/**
 * Veritabanından uygulamaları getiren fonksiyon
 */
function getApplicationsFromDB($db) {
    try {
        $stmt = $db->query("SELECT * FROM applications ORDER BY category, name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Veritabanından uygulama verisi çekilemedi: " . $e->getMessage());
        return [];
    }
}

/**
 * Uygulamaların veritabanına aktarılıp aktarılmadığını kontrol eden fonksiyon
 */
function checkAndImportStaticData($db, $static_applications) {
    try {
        // Veritabanında uygulama olup olmadığını kontrol et
        $stmt = $db->query("SELECT COUNT(*) FROM applications");
        $count = $stmt->fetchColumn();
        
        // Veritabanı boşsa statik verileri aktar
        if ($count == 0) {
            foreach ($static_applications as $app) {
                // Uygulama adını temizle ve dosya adı olarak kullan
                $clean_name = preg_replace('/[^a-z0-9]/i', '', strtolower($app['name']));
                $image_url = "images/" . $clean_name . ".png";
                
                $stmt = $db->prepare("INSERT INTO applications (id, name, description, category, url, purpose, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $app['id'],
                    $app['name'],
                    $app['description'],
                    $app['category'],
                    $app['url'],
                    $app['purpose'],
                    $image_url
                ]);
            }
            error_log("Statik uygulama verileri veritabanına aktarıldı.");
            return true;
        }
        return false;
    } catch (PDOException $e) {
        error_log("Statik veri aktarımı sırasında hata: " . $e->getMessage());
        return false;
    }
}

/**
 * Ana getApplications fonksiyonu - index.php'nin kullandığı ana fonksiyon
 * 
 * @return array Tüm uygulamaların listesi
 */
function getApplications() {
    global $applications;
    return $applications;
}

// Uygulamaları veritabanından veya statik verilerden al
if (isset($db) && $db !== null) {
    // Veritabanında hiç uygulama yoksa statik verileri aktar
    checkAndImportStaticData($db, $static_applications);
    
    // Uygulamaları veritabanından çek
    $applications = getApplicationsFromDB($db);
    
    // Eğer veritabanından veri çekilemezse statik verileri kullan
    if (empty($applications)) {
        $applications = $static_applications;
    }
} else {
    // Veritabanı bağlantısı yoksa statik verileri kullan
    $applications = $static_applications;
}

// Kategorilere göre uygulama gruplandırma
$categories = [];
foreach ($applications as $app) {
    if (!isset($categories[$app['category']])) {
        $categories[$app['category']] = [];
    }
    $categories[$app['category']][] = $app;
}
?> 