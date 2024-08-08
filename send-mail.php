<?php

require "./vendor/autoload.php";
use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$config = require './config.php';

$email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
$name = htmlspecialchars(trim($_POST["name"]), ENT_QUOTES, 'UTF-8');
$message = htmlspecialchars(trim($_POST["text"]), ENT_QUOTES, 'UTF-8');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // die("Invalid email address.");
    header("Location: ./index.html?status=error");
    exit();
}

if (!preg_match("/^[a-zA-Zá-žÁ-Ž\s]+$/", $name)) {
    // die("Invalid name. Only letters and spaces are allowed.");
    header("Location: ./index.html?status=error");
    exit();
}

if (strlen($message) < 10) {
    // die("Message is too short. Please enter at least 10 characters.");
    header("Location: ./index.html?status=error");
    exit();
}

try {
    $mail = new PHPMailer(true);
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->isSMTP();
    $mail->Host = $config['smtp_host'];
    $mail->SMTPAuth = true;
    $mail->Username = $config['smtp_username'];
    $mail->Password = $config['smtp_password'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $config['smtp_port'];

    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    $mail->setFrom($email, $name);
    $mail->addAddress("recipient@mail.com");

    $mail->isHTML(true);
    $mail->Subject = "Email from your website";
    $mail->Body = "<p>You have received a new message from your website contact form.</p>
                   <p><strong>Name:</strong> $name</p>
                   <p><strong>Email:</strong> $email</p>
                   <p><strong>Message:</strong><br>$message</p>";
    $mail->AltBody = "You have received a new message from your website contact form.\n\n" .
                     "Name: $name\n" .
                     "Email: $email\n\n" .
                     "Message:\n$message";

    $mail->send();
    // echo "Message has been sent";
    header("Location: ./index.html?status=success");
    exit();
} catch (Exception $e) {
    // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    header("Location: ./index.html?status=error");
    exit();
}
