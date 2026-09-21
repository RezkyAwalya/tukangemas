/* =========================================
   TUKANG EMAS REZKY
   JavaScript + jQuery
========================================= */

$(document).ready(function () {


    /* =====================================
       FILTER PRODUK
    ===================================== */

    $(".filter-btn").click(function () {

        let filter = $(this).data("filter");

        $(".filter-btn").removeClass("active");
        $(this).addClass("active");

        $(".product-item").each(function () {

            let category = $(this).data("category");

            if (filter === "all" || category === filter) {

                $(this).fadeIn(300);

            } else {

                $(this).fadeOut(200);

            }

        });

    });


    /* =====================================
       SEARCH PRODUK
    ===================================== */

    $("#searchProduct").on("keyup", function () {

        let keyword = $(this).val().toLowerCase();

        $(".product-item").each(function () {

            let productName =
                $(this).data("name").toLowerCase();

            if (productName.includes(keyword)) {

                $(this).fadeIn(200);

            } else {

                $(this).fadeOut(200);

            }

        });

    });


    /* =====================================
       MODAL PRODUK
    ===================================== */

    $(".btn-detail").click(function () {

        let name = $(this).data("name");
        let category = $(this).data("category");
        let image = $(this).data("image");
        let description = $(this).data("description");

        $("#modalProductTitle").text(name);
        $("#modalProductCategory").text(category);
        $("#modalProductDescription").text(description);

        $("#modalProductImage")
            .attr("src", image)
            .attr("alt", name);

    });


    /* =====================================
       HITUNG ESTIMASI
    ===================================== */

    $("#calculateBtn").click(function () {

        calculateEstimate();

    });


    /* =====================================
       AUTO UPDATE ESTIMASI
    ===================================== */

    $("#weight, #goldPrice").on("input", function () {

        let weight = parseFloat(
            $("#weight").val()
        );

        let price = parseFloat(
            $("#goldPrice").val()
        );

        if (!isNaN(weight) && !isNaN(price)) {

            calculateEstimate();

        }

    });


    /* =====================================
       FORM CUSTOM
    ===================================== */

    $("#customForm").submit(function (event) {

        event.preventDefault();

        if (validateForm()) {

            let total = calculateEstimate();

            if (total > 0) {

                sendToWhatsApp();

            }

        }

    });


    /* =====================================
       ANIMASI NAVBAR MOBILE
    ===================================== */

    $(".nav-link").click(function () {

        $(".navbar-collapse").removeClass("show");

    });

});


/* =========================================
   HITUNG ESTIMASI HARGA
========================================= */

function calculateEstimate() {

    let weightElement =
        document.getElementById("weight");

    let priceElement =
        document.getElementById("goldPrice");

    let resultElement =
        document.getElementById("estimateResult");


    if (
        !weightElement ||
        !priceElement ||
        !resultElement
    ) {

        return 0;

    }


    let weight =
        parseFloat(weightElement.value);

    let goldPrice =
        parseFloat(priceElement.value);


    if (
        isNaN(weight) ||
        isNaN(goldPrice) ||
        weight <= 0 ||
        goldPrice <= 0
    ) {

        resultElement.innerText = "Rp 0";

        return 0;

    }


    let total =
        weight * goldPrice;


    resultElement.innerText =
        formatRupiah(total);


    return total;

}


/* =========================================
   FORMAT RUPIAH
========================================= */

function formatRupiah(number) {

    return "Rp " +
        Math.round(number)
            .toLocaleString("id-ID");

}


/* =========================================
   VALIDASI FORM
========================================= */

function validateForm() {

    let name =
        document.getElementById("customerName").value.trim();

    let type =
        document.getElementById("jewelryType").value;

    let size =
        document.getElementById("size").value;

    let weight =
        parseFloat(
            document.getElementById("weight").value
        );

    let goldPrice =
        parseFloat(
            document.getElementById("goldPrice").value
        );

    let purity =
        document.querySelector(
            'input[name="purity"]:checked'
        );


    /* Nama */

    if (name === "") {

        document.getElementById("nameError").innerText =
            "Nama wajib diisi.";

        document.getElementById("customerName").focus();

        return false;

    }


    document.getElementById("nameError").innerText = "";


    /* Jenis */

    if (type === "") {

        alert("Silakan pilih jenis perhiasan.");

        return false;

    }


    /* Ukuran */

    if (size === "") {

        alert("Silakan pilih ukuran.");

        return false;

    }


    /* Kadar */

    if (!purity) {

        alert(
            "Silakan pilih kadar emas 88% atau 90%."
        );

        return false;

    }


    /* Berat */

    if (
        isNaN(weight) ||
        weight <= 0
    ) {

        alert(
            "Masukkan berat perhiasan yang valid."
        );

        document.getElementById("weight").focus();

        return false;

    }


    /* Harga */

    if (
        isNaN(goldPrice) ||
        goldPrice <= 0
    ) {

        alert(
            "Masukkan harga emas per gram."
        );

        document.getElementById("goldPrice").focus();

        return false;

    }


    return true;

}


/* =========================================
   KIRIM PESAN WHATSAPP
========================================= */

function sendToWhatsApp() {

    /*
       GANTI NOMOR INI DENGAN NOMOR WHATSAPP AYAH
       Contoh:
       6281234567890
    */

    const phoneNumber =
        "628xxxxxxxxxx";


    let name =
        document.getElementById("customerName")
            .value.trim();

    let type =
        document.getElementById("jewelryType")
            .value;

    let size =
        document.getElementById("size")
            .value;

    let purity =
        document.querySelector(
            'input[name="purity"]:checked'
        ).value;

    let weight =
        document.getElementById("weight")
            .value;

    let goldPrice =
        parseFloat(
            document.getElementById("goldPrice")
                .value
        );

    let design =
        document.getElementById("design")
            .value.trim();

    let engraving =
        document.getElementById("engraving")
            .value.trim();

    let notes =
        document.getElementById("notes")
            .value.trim();


    let total =
        parseFloat(weight) * goldPrice;


    if (design === "") {
        design = "-";
    }

    if (engraving === "") {
        engraving = "-";
    }

    if (notes === "") {
        notes = "-";
    }


    let message =
        "Halo TUKANG EMAS REZKY,%0A%0A" +

        "Saya ingin melakukan pemesanan/custom perhiasan.%0A%0A" +

        "*DATA PELANGGAN*%0A" +
        "Nama: " + name + "%0A%0A" +

        "*DETAIL PERHIASAN*%0A" +
        "Jenis: " + type + "%0A" +
        "Ukuran: " + size + "%0A" +
        "Kadar emas: " + purity + "%0A" +
        "Perkiraan berat: " + weight + " gram%0A" +
        "Desain/keinginan: " + design + "%0A" +
        "Ukiran/tulisan: " + engraving + "%0A" +
        "Catatan: " + notes + "%0A%0A" +

        "*ESTIMASI HARGA*%0A" +
        "Harga emas/gram: " +
        formatRupiah(goldPrice) + "%0A" +

        "Estimasi: " +
        formatRupiah(total) + "%0A%0A" +

        "Mohon dikonfirmasi kembali untuk harga akhir.%0A" +

        "Terima kasih.";


    let whatsappURL =
        "https://wa.me/" +
        phoneNumber +
        "?text=" +
        message;


    window.open(
        whatsappURL,
        "_blank"
    );

}