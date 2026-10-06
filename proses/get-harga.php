<?php

require_once "../config/db.php";

header("Content-Type: application/json; charset=UTF-8");

$tanggal = date("Y-m-d");

/*
|--------------------------------------------------------------------------
| 1. Cek harga hari ini di database
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT harga_88, harga_90, tanggal, sumber
    FROM harga_emas
    WHERE tanggal = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Query database gagal."
    ]);
    exit;
}

$stmt->bind_param("s", $tanggal);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $data = $result->fetch_assoc();

    echo json_encode([
        "status" => "success",
        "source" => "database",
        "tanggal" => $data["tanggal"],
        "harga_88" => (float) $data["harga_88"],
        "harga_90" => (float) $data["harga_90"],
        "sumber" => $data["sumber"]
    ]);

    exit;
}

$stmt->close();

/*
|--------------------------------------------------------------------------
| 2. Jika belum ada harga hari ini, ambil dari GoldPrice.dev
|--------------------------------------------------------------------------
*/

$apiUrl = "https://api.goldprice.dev/v1/carat?currency=IDR";

$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 20,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_HTTPHEADER => [
        "Accept: application/json"
    ],
    CURLOPT_USERAGENT => "Mozilla/5.0"
]);

$response = curl_exec($ch);

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);

curl_close($ch);

if ($response === false || empty($response)) {

    echo json_encode([
        "status" => "error",
        "message" => "Gagal mengambil harga emas.",
        "http_code" => $httpCode,
        "error" => $curlError
    ]);

    exit;
}

if ($httpCode !== 200) {

    echo json_encode([
        "status" => "error",
        "message" => "API harga emas mengembalikan HTTP Code " . $httpCode
    ]);

    exit;
}

$data = json_decode($response, true);

if ($data === null) {

    echo json_encode([
        "status" => "error",
        "message" => "Data API tidak dapat dibaca."
    ]);

    exit;
}

if (!isset($data["price_gram_24k"])) {

    echo json_encode([
        "status" => "error",
        "message" => "Harga emas 24K tidak ditemukan."
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| 3. Hitung harga 88% dan 90%
|--------------------------------------------------------------------------
*/

$harga24k = (float) $data["price_gram_24k"];

if ($harga24k <= 0) {

    echo json_encode([
        "status" => "error",
        "message" => "Harga emas 24K tidak valid."
    ]);

    exit;
}

$harga88 = round($harga24k * 0.88, 2);
$harga90 = round($harga24k * 0.90, 2);

$sumber = "GoldPrice.dev";

/*
|--------------------------------------------------------------------------
| 4. Simpan ke database
|--------------------------------------------------------------------------
*/

$sql = "
    INSERT INTO harga_emas
    (tanggal, harga_88, harga_90, sumber)
    VALUES (?, ?, ?, ?)

    ON DUPLICATE KEY UPDATE
        harga_88 = VALUES(harga_88),
        harga_90 = VALUES(harga_90),
        sumber = VALUES(sumber),
        waktu_update = CURRENT_TIMESTAMP
";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    echo json_encode([
        "status" => "error",
        "message" => "Query penyimpanan harga gagal."
    ]);

    exit;
}

$stmt->bind_param(
    "sdds",
    $tanggal,
    $harga88,
    $harga90,
    $sumber
);

if (!$stmt->execute()) {

    echo json_encode([
        "status" => "error",
        "message" => "Gagal menyimpan harga emas."
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| 5. Kirim hasil
|--------------------------------------------------------------------------
*/

echo json_encode([
    "status" => "success",
    "source" => "api",
    "tanggal" => $tanggal,
    "harga_88" => $harga88,
    "harga_90" => $harga90,
    "sumber" => $sumber
]);

$stmt->close();
$conn->close();
?>