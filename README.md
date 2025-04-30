# DevSecOpsHub

[English](#english) | [Türkçe](#turkish)

## <a name="turkish"></a>Türkçe

DevSecOpsHub, modern yazılım geliştirme süreçlerinde güvenlik, geliştirme ve operasyon ekiplerinin ihtiyaç duyduğu araçları tek bir platformda birleştiren, açık kaynak bir merkezdir. Platform, DevOps ve güvenlik araçlarının katalog halinde sunulduğu, her bir aracın detaylı bilgileri ve erişim URL'lerinin yer aldığı, ekip bazlı takvim yönetimi ve prosedür dokümanlarının merkezi olarak yönetildiği kapsamlı bir çözümdür.

### Özellikler

- 📊 DevOps ve Güvenlik Araçları Kataloğu
- 📅 Ekip Bazlı Takvim Yönetimi
- 📑 Merkezi Prosedür Dokümantasyonu
- 🔐 Güvenli Erişim Yönetimi
- 🌐 Kolay Entegrasyon

### Gereksinimler

- Docker
- Docker Compose
- PostgreSQL (Docker içinde otomatik kurulur)

### Kurulum

```bash
# Repoyu klonlayın
git clone https://github.com/kariyertech/DevSecOpsHub.git

# Proje dizinine gidin
cd DevSecOpsHub

# Docker container'larını başlatın
docker-compose up -d
```

### Erişim Bilgileri

- **Web Arayüzü**: http://localhost:8080
- **Admin Paneli**: http://localhost:8080/admin.php
  - Kullanıcı adı: `admin`
  - Şifre: `admin123`

### Veritabanı Bilgileri

PostgreSQL veritabanı Docker ile otomatik olarak kurulur ve yapılandırılır:
- Veritabanı: `devopstool`
- Kullanıcı adı: `admin`
- Şifre: `admin123`
- Port: `5432`

### Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Detaylar için [LICENSE](LICENSE) dosyasına bakabilirsiniz.

---

## <a name="english"></a>English

DevSecOpsHub is an open-source platform that combines security, development, and operations tools in a single platform for modern software development processes. The platform offers a comprehensive solution where DevOps and security tools are presented in a catalog format, with detailed information and access URLs for each tool, team-based calendar management, and centralized procedure document management.

### Features

- 📊 DevOps and Security Tools Catalog
- 📅 Team-Based Calendar Management
- 📑 Centralized Procedure Documentation
- 🔐 Secure Access Management
- 🌐 Easy Integration

### Requirements

- Docker
- Docker Compose
- PostgreSQL (automatically installed in Docker)

### Installation

```bash
# Clone the repository
git clone https://github.com/kariyertech/DevSecOpsHub.git

# Navigate to project directory
cd DevSecOpsHub

# Start Docker containers
docker-compose up -d
```

### Access Information

- **Web Interface**: http://localhost:8080
- **Admin Panel**: http://localhost:8080/admin.php
  - Username: `admin`
  - Password: `admin123`

### Database Information

PostgreSQL database is automatically installed and configured with Docker:
- Database: `devopstool`
- Username: `admin`
- Password: `admin123`
- Port: `5432`

### License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.
