# 🤖 Chatbot Toko Komputer - PHP Native

Chatbot AI untuk toko komputer dengan sistem **RAG (Retrieval Augmented Generation)** yang dapat menjawab pertanyaan tentang stok dan harga produk secara otomatis.

![PHP](https://img.shields.io/badge/PHP-8.0+-blue)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange)
![OpenRouter](https://img.shields.io/badge/OpenRouter-AI-green)
![License](https://img.shields.io/badge/License-MIT-yellow)

## ✨ Fitur Utama

- **🤖 AI Assistant Pintar** - Menggunakan OpenRouter/OpenAI untuk percakapan natural
- **📦 RAG System** - Sistem pencarian produk otomatis dari database MySQL
- **💬 Multi-Session Chat** - Support multiple percakapan sekaligus
- **🎯 Produk Management** - Kelola stok dan harga produk di database
- **🎨 Modern UI** - Interface yang clean dan user-friendly
- **📱 Responsive** - Bekerja di desktop dan mobile
- **⚙️ Session Management** - Buat, rename, hapus percakapan
- **🔍 Real-time Search** - Pencarian produk real-time berdasarkan keyword

## 🚀 Demo Screenshots

*Interface chatbot dengan sidebar percakapan dan area chat utama*

## 🛠️ Teknologi

- **Backend**: PHP 8.0+ (Native)
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **AI Provider**: OpenRouter API / OpenAI API
- **Architecture**: MVC Pattern dengan AJAX

## 📋 Requirements

- PHP 8.0 atau lebih baru
- MySQL 5.7 atau lebih baru
- Web server (Apache/Nginx/XAMPP/WAMP)
- OpenRouter API Key (atau OpenAI API Key)

## 🔧 Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/qtonnnn/chatbot.git
cd chatbot
```

### 2. Setup Database
Import file `chatbot.sql` ke database MySQL Anda:
```sql
mysql -u username -p database_name < chatbot.sql
```

### 3. Konfigurasi Database
Edit file `index.php`, bagian **KONFIGURASI**:
```php
$DB_HOST = "localhost";
$DB_USER = "root";          // Username MySQL Anda
$DB_PASS = "";              // Password MySQL Anda
$DB_NAME = "chatbot";       // Nama database
```

### 4. Setup API Key
Dapatkan API Key dari [OpenRouter](https://openrouter.ai/) atau [OpenAI](https://platform.openai.com/):

```php
$OPENROUTER_API_KEY = "sk-or-v1-your-api-key-here";
$SITE_URL = "http://localhost/chat"; 
$SITE_TITLE = "Chat Toko Saya";
```

### 5. Jalankan
Place semua file di web server directory dan akses via browser:
```
http://localhost/chat/index.php
```

## ⚙️ Konfigurasi Lanjutan

### Mengganti Model AI
Edit fungsi `kirim_ke_openrouter()` untuk mengubah model:

```php
$data = [
    "model" => "openai/gpt-4o",           // Model pintar (mahal)
    // atau "google/gemini-2.0-flash-001"  // Model cepat (murah)
    // atau "deepseek/deepseek-chat"       // Model balance
    "messages" => $history
];
```

### Menambah Produk
Tambah data produk di tabel `produk`:
```sql
INSERT INTO produk (nama_produk, kategori, harga, stok, deskripsi) 
VALUES ('Nama Produk', 'Kategori', 1000000, 10, 'Deskripsi produk');
```

### Beralih ke OpenAI Resmi
Untuk menggunakan OpenAI API langsung, ubah URL endpoint:

```php
// Ganti URL ini
$url = "https://api.openai.com/v1/chat/completions";

// Hapus header OpenRouter
CURLOPT_HTTPHEADER => [
    "Content-Type: application/json",
    "Authorization: Bearer " . $apiKey
    // HAPUS: "HTTP-Referer" dan "X-Title"
],
```

## 📁 Struktur File

```
chatbot/
├── index.php              # File utama aplikasi
├── chatbot.sql            # Schema database
├── dokumentasi.md         # Dokumentasi teknis
├── package.json           # Dependencies (jika ada)
├── .gitignore            # Git ignore rules
├── test_ajax.php         # Test file AJAX
├── test_db.php           # Test file database
└── js/
    └── chat.js           # JavaScript frontend
```

## 🗄️ Database Schema

### Tabel `chat_sessions`
Menyimpan data percakapan:
- `id` - Primary key
- `session_name` - Nama percakapan
- `created_at` - Waktu pembuatan

### Tabel `chat_messages`
Menyimpan pesan chat:
- `id` - Primary key
- `session_id` - Foreign key ke chat_sessions
- `sender` - 'user' atau 'bot'
- `message` - Isi pesan
- `created_at` - Waktu pengiriman

### Tabel `produk`
Menyimpan data produk:
- `id` - Primary key
- `nama_produk` - Nama produk
- `kategori` - Kategori produk
- `harga` - Harga produk
- `stok` - Stok tersedia
- `deskripsi` - Deskripsi produk
- `created_at` - Waktu input

## 🎯 Cara Penggunaan

### Untuk Customer:
1. **Mulai Chat** - Klik tombol "+ Chat Baru"
2. **Tanya Produk** - Tanyakan stok, harga, atau info produk
3. **Kelola Riwayat** - Lihat semua percakapan di sidebar

### Untuk Admin:
1. **Tambah Produk** - Input via phpMyAdmin atau SQL
2. **Monitor Chat** - Lihat semua session di database
3. **Update Stok** - Update tabel `produk` secara berkala

## 🔍 Contoh Query Chat

```
User: "Ada laptop Asus VivoBook stoknya?"
Bot: "Ya, Laptop Asus VivoBook tersedia dengan stok 3 unit. 
      Harga: Rp 10.500.000 dengan spesifikasi i7 RAM 16GB."

User: "Berapa harga CCTV 2MP?"
Bot: "CCTV Hikvision 2MP seharga Rp 350.000 dengan stok 15 unit.
      Kamera indoor 2MP tajam."

User: "Mouse wireless ada yang bagus?"
Bot: "Ya, ada Mouse Logitech Wireless seharga Rp 150.000 
      dengan stok 20 unit. Mouse tanpa kabel yang awet."
```

## 🛠️ API Endpoints

Semua endpoint melalui parameter `action`:

- `send_message` - Kirim pesan chat
- `load_messages` - Load pesan chat berdasarkan session
- `list_sessions` - List semua percakapan
- `new_session` - Buat percakapan baru
- `rename_session` - Ganti nama percakapan
- `delete_session` - Hapus percakapan tertentu
- `delete_all_sessions` - Hapus semua percakapan

## 🚨 Troubleshooting

### 1. "Maaf, tidak ada respon"
**Penyebab**: API Key belum disetting atau salah
**Solusi**: Pastikan `$OPENROUTER_API_KEY` sudah benar format `sk-or-v1-...`

### 2. Error koneksi database
**Penyebab**: Koneksi MySQL gagal
**Solusi**: 
- Periksa kredensial database di konfigurasi
- Pastikan MySQL service sudah berjalan
- Cek apakah database `chatbot` sudah dibuat

### 3. "Error Koneksi" 
**Penyebab**: Masalah SSL di localhost
**Solusi**: Pastikan setting SSL sudah benar:
```php
CURLOPT_SSL_VERIFYPEER => false,
CURLOPT_SSL_VERIFYHOST => 0,
```

### 4. Chatbot jawab "Stok habis" padahal ada produk
**Penyebab**: Data produk tidak cocok dengan keyword
**Solusi**: 
- Cek tabel `produk` apakah ada data
- Pastikan nama produk jelas dan searchable
- Tambah lebih banyak keyword di produk

## 🔧 Development

### Testing Database Connection
Buka `test_db.php` untuk test koneksi database.

### Testing AJAX
Buka `test_ajax.php` untuk test fungsi AJAX.

### Menambah Fitur Baru
1. Tambah function di `index.php`
2. Tambah case di switch statement
3. Update JavaScript di bagian bawah file

## 📝 Customization

### Mengubah Tampilan
Edit bagian CSS di `<style>` tag:
```css
:root{
    --primary: #2563eb;     /* Warna primary */
    --danger: #ef4444;      /* Warna danger */
    --bg: #f6f7fb;          /* Background */
}
```

### Menambah Filter Produk
Edit fungsi `cari_konteks_produk()` untuk menambah filter:
```php
$stmt = $mysqli->prepare("SELECT nama_produk, harga, stok 
                          FROM produk 
                          WHERE nama_produk LIKE ? OR kategori LIKE ? 
                          AND stok > 0  -- Tambah filter stok
");
```

##                          LIMIT 3 📄 License

MIT License - Silakan gunakan dan modifikasi sesuai kebutuhan.

## 🤝 Contributing

1. Fork repository
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📞 Support

Jika ada pertanyaan atau butuh bantuan:
- Buka issue di GitHub
- Baca dokumentasi lengkap di `dokumentasi.md`

---

**⭐ Jika project ini bermanfaat, jangan lupa berikan star di GitHub!**
