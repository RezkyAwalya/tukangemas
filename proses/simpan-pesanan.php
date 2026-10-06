<?php

require_once "../config/db.php";

header("Content-Type: application/json; charset=UTF-8");

$input = json_decode(
    file_get_contents("php://input"),
    true
);

if (!$input) {

    echo json_encode([
        "status" => "error",
        "message" => "Data pesanan tidak diterima."
    ]);

    exit;
}

$nama = trim($input["nama"] ?? "");
$jenisPerhiasan = trim($input["jenis_perhiasan"] ?? "");
$ukuran = trim($input["ukuran"] ?? "");
$kadar = trim($input["kadar"] ?? "");

$berat = (float) ($input["berat"] ?? 0);
$hargaEmas = (float) ($input["harga_emas"] ?? 0);

$desain = trim($input["desain"] ?? "");
$ukiran = trim($input["ukiran"] ?? "");
$catatan = trim($input["catatan"] ?? "");

$estimasi = (float) ($input["estimasi"] ?? 0);


/* ================================
   VALIDASI DATA
================================ */

if (
    $nama === "" ||
    $jenisPerhiasan === "" ||
    $ukuran === "" ||
    $kadar === "" ||
    $berat <= 0 ||
    $hargaEmas <= 0 ||
    $estimasi <= 0
) {

    echo json_encode([
        "status" => "error",
        "message" => "Data pesanan belum lengkap.",
        "data" => $input
    ]);

    exit;
}


/* ================================
   QUERY INSERT
================================ */

$sql = "
    INSERT INTO pesanan
    (
        nama,
        jenis_perhiasan,
        ukuran,
        kadar,
        berat,
        harga_emas,
        desain,
        ukiran,
        catatan,
        estimasi
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
";


$stmt = $conn->prepare($sql);

if (!$stmt) {

    echo json_encode([
        "status" => "error",
        "message" => "Prepare query gagal.",
        "error" => $conn->error
    ]);

    exit;
}


/* ================================
   BIND DATA
================================ */

$stmt->bind_param(
    "ssssddsssd",
    $nama,
    $jenisPerhiasan,
    $ukuran,
    $kadar,
    $berat,
    $hargaEmas,
    $desain,
    $ukiran,
    $catatan,
    $estimasi
);


/* ================================
   EKSEKUSI
================================ */

if (!$stmt->execute()) {

    echo json_encode([
        "status" => "error",
        "message" => "Pesanan gagal disimpan.",
        "error" => $stmt->error
    ]);

    exit;
}


/* ================================
   BERHASIL
================================ */

echo json_encode([
    "status" => "success",
    "message" => "Pesanan berhasil disimpan.",
    "id" => $stmt->insert_id
]);


$stmt->close();
$conn->close();

?>