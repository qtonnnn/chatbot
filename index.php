<?php
session_start();

/**
 * ==========================================
 * KONFIGURASI (DATABASE & API KEY)
 * ==========================================
 */
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";     // Ubah sesuai password XAMPP Anda (default kosong)
$DB_NAME = "chatbot";

// API KEY OPENROUTER (Wajib diisi agar Bot pintar)
$OPENROUTER_API_KEY = "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"; 

// Metadata untuk OpenRouter (Wajib)
$SITE_URL = "http://localhost/chat"; 
$SITE_TITLE = "Chat Toko Saya";     

/**
 * ==========================================
 * SETUP DATABASE & TABEL OTOMATIS
 * ==========================================
 */
$mysqli = @new mysqli($DB_HOST, $DB_USER, $DB_PASS);
if ($mysqli->connect_errno) {
    die("Gagal koneksi ke MySQL: " . $mysqli->connect_error);
}

// Buat DB jika belum ada
$mysqli->query("CREATE DATABASE IF NOT EXISTS `$DB_NAME` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
$mysqli->select_db($DB_NAME);

// Tabel Sesi Chat
$mysqli->query("CREATE TABLE IF NOT EXISTS chat_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Tabel Pesan
$mysqli->query("CREATE TABLE IF NOT EXISTS chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    sender ENUM('user','bot') NOT NULL,
    message LONGTEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES chat_sessions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Tabel Produk (Untuk RAG)
$mysqli->query("CREATE TABLE IF NOT EXISTS produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(255) NOT NULL,
    kategori VARCHAR(100) NOT NULL,
    harga DECIMAL(15,2) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Data Dummy Produk (Hanya insert jika kosong)
$cekIsi = $mysqli->query("SELECT COUNT(*) as jml FROM produk")->fetch_assoc();
if ($cekIsi['jml'] == 0) {
    $mysqli->query("INSERT INTO produk (nama_produk, kategori, harga, stok, deskripsi) VALUES
        ('CCTV Hikvision 2MP', 'CCTV', 350000, 15, 'Kamera indoor 2MP tajam'),
        ('NVR Hikvision 8CH', 'NVR', 2100000, 4, 'NVR 8 Channel support 4K'),
        ('Laptop Asus VivoBook', 'Laptop', 10500000, 3, 'Laptop kerja i7 RAM 16GB'),
        ('Mouse Logitech Wireless', 'Aksesoris', 150000, 20, 'Mouse tanpa kabel awet'),
        ('Harddisk Eksternal 1TB', 'Storage', 750000, 10, 'HDD Seagate Backup Plus')
    ");
}

/**
 * ==========================================
 * LOGIKA BACKEND (PHP FUNCTIONS)
 * ==========================================
 */

function json_out($data) {
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function get_current_session_id($mysqli) {
    // Jika belum ada session di PHP, buat baru di DB
    if (!isset($_SESSION['chat_session_id'])) {
        $defaultName = 'Percakapan ' . date('d M H:i');
        $stmt = $mysqli->prepare('INSERT INTO chat_sessions(session_name) VALUES (?)');
        $stmt->bind_param('s', $defaultName);
        $stmt->execute();
        $_SESSION['chat_session_id'] = $stmt->insert_id;
    }
    return (int)$_SESSION['chat_session_id'];
}

// Fitur RAG: Cari produk di database SQL untuk konteks AI
function cari_konteks_produk($keyword, $mysqli) {
    $keywords = explode(" ", preg_replace("/[^a-zA-Z0-9 ]+/", "", $keyword));
    $found_products = [];
    
    foreach($keywords as $k) {
        if(strlen($k) < 3) continue;
        $stmt = $mysqli->prepare("SELECT nama_produk, harga, stok FROM produk WHERE nama_produk LIKE ? OR kategori LIKE ? LIMIT 3");
        $like = "%$k%";
        $stmt->bind_param("ss", $like, $like);
        $stmt->execute();
        $res = $stmt->get_result();
        while($row = $res->fetch_assoc()) {
            $found_products[] = "- {$row['nama_produk']} (Rp " . number_format($row['harga'],0,',','.') . ", Stok: {$row['stok']})";
        }
    }
    
    if(empty($found_products)) return "";
    return "DATA PRODUK DARI DATABASE:\n" . implode("\n", array_unique($found_products)) . "\n\n";
}


// Generate contextual name based on conversation
function generate_contextual_name($session_id, $mysqli) {
    // Ambil 5 pesan terakhir untuk analisis konteks
    $stmt = $mysqli->prepare('SELECT sender, message FROM chat_messages WHERE session_id=? ORDER BY id DESC LIMIT 5');
    $stmt->bind_param('i', $session_id);
    $stmt->execute();
    $res = $stmt->get_result();
    
    $messages = [];
    while ($row = $res->fetch_assoc()) {
        $messages[] = $row['message'];
    }
    
    if (empty($messages)) return null;
    
    // Gabungkan semua pesan untuk analisis
    $conversation_text = implode(' ', array_reverse($messages));
    
    // Cari produk yang disebutkan dalam percakapan
    $products_found = [];
    $stmt = $mysqli->query("SELECT nama_produk FROM produk");
    while ($row = $stmt->fetch_assoc()) {
        $product_name = $row['nama_produk'];
        // Cek apakah produk disebutkan dalam percakapan (case insensitive)
        if (stripos($conversation_text, $product_name) !== false) {
            $products_found[] = $product_name;
        }
    }
    
    // Jika ada produk yang ditemukan, gunakan nama produk
    if (!empty($products_found)) {
        $product = $products_found[0]; // Ambil produk pertama yang disebutkan
        return "Info " . $product;
    }
    
    // Analisis kata kunci untuk menentukan topik
    $keywords = [
        'harga' => 'Tanya Harga',
        'stok' => 'Cek Stok', 
        'beli' => 'Pembelian',
        'order' => 'Order',
        'bayar' => 'Pembayaran',
        'ongkir' => 'Ongkir',
        'cctv' => 'Info CCTV',
        'laptop' => 'Info Laptop',
        'mouse' => 'Aksesoris Mouse',
        'harddisk' => 'Storage',
        'nvr' => 'Info NVR',
        'kamera' => 'Info Kamera',
        'asus' => 'Info Asus',
        'hikvision' => 'Info Hikvision',
        'logitech' => 'Info Logitech',
        'seagate' => 'Info Seagate'
    ];
    
    foreach ($keywords as $keyword => $label) {
        if (stripos($conversation_text, $keyword) !== false) {
            return $label;
        }
    }
    
    // Jika tidak ada match spesifik, buat nama berdasarkan pola percakapan
    if (preg_match('/apa|bagaimana|dimana|kapan|siapa|mengapa|kenapa/i', $conversation_text)) {
        return "Pertanyaan Umum";
    }
    
    // Default fallback
    return "Chat Baru";
}

// Kirim ke OpenRouter
function kirim_ke_openrouter($history, $apiKey) {
    global $SITE_URL, $SITE_TITLE;
    
    $url = "https://openrouter.ai/api/v1/chat/completions";
    $data = [
        "model" => "openai/gpt-3.5-turbo", // Ganti model lain jika mau (misal: google/gemini-2.0-flash-001)
        "messages" => $history
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_SSL_VERIFYPEER => false, // Bypass SSL untuk Localhost
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "Authorization: Bearer " . $apiKey,
            "HTTP-Referer: " . $SITE_URL,
            "X-Title: " . $SITE_TITLE
        ],
        CURLOPT_POSTFIELDS => json_encode($data),
    ]);

    $res = curl_exec($ch);
    if ($res === false) return "Error Koneksi: " . curl_error($ch);
    curl_close($ch);

    $j = json_decode($res, true);
    if (isset($j['error'])) return "API Error: " . ($j['error']['message'] ?? 'Unknown');
    
    return $j['choices'][0]['message']['content'] ?? "Maaf, tidak ada respon.";
}

/**
 * ==========================================
 * ROUTER AJAX REQUEST
 * ==========================================
 */
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        
        // 1. Kirim Pesan
        case 'send_message':
            $id = (int)($_POST['id'] ?? 0);
            if (!$id) $id = get_current_session_id($mysqli);
            $_SESSION['chat_session_id'] = $id;

            $msg = trim($_POST['message'] ?? '');
            if ($msg === '') json_out(['ok'=>false, 'error'=>'Pesan kosong']);

            // Simpan User Msg
            $stmt = $mysqli->prepare('INSERT INTO chat_messages(session_id, sender, message) VALUES (?,\'user\',?)');
            $stmt->bind_param('is', $id, $msg);
            $stmt->execute();

            $reply = "";

            if (!empty($OPENROUTER_API_KEY) && strpos($OPENROUTER_API_KEY, 'sk-') === 0) {
                // RAG Logic
                $context = cari_konteks_produk($msg, $mysqli);
                $system_prompt = "Kamu adalah CS Toko Komputer yang ramah. Jawab dalam Bahasa Indonesia.\n" .
                                 "Jika user tanya harga/stok, gunakan data ini:\n" . $context . 
                                 "\nJika tidak ada di data, jawab bahwa stok habis atau tidak tersedia.";

                // Build History (System + Last 6 messages)
                $history = [["role" => "system", "content" => $system_prompt]];
                
                $stmtH = $mysqli->prepare('SELECT sender, message FROM chat_messages WHERE session_id=? ORDER BY id DESC LIMIT 6');
                $stmtH->bind_param('i', $id);
                $stmtH->execute();
                $resH = $stmtH->get_result();
                $temp_hist = [];
                while ($r = $resH->fetch_assoc()) {
                    $role = ($r['sender'] == 'user') ? 'user' : 'assistant';
                    $temp_hist[] = ["role" => $role, "content" => $r['message']];
                }
                $history = array_merge($history, array_reverse($temp_hist));

                $reply = kirim_ke_openrouter($history, $OPENROUTER_API_KEY);
            } else {
                $reply = "API Key belum disetting atau salah format. (Mode Offline)";
            }


            // Simpan Bot Msg
            $stmt = $mysqli->prepare('INSERT INTO chat_messages(session_id, sender, message) VALUES (?,\'bot\',?)');
            $stmt->bind_param('is', $id, $reply);
            $stmt->execute();

            // Auto-update session name based on conversation context
            $new_name = generate_contextual_name($id, $mysqli);
            if ($new_name) {
                // Cek nama session saat ini
                $stmt = $mysqli->prepare('SELECT session_name FROM chat_sessions WHERE id=?');
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $current_name = $result->fetch_assoc()['session_name'];
                
                // Update nama jika masih default dan ada nama baru yang lebih baik
                if (preg_match('/^Percakapan \d{2} [A-Za-z]{3} \d{2}:\d{2}$/', $current_name) && $new_name !== 'Chat Baru') {
                    $stmt = $mysqli->prepare('UPDATE chat_sessions SET session_name=? WHERE id=?');
                    $stmt->bind_param('si', $new_name, $id);
                    $stmt->execute();
                    
                    json_out(['ok'=>true, 'reply'=>$reply, 'new_name'=>$new_name]);
                }
            }

            json_out(['ok'=>true, 'reply'=>$reply]);
            break;

        // 2. Load Chat
        case 'load_messages':
            $id = (int)($_GET['id'] ?? 0);
            if($id){
                 $_SESSION['chat_session_id'] = $id;
            }
            $messages = [];
            $stmt = $mysqli->prepare('SELECT sender, message FROM chat_messages WHERE session_id=? ORDER BY id ASC');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $res = $stmt->get_result();
            while ($row = $res->fetch_assoc()) $messages[] = $row;
            json_out(['session_id' => $id, 'messages' => $messages]);
            break;

        // 3. List Sessions
        case 'list_sessions':
            $sessions = [];
            $res = $mysqli->query('SELECT id, session_name FROM chat_sessions ORDER BY created_at DESC LIMIT 20');
            while ($row = $res->fetch_assoc()) $sessions[] = $row;
            json_out(['sessions' => $sessions, 'current' => $_SESSION['chat_session_id'] ?? null]);
            break;

        // 4. New Session
        case 'new_session':
            unset($_SESSION['chat_session_id']);
            $new_id = get_current_session_id($mysqli);
            json_out(['ok' => true, 'id' => $new_id]);
            break;
            
        // 5. Rename Session
        case 'rename_session':
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            if ($id && $name) {
                $stmt = $mysqli->prepare('UPDATE chat_sessions SET session_name=? WHERE id=?');
                $stmt->bind_param('si', $name, $id);
                $stmt->execute();
            }
            json_out(['ok' => true]);
            break;

        // 6. Delete Session
        case 'delete_session':
            $id = (int)($_POST['id'] ?? 0);
            if ($id) {
                $stmt = $mysqli->prepare('DELETE FROM chat_sessions WHERE id=?');
                $stmt->bind_param('i', $id);
                $stmt->execute();
                if(isset($_SESSION['chat_session_id']) && $_SESSION['chat_session_id'] == $id) {
                    unset($_SESSION['chat_session_id']);
                }
            }
            json_out(['ok' => true]);
            break;

        // 7. Delete All
        case 'delete_all_sessions':
            $mysqli->query('DELETE FROM chat_sessions');
            unset($_SESSION['chat_session_id']);
            json_out(['ok' => true]);
            break;
    }
    exit;
}
?>

<!doctype html>
<html lang="id">
<head>

  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Chatbot Toko (PHP Native)</title>
  <link rel="icon" type="image/png" href="logochat.png">

  <style>
    :root{--bg:#f6f7fb;--card:#fff;--text:#1f2937;--muted:#6b7280;--primary:#2563eb;--danger:#ef4444}
    *{box-sizing:border-box}

    html, body{margin:0;height:100%;background:var(--bg);font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;color:var(--text);overflow:auto;}
    
    .app{display:grid;grid-template-columns:280px 1fr;height:100vh}
    
    /* Sidebar */
    .sidebar{background:#fff;border-right:1px solid #e5e7eb;display:flex;flex-direction:column;height:100vh;overflow:hidden;}
    .side-top{padding:15px;border-bottom:1px solid #e5e7eb;display:flex;gap:8px;flex-shrink:0;}
    .btn-new{background:var(--primary);color:#fff;border:none;padding:10px;border-radius:8px;cursor:pointer;flex:1;font-weight:600;}
    .btn-del-all{background:var(--danger);color:#fff;border:none;padding:10px;border-radius:8px;cursor:pointer;}
    .side-list{overflow-y:auto;flex:1;min-height:0;}
    
    .chat-item{padding:12px 15px;border-bottom:1px solid #f3f4f6;cursor:pointer;display:flex;justify-content:space-between;align-items:center;transition:0.2s}
    .chat-item:hover{background:#f9fafb}
    .chat-item.active{background:#eef2ff;border-left:4px solid var(--primary)}
    .chat-name{font-size:14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:160px;}
    .chat-actions button{border:none;background:none;cursor:pointer;color:#9ca3af;font-size:14px;padding:2px 5px;}
    .chat-actions button:hover{color:var(--text)}

    /* Main Area */
    .main{display:flex;flex-direction:column;height:100vh;background:#fff;overflow:hidden;}
    .header{padding:15px 20px;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center;background:#fff;flex-shrink:0;}
    .chat-title{font-weight:700;font-size:16px;}
    
    .messages{flex:1;overflow-y:auto;padding:20px;display:flex;flex-direction:column;gap:15px;background:var(--bg);min-height:0;}
    
    .bubble{max-width:75%;padding:12px 16px;border-radius:12px;line-height:1.5;font-size:15px;position:relative;word-wrap:break-word;}
    .user{align-self:flex-end;background:var(--primary);color:#fff;border-bottom-right-radius:2px;}
    .bot{align-self:flex-start;background:#fff;border:1px solid #e5e7eb;color:var(--text);border-bottom-left-radius:2px;box-shadow:0 1px 2px rgba(0,0,0,0.05);}
    
    .composer{padding:20px;background:#fff;border-top:1px solid #e5e7eb;display:flex;gap:10px;flex-shrink:0;}
    .composer input{flex:1;padding:12px 15px;border:1px solid #d1d5db;border-radius:8px;font-size:15px;outline:none;transition:0.2s;}
    .composer input:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(37,99,235,0.1);}
    .composer button{background:var(--primary);color:#fff;border:none;padding:0 20px;border-radius:8px;font-weight:600;cursor:pointer;}
    .composer button:disabled, .composer input:disabled{opacity:0.6;cursor:not-allowed;}

    /* Responsive Design */
    @media (max-width: 768px) {
        .app{grid-template-columns:1fr;}
        .sidebar{display:none;}
        .main{height:100vh;}
        .composer{padding:15px;}
        .messages{padding:15px;}
    }


    /* Typing Animation */
    .typing-dots{display:inline-flex;gap:4px;padding:4px 0}
    .typing-dot{width:6px;height:6px;background:#9ca3af;border-radius:50%;animation:typing 1.4s infinite ease-in-out}
    .typing-dot:nth-child(1){animation-delay:-0.32s}
    .typing-dot:nth-child(2){animation-delay:-0.16s}
    @keyframes typing{0%,80%,100%{transform:scale(0)}40%{transform:scale(1)}}


    /* Custom Confirmation Modal */
    .modal-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);display:flex;align-items:center;justify-content:center;z-index:1000;opacity:0;visibility:hidden;transition:0.3s;}
    .modal-overlay.show{opacity:1;visibility:visible;}
    .modal{background:#fff;border-radius:12px;padding:24px;max-width:420px;width:90%;box-shadow:0 20px 25px -5px rgba(0,0,0,0.1),0 10px 10px -5px rgba(0,0,0,0.04);transform:scale(0.9);transition:0.3s;}
    .modal-overlay.show .modal{transform:scale(1);}
    .modal-header{display:flex;align-items:center;gap:12px;margin-bottom:16px;}
    .modal-icon{width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:24px;}
    .modal-icon.danger{background:#fef2f2;color:#ef4444;}
    .modal-title{font-size:18px;font-weight:700;color:var(--text);margin:0;}
    .modal-message{color:#6b7280;line-height:1.6;margin-bottom:24px;}
    .modal-actions{display:flex;gap:12px;justify-content:flex-end;}
    .btn{padding:10px 20px;border-radius:8px;border:none;font-weight:600;cursor:pointer;transition:0.2s;font-size:14px;}
    .btn-secondary{background:#f3f4f6;color:#374151;}
    .btn-secondary:hover{background:#e5e7eb;}
    .btn-danger{background:var(--danger);color:#fff;}
    .btn-danger:hover{background:#dc2626;}
    .modal-warning{display:flex;align-items:center;gap:8px;padding:12px;background:#fffbeb;border:1px solid #fed7aa;border-radius:8px;margin-bottom:16px;color:#92400e;font-size:14px;}

    /* Chat Name Animation */
    .chat-name-updating {
        animation: nameUpdatePulse 0.6s ease-in-out;
    }
    
    @keyframes nameUpdatePulse {
        0% { 
            transform: scale(1);
            color: var(--text);
            background: transparent;
        }
        25% { 
            transform: scale(1.05);
            color: var(--primary);
            background: rgba(37, 99, 235, 0.1);
            border-radius: 4px;
        }
        50% { 
            transform: scale(1.08);
            color: var(--primary);
            background: rgba(37, 99, 235, 0.15);
        }
        75% { 
            transform: scale(1.05);
            color: var(--primary);
            background: rgba(37, 99, 235, 0.1);
        }
        100% { 
            transform: scale(1);
            color: var(--text);
            background: transparent;
        }
    }

    .chat-title-updating {
        animation: titleUpdateSlide 0.8s ease-out;
    }
    
    @keyframes titleUpdateSlide {
        0% { 
            transform: translateX(-10px);
            opacity: 0.7;
            color: var(--muted);
        }
        30% { 
            transform: translateX(0);
            opacity: 1;
            color: var(--primary);
        }
        100% { 
            transform: translateX(0);
            opacity: 1;
            color: var(--text);
        }
    }

    .sidebar-updating {
        animation: sidebarRefresh 0.5s ease-in-out;
    }
    
    @keyframes sidebarRefresh {
        0% { opacity: 0.8; }
        50% { opacity: 0.6; }
        100% { opacity: 1; }
    }

    /* Sparkle effect for name change */
    .sparkle-effect {
        position: relative;
        overflow: hidden;
    }
    
    .sparkle-effect::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.8), transparent);
        animation: sparkle 0.8s ease-out;
    }
    
    @keyframes sparkle {
        0% { left: -100%; }
        100% { left: 100%; }
    }
  </style>
</head>
<body>
  <div class="app">
    <aside class="sidebar">
      <div class="side-top">
        <button id="newChat" class="btn-new">+ Chat Baru</button>
        <button id="deleteAllChats" class="btn-del-all" title="Hapus Semua">🗑</button>
      </div>
      <div id="sessionList" class="side-list">
        </div>
    </aside>

    <main class="main">
      <div class="header">
        <div class="chat-title" id="chatTitle">Percakapan Baru</div>
        <div style="font-size:12px;color:#6b7280">AI Assistant (OpenRouter)</div>
      </div>
      
      <div id="messages" class="messages">
        </div>
      

      <div class="composer">
        <input id="input" type="text" placeholder="Tanyakan stok, harga, atau apapun..." autocomplete="off" />
        <button id="sendBtn">Kirim</button>
      </div>
    </main>
  </div>


  <!-- Custom Confirmation Modal - Delete All -->
  <div id="confirmModal" class="modal-overlay">
    <div class="modal">
      <div class="modal-header">
        <div class="modal-icon danger">⚠️</div>
        <h3 class="modal-title">Konfirmasi Penghapusan</h3>
      </div>
      <div class="modal-warning">
        <span>⚠️</span>
        <span>Tindakan ini tidak dapat dibatalkan!</span>
      </div>
      <div class="modal-message">
        <p><strong>Yakin hapus SEMUA riwayat chat?</strong></p>
        <p>Semua percakapan dan pesan akan dihapus permanen dari database. Anda tidak akan bisa mengembalikan data ini.</p>
      </div>
      <div class="modal-actions">
        <button id="cancelBtn" class="btn btn-secondary">Batal</button>
        <button id="confirmBtn" class="btn btn-danger">Ya, Hapus Semua</button>
      </div>
    </div>
  </div>


  <!-- Custom Confirmation Modal - Delete Single Chat -->
  <div id="confirmSingleModal" class="modal-overlay">
    <div class="modal">
      <div class="modal-header">
        <div class="modal-icon danger">🗑️</div>
        <h3 class="modal-title">Hapus Percakapan</h3>
      </div>
      <div class="modal-warning">
        <span>⚠️</span>
        <span>Percakapan ini akan dihapus permanen!</span>
      </div>
      <div class="modal-message">
        <p><strong>Hapus chat ini?</strong></p>
        <p>Percakapan dan semua pesan di dalamnya akan dihapus permanen dari database. Tindakan ini tidak dapat dibatalkan.</p>
      </div>
      <div class="modal-actions">
        <button id="cancelSingleBtn" class="btn btn-secondary">Batal</button>
        <button id="confirmSingleBtn" class="btn btn-danger">Ya, Hapus Chat</button>
      </div>
    </div>
  </div>

  <!-- Custom Rename Modal -->
  <div id="renameModal" class="modal-overlay">
    <div class="modal">
      <div class="modal-header">
        <div class="modal-icon" style="background:#eff6ff;color:#2563eb;">✎</div>
        <h3 class="modal-title">Ganti Nama Percakapan</h3>
      </div>
      <div class="modal-message">
        <label for="renameInput" style="display:block;margin-bottom:8px;font-weight:600;color:var(--text);">Nama baru untuk percakapan:</label>
        <input type="text" id="renameInput" style="width:100%;padding:12px 15px;border:1px solid #d1d5db;border-radius:8px;font-size:15px;outline:none;transition:0.2s;" placeholder="Masukkan nama baru..." />
      </div>
      <div class="modal-actions">
        <button id="cancelRenameBtn" class="btn btn-secondary">Batal</button>
        <button id="confirmRenameBtn" class="btn" style="background:var(--primary);color:#fff;">Simpan</button>
      </div>
    </div>
  </div>

  <script>
    const el = (sel)=>document.querySelector(sel);
    const sessionList = el('#sessionList');
    const messagesEl = el('#messages');
    const input = el('#input');
    const sendBtn = el('#sendBtn');
    const chatTitle = el('#chatTitle');
    let currentId = null;

    // --- RENDER FUNCTIONS ---
    function renderSessions(list, current){
      sessionList.innerHTML = '';
      if(list.length === 0) {
          sessionList.innerHTML = '<div style="padding:15px;color:#999;text-align:center;font-size:13px">Belum ada riwayat</div>';
          return;
      }
      list.forEach(item=>{
        const div = document.createElement('div');
        div.className = 'chat-item' + (item.id == current ? ' active' : '');
        div.innerHTML = `
          <div class="chat-name">${item.session_name}</div>
          <div class="chat-actions">
            <button onclick="renameSession(${item.id}, event)">✎</button>
            <button onclick="deleteSession(${item.id}, event)" style="color:var(--danger)">×</button>
          </div>`;
        div.onclick = (e)=> {
            if(e.target.tagName !== 'BUTTON') loadMessages(item.id);
        };
        sessionList.appendChild(div);
      });
    }

    function renderMessages(list){
      messagesEl.innerHTML = '';
      list.forEach(m=>{
        addBubble(m.message, m.sender);
      });
      scrollToBottom();
    }

    function addBubble(text, sender){
        const b = document.createElement('div');
        // Convert Newlines to <br> for display
        const cleanText = text.replace(/\n/g, '<br>');
        b.className = 'bubble ' + sender;
        b.innerHTML = cleanText;
        messagesEl.appendChild(b);
        scrollToBottom();
    }

    function scrollToBottom(){
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    // --- API CALLS ---
    function loadSessions(){
      fetch('?action=list_sessions')
        .then(r=>r.json())
        .then(data=>{
            const sessions = data.sessions;
            // Jika ada sesi tapi currentId null, ambil yang pertama
            if(!currentId && sessions.length > 0) currentId = data.current || sessions[0].id;
            // Jika currentId ada, load messagenya
            if(currentId) loadMessages(currentId, false);
            renderSessions(sessions, currentId);
        });
    }

    function loadMessages(id, refreshList = true){
      currentId = id;
      fetch(`?action=load_messages&id=${id}`)
        .then(r=>r.json())
        .then(data=>{
            renderMessages(data.messages);
            if(refreshList) loadSessions(); // Refresh sidebar active state
            
            // Update Title Header
            const activeItem = document.querySelector(`.chat-item.active .chat-name`);
            if(activeItem) chatTitle.textContent = activeItem.textContent;
        });
    }

    function sendMessage(){
        const text = input.value.trim();
        if(!text) return;

        // UI Optimistic Update
        addBubble(text, 'user');
        input.value = '';
        input.disabled = true;
        sendBtn.disabled = true;
        
        // Typing Indicator
        const typingDiv = document.createElement('div');
        typingDiv.className = 'bubble bot';
        typingDiv.innerHTML = '<div class="typing-dots"><div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div></div>';
        messagesEl.appendChild(typingDiv);
        scrollToBottom();

        const formData = new FormData();
        formData.append('id', currentId || 0);
        formData.append('message', text);

        fetch('?action=send_message', {
            method: 'POST',
            body: formData
        })
        .then(r=>r.json())

        .then(data=>{
            typingDiv.remove(); // Hapus indikator mengetik
            input.disabled = false;
            sendBtn.disabled = false;
            input.focus();
            

            if(data.ok){
                addBubble(data.reply, 'bot');
                
                // Jika ada nama baru dari server, update UI dan refresh sidebar
                if(data.new_name) {
                    // Animasi untuk chat title
                    chatTitle.classList.add('chat-title-updating');
                    setTimeout(() => {
                        chatTitle.textContent = data.new_name;
                        setTimeout(() => {
                            chatTitle.classList.remove('chat-title-updating');
                        }, 300);
                    }, 100);
                    
                    // Animasi untuk nama di sidebar dengan sparkle effect
                    setTimeout(() => {
                        animateSessionNameUpdate();
                        // Refresh sidebar untuk menampilkan nama yang diperbarui
                        loadSessions(); 
                    }, 400);
                }
                
                // Jika ini chat pertama di sesi baru, refresh sidebar
                if(!data.new_name) {
                    loadSessions(); 
                }
            } else {
                addBubble("Error: " + data.error, 'bot');
            }
        })
        .catch(e=>{
            typingDiv.remove();
            input.disabled = false;
            sendBtn.disabled = false;
            addBubble("Gagal menghubungi server.", 'bot');
        });
    }

    // --- ACTIONS ---
    el('#newChat').onclick = () => {
        fetch('?action=new_session').then(r=>r.json()).then(d=>{
            currentId = d.id;
            loadMessages(d.id);
        });
    };


    // Custom Modal Functions
    function showConfirmModal() {
        const modal = el('#confirmModal');
        modal.classList.add('show');
        el('#cancelBtn').focus();
    }

    function hideConfirmModal() {
        const modal = el('#confirmModal');
        modal.classList.remove('show');
    }

    function deleteAllChats() {
        fetch('?action=delete_all_sessions').then(()=> {
            currentId = null;
            messagesEl.innerHTML = '';
            loadSessions();
            hideConfirmModal();
        });
    }

    el('#deleteAllChats').onclick = showConfirmModal;

    // Modal Events
    el('#cancelBtn').onclick = hideConfirmModal;
    el('#confirmBtn').onclick = deleteAllChats;

    // Close modal on ESC key
    document.addEventListener('keydown', (e) => {
        if(e.key === 'Escape') {
            hideConfirmModal();
        }
    });

    // Close modal on overlay click
    el('#confirmModal').addEventListener('click', (e) => {
        if(e.target.id === 'confirmModal') {
            hideConfirmModal();
        }
    });


    // Custom Modal Functions for Rename
    function showRenameModal(sessionId, currentName) {
        const modal = el('#renameModal');
        modal.classList.add('show');
        modal.dataset.sessionId = sessionId;
        
        const input = el('#renameInput');
        input.value = currentName;
        input.focus();
        input.select();
    }

    function hideRenameModal() {
        const modal = el('#renameModal');
        modal.classList.remove('show');
        delete modal.dataset.sessionId;
        
        const input = el('#renameInput');
        input.value = '';
    }

    function saveRename() {
        const modal = el('#renameModal');
        const sessionId = modal.dataset.sessionId;
        const input = el('#renameInput');
        const newName = input.value.trim();
        
        if(newName){
            const fd = new FormData();
            fd.append('id', sessionId);
            fd.append('name', newName);
            fetch('?action=rename_session', {method:'POST', body:fd}).then(()=> {
                loadSessions();
                hideRenameModal();
            });
        }
    }


    // Store sessions data globally for access
    let sessionsData = [];
    
    window.renameSession = (id, e) => {
        e.stopPropagation();
        
        // Find current session name from stored data
        const session = sessionsData.find(s => s.id == id);
        const currentName = session ? session.session_name : 'Percakapan';
        
        showRenameModal(id, currentName);
    };

    // Update renderSessions to store sessions data
    function renderSessions(list, current){
      sessionList.innerHTML = '';
      sessionsData = list; // Store globally
      
      if(list.length === 0) {
          sessionList.innerHTML = '<div style="padding:15px;color:#999;text-align:center;font-size:13px">Belum ada riwayat</div>';
          return;
      }
      list.forEach(item=>{
        const div = document.createElement('div');
        div.className = 'chat-item' + (item.id == current ? ' active' : '');
        div.innerHTML = `
          <div class="chat-name">${item.session_name}</div>
          <div class="chat-actions">
            <button onclick="renameSession(${item.id}, event)">✎</button>
            <button onclick="deleteSession(${item.id}, event)" style="color:var(--danger)">×</button>
          </div>`;
        div.onclick = (e)=> {
            if(e.target.tagName !== 'BUTTON') loadMessages(item.id);
        };
        sessionList.appendChild(div);
      });
    }

    // Rename Modal Events
    el('#cancelRenameBtn').onclick = hideRenameModal;
    el('#confirmRenameBtn').onclick = saveRename;

    // Handle Enter key in rename input
    el('#renameInput').onkeydown = (e) => {
        if(e.key === 'Enter') {
            saveRename();
        } else if(e.key === 'Escape') {
            hideRenameModal();
        }
    };


    // Custom Modal Functions for Single Delete
    function showConfirmSingleModal(sessionId) {
        const modal = el('#confirmSingleModal');
        modal.classList.add('show');
        modal.dataset.sessionId = sessionId;
        el('#cancelSingleBtn').focus();
    }

    function hideConfirmSingleModal() {
        const modal = el('#confirmSingleModal');
        modal.classList.remove('show');
        delete modal.dataset.sessionId;
    }

    function deleteSingleChat() {
        const modal = el('#confirmSingleModal');
        const sessionId = modal.dataset.sessionId;
        
        const fd = new FormData();
        fd.append('id', sessionId);
        fetch('?action=delete_session', {method:'POST', body:fd}).then(()=>{
            if(currentId == sessionId) {
                messagesEl.innerHTML = '';
                currentId = null;
            }
            loadSessions();
            hideConfirmSingleModal();
        });
    }

    window.deleteSession = (id, e) => {
        e.stopPropagation();
        showConfirmSingleModal(id);
    };

    // Single Modal Events
    el('#cancelSingleBtn').onclick = hideConfirmSingleModal;
    el('#confirmSingleBtn').onclick = deleteSingleChat;


    // Close single modal on ESC key
    document.addEventListener('keydown', (e) => {
        if(e.key === 'Escape') {
            const allModal = el('#confirmModal');
            const singleModal = el('#confirmSingleModal');
            
            if(allModal.classList.contains('show')) {
                hideConfirmModal();
            } else if(singleModal.classList.contains('show')) {
                hideConfirmSingleModal();
            }
        }
    });

    // Close single modal on overlay click
    el('#confirmSingleModal').addEventListener('click', (e) => {
        if(e.target.id === 'confirmSingleModal') {
            hideConfirmSingleModal();
        }
    });

    // --- EVENTS ---
    sendBtn.onclick = sendMessage;
    input.onkeydown = (e) => {
        if(e.key === 'Enter') sendMessage();
    };


    // Init
    loadSessions();

    // Function to animate session name updates in sidebar
    function animateSessionNameUpdate() {
        // Add sparkle effect to current active chat item
        const activeItem = document.querySelector('.chat-item.active .chat-name');
        if (activeItem) {
            // Add sparkle effect
            activeItem.classList.add('sparkle-effect');
            activeItem.classList.add('chat-name-updating');
            
            // Add sidebar refresh effect
            sessionList.classList.add('sidebar-updating');
            
            // Remove effects after animation completes
            setTimeout(() => {
                activeItem.classList.remove('sparkle-effect');
                activeItem.classList.remove('chat-name-updating');
                sessionList.classList.remove('sidebar-updating');
            }, 1000);
        }
    }

  </script>
</body>
</html>
