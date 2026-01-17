<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /contact");
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $message === '') {
    $_SESSION['flash_error'] = "Tous les champs sont obligatoires.";
    header("Location: /contact");
    exit;
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'mail.luxestarspower.com'; // à adapter
    $mail->SMTPAuth = true;
    $mail->Username = 'contact@luxestarspower.com';
    $mail->Password = 'MOT_DE_PASSE_EMAIL';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('contact@luxestarspower.com', 'LuxeStarsPower');
    $mail->addReplyTo($email, $name);
    $mail->addAddress('contact@luxestarspower.com');

    $mail->isHTML(true);
    $mail->Subject = '📩 Nouveau message de contact';
    $mail->Body = "
        <strong>Nom :</strong> {$name}<br>
        <strong>Email :</strong> {$email}<br><br>
        <strong>Message :</strong><br>
        " . nl2br(htmlspecialchars($message)) . "
    ";

    $mail->send();
    $_SESSION['flash_success'] = "Message envoyé avec succès.";
} catch (Exception $e) {
    $_SESSION['flash_error'] = "Erreur lors de l’envoi du message.";
}

header("Location: /contact");
exit;
