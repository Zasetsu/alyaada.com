<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
include 'tr.php';

require 'vendor/autoload.php';

if (isset($_POST['submit'])) {
    // Form verilerini al
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $message = $_POST['message'];

    // Dosya yükleme işlemi
    $uploaded_files = [];
    if (!empty($_FILES['files']['name'][0])) {
        foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['files']['name'][$key];
            $file_tmp = $_FILES['files']['tmp_name'][$key];
            $upload_dir = "uploads/";

            // Dosya adını ve uzantısını ayır
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $file_base_name = pathinfo($file_name, PATHINFO_FILENAME);

            // Benzersiz ID oluştur ve dosya adını güncelle
            $unique_id = uniqid();
            $new_file_name = $file_base_name . '-' . $unique_id . '.' . $file_ext;

            if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
                $uploaded_files[] = $upload_dir . $new_file_name;
                echo "Dosya başarıyla yüklendi: " . $new_file_name . "<br>";
            } else {
                echo "Dosya yüklenirken hata oluştu: " . $new_file_name . "<br>";
            }
        }
    }

    // PHPMailer ile e-posta gönderme işlemi
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'mail.ursamedia.io'; // SMTP sunucusu
        $mail->SMTPAuth = true;
        $mail->Username = 'eposta@ursamedia.io'; // SMTP kullanıcı adı
        $mail->Password = 'nedvi8-Getvud-fotjyj'; // SMTP şifresi
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Güvenlik protokolü
        $mail->Port = 465; // SMTP portu
        $mail->CharSet = 'UTF-8';
        // Alıcılar
        $mail->setFrom($email, $name);
        $mail->addAddress('turanumutyilmaz@gmail.com');

        // İçerik
        $mail->isHTML(true);
        $mail->Subject = 'Yeni Form Mesajı';
        $mail->Body = "İsim: $name<br>Email: $email<br>Telefon: $phone<br>Mesaj: $message<br>";

        if (!empty($uploaded_files)) {
            $mail->Body .= "Yüklenen Dosyalar:<br>";
            foreach ($uploaded_files as $file) {
                $mail->Body .= "<a href='https://adaalya.com/$file'>$file</a><br>";
            }
        }

        $mail->send();
        $_SESSION['message'] = 'E-posta başarıyla gönderildi.';
        echo "E-posta başarıyla gönderildi.";
        //alert
        echo "<script>alert('E-posta başarıyla gönderildi.');</script>";
    } catch (Exception $e) {
        $_SESSION['message'] = "E-posta gönderilirken hata oluştu. Hata: {$mail->ErrorInfo}";
        echo "E-posta gönderilirken hata oluştu. Hata: {$mail->ErrorInfo}";
        //alert
        echo "<script>alert('E-posta gönderilirken hata oluştu. Hata: {$mail->ErrorInfo}');</script>";
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}