<?php

session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/db.php";

$id = (int) ($_GET["id"] ?? 0);

if ($id <= 0) {
    header("Location: pesanan.php");
    exit;
}

$sql = "
    DELETE FROM pesanan
    WHERE id = ?
";

$stmt = $conn->prepare($sql);

if ($stmt) {

    $stmt->bind_param(
        "i",
        $id
    );

    $stmt->execute();

    $stmt->close();
}

$conn->close();

header("Location: pesanan.php");

exit;

?>