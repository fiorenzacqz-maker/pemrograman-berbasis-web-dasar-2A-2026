<?php
function highlight($tahun, $text){
    if($tahun == "2026"){
        return "<b style='color:#dc3545'>$text</b>";
    }
    return $text;
}

$data = [
    "2024 (awal)" => "Belajar HTML",
    "2024 (akhir)" => "Belajar CSS & JavaScript",
    "2025 (awal)" => "Masuk Kuliah",
    "2025 (akhir)" => "Membuat Project Pertama",
    "2026" => "Belajar PHP"
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Timeline</title>

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

    .timeline {
        width: 60%;
        margin: auto;
        position: relative;
        padding-left: 20px;
        border-left: 3px solid #dc3545;
    }

    .item {
        margin-bottom: 20px;
        position: relative;
    }

    .item::before {
        content: "";
        position: absolute;
        left: -10px;
        top: 5px;
        width: 12px;
        height: 12px;
        background: #dc3545;
        border-radius: 50%;
    }

    .box {
        background: white;
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #dc3545;
    }

    a {
        display: inline-block;
        margin-top: 15px;
        margin-right: 5px;
        text-decoration: none;
        color: white;
        background: #dc3545;
        padding: 8px 12px;
        border-radius: 5px;
    }

    a:hover {
        background: #c82333;
    }
    </style>

</head>
<body>

<h2>Timeline Belajar Coding</h2>

<div class="timeline">
    <?php foreach($data as $tahun => $isi){ ?>
        <div class="item">
            <div class="box">
                <?php echo highlight($tahun, "$tahun - $isi"); ?>
            </div>
        </div>
    <?php } ?>
</div>

<br>
<div style="text-align:center;">
    <a href="index.php">← Kembali ke Profil</a>
    <a href="blog.php">Ke Blog →</a>
</div>

</body>
</html>