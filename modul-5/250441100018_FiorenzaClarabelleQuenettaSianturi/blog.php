<?php
$artikel = [
    "html" => [
        "judul" => "Awal Belajar HTML",
        "tanggal" => "12 Januari 2026",
        "refleksi" => "Saya memulai perjalanan coding dengan mempelajari HTML sebagai dasar untuk membuat struktur halaman web. Dari sana, saya belajar CSS untuk mempercantik tampilan dengan mengatur warna, layout, dan responsivitas sehingga website terlihat lebih menarik. Setelah itu, saya mengenal JavaScript yang membuat halaman menjadi interaktif, seperti menambahkan efek klik dan validasi form. Terakhir, saya mempelajari PHP untuk mengelola data secara dinamis di sisi server, seperti menampilkan konten otomatis dan menghubungkan dengan database. Dari proses ini, saya menyadari bahwa setiap teknologi saling melengkapi dan sangat penting dalam membangun website yang utuh.",
        "gambar" => "img/html.jpeg",
        "referensi" => [
            "HTML" => "https://www.w3schools.com",
            "CSS" => "https://css-tricks.com",
            "JavaScript" => "https://javascript.info",
            "HTML Form" => "https://www.freecodecamp.org",
            "PHP & Koneksi Database" => "https://www.tutorialspoint.com/php",
            "Latihan" => "https://codepen.io"
        ]
    ],
    "error" => [
        "judul" => "Belajar dari Error Pertama",
        "tanggal" => "20 Februari 2026",
        "refleksi" => "Pengalaman pertama kali mengalami error saat coding membuat saya cukup bingung dan frustasi karena program yang dibuat tidak berjalan sesuai harapan. Saat itu, saya tidak langsung mengetahui letak kesalahannya, sehingga harus mencoba membaca ulang kode secara teliti dan mencari referensi di internet. Dari situ, saya mulai belajar bahwa error adalah hal yang wajar dalam proses coding dan justru membantu saya memahami logika program dengan lebih baik. Seiring waktu, saya menjadi lebih terbiasa menghadapi error dan tidak panik, melainkan menjadikannya sebagai bagian dari proses belajar yang penting.",
        "gambar" => "img/eror.png",
        "referensi" => "https://www.w3schools.com"
    ]
];

$quotes = [
    "html" => [
        "Pertama kali belajar HTML, saya merasa seperti sedang membangun kerangka sebuah rumah dari nol.",
        "Belajar HTML membuka pemahaman saya bahwa setiap website memiliki struktur yang tersusun rapi.",
        "Saat pertama belajar HTML, saya hanya mengikuti tag tanpa tahu arti, tapi lama-lama mulai memahami fungsinya.",
        "HTML menjadi langkah awal saya mengenal dunia coding yang ternyata tidak sesulit yang dibayangkan.",
        "Melihat halaman pertama berhasil tampil di browser memberi rasa puas dan semangat untuk belajar lebih jauh."
    ],
    "error" => [
        "Error pertama membuat saya sadar bahwa coding bukan hanya menulis, tapi juga memahami kesalahan.",
        "Saat pertama kali error, saya merasa bingung karena kode terlihat benar, tetapi hasilnya tidak sesuai.",
        "Error pertama mengajarkan saya untuk lebih teliti dan tidak terburu-buru dalam menulis kode.",
        "Awalnya error terasa menyulitkan, namun justru dari situ saya mulai belajar mencari solusi.",
        "Pengalaman error pertama membuat saya lebih sabar dan terbiasa menghadapi tantangan dalam coding."
    ]
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Blog Developer</title>

    <style>
    body {
        font-family: Arial;
        background: linear-gradient(135deg, #ffe6e6 0%, #fff5f5 100%);
        padding: 20px;
    }

    h2 {
        text-align: center;
        color: #c82333;
    }

    .box {
        background: white;
        padding: 20px;
        width: min(90%, 1000px);
        margin: auto;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(200,35,51,0.2);
        border: 2px solid #ffcccc;
        display: flex;
        gap: 24px;
    }

    .sidebar {
        width: 30%;
        border-right: 2px solid #dc3545;
        padding-right: 16px;
    }

    .content {
        width: 70%;
    }

    li {
        list-style: none;
        margin: 8px 0;
    }

    .article-list {
        padding: 0;
        margin: 12px 0 0;
    }

    .article-list a {
        display: block;
        background: #fff0f0;
        color: #c82333;
        padding: 10px 12px;
        border-radius: 8px;
        border-left: 3px solid #dc3545;
    }

    .article-list a:hover {
        background: #ffcccc;
    }

    a {
        text-decoration: none;
        color: #dc3545;
    }

    img {
        width: 100%;
        max-width: 500px;
        margin-top: 10px;
        border-radius: 10px;
    }

    .quote {
        margin-top: 15px;
        padding: 10px;
        background: #fff0f0;
        border-left: 3px solid #dc3545;
        font-style: italic;
    }

    .meta {
        color: #555;
        margin-top: -8px;
        margin-bottom: 12px;
        font-size: 14px;
    }

    .ref-link {
        display: inline-block;
        margin-top: 12px;
    }

    .references {
        margin-top: 10px;
        padding: 0;
    }

    .references ul {
        margin: 5px 0 0;
        padding-left: 18px;
    }

    .references li {
        margin: 4px 0;
    }

    .references a {
        color: #dc3545;
    }

    .timeline-btn {
        display: block;
        width: max-content;
        margin: 20px auto 0;
        text-decoration: none;
        color: white;
        background: #dc3545;
        padding: 8px 15px;
        border-radius: 5px;
    }

    .timeline-btn:hover {
        background: #c82333;
    }


    @media (max-width: 768px) {
        .box {
            flex-direction: column;
        }

        .sidebar,
        .content {
            width: 100%;
            padding-right: 0;
            border-right: none;
        }
    }
    </style>

</head>
<body>

<h2>Blog Developer</h2>

<div class="box">

    <div class="sidebar">
        <b>Daftar Artikel:</b>
        <ul class="article-list">
        <?php foreach($artikel as $key => $a){ ?>
            <li>
                <a href="?id=<?php echo $key ?>">
                    <?php echo ucfirst($key) ?>
                </a>
            </li>
        <?php } ?>
        </ul>
    </div>

    <div class="content">
        <?php
        if(isset($_GET['id']) && isset($artikel[$_GET['id']])){
            $id = $_GET['id'];
            $selected = $artikel[$id];
            $quote = $quotes[$id][array_rand($quotes[$id])];

            echo "<h3>".$selected['judul']."</h3>";
            echo "<div class='meta'>Tanggal Posting: ".$selected['tanggal']."</div>";
            echo "<p>".$selected['refleksi']."</p>";
            echo "<img src='".$selected['gambar']."' alt='Ilustrasi ".$selected['judul']."'>";
            echo "<div class='quote'>\"".$quote."\"</div>";


            // Tampilkan referensi
            if(is_array($selected['referensi'])){
                echo "<div class='references'><b>Referensi:</b><ul>";
                foreach($selected['referensi'] as $title => $url){
                    echo "<li><a href='".$url."' target='_blank'>".$title."</a></li>";
                }
                echo "</ul></div>";
            } else {
                echo "<a class='ref-link' href='".$selected['referensi']."' target='_blank'>Referensi tambahan</a>";
            }

        } else {
            echo "<h3>Pilih Artikel</h3>";
            echo "Silakan pilih artikel di sebelah kiri.";
        }
        ?>
    </div>

</div>

<a class="timeline-btn" href="timeline.php">← Kembali ke Timeline</a>

</body>
</html>