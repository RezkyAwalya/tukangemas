<?php

session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/db.php";

$id = (int)($_GET["id"] ?? 0);

if ($id <= 0) {
    die("ID tidak valid.");
}


$stmt = $conn->prepare(
    "SELECT gambar FROM koleksi WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Koleksi tidak ditemukan.");
}

$data = $result->fetch_assoc();


$folder = "../uploads/koleksi/";

if (
    !empty($data["gambar"]) &&
    file_exists($folder . $data["gambar"])
) {
    unlink($folder . $data["gambar"]);
}


$stmt = $conn->prepare(
    "DELETE FROM koleksi WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();


header("Location: koleksi.php");
exit;

?>