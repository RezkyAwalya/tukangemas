<?php

require_once "../config/db.php";

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

    die(
        "Gagal mengambil data harga emas.<br>" .
        "HTTP Code: " . $httpCode . "<br>" .
        "Error: " . htmlspecialchars($curlError)
    );
}

if ($httpCode !== 200) {

    die(
        "API harga emas mengembalikan HTTP Code: " .
        $httpCode
    );
}

$data = json_decode($response, true);

if ($data === null) {
    die("Data API tidak dapat dibaca sebagai JSON.");
}

if (!isset($data["price_gram_24k"])) {
    die("Harga emas 24K tidak ditemukan pada API.");
}

$harga24k = (float) $data["price_gram_24k"];

if ($harga24k <= 0) {
    die("Harga emas 24K tidak valid.");
}

$harga88 = round($harga24k * 0.88, 2);
$harga90 = round($harga24k * 0.90, 2);

$tanggal = date("Y-m-d");
$sumber = "GoldPrice.dev";

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
    die("Query database gagal: " . htmlspecialchars($conn->error));
}

$stmt->bind_param(
    "sdds",
    $tanggal,
    $harga88,
    $harga90,
    $sumber
);

if (!$stmt->execute()) {
    die(
        "Gagal menyimpan harga ke database: " .
        htmlspecialchars($stmt->error)
    );
}

echo "Harga emas berhasil diperbarui.<br><br>";

echo "Harga 24K: Rp " .
    number_format($harga24k, 2, ",", ".") .
    " / gram<br>";

echo "Harga 88%: Rp " .
    number_format($harga88, 2, ",", ".") .
    " / gram<br>";

echo "Harga 90%: Rp " .
    number_format($harga90, 2, ",", ".") .
    " / gram<br>";

echo "Sumber: " . htmlspecialchars($sumber) . "<br>";
echo "Tanggal: " . htmlspecialchars($tanggal);

$stmt->close();
$conn->close();

?>