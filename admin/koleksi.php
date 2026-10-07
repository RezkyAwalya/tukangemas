<?php
session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/db.php";

$sql = "SELECT * FROM koleksi ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola Koleksi - TUKANG EMAS REZKY</title>

    <link rel="stylesheet" href="../style.css">
</head>

<body>

<nav class="navbar">
    <div class="container">

        <a href="index.php" class="navbar-brand">
            TUKANG EMAS REZKY
        </a>

        <div>
            <a href="index.php" class="nav-link">
                Dashboard
            </a>

            <a href="logout.php" class="nav-link">
                Logout
            </a>
        </div>

    </div>
</nav>

<section class="section">

    <div class="container">

        <div class="section-heading">
            <h1>Kelola Koleksi</h1>
            <p>Kelola koleksi perhiasan yang ditampilkan pada website.</p>
        </div>

        <div class="form-buttons">

            <a href="index.php" class="btn-outline-gold">
                Kembali
            </a>

            <a href="koleksi-tambah.php" class="btn-gold">
                + Tambah Koleksi
            </a>

        </div>

        <br>

        <?php if ($result->num_rows > 0): ?>

            <div class="collection-tools">

                <?php while ($row = $result->fetch_assoc()): ?>

                    <div class="product-card">

                        <div class="product-image">

                            <img
                                src="../uploads/koleksi/<?php echo htmlspecialchars($row["gambar"]); ?>"
                                alt="<?php echo htmlspecialchars($row["nama"]); ?>"
                            >

                        </div>

                        <div class="product-content">

                            <span class="product-category">
                                <?php echo htmlspecialchars($row["kategori"]); ?>
                            </span>

                            <h3>
                                <?php echo htmlspecialchars($row["nama"]); ?>
                            </h3>

                            <p>
                                <?php echo htmlspecialchars($row["deskripsi"]); ?>
                            </p>

                            <div class="form-buttons">

                                <a
                                    href="koleksi-edit.php?id=<?php echo $row["id"]; ?>"
                                    class="btn-outline-gold"
                                >
                                    Edit
                                </a>

                                <a
                                    href="koleksi-hapus.php?id=<?php echo $row["id"]; ?>"
                                    class="btn-outline-gold"
                                    onclick="return confirm('Yakin ingin menghapus koleksi ini?')"
                                >
                                    Hapus
                                </a>

                            </div>

                        </div>

                    </div>

                <?php endwhile; ?>

            </div>

        <?php else: ?>

            <p>
                Belum ada data koleksi.
            </p>

        <?php endif; ?>

    </div>

</section>

</body>
</html>