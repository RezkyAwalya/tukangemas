<?php

session_start();

if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit;
}

require_once "../config/db.php";

$sql = "SELECT * FROM galeri ORDER BY id DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Kelola Galeri - TUKANG EMAS REZKY</title>

    <link rel="stylesheet" href="../style.css">

</head>

<body>

<nav class="navbar">

    <div class="container">

        <a href="index.php" class="navbar-brand">
            TUKANG EMAS REZKY
        </a>

    </div>

</nav>


<section class="section">

    <div class="container">

        <div class="section-heading">

            <h1>Kelola Galeri</h1>

            <p>
                Kelola foto yang ditampilkan pada halaman galeri.
            </p>

        </div>


        <div class="form-buttons">

            <a
                href="index.php"
                class="btn-outline-gold"
            >
                Kembali
            </a>

            <a
                href="galeri-tambah.php"
                class="btn-gold"
            >
                + Tambah Galeri
            </a>

        </div>

        <br>


        <?php if ($result->num_rows > 0): ?>

            <div class="collection-tools">

                <?php while ($row = $result->fetch_assoc()): ?>

                    <div class="product-card">

                        <div class="product-image">

                            <img
                                src="../uploads/galeri/<?php echo htmlspecialchars($row["gambar"]); ?>"
                                alt="<?php echo htmlspecialchars($row["judul"]); ?>"
                            >

                        </div>


                        <div class="product-content">

                            <h3>
                                <?php echo htmlspecialchars($row["judul"]); ?>
                            </h3>

                            <p>
                                <?php echo htmlspecialchars($row["deskripsi"]); ?>
                            </p>


                            <div class="form-buttons">

                                <a
                                    href="galeri-edit.php?id=<?php echo $row["id"]; ?>"
                                    class="btn-outline-gold"
                                >
                                    Edit
                                </a>

                                <a
                                    href="galeri-hapus.php?id=<?php echo $row["id"]; ?>"
                                    class="btn-outline-gold"
                                    onclick="return confirm('Yakin ingin menghapus foto ini?')"
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
                Belum ada data galeri.
            </p>

        <?php endif; ?>

    </div>

</section>

</body>

</html>