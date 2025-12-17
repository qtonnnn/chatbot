

File ini berisi panduan teknis untuk mengubah pengaturan API pada chatbot (PHP Native), termasuk mengganti API Key, mengubah Model AI, dan beralih Provider.

## 📂 Lokasi File Utama
Semua konfigurasi backend terletak di dalam file utama (biasanya bernama **`index.php`** atau **`api.php`** tergantung nama yang Anda simpan).

---

## 1. Mengganti API Key (OpenRouter)

Jika kuota habis atau Anda membuat key baru, ikuti langkah ini:

1. Buka file utama dengan text editor (VS Code, Notepad++, dll).
2. Cari bagian **KONFIGURASI** di baris-baris awal (sekitar baris 10-15).
3. Temukan variabel `$OPENROUTER_API_KEY`.
4. Ganti nilainya dengan key baru Anda.

```php
// SEBELUM
$OPENROUTER_API_KEY = "sk-or-v1-lama...";

// SESUDAH
$OPENROUTER_API_KEY = "sk-or-v1-baru-dari-dashboard-openrouter...";

```

> **Tips:** Pastikan tidak ada spasi tambahan di dalam tanda kutip.

---

## 2. Mengganti Model AI (Misal: ke GPT-4 atau Claude)

Secara default, kode menggunakan `openai/gpt-3.5-turbo`. Jika Anda ingin model yang lebih pintar (tapi mungkin lebih mahal) atau lebih murah/cepat:

1. Cari fungsi bernama `function kirim_ke_openrouter` (biasanya di bagian tengah file).
2. Cari array `$data`.
3. Ubah bagian `"model"`.

```php
// Contoh mengganti ke Google Gemini Flash (Cepat & Murah)
$data = [
    "model" => "google/gemini-2.0-flash-001", // Ganti string ini
    "messages" => $history
];

```

**Daftar Model Populer di OpenRouter:**

* `openai/gpt-3.5-turbo` (Standar)
* `openai/gpt-4o` (Sangat Pintar)
* `deepseek/deepseek-chat` (Murah & Bagus)
* `google/gemini-2.0-flash-001` (Cepat & Gratisan di tier tertentu)

---

## 3. Berpindah Provider (Dari OpenRouter ke OpenAI Resmi)

Jika Anda ingin berhenti menggunakan OpenRouter dan langsung menggunakan API resmi dari **OpenAI (https://www.google.com/search?q=api.openai.com)**, Anda perlu mengubah sedikit kode di fungsi cURL.

**Langkah-langkah:**

1. Ubah **URL Endpoint** di dalam fungsi `kirim_ke_openrouter` (atau buat fungsi baru).
2. Hapus header khusus OpenRouter (`HTTP-Referer` dan `X-Title`).

**Contoh Perubahan Kode cURL:**

Cari blok kode ini dan sesuaikan:

```php
// 1. Ganti URL Tujuan
$url = "[https://api.openai.com/v1/chat/completions](https://api.openai.com/v1/chat/completions)"; // URL Resmi OpenAI

// ...

// 2. Bersihkan Headers
curl_setopt_array($ch, [
    // ... settingan lain tetap sama ...
    
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer " . $apiKey 
        // HAPUS baris HTTP-Referer dan X-Title karena OpenAI menolaknya
    ],
    // ...
]);

```

> **Catatan:** Jika memakai OpenAI resmi, nama model cukup `gpt-3.5-turbo` (tanpa awalan `openai/`).

---

## 4. Troubleshooting (Masalah Umum)

### A. Chatbot Menjawab "Maaf, tidak ada respon" atau Error CURL

* **Penyebab:** Biasanya masalah koneksi internet atau SSL di Localhost.
* **Solusi:** Pastikan baris ini ada di setingan cURL Anda (khusus Localhost):
```php
CURLOPT_SSL_VERIFYPEER => false,
CURLOPT_SSL_VERIFYHOST => 0,

```



### B. Chatbot Halusinasi (Menjawab Stok/Harga Salah)

Chatbot ini menggunakan sistem **RAG (Retrieval Augmented Generation)**. Dia hanya tahu harga berdasarkan apa yang ada di Database MySQL.

* **Solusi:** Update data di tabel `produk` pada database `chat_db`. Chatbot akan otomatis membaca data terbaru saat ada user bertanya.

### C. Error "Insufficient Quota"

* **Penyebab:** Saldo API Key Anda habis.
* **Solusi:** Top up saldo di dashboard provider API (OpenRouter/OpenAI).

---

## 5. Struktur Database (Referensi)

Jika Anda ingin mengubah struktur data produk, pastikan tabel `produk` memiliki kolom minimal:

* `nama_produk` (VARCHAR)
* `harga` (DECIMAL/INT)
* `stok` (INT)

Query pencarian ada di fungsi `cari_konteks_produk`. Jika Anda mengubah nama kolom, jangan lupa ubah query `SELECT` di fungsi tersebut.

```

```