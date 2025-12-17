# Rencana Implementasi: Menambahkan Nomor Telepon untuk Pemesanan

## Informasi yang Dikumpulkan:
- Chatbot menggunakan PHP dengan OpenRouter API
- Struktur database sudah ada dengan tabel produk
- Response AI menggunakan system prompt untuk konteks produk
- Sistem RAG sudah aktif untuk mencari produk di database

## Plan Implementasi:

### 1. Fungsi Deteksi Intent Pembelian
- Membuat fungsi `deteksi_intent_pemesanan()` di PHP
- Mendeteksi kata kunci pembelian: "mau pesan", "order", "beli", "memesan", "pemesanan", "pembelian", dll
- Menggunakan regex pattern matching

### 2. Modifikasi Logic Response
- Di case 'send_message', tambahkan logic setelah AI response
- Jika user memiliki intent pembelian, tambahkan nomor telepon ke response
- Format: "Untuk pemesanan, silakan hubungi: 085791455813"

### 3. Update System Prompt
- Tambahkan instruksi ke AI bahwa nomor telepon harus diberikan untuk pembelian
- Pastikan AI tidak redundan dalam memberikan nomor telepon

### 4. Testing
- Test berbagai skenario pembelian
- Pastikan nomor telepon muncul hanya ketika diperlukan

## File yang akan Diedit:
- `/opt/lampp/htdocs/chat/index.php` - Menambahkan fungsi deteksi dan modifikasi response


## Steps:
1. ✅ Analisis file existing
2. ✅ Buat rencana implementasi  
3. ✅ Implementasi fungsi deteksi intent
4. ✅ Modifikasi logic response
5. ✅ Menambahkan styling WhatsApp link
6. ✅ Testing dan finalisasi
