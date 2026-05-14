<?php
/**
 * KasirPRO - Backend API (PHP + MySQL)
 * Simpan file ini di server PHP (XAMPP, Laragon, dsb.)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// ============================================
// KONFIGURASI DATABASE
// ============================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'kasirpro');

function getDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        http_response_code(500);
        die(json_encode(['error' => 'Koneksi database gagal: ' . $conn->connect_error]));
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}

// ============================================
// ROUTER
// ============================================
$method = $_SERVER['REQUEST_METHOD'];
$path   = $_GET['action'] ?? '';

switch ($path) {
    case 'produk':       handleProduk($method); break;
    case 'kategori':     handleKategori($method); break;
    case 'transaksi':    handleTransaksi($method); break;
    case 'laporan':      handleLaporan($method); break;
    case 'pengaturan':   handlePengaturan($method); break;
    default:
        echo json_encode(['status' => 'KasirPRO API v1.0', 'waktu' => date('Y-m-d H:i:s')]);
}

// ============================================
// PRODUK
// ============================================
function handleProduk($method) {
    $db = getDB();
    if ($method === 'GET') {
        $kategori = intval($_GET['kategori'] ?? 0);
        $cari     = $db->real_escape_string($_GET['cari'] ?? '');
        $where    = "WHERE p.aktif = 1";
        if ($kategori) $where .= " AND p.kategori_id = $kategori";
        if ($cari)     $where .= " AND (p.nama LIKE '%$cari%' OR p.barcode = '$cari')";
        $q = $db->query("SELECT p.*, k.nama AS kategori FROM produk p LEFT JOIN kategori k ON p.kategori_id=k.id $where ORDER BY p.nama");
        $data = [];
        while ($r = $q->fetch_assoc()) $data[] = $r;
        echo json_encode(['success' => true, 'data' => $data]);
    } elseif ($method === 'POST') {
        $body = json_decode(file_get_contents('php://input'), true);
        $nama       = $db->real_escape_string($body['nama']);
        $harga      = floatval($body['harga']);
        $stok       = intval($body['stok']);
        $kategori   = intval($body['kategori_id']);
        $barcode    = $db->real_escape_string($body['barcode'] ?? '');
        $db->query("INSERT INTO produk (nama,harga,stok,kategori_id,barcode) VALUES ('$nama',$harga,$stok,$kategori,'$barcode')");
        echo json_encode(['success' => true, 'id' => $db->insert_id]);
    } elseif ($method === 'PUT') {
        $body = json_decode(file_get_contents('php://input'), true);
        $id    = intval($body['id']);
        $nama  = $db->real_escape_string($body['nama']);
        $harga = floatval($body['harga']);
        $stok  = intval($body['stok']);
        $db->query("UPDATE produk SET nama='$nama',harga=$harga,stok=$stok WHERE id=$id");
        echo json_encode(['success' => true]);
    } elseif ($method === 'DELETE') {
        $id = intval($_GET['id']);
        $db->query("UPDATE produk SET aktif=0 WHERE id=$id");
        echo json_encode(['success' => true]);
    }
    $db->close();
}

// ============================================
// KATEGORI
// ============================================
function handleKategori($method) {
    $db = getDB();
    $q = $db->query("SELECT * FROM kategori ORDER BY nama");
    $data = [];
    while ($r = $q->fetch_assoc()) $data[] = $r;
    echo json_encode(['success' => true, 'data' => $data]);
    $db->close();
}

// ============================================
// TRANSAKSI
// ============================================
function handleTransaksi($method) {
    $db = getDB();
    if ($method === 'GET') {
        $limit  = intval($_GET['limit'] ?? 50);
        $offset = intval($_GET['offset'] ?? 0);
        $q = $db->query("SELECT t.*, COUNT(d.id) as total_item FROM transaksi t LEFT JOIN detail_transaksi d ON t.id=d.transaksi_id GROUP BY t.id ORDER BY t.created_at DESC LIMIT $limit OFFSET $offset");
        $data = [];
        while ($r = $q->fetch_assoc()) $data[] = $r;
        echo json_encode(['success' => true, 'data' => $data]);
    } elseif ($method === 'POST') {
        $body    = json_decode(file_get_contents('php://input'), true);
        $items   = $body['items'];
        $total   = floatval($body['total']);
        $bayar   = floatval($body['bayar']);
        $kembalian = $bayar - $total;
        $metode  = $db->real_escape_string($body['metode_bayar'] ?? 'tunai');
        $noTrx   = 'TRX-' . date('Ymd') . '-' . str_pad(rand(1,9999), 4, '0', STR_PAD_LEFT);
        $subtotal = floatval($body['subtotal']);
        $diskon  = floatval($body['diskon'] ?? 0);
        $pajak   = floatval($body['pajak'] ?? 0);

        $db->begin_transaction();
        try {
            $db->query("INSERT INTO transaksi (nomor_transaksi,subtotal,diskon,pajak,total,bayar,kembalian,metode_bayar) VALUES ('$noTrx',$subtotal,$diskon,$pajak,$total,$bayar,$kembalian,'$metode')");
            $trxId = $db->insert_id;
            foreach ($items as $item) {
                $pid     = intval($item['id']);
                $nama    = $db->real_escape_string($item['nama']);
                $harga   = floatval($item['harga']);
                $qty     = intval($item['qty']);
                $sub     = $harga * $qty;
                $db->query("INSERT INTO detail_transaksi (transaksi_id,produk_id,nama_produk,harga,qty,subtotal) VALUES ($trxId,$pid,'$nama',$harga,$qty,$sub)");
                $db->query("UPDATE produk SET stok = stok - $qty WHERE id = $pid");
            }
            $db->commit();
            echo json_encode(['success' => true, 'nomor_transaksi' => $noTrx, 'kembalian' => $kembalian]);
        } catch (Exception $e) {
            $db->rollback();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
    $db->close();
}

// ============================================
// LAPORAN
// ============================================
function handleLaporan($method) {
    $db   = getDB();
    $tipe = $_GET['tipe'] ?? 'hari';
    $tgl  = $db->real_escape_string($_GET['tanggal'] ?? date('Y-m-d'));

    $where = match($tipe) {
        'bulan' => "WHERE DATE_FORMAT(created_at,'%Y-%m') = DATE_FORMAT('$tgl','%Y-%m')",
        'tahun' => "WHERE YEAR(created_at) = YEAR('$tgl')",
        default => "WHERE DATE(created_at) = '$tgl'"
    };

    $r = $db->query("SELECT COUNT(*) as total_transaksi, SUM(total) as total_omzet, SUM(kembalian) as total_kembalian FROM transaksi $where AND status='selesai'")->fetch_assoc();
    $produkTerlaris = [];
    $q = $db->query("SELECT d.nama_produk, SUM(d.qty) as total_qty, SUM(d.subtotal) as total_nilai FROM detail_transaksi d JOIN transaksi t ON d.transaksi_id=t.id $where AND t.status='selesai' GROUP BY d.produk_id ORDER BY total_qty DESC LIMIT 5");
    while ($row = $q->fetch_assoc()) $produkTerlaris[] = $row;

    echo json_encode(['success' => true, 'ringkasan' => $r, 'produk_terlaris' => $produkTerlaris]);
    $db->close();
}

// ============================================
// PENGATURAN
// ============================================
function handlePengaturan($method) {
    $db = getDB();
    if ($method === 'GET') {
        $q = $db->query("SELECT kunci, nilai FROM pengaturan");
        $data = [];
        while ($r = $q->fetch_assoc()) $data[$r['kunci']] = $r['nilai'];
        echo json_encode(['success' => true, 'data' => $data]);
    } elseif ($method === 'POST') {
        $body = json_decode(file_get_contents('php://input'), true);
        foreach ($body as $kunci => $nilai) {
            $k = $db->real_escape_string($kunci);
            $v = $db->real_escape_string($nilai);
            $db->query("INSERT INTO pengaturan (kunci,nilai) VALUES ('$k','$v') ON DUPLICATE KEY UPDATE nilai='$v'");
        }
        echo json_encode(['success' => true]);
    }
    $db->close();
}
