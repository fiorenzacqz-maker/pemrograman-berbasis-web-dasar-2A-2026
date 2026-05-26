<?php
session_start();

function tampilkanData($data) {
    echo "<table border='1' cellpadding='10'>";
    foreach ($data as $key => $value) {
        echo "<tr><td><b>$key</b></td><td>$value</td></tr>";
    }
    echo "</table>";
}

if(isset($_POST['submit'])){
    if(!isset($_POST['tools']) || trim($_POST['tools']) === ''){
        echo "
<div class='hasil-card'>
                <h3 style='color:#c82333'>Error</h3>
                <p style='color:#c82333; font-weight:bold;'>Framework/Tools wajib diisi.</p>
              </div>";
    } else {

        $tools = array_map('trim', explode(",", $_POST['tools']));
        $penunjang = isset($_POST['penunjang']) ? implode(", ", $_POST['penunjang']) : '-';

        $data = [
            "Framework/Tools" => implode(", ", $tools),
            "Tools Penunjang" => $penunjang,
            "Minat" => isset($_POST['minat']) && $_POST['minat'] ? $_POST['minat'] : '-',
            "Skill" => isset($_POST['skill']) && $_POST['skill'] ? $_POST['skill'] : '-',
            "Pengalaman" => $_POST['pengalaman']
        ];

        if(!isset($_SESSION['history'])){
            $_SESSION['history'] = [];
        }

        $_SESSION['history'][] = $data;
    }
}

if(isset($_POST['reset'])){
    unset($_SESSION['history']);
    $_POST = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profil Developer</title>

    <style>
body {
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #ffe6e6 0%, #fff5f5 100%);
    margin: 0;
    padding: 20px;
}

h2 {
    text-align: center;
    color: #c82333;
}

table {
    border-collapse: collapse;
    margin: 20px auto;
    width: 80%;
    background: white;
    border: 2px solid #dc3545;
}

table td {
    padding: 10px;
}

form {
    background: #fff0f0;
    padding: 20px;
    width: 60%;
    margin: auto;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(200,35,51,0.2);
    border: 1px solid #ffcccc;
}

input, textarea, select {
    width: 98%;
    padding: 8px;
    margin-top: 5px;
    margin-bottom: 15px;
    border-radius: 5px;
    border: 1px solid #dc3545;
}

input[type="checkbox"],
input[type="radio"] {
    width: auto;
}

input[type="submit"] {
    background: #dc3545;
    color: white;
    border: none;
    cursor: pointer;
}

input[type="submit"]:hover {
    background: #c82333;
}

a {
    display: block;
    width: max-content;
    margin: 20px auto;
    text-decoration: none;
    color: white;
    background: #dc3545;
    padding: 8px 15px;
    border-radius: 5px;
}

a:hover {
    background: #c82333;
}

ul {
    width: 60%;
    margin: auto;
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

li {
    margin-bottom: 10px;
}

img {
    display: block;
    margin-top: 10px;
    border-radius: 10px;
}

.hasil-card {
    width: 60%;
    margin: 30px auto;
    background: #fff0f0;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(200,35,51,0.15);
    border: 2px solid #ffcccc;
    text-align: center;
}

.hasil-card h3 {
    margin-bottom: 15px;
    color: #c82333;
}

.hasil-card table {
    margin: auto;
    width: 100%;
}

.pengalaman {
    margin-top: 15px;
    padding: 10px;
    background: #fff5f5;
    border-radius: 8px;
    border-left: 3px solid #dc3545;
}

.hasil-card:hover {
    transform: translateY(-3px);
    transition: 0.3s;
}

.profile-card {
    width: 60%;
    margin: 20px auto;
    background: #fff0f0;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(200,35,51,0.15);
    border: 2px solid #ffcccc;
}

.profile-card table {
    width: 100%;
    margin: 0;
}
</style>
</head>
<body>

<h2>Profil Interaktif Developer Pemula</h2>

<div class="profile-card">
<table border="1" cellpadding="10">
<tr><td>Nama</td><td>Fiorenza Clarabelle Quenetta Sianturi</td></tr>
<tr><td>ID Developer</td><td>P10</td></tr>
<tr><td>Kota/Thn Lahir</td><td>SIANTAR, 2008</td></tr>
<tr><td>Email</td><td>fiorenza.cqz@email.com</td></tr>
<tr><td>No WA</td><td>087879713555</td></tr>
</table>
</div>

<br>

<form method="POST" id="profil-form">
Framework/Tools (pisahkan koma):<br>
<input type="text" name="tools" value="<?php echo isset($_POST['tools']) ? htmlspecialchars($_POST['tools']) : ''; ?>" required><br><br>

Pengalaman:<br>
<textarea name="pengalaman"><?php echo isset($_POST['pengalaman']) ? htmlspecialchars($_POST['pengalaman']) : ''; ?></textarea><br><br>

Tools Penunjang:<br>
<input type="checkbox" name="penunjang[]" value="VS Code" <?php echo (isset($_POST['penunjang']) && in_array('VS Code', $_POST['penunjang'])) ? 'checked' : ''; ?>>VS Code
<input type="checkbox" name="penunjang[]" value="GitHub" <?php echo (isset($_POST['penunjang']) && in_array('GitHub', $_POST['penunjang'])) ? 'checked' : ''; ?>>GitHub
<input type="checkbox" name="penunjang[]" value="Figma" <?php echo (isset($_POST['penunjang']) && in_array('Figma', $_POST['penunjang'])) ? 'checked' : ''; ?>>Figma
<input type="checkbox" name="penunjang[]" value="Postman" <?php echo (isset($_POST['penunjang']) && in_array('Postman', $_POST['penunjang'])) ? 'checked' : ''; ?>>Postman
<br><br>

Minat:<br>
<input type="radio" name="minat" value="Frontend" <?php echo (isset($_POST['minat']) && $_POST['minat'] === 'Frontend') ? 'checked' : ''; ?>>Frontend
<input type="radio" name="minat" value="Backend" <?php echo (isset($_POST['minat']) && $_POST['minat'] === 'Backend') ? 'checked' : ''; ?>>Backend
<input type="radio" name="minat" value="Fullstack" <?php echo (isset($_POST['minat']) && $_POST['minat'] === 'Fullstack') ? 'checked' : ''; ?>>Fullstack
<br><br>

Skill:<br>
<select name="skill">
<option value=""<?php echo (!isset($_POST['skill']) || $_POST['skill'] === '') ? ' selected' : ''; ?>>Pilih</option>
<option value="Dasar"<?php echo (isset($_POST['skill']) && $_POST['skill'] === 'Dasar') ? ' selected' : ''; ?>>Dasar</option>
<option value="Cukup"<?php echo (isset($_POST['skill']) && $_POST['skill'] === 'Cukup') ? ' selected' : ''; ?>>Cukup</option>
<option value="Profesional"<?php echo (isset($_POST['skill']) && $_POST['skill'] === 'Profesional') ? ' selected' : ''; ?>>Profesional</option>
</select>
<br><br>

<input type="submit" name="submit" value="Kirim">
<input type="submit" name="reset" value="Reset Data">
</form>

<div id="hasil-list" style="width:60%; margin:20px auto;"></div>

<?php
if(isset($_SESSION['history'])){
    foreach($_SESSION['history'] as $item){

        echo "<div class='hasil-card'>";
        echo "<h3>Hasil Input</h3>";

        tampilkanData($item);

        echo "<div class='pengalaman'>";
        echo "<b>Pengalaman:</b><br>";
        echo nl2br(htmlspecialchars($item['Pengalaman']));
        echo "</div>";

        $jumlah = count(array_filter(explode(", ", $item["Framework/Tools"])));
        if($jumlah > 2){
            echo "<p>Skill Anda cukup luas di bidang development!</p>";
        }

        echo "</div>";
    }
}
?>

<br>
<a href="timeline.php">Ke Timeline</a>

</body>
</html>