<?php
session_start();
require_once 'db_config.php';

// Admin kullanıcı bilgileri
$admin_username = 'admin';
$admin_password = 'admin123'; // Gerçek uygulamada hash kullanılmalı

// Oturum kontrolü
$is_logged_in = false;
$login_error = '';
$success_message = '';
$error_message = '';

// Çıkış işlemi
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Giriş işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    if ($_POST['username'] === $admin_username && $_POST['password'] === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin.php');
        exit;
    } else {
        $login_error = 'Geçersiz kullanıcı adı veya şifre!';
    }
}

// Oturum durumunu kontrol et
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    $is_logged_in = true;

    // Veritabanı bağlantısı başarılıysa
    if ($db) {
// Erişim bilgisi ekleme işlemi
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['app_id'], $_POST['name'], $_POST['url']) && !isset($_POST['action'])) {
            try {
                $stmt = $db->prepare("INSERT INTO access_data (app_id, name, url) VALUES (?, ?, ?)");
                $stmt->execute([
                    $_POST['app_id'],
                    $_POST['name'],
                    $_POST['url']
                ]);
                $success_message = 'Erişim bilgisi başarıyla eklendi!';
                // Erişim Yönetimi sekmesinde kalmak için URL'e parametre ekleyip yönlendirme yapıyoruz
                header('Location: admin.php?tab=access-list');
                exit;
        } catch (PDOException $e) {
                $error_message = 'Erişim bilgisi eklenirken hata oluştu: ' . $e->getMessage();
    }
}

// Erişim bilgisi silme işlemi
        if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
            try {
                $stmt = $db->prepare("DELETE FROM access_data WHERE id = ?");
                $stmt->execute([$_GET['delete_id']]);
                $success_message = 'Erişim bilgisi başarıyla silindi!';
                header('Location: admin.php?tab=access-list');
        exit;
    } catch (PDOException $e) {
                $error_message = 'Erişim bilgisi silinirken hata oluştu: ' . $e->getMessage();
            }
        }
        
        // Uygulama ekleme işlemi
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_app'])) {
            $error_message = '';
            
            // Form verilerini al
            $name = isset($_POST['name']) ? trim($_POST['name']) : '';
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            $category = isset($_POST['category']) ? trim($_POST['category']) : '';
            $url = isset($_POST['url']) ? trim($_POST['url']) : '';
            $purpose = isset($_POST['purpose']) ? trim($_POST['purpose']) : '';
            
            // Resim için değişkenler
            $image_url = '';
            $uploadSuccess = false;
            
            // Dosya yükleme işlemi
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $temp_file = $_FILES['image']['tmp_name'];
                $file_name = basename($_FILES['image']['name']);
                $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                
                // Dosya türünü kontrol et
                $allowed_extensions = ['png'];
                if (in_array($file_extension, $allowed_extensions)) {
                    // Uygulama adını temizle ve dosya adı olarak kullan
                    $clean_name = preg_replace('/[^a-z0-9]/i', '', strtolower($name));
                    $new_file_name = $clean_name . '.png';
                    
                    // Klasik dosya yükleme yöntemini kullan
                    $upload_dir = 'images/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $target_path = $upload_dir . $new_file_name;
                    if (move_uploaded_file($temp_file, $target_path)) {
                        $image_url = $target_path;
                        $uploadSuccess = true;
                    } else {
                        $error_message = 'Resim yüklenirken bir hata oluştu.';
                    }
                } else {
                    $error_message = 'Yalnızca PNG dosyaları yüklenebilir.';
                }
            }
            
            // Verileri doğrula
            if (empty($name)) {
                $error_message = 'Lütfen bir uygulama adı girin.';
            } else if (empty($category)) {
                $error_message = 'Lütfen bir kategori seçin.';
            } else if (empty($url)) {
                $error_message = 'Lütfen bir URL girin.';
            } else if (empty($image_url) && !$uploadSuccess) {
                $error_message = 'Lütfen bir resim yükleyin.';
            }
            
            // Hata yoksa veritabanına kaydet
            if (empty($error_message)) {
                try {
                    $stmt = $db->prepare("INSERT INTO applications (name, description, category, url, purpose, image) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$name, $description, $category, $url, $purpose, $image_url]);
                    $success_message = 'Uygulama başarıyla eklendi!';
                    header('Location: admin.php?tab=app-list');
                    exit;
                } catch (PDOException $e) {
                    $error_message = 'Uygulama eklenirken hata oluştu: ' . $e->getMessage();
                }
            }
        }
        
        // Uygulama silme işlemi
        if (isset($_GET['delete_app'])) {
            $app_id = (int)$_GET['delete_app'];
            
            try {
                // Önce uygulamanın resim URL'sini al
                $stmt = $db->prepare("SELECT image FROM applications WHERE id = ?");
                $stmt->execute([$app_id]);
                $app = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Uygulamayı sil
                $stmt = $db->prepare("DELETE FROM applications WHERE id = ?");
                $stmt->execute([$app_id]);
                
                // Eğer bir resim varsa yerel dosya sisteminden sil
                if (!empty($app['image']) && file_exists($app['image'])) {
                    @unlink($app['image']);
                }
                
                $success_message = 'Uygulama başarıyla silindi!';
                header('Location: admin.php?tab=app-list');
                exit;
            } catch (PDOException $e) {
                $error_message = 'Uygulama silinirken hata oluştu: ' . $e->getMessage();
            }
        }
        
        // Uygulama güncelleme işlemi
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_app'])) {
            $error_message = '';
            
            // Form verilerini al
            $app_id = isset($_POST['app_id']) ? (int)$_POST['app_id'] : 0;
            $name = isset($_POST['edit_name']) ? trim($_POST['edit_name']) : '';
            $description = isset($_POST['edit_description']) ? trim($_POST['edit_description']) : '';
            $category = isset($_POST['edit_category']) ? trim($_POST['edit_category']) : '';
            $url = isset($_POST['edit_url']) ? trim($_POST['edit_url']) : '';
            $purpose = isset($_POST['edit_purpose']) ? trim($_POST['edit_purpose']) : '';
            $current_image = isset($_POST['current_image']) ? trim($_POST['current_image']) : '';
            
            // Resim için değişkenler
            $image_url = $current_image;
            $uploadSuccess = false;
            
            // Dosya yükleme işlemi
            if (isset($_FILES['edit_image']) && $_FILES['edit_image']['error'] === UPLOAD_ERR_OK) {
                $temp_file = $_FILES['edit_image']['tmp_name'];
                $file_name = basename($_FILES['edit_image']['name']);
                $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                
                // Dosya türünü kontrol et
                $allowed_extensions = ['png'];
                if (in_array($file_extension, $allowed_extensions)) {
                    // Uygulama adını temizle ve dosya adı olarak kullan
                    $clean_name = preg_replace('/[^a-z0-9]/i', '', strtolower($name));
                    $new_file_name = $clean_name . '.png';
                    
                    // Klasik dosya yükleme yöntemini kullan
                    $upload_dir = 'images/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $target_path = $upload_dir . $new_file_name;
                    if (move_uploaded_file($temp_file, $target_path)) {
                        // Eski resmi sistemden kaldır (eğer farklıysa)
                        if (!empty($current_image) && file_exists($current_image) && $current_image !== $target_path) {
                            @unlink($current_image);
                        }
                        $image_url = $target_path;
                        $uploadSuccess = true;
                    } else {
                        $error_message = 'Resim yüklenirken bir hata oluştu.';
                    }
                } else {
                    $error_message = 'Yalnızca PNG dosyaları yüklenebilir.';
                }
            }
            
            // Verileri doğrula
            if (empty($name)) {
                $error_message = 'Lütfen bir uygulama adı girin.';
            } else if (empty($category)) {
                $error_message = 'Lütfen bir kategori seçin.';
            } else if (empty($url)) {
                $error_message = 'Lütfen bir URL girin.';
            }
            
            // Hata yoksa veritabanını güncelle
            if (empty($error_message)) {
                try {
                    $stmt = $db->prepare("UPDATE applications SET name = ?, description = ?, category = ?, url = ?, purpose = ?, image = ? WHERE id = ?");
                    $stmt->execute([$name, $description, $category, $url, $purpose, $image_url, $app_id]);
                    $success_message = 'Uygulama başarıyla güncellendi!';
                    header('Location: admin.php?tab=app-list');
                    exit;
                } catch (PDOException $e) {
                    $error_message = 'Uygulama güncellenirken hata oluştu: ' . $e->getMessage();
                }
            }
        }
        
        // Uygulamaları veritabanından çek
        try {
            $stmt = $db->query("SELECT * FROM applications ORDER BY category, name");
            $apps = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $error_message = 'Uygulamalar yüklenirken hata oluştu: ' . $e->getMessage();
            $apps = [];
        }
    } else {
        $error_message = 'Veritabanı bağlantısı kurulamadı!';
        $apps = [];
    }
}

// Erişim verilerini çek
$access_data = [];
if ($is_logged_in && $db !== null) {
        try {
            $stmt = $db->query("SELECT ad.id, ad.app_id, ad.name, ad.url, ad.created_at 
                               FROM access_data ad 
                               ORDER BY ad.app_id, ad.name");
            $access_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
        $error_message = 'Erişim verileri yüklenirken hata oluştu: ' . $e->getMessage();
    }
}

// Etkinlik ekleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_event'])) {
    $error_message = '';
    
    // Form verilerini al
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $event_date = isset($_POST['event_date']) ? trim($_POST['event_date']) : '';
    $event_time = isset($_POST['event_time']) ? trim($_POST['event_time']) : null;
    $priority = isset($_POST['priority']) ? trim($_POST['priority']) : 'Orta';
    $category = isset($_POST['category']) ? trim($_POST['category']) : '';
    $team = isset($_POST['team']) ? trim($_POST['team']) : null;
    $is_recurring = isset($_POST['is_recurring']) ? 1 : 0;
    $recurrence_type = isset($_POST['recurrence_type']) ? trim($_POST['recurrence_type']) : '';
    $recurrence_end_date = isset($_POST['recurrence_end_date']) ? trim($_POST['recurrence_end_date']) : '';
    
    // Verileri doğrula
    if (empty($title)) {
        $error_message = 'Lütfen bir başlık girin.';
    } else if (empty($event_date)) {
        $error_message = 'Lütfen bir tarih seçin.';
    } else if (empty($category)) {
        $error_message = 'Lütfen bir kategori seçin.';
    } else if ($is_recurring && empty($recurrence_type)) {
        $error_message = 'Lütfen tekrarlama tipini seçin.';
    } else if ($is_recurring && empty($recurrence_end_date)) {
        $error_message = 'Lütfen tekrarlama bitiş tarihini seçin.';
    }
    
    // Hata yoksa veritabanına kaydet
    if (empty($error_message)) {
        try {
            // Tekrarlayan etkinlik mi?
            if ($is_recurring && !empty($recurrence_type) && !empty($recurrence_end_date)) {
                // Bitiş tarihini ve başlangıç tarihini DateTime nesnelerine dönüştür
                $start_date = new DateTime($event_date);
                $end_date = new DateTime($recurrence_end_date);
                $current_date = clone $start_date;
                
                // Başlangıç tarihi bitiş tarihinden önce olmalı
                if ($start_date > $end_date) {
                    throw new Exception('Başlangıç tarihi bitiş tarihinden sonra olamaz.');
                }
                
                $db->beginTransaction();
                
                // Tekrarlama tipine göre etkinlikleri oluştur
                while ($current_date <= $end_date) {
                    $stmt = $db->prepare("INSERT INTO events (title, description, event_date, event_time, priority, category, team) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $title,
                        $description,
                        $current_date->format('Y-m-d'),
                        $event_time,
                        $priority,
                        $category,
                        $team
                    ]);
                    
                    // Tekrarlama tipine göre tarihi güncelle
                    switch ($recurrence_type) {
                        case 'daily':
                            $current_date->modify('+1 day');
                            break;
                        case 'weekly':
                            $current_date->modify('+1 week');
                            break;
                        case 'bi-weekly':
                            $current_date->modify('+2 weeks');
                            break;
                        case 'monthly':
                            $current_date->modify('+1 month');
                            break;
                        case 'yearly':
                            $current_date->modify('+1 year');
                            break;
                    }
                }
                
                $db->commit();
                $success_message = 'Tekrarlayan etkinlikler başarıyla eklendi!';
            } else {
                // Tekrarlamayan normal etkinlik
                $stmt = $db->prepare("INSERT INTO events (title, description, event_date, event_time, priority, category, team) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $title,
                    $description,
                    $event_date,
                    $event_time,
                    $priority,
                    $category,
                    $team
                ]);
                $success_message = 'Etkinlik başarıyla eklendi!';
            }
            
            header('Location: admin.php?tab=event-list');
            exit;
        } catch (Exception $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            $error_message = 'Etkinlik eklenirken hata oluştu: ' . $e->getMessage();
        }
    }
}

// Etkinlik düzenleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_event'])) {
    $error_message = '';
    
    // Form verilerini al
    $event_id = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;
    $title = isset($_POST['edit_title']) ? trim($_POST['edit_title']) : '';
    $description = isset($_POST['edit_description']) ? trim($_POST['edit_description']) : '';
    $event_date = isset($_POST['edit_event_date']) ? trim($_POST['edit_event_date']) : '';
    $event_time = isset($_POST['edit_event_time']) ? trim($_POST['edit_event_time']) : null;
    $priority = isset($_POST['edit_priority']) ? trim($_POST['edit_priority']) : 'Orta';
    $category = isset($_POST['edit_category']) ? trim($_POST['edit_category']) : '';
    $team = isset($_POST['edit_team']) ? trim($_POST['edit_team']) : null;
    
    // Verileri doğrula
    if (empty($title)) {
        $error_message = 'Lütfen bir başlık girin.';
    } else if (empty($event_date)) {
        $error_message = 'Lütfen bir tarih seçin.';
    } else if (empty($category)) {
        $error_message = 'Lütfen bir kategori seçin.';
    }
    
    // Hata yoksa veritabanını güncelle
    if (empty($error_message)) {
        try {
            $stmt = $db->prepare("UPDATE events SET title = ?, description = ?, event_date = ?, event_time = ?, priority = ?, category = ?, team = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
            $stmt->execute([
                $title,
                $description,
                $event_date,
                $event_time,
                $priority,
                $category,
                $team,
                $event_id
            ]);
            $success_message = 'Etkinlik başarıyla güncellendi!';
            header('Location: admin.php?tab=event-list');
            exit;
        } catch (PDOException $e) {
            $error_message = 'Etkinlik güncellenirken hata oluştu: ' . $e->getMessage();
        }
    }
}

// Etkinlik silme
if (isset($_GET['delete_event'])) {
    $event_id = (int)$_GET['delete_event'];
    
    try {
        $stmt = $db->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$event_id]);
        $success_message = 'Etkinlik başarıyla silindi!';
        header('Location: admin.php?tab=event-list');
        exit;
    } catch (PDOException $e) {
        $error_message = 'Etkinlik silinirken hata oluştu: ' . $e->getMessage();
    }
}

// Prosedür ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_procedure'])) {
    $error_message = '';
    
    // Form verilerini al
    $title = isset($_POST['procedure_title']) ? trim($_POST['procedure_title']) : '';
    $summary = isset($_POST['procedure_summary']) ? trim($_POST['procedure_summary']) : '';
    $period = isset($_POST['procedure_period']) ? trim($_POST['procedure_period']) : '';
    
    // Dosya yükleme işlemi
    $document_url = '';
    $uploadSuccess = false;
    
    if (isset($_FILES['procedure_document']) && $_FILES['procedure_document']['error'] === UPLOAD_ERR_OK) {
        $temp_file = $_FILES['procedure_document']['tmp_name'];
        $file_name = basename($_FILES['procedure_document']['name']);
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Dosya türünü kontrol et
        $allowed_extensions = ['pdf'];
        if (in_array($file_extension, $allowed_extensions)) {
            // Prosedür adını temizle ve dosya adı olarak kullan
            $clean_name = preg_replace('/[^a-z0-9]/i', '', strtolower($title));
            $new_file_name = $clean_name . '_' . date('Ymd') . '.pdf';
            
            // Klasik dosya yükleme yöntemini kullan
            $upload_dir = 'documents/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $target_path = $upload_dir . $new_file_name;
            if (move_uploaded_file($temp_file, $target_path)) {
                $document_url = $target_path;
                $uploadSuccess = true;
            } else {
                $error_message = 'Doküman yüklenirken bir hata oluştu.';
            }
        } else {
            $error_message = 'Yalnızca PDF dosyaları yüklenebilir.';
        }
    }
    
    // Verileri doğrula
    if (empty($title)) {
        $error_message = 'Lütfen bir prosedür başlığı girin.';
    } else if (empty($summary)) {
        $error_message = 'Lütfen bir özet girin.';
    } else if (empty($period)) {
        $error_message = 'Lütfen kontrol süresini girin.';
    } else if (empty($document_url) && !$uploadSuccess) {
        $error_message = 'Lütfen bir doküman yükleyin.';
    }
    
    // Hata yoksa veritabanına kaydet
    if (empty($error_message)) {
        try {
            $stmt = $db->prepare("INSERT INTO procedures (title, summary, period, document_url) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $summary, $period, $document_url]);
            $success_message = 'Prosedür başarıyla eklendi!';
            header('Location: admin.php?tab=procedure-list');
            exit;
        } catch (PDOException $e) {
            $error_message = 'Prosedür eklenirken hata oluştu: ' . $e->getMessage();
        }
    }
}

// Prosedür silme işlemi
if (isset($_GET['delete_procedure'])) {
    $procedure_id = (int)$_GET['delete_procedure'];
    
    try {
        // Önce prosedürün doküman URL'sini al
        $stmt = $db->prepare("SELECT document_url FROM procedures WHERE id = ?");
        $stmt->execute([$procedure_id]);
        $procedure = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Prosedürü sil
        $stmt = $db->prepare("DELETE FROM procedures WHERE id = ?");
        $stmt->execute([$procedure_id]);
        
        // Dosyayı sistemden sil
        if (!empty($procedure['document_url']) && file_exists($procedure['document_url'])) {
            @unlink($procedure['document_url']);
        }
        
        $success_message = 'Prosedür başarıyla silindi!';
        header('Location: admin.php?tab=procedure-list');
        exit;
    } catch (PDOException $e) {
        $error_message = 'Prosedür silinirken hata oluştu: ' . $e->getMessage();
    }
}

// Prosedür güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_procedure'])) {
    $error_message = '';
    
    // Form verilerini al
    $procedure_id = isset($_POST['procedure_id']) ? (int)$_POST['procedure_id'] : 0;
    $title = isset($_POST['edit_procedure_title']) ? trim($_POST['edit_procedure_title']) : '';
    $summary = isset($_POST['edit_procedure_summary']) ? trim($_POST['edit_procedure_summary']) : '';
    $period = isset($_POST['edit_procedure_period']) ? trim($_POST['edit_procedure_period']) : '';
    $current_document = isset($_POST['current_document']) ? trim($_POST['current_document']) : '';
    
    // Dosya değişkenleri
    $document_url = $current_document;
    $uploadSuccess = false;
    
    // Yeni dosya yükleme işlemi
    if (isset($_FILES['edit_procedure_document']) && $_FILES['edit_procedure_document']['error'] === UPLOAD_ERR_OK) {
        $temp_file = $_FILES['edit_procedure_document']['tmp_name'];
        $file_name = basename($_FILES['edit_procedure_document']['name']);
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Dosya türünü kontrol et
        $allowed_extensions = ['pdf'];
        if (in_array($file_extension, $allowed_extensions)) {
            // Prosedür adını temizle ve dosya adı olarak kullan
            $clean_name = preg_replace('/[^a-z0-9]/i', '', strtolower($title));
            $new_file_name = $clean_name . '_' . date('Ymd') . '.pdf';
            
            $upload_dir = 'documents/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $target_path = $upload_dir . $new_file_name;
            if (move_uploaded_file($temp_file, $target_path)) {
                // Eski dokümanı sistemden sil
                if (!empty($current_document) && file_exists($current_document) && $current_document !== $target_path) {
                    @unlink($current_document);
                }
                $document_url = $target_path;
                $uploadSuccess = true;
            } else {
                $error_message = 'Doküman yüklenirken bir hata oluştu.';
            }
        } else {
            $error_message = 'Yalnızca PDF dosyaları yüklenebilir.';
        }
    }
    
    // Verileri doğrula
    if (empty($title)) {
        $error_message = 'Lütfen bir prosedür başlığı girin.';
    } else if (empty($summary)) {
        $error_message = 'Lütfen bir özet girin.';
    } else if (empty($period)) {
        $error_message = 'Lütfen kontrol süresini girin.';
    }
    
    // Hata yoksa veritabanını güncelle
    if (empty($error_message)) {
        try {
            $stmt = $db->prepare("UPDATE procedures SET title = ?, summary = ?, period = ?, document_url = ? WHERE id = ?");
            $stmt->execute([$title, $summary, $period, $document_url, $procedure_id]);
            $success_message = 'Prosedür başarıyla güncellendi!';
            header('Location: admin.php?tab=procedure-list');
            exit;
        } catch (PDOException $e) {
            $error_message = 'Prosedür güncellenirken hata oluştu: ' . $e->getMessage();
        }
    }
}

// Prosedürleri veritabanından çek
$procedures = [];
if ($is_logged_in && $db !== null) {
    try {
        $stmt = $db->query("SELECT * FROM procedures ORDER BY title");
        $procedures = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error_message = 'Prosedürler yüklenirken hata oluştu: ' . $e->getMessage();
    }
}

// Uygulamaları çek (statik veya veritabanı)
require_once 'data.php';

// Toplu etkinlik silme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_delete_events']) && isset($_POST['event_ids']) && is_array($_POST['event_ids'])) {
    try {
        $db->beginTransaction();
        
        $event_ids = array_map('intval', $_POST['event_ids']);
        $placeholders = implode(',', array_fill(0, count($event_ids), '?'));
        
        $stmt = $db->prepare("DELETE FROM events WHERE id IN ($placeholders)");
        $stmt->execute($event_ids);
        
        $db->commit();
        $success_message = count($event_ids) . ' etkinlik başarıyla silindi!';
        header('Location: admin.php?tab=event-list');
        exit;
    } catch (PDOException $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        $error_message = 'Etkinlikler silinirken hata oluştu: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .login-form {
            max-width: 400px;
            margin: 0 auto;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-group textarea {
            height: 100px;
        }
        .btn {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-danger {
            background-color: #f44336;
        }
        .btn-warning {
            background-color: #ff9800;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #dff0d8;
            color: #3c763d;
        }
        .alert-danger {
            background-color: #f2dede;
            color: #a94442;
        }
        .app-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .app-table th, .app-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .app-table th {
            background-color: #f2f2f2;
        }
        .app-actions {
            display: flex;
            gap: 5px;
            justify-content: center;
        }
        .tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        .tab {
            padding: 10px 15px;
            cursor: pointer;
        }
        .tab.active {
            border-bottom: 2px solid #4CAF50;
            font-weight: bold;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .image-preview {
            max-width: 100px;
            max-height: 100px;
            margin-top: 10px;
            border: 1px solid #ddd;
            padding: 5px;
        }
        .file-input-container {
            margin-top: 10px;
        }
        .or-text {
            margin: 10px 0;
            text-align: center;
            font-style: italic;
            color: #666;
        }
        .file-info {
            font-size: 0.8em;
            color: #666;
            margin-top: 5px;
        }
        .button {
            padding: 6px 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            white-space: nowrap;
            display: inline-block;
            text-decoration: none;
            font-size: 14px;
            margin: 2px;
        }
        .button.delete {
            background-color: #f44336;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 800px;
            position: relative;
        }

        .close-modal {
            position: absolute;
            right: 15px;
            top: 10px;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-modal:hover {
            color: #f44336;
        }
        .bulk-actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="banner">
        <h1>Platform&Security DevSecOps Tools</h1>
    </div>
    
    <div class="admin-container">
        <h1>Admin Panel</h1>
        
        <?php if (!$is_logged_in): ?>
            <!-- Giriş Formu -->
            <div class="login-form">
                <?php if ($login_error): ?>
                    <div class="alert alert-danger"><?php echo $login_error; ?></div>
                <?php endif; ?>
                
                <form method="post" action="">
                    <div class="form-group">
                        <label for="username">Kullanıcı Adı:</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Şifre:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn">Giriş Yap</button>
                </form>
            </div>
        <?php else: ?>
            <!-- Admin Kontrol Paneli -->
            <p>Hoş geldiniz, Admin! <a href="?logout=1">Çıkış Yap</a> | <a href="index.php">Ana Sayfaya Dön</a></p>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <div class="tabs">
                <div class="tab" data-tab="app-list">Uygulama Listesi</div>
                <div class="tab" data-tab="add-app">Yeni Uygulama Ekle</div>
                <div class="tab" data-tab="access-list">Erişim Listesi</div>
                <div class="tab" data-tab="event-list">Etkinlik Listesi</div>
                <div class="tab" data-tab="add-event">Yeni Etkinlik Ekle</div>
                <div class="tab" data-tab="procedure-list">Prosedür Listesi</div>
                <div class="tab" data-tab="add-procedure">Yeni Prosedür Ekle</div>
            </div>
            
            <!-- Uygulama Listesi -->
            <div class="tab-content active" id="app-list">
                <h2>Uygulama Listesi</h2>
                
                <?php if (isset($apps) && !empty($apps)): ?>
                    <table class="app-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>İsim</th>
                                <th>Açıklama</th>
                                <th>Kategori</th>
                                <th>URL</th>
                                <th>Resim</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($apps as $app): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($app['id']); ?></td>
                                    <td><?php echo htmlspecialchars($app['name']); ?></td>
                                    <td><?php echo htmlspecialchars($app['description']); ?></td>
                                    <td><?php echo htmlspecialchars($app['category']); ?></td>
                                    <td><?php echo htmlspecialchars($app['url']); ?></td>
                                    <td>
                                        <?php if (!empty($app['image'])): ?>
                                            <img src="<?php echo htmlspecialchars($app['image']); ?>" alt="<?php echo htmlspecialchars($app['name']); ?>" class="image-preview">
                                        <?php else: ?>
                                            Resim yok
                                        <?php endif; ?>
                                    </td>
                                    <td class="app-actions">
                                        <button class="edit-app button" 
                                                data-id="<?php echo $app['id']; ?>"
                                                data-name="<?php echo htmlspecialchars($app['name']); ?>"
                                                data-description="<?php echo htmlspecialchars($app['description']); ?>"
                                                data-category="<?php echo htmlspecialchars($app['category']); ?>"
                                                data-url="<?php echo htmlspecialchars($app['url']); ?>"
                                                data-purpose="<?php echo htmlspecialchars($app['purpose'] ?? ''); ?>"
                                                data-image="<?php echo htmlspecialchars($app['image']); ?>">Düzenle</button>
                                        <a href="admin.php?delete_app=<?php echo $app['id']; ?>" class="button delete" onclick="return confirm('Bu uygulamayı silmek istediğinizden emin misiniz?');">Sil</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Henüz hiç uygulama veritabanına kaydedilmemiş veya veritabanı bağlantısı kurulamadı.</p>
                    
                    <h3>Mevcut Statik Uygulamalar:</h3>
                    <table class="app-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>İsim</th>
                                <th>Açıklama</th>
                                <th>Kategori</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($app['id']); ?></td>
                                    <td><?php echo htmlspecialchars($app['name']); ?></td>
                                    <td><?php echo htmlspecialchars($app['description']); ?></td>
                                    <td><?php echo htmlspecialchars($app['category']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            
            <!-- Yeni Uygulama Ekleme Formu -->
            <div class="tab-content" id="add-app">
                <h2>Yeni Uygulama Ekle</h2>
                <form method="post" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Uygulama Adı *</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Açıklama</label>
                        <textarea id="description" name="description" rows="4"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Kategori *</label>
                        <select id="category" name="category" required>
                            <option value="">Seçiniz...</option>
                            <option value="DevOps">DevOps</option>
                            <option value="Monitoring">Monitoring</option>
                            <option value="Security">Security</option>
                            <option value="AI">AI</option>
                            <option value="Middleware">Middleware</option>
                            <option value="Automation">Automation</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="url">URL *</label>
                        <input type="url" id="url" name="url" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="purpose">Kullanım Amacı</label>
                        <textarea id="purpose" name="purpose" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Resim Yükle: *</label>
                        <input type="file" id="image" name="image" accept="image/png" required>
                        <div id="image_preview_container"></div>
                        <p class="file-info">Desteklenen format: PNG</p>
                        <p class="file-info">Dosya adı otomatik olarak uygulama adı kullanılarak oluşturulacaktır (örn: uygulamaadi.png).</p>
                        <p class="file-info">Resimler yerel sunucu üzerindeki "images" dizinine kaydedilecektir.</p>
                    </div>
                    
                    <button type="submit" name="add_app" class="button">Uygulama Ekle</button>
                </form>
            </div>
            
            <!-- Erişim Yönetimi -->
            <div class="tab-content" id="access-list">
                <h2>Erişim Yönetimi</h2>
                    
                    <form method="post" action="" class="add-form">
                    <h3>Yeni Erişim Ekle</h3>
                            <div class="form-group">
                                <label for="app_id">Uygulama:</label>
                                <select id="app_id" name="app_id" required>
                                    <option value="">Uygulama Seçin</option>
                                    <?php foreach ($applications as $app): ?>
                                        <option value="<?php echo $app['id']; ?>"><?php echo $app['name']; ?> (<?php echo $app['category']; ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                        <label for="access_name">Erişim Adı:</label>
                        <input type="text" id="access_name" name="name" required>
                            </div>
                            <div class="form-group">
                        <label for="access_url">URL:</label>
                        <input type="url" id="access_url" name="url" required>
                            </div>
                    <button type="submit" class="btn">Erişim Ekle</button>
                    </form>
                    
                    <h3>Mevcut Erişimler</h3>
                <?php if (!empty($access_data)): ?>
                    <table class="app-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Uygulama</th>
                                    <th>Erişim Adı</th>
                                    <th>URL</th>
                                <th>Oluşturulma Tarihi</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($access_data as $access): ?>
                                    <?php 
                                    $app_name = '';
                                    foreach ($applications as $app) {
                                        if ($app['id'] == $access['app_id']) {
                                            $app_name = $app['name'];
                                            break;
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo $access['id']; ?></td>
                                        <td><?php echo htmlspecialchars($app_name); ?></td>
                                        <td><?php echo htmlspecialchars($access['name']); ?></td>
                                        <td><a href="<?php echo htmlspecialchars($access['url']); ?>" target="_blank"><?php echo htmlspecialchars($access['url']); ?></a></td>
                                        <td><?php echo $access['created_at']; ?></td>
                                        <td>
                                        <a href="admin.php?delete_id=<?php echo $access['id']; ?>" class="btn btn-danger" onclick="return confirm('Bu erişim bilgisini silmek istediğinizden emin misiniz?');">Sil</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                <?php else: ?>
                    <p>Henüz hiç erişim bilgisi yok.</p>
                    <?php endif; ?>
                </div>
            
            <!-- Etkinlik Listesi -->
            <div id="event-list" class="tab-content">
                <h2>Etkinlik Listesi</h2>
                <form method="post" action="admin.php">
                    <table class="app-table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all-events"></th>
                                <th>ID</th>
                                <th>Başlık</th>
                                <th>Tarih</th>
                                <th>Saat</th>
                                <th>Öncelik</th>
                                <th>Kategori</th>
                                <th>Ekip</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $stmt = $db->query("SELECT * FROM events ORDER BY event_date DESC");
                                $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                foreach ($events as $event) {
                                    $event_date = new DateTime($event['event_date']);
                                    $formatted_date = $event_date->format('d.m.Y');
                                    $formatted_time = !empty($event['event_time']) ? substr($event['event_time'], 0, 5) : '-';
                                    
                                    echo '<tr>';
                                    echo '<td><input type="checkbox" name="event_ids[]" value="' . $event['id'] . '" class="event-checkbox"></td>';
                                    echo '<td>' . $event['id'] . '</td>';
                                    echo '<td>' . htmlspecialchars($event['title']) . '</td>';
                                    echo '<td>' . $formatted_date . '</td>';
                                    echo '<td>' . $formatted_time . '</td>';
                                    echo '<td>' . htmlspecialchars($event['priority']) . '</td>';
                                    echo '<td>' . htmlspecialchars($event['category']) . '</td>';
                                    echo '<td>' . htmlspecialchars($event['team'] ?? '') . '</td>';
                                    echo '<td>';
                                    echo '<button class="edit-event button" 
                                        data-id="' . $event['id'] . '"
                                        data-title="' . htmlspecialchars($event['title']) . '"
                                        data-description="' . htmlspecialchars($event['description']) . '"
                                        data-date="' . $event['event_date'] . '"
                                        data-time="' . ($event['event_time'] ?? '') . '"
                                        data-priority="' . htmlspecialchars($event['priority']) . '"
                                        data-category="' . htmlspecialchars($event['category']) . '"
                                        data-team="' . htmlspecialchars($event['team'] ?? '') . '">Düzenle</button>';
                                    echo ' <a href="admin.php?delete_event=' . $event['id'] . '&tab=event-list" class="button delete" onclick="return confirm(\'Bu etkinliği silmek istediğinizden emin misiniz?\')">Sil</a>';
                                    echo '</td>';
                                    echo '</tr>';
                                }
                            } catch (PDOException $e) {
                                echo '<tr><td colspan="9">Etkinlikler yüklenirken bir hata oluştu.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                    <div class="bulk-actions">
                        <button type="submit" name="bulk_delete_events" class="button delete" onclick="return confirm('Seçili etkinlikleri silmek istediğinizden emin misiniz?')">Seçili Etkinlikleri Sil</button>
                    </div>
                </form>
            </div>
            
            <!-- Yeni Etkinlik Ekle -->
            <div id="add-event" class="tab-content">
                <h2>Yeni Etkinlik Ekle</h2>
                <?php if (isset($error_message) && !empty($error_message)): ?>
                    <div class="error"><?= $error_message ?></div>
        <?php endif; ?>
                <?php if (isset($success_message) && !empty($success_message)): ?>
                    <div class="success"><?= $success_message ?></div>
                <?php endif; ?>
                
                <form method="post" action="">
                    <div class="form-group">
                        <label for="title">Başlık *</label>
                        <input type="text" id="title" name="title" required>
    </div>
    
                    <div class="form-group">
                        <label for="description">Açıklama</label>
                        <textarea id="description" name="description" rows="4"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="event_date">Tarih *</label>
                        <input type="date" id="event_date" name="event_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="event_time">Saat</label>
                        <input type="time" id="event_time" name="event_time">
                    </div>
                    
                    <div class="form-group">
                        <label for="priority">Öncelik</label>
                        <select id="priority" name="priority">
                            <option value="Yüksek">Yüksek</option>
                            <option value="Orta" selected>Orta</option>
                            <option value="Düşük">Düşük</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Kategori *</label>
                        <select id="category" name="category" required>
                            <option value="">Seçiniz...</option>
                            <option value="Canlı Geçiş">Canlı Geçiş</option>
                            <option value="Lansman">Lansman</option>
                            <option value="Techtalks">TechTalks</option>
                            <option value="Rapor">Rapor</option>
                            <option value="Proje">Proje</option>
                            <option value="Planlı Çalışma">Planlı Çalışma</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="team">Ekip</label>
                        <select id="team" name="team">
                            <option value="">Seçiniz...</option>
                            <option value="ARGE">ARGE</option>
                            <option value="DevOps">DevOps</option>
                            <option value="Sistem & Network">Sistem & Network</option>
                            <option value="Database">Database</option>
                            <option value="YAZILIM">YAZILIM</option>
                        </select>
                    </div>
                    
                    <div class="form-group checkbox-group">
                        <input type="checkbox" id="is_recurring" name="is_recurring">
                        <label for="is_recurring">Tekrarlayan Etkinlik</label>
                    </div>
                    
                    <div id="recurrence-options" style="display: none;">
                        <div class="form-group">
                            <label for="recurrence_type">Tekrarlama Tipi *</label>
                            <select id="recurrence_type" name="recurrence_type">
                                <option value="">Seçiniz...</option>
                                <option value="daily">Günlük</option>
                                <option value="weekly">Haftalık</option>
                                <option value="bi-weekly">İki Haftalık</option>
                                <option value="monthly">Aylık</option>
                                <option value="yearly">Yıllık</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="recurrence_end_date">Bitiş Tarihi *</label>
                            <input type="date" id="recurrence_end_date" name="recurrence_end_date">
                        </div>
                    </div>
                    
                    <button type="submit" name="add_event" class="button">Etkinlik Ekle</button>
                </form>
            </div>
            
            <!-- Prosedür Listesi -->
            <div id="procedure-list" class="tab-content">
                <h2>Prosedür Listesi</h2>
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Başlık</th>
                            <th>Özet</th>
                            <th>Kontrol Süresi</th>
                            <th>Doküman</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($procedures) && !empty($procedures)): ?>
                            <?php foreach ($procedures as $procedure): ?>
                                <tr>
                                    <td><?php echo $procedure['id']; ?></td>
                                    <td><?php echo htmlspecialchars($procedure['title']); ?></td>
                                    <td><?php echo htmlspecialchars($procedure['summary']); ?></td>
                                    <td><?php echo htmlspecialchars($procedure['period']); ?></td>
                                    <td>
                                        <a href="<?php echo htmlspecialchars($procedure['document_url']); ?>" target="_blank" class="button">Görüntüle</a>
                                    </td>
                                    <td>
                                        <button class="edit-procedure button" 
                                                data-id="<?php echo $procedure['id']; ?>"
                                                data-title="<?php echo htmlspecialchars($procedure['title']); ?>"
                                                data-summary="<?php echo htmlspecialchars($procedure['summary']); ?>"
                                                data-period="<?php echo htmlspecialchars($procedure['period']); ?>"
                                                data-document="<?php echo htmlspecialchars($procedure['document_url']); ?>">Düzenle</button>
                                        <a href="admin.php?delete_procedure=<?php echo $procedure['id']; ?>" class="button delete" onclick="return confirm('Bu prosedürü silmek istediğinizden emin misiniz?');">Sil</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">Henüz prosedür eklenmemiş.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Yeni Prosedür Ekle -->
            <div id="add-procedure" class="tab-content">
                <h2>Yeni Prosedür Ekle</h2>
                <form method="post" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="procedure_title">Başlık *</label>
                        <input type="text" id="procedure_title" name="procedure_title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="procedure_summary">Özet *</label>
                        <textarea id="procedure_summary" name="procedure_summary" rows="4" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="procedure_period">Kontrol Süresi *</label>
                        <input type="text" id="procedure_period" name="procedure_period" placeholder="Örn: 3 Ay, 6 Ay, Yıllık" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="procedure_document">Doküman (PDF) *</label>
                        <input type="file" id="procedure_document" name="procedure_document" accept="application/pdf" required>
                        <p class="file-info">Yalnızca PDF dosyaları yüklenebilir.</p>
                    </div>
                    
                    <button type="submit" name="add_procedure" class="button">Prosedür Ekle</button>
                </form>
            </div>
            
        <?php endif; ?>
    </div>
    
    <!-- Uygulama Düzenle Modal -->
    <div id="edit-app-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" id="close-edit-app">&times;</span>
            <h2>Uygulama Düzenle</h2>
            <form method="post" action="" enctype="multipart/form-data">
                <input type="hidden" id="edit-app-id" name="app_id">
                
                <div class="form-group">
                    <label for="edit_name">Uygulama Adı *</label>
                    <input type="text" id="edit-name" name="edit_name" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_description">Açıklama</label>
                    <textarea id="edit-description" name="edit_description" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="edit_category">Kategori *</label>
                    <select id="edit-category" name="edit_category" required>
                        <option value="">Seçiniz...</option>
                        <option value="DevOps">DevOps</option>
                        <option value="Monitoring">Monitoring</option>
                        <option value="Security">Security</option>
                        <option value="AI">AI</option>
                        <option value="Middleware">Middleware</option>
                        <option value="Automation">Automation</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_url">URL *</label>
                    <input type="url" id="edit-url" name="edit_url" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_purpose">Kullanım Amacı</label>
                    <textarea id="edit-purpose" name="edit_purpose" rows="3"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Mevcut Resim:</label>
                    <div class="current-image">
                        <img id="current-image-preview" src="" alt="Mevcut resim" style="max-width: 200px; max-height: 150px; display: none;">
                        <input type="hidden" id="current-image" name="current_image">
                    </div>
                    
                    <label for="edit_image">Yeni Resim Yükle:</label>
                    <input type="file" id="edit-image" name="edit_image" accept="image/png">
                    <div id="edit-image-preview-container"></div>
                    <p class="file-info">Desteklenen format: PNG</p>
                    <p class="file-info">Dosya adı otomatik olarak uygulama adı kullanılarak oluşturulacaktır (örn: uygulamaadi.png).</p>
                    <p class="file-info">Resimler yerel sunucu üzerindeki "images" dizinine kaydedilecektir.</p>
                </div>
                
                <button type="submit" name="edit_app" class="button">Uygulamayı Güncelle</button>
                <button type="button" class="button" id="cancel-edit">İptal</button>
            </form>
        </div>
    </div>
    
    <!-- Etkinlik Düzenle Modal -->
    <div id="edit-event-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" id="close-edit-event">&times;</span>
            <h2>Etkinlik Düzenle</h2>
            <?php if (isset($error_message) && !empty($error_message)): ?>
                <div class="error"><?= $error_message ?></div>
            <?php endif; ?>
            
            <form method="post" action="">
                <input type="hidden" id="edit-event-id" name="event_id">
                
                <div class="form-group">
                    <label for="edit_title">Başlık *</label>
                    <input type="text" id="edit-title" name="edit_title" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_description">Açıklama</label>
                    <textarea id="edit-description" name="edit_description" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="edit_event_date">Tarih *</label>
                    <input type="date" id="edit-event-date" name="edit_event_date" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_event_time">Saat</label>
                    <input type="time" id="edit-event-time" name="edit_event_time">
                </div>
                
                <div class="form-group">
                    <label for="edit_priority">Öncelik</label>
                    <select id="edit-priority" name="edit_priority">
                        <option value="Yüksek">Yüksek</option>
                        <option value="Orta">Orta</option>
                        <option value="Düşük">Düşük</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_category">Kategori *</label>
                    <select id="edit-category" name="edit_category" required>
                        <option value="">Seçiniz...</option>
                        <option value="Canlı Geçiş">Canlı Geçiş</option>
                        <option value="Lansman">Lansman</option>
                        <option value="Techtalks">TechTalks</option>
                        <option value="Rapor">Rapor</option>
                        <option value="Proje">Proje</option>
                        <option value="Planlı Çalışma">Planlı Çalışma</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_team">Ekip</label>
                    <select id="edit-team" name="edit_team">
                        <option value="">Seçiniz...</option>
                        <option value="ARGE">ARGE</option>
                        <option value="DevOps">DevOps</option>
                        <option value="Sistem & Network">Sistem & Network</option>
                        <option value="Database">Database</option>
                        <option value="YAZILIM">YAZILIM</option>
                    </select>
                </div>
                
                <button type="submit" name="edit_event" class="button">Etkinliği Güncelle</button>
                <button type="button" id="cancel-edit-event" class="button">İptal</button>
            </form>
        </div>
    </div>
    
    <!-- Prosedür Düzenle Modal -->
    <div id="edit-procedure-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" id="close-edit-procedure">&times;</span>
            <h2>Prosedür Düzenle</h2>
            <form method="post" action="" enctype="multipart/form-data">
                <input type="hidden" id="edit-procedure-id" name="procedure_id">
                
                <div class="form-group">
                    <label for="edit_procedure_title">Başlık *</label>
                    <input type="text" id="edit-procedure-title" name="edit_procedure_title" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_procedure_summary">Özet *</label>
                    <textarea id="edit-procedure-summary" name="edit_procedure_summary" rows="4" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="edit_procedure_period">Kontrol Süresi *</label>
                    <input type="text" id="edit-procedure-period" name="edit_procedure_period" required>
                </div>
                
                <div class="form-group">
                    <label>Mevcut Doküman:</label>
                    <div class="current-document">
                        <a id="current-document-link" href="" target="_blank">Mevcut Dokümanı Görüntüle</a>
                        <input type="hidden" id="current-document" name="current_document">
                    </div>
                    
                    <label for="edit_procedure_document">Yeni Doküman Yükle (PDF):</label>
                    <input type="file" id="edit-procedure-document" name="edit_procedure_document" accept="application/pdf">
                    <p class="file-info">Yalnızca PDF dosyaları yüklenebilir.</p>
                </div>
                
                <button type="submit" name="edit_procedure" class="button">Prosedürü Güncelle</button>
                <button type="button" id="cancel-edit-procedure" class="button">İptal</button>
            </form>
        </div>
    </div>
    
    <script>
        // Sekme değiştirme işlevi
        document.addEventListener('DOMContentLoaded', function() {
            // URL'den tab parametresini almak için fonksiyon
            function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                var results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            }
            
            // URL'de tab parametresi varsa ilgili sekmeyi aç
            var tabParam = getUrlParameter('tab');
            if (tabParam) {
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                
                // İlgili tab ve içeriğini aktif yap
                var selectedTab = document.querySelector('[data-tab="' + tabParam + '"]');
                if (selectedTab) {
                    selectedTab.classList.add('active');
                    document.getElementById(tabParam).classList.add('active');
                }
            }
            
            // Etkinlik listesi için toplu seçim
            const selectAllEvents = document.getElementById('select-all-events');
            if (selectAllEvents) {
                selectAllEvents.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.event-checkbox');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });
            }
            
            // Sekme değiştirme
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    
                    // Tüm sekmeleri pasif yap
                    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                    
                    // Seçilen sekmeyi aktif yap
                    this.classList.add('active');
                    document.getElementById(tabId).classList.add('active');
                });
            });
            
            // Dosya önizleme
            const appImage = document.getElementById('image');
            const previewContainer = document.getElementById('image_preview_container');
            
            if (appImage) {
                appImage.addEventListener('change', function() {
                    previewContainer.innerHTML = '';
                    
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'image-preview';
                            previewContainer.appendChild(img);
                        }
                        
                        reader.readAsDataURL(this.files[0]);
                    }
                });
            }
            
            // Düzenleme önizleme
            const editAppImage = document.getElementById('edit-image');
            const editPreviewContainer = document.getElementById('edit-image-preview-container');
            
            if (editAppImage) {
                editAppImage.addEventListener('change', function() {
                    editPreviewContainer.innerHTML = '';
                    
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'image-preview';
                            editPreviewContainer.appendChild(img);
                        }
                        
                        reader.readAsDataURL(this.files[0]);
                    }
                });
            }
            
            // Uygulama düzenleme modal
            const editButtons = document.querySelectorAll('.edit-app');
            const editModal = document.getElementById('edit-app-modal');
            const closeEditModal = document.getElementById('close-edit-app');
            const cancelEdit = document.getElementById('cancel-edit');
            
            editButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');
                    const description = this.getAttribute('data-description');
                    const category = this.getAttribute('data-category');
                    const url = this.getAttribute('data-url');
                    const purpose = this.getAttribute('data-purpose');
                    const image = this.getAttribute('data-image');
                    
                    // Form alanlarını doldur
                    document.getElementById('edit-app-id').value = id;
                    document.getElementById('edit-name').value = name;
                    document.getElementById('edit-description').value = description;
                    document.getElementById('edit-category').value = category;
                    document.getElementById('edit-url').value = url;
                    document.getElementById('edit-purpose').value = purpose;
                    document.getElementById('current-image').value = image;
                    
                    // Mevcut resim önizlemesi
                    const currentImagePreview = document.getElementById('current-image-preview');
                    if (image && image.trim() !== '') {
                        currentImagePreview.src = image;
                        currentImagePreview.style.display = 'block';
                    } else {
                        currentImagePreview.style.display = 'none';
                    }
                    
                    // Modalı göster
                    editModal.style.display = 'block';
                });
            });
            
            // Modal'ı kapat
            if (closeEditModal) {
                closeEditModal.addEventListener('click', function() {
                    editModal.style.display = 'none';
                });
            }
            
            // İptal butonu
            if (cancelEdit) {
                cancelEdit.addEventListener('click', function() {
                    editModal.style.display = 'none';
                });
            }
            
            // Etkinlik düzenleme modal
            const editEventButtons = document.querySelectorAll('.edit-event');
            const editEventModal = document.getElementById('edit-event-modal');
            const closeEditEvent = document.getElementById('close-edit-event');
            const cancelEditEvent = document.getElementById('cancel-edit-event');
            
            editEventButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');
                    const title = this.getAttribute('data-title');
                    const description = this.getAttribute('data-description');
                    const date = this.getAttribute('data-date');
                    const time = this.getAttribute('data-time');
                    const priority = this.getAttribute('data-priority');
                    const category = this.getAttribute('data-category');
                    const team = this.getAttribute('data-team');
                    
                    // Form alanlarını doldur
                    document.getElementById('edit-event-id').value = id;
                    document.getElementById('edit-title').value = title;
                    document.getElementById('edit-description').value = description;
                    document.getElementById('edit-event-date').value = date;
                    document.getElementById('edit-event-time').value = time;
                    document.getElementById('edit-priority').value = priority;
                    document.getElementById('edit-category').value = category;
                    document.getElementById('edit-team').value = team;
                    
                    // Modalı göster
                    editEventModal.style.display = 'block';
                });
            });
            
            // Modal'ı kapat
            if (closeEditEvent) {
                closeEditEvent.addEventListener('click', function() {
                    editEventModal.style.display = 'none';
                });
            }
            
            // İptal butonu
            if (cancelEditEvent) {
                cancelEditEvent.addEventListener('click', function() {
                    editEventModal.style.display = 'none';
                });
            }
            
            // Prosedür düzenleme modalı
            const editProcedureButtons = document.querySelectorAll('.edit-procedure');
            const editProcedureModal = document.getElementById('edit-procedure-modal');
            const closeEditProcedure = document.getElementById('close-edit-procedure');
            const cancelEditProcedure = document.getElementById('cancel-edit-procedure');
            
            editProcedureButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');
                    const title = this.getAttribute('data-title');
                    const summary = this.getAttribute('data-summary');
                    const period = this.getAttribute('data-period');
                    const document = this.getAttribute('data-document');
                    
                    // Form alanlarını doldur
                    document.getElementById('edit-procedure-id').value = id;
                    document.getElementById('edit-procedure-title').value = title;
                    document.getElementById('edit-procedure-summary').value = summary;
                    document.getElementById('edit-procedure-period').value = period;
                    document.getElementById('current-document').value = document;
                    
                    // Doküman bağlantısını güncelle
                    const documentLink = document.getElementById('current-document-link');
                    documentLink.href = document;
                    
                    // Modalı göster
                    editProcedureModal.style.display = 'block';
                });
            });
            
            // Modal'ı kapat
            if (closeEditProcedure) {
                closeEditProcedure.addEventListener('click', function() {
                    editProcedureModal.style.display = 'none';
                });
            }
            
            // İptal butonu
            if (cancelEditProcedure) {
                cancelEditProcedure.addEventListener('click', function() {
                    editProcedureModal.style.display = 'none';
                });
            }
            
            // Modal dışına tıklandığında kapat
            window.addEventListener('click', function(event) {
                if (event.target === editModal) {
                    editModal.style.display = 'none';
                }
                if (event.target === editEventModal) {
                    editEventModal.style.display = 'none';
                }
                if (event.target === editProcedureModal) {
                    editProcedureModal.style.display = 'none';
                }
            });
            
            // Tekrarlayan etkinlik seçeneği için
            const isRecurringCheckbox = document.getElementById('is_recurring');
            const recurrenceOptions = document.getElementById('recurrence-options');
            
            if (isRecurringCheckbox && recurrenceOptions) {
                isRecurringCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        recurrenceOptions.style.display = 'block';
                    } else {
                        recurrenceOptions.style.display = 'none';
                    }
                });
            }
        });
    </script>
</body>
</html> 