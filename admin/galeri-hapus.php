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
    "SELECT gambar FROM galeri WHERE id = ?"
);

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows === 0) {
    die("Data galeri tidak ditemukan.");
}


$data = $result->fetch_assoc();


$folder = "../uploads/galeri/";


if (
    !empty($data["gambar"]) &&
    file_exists($folder . $data["gambar"])
) {
    unlink($folder . $data["gambar"]);
}


$stmt = $conn->prepare(
    "DELETE FROM galeri WHERE id = ?"
);

$stmt->bind_param("i", $id);

$stmt->execute();


header("Location: galeri.php");

exit;

?>