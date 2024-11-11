<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include Composer's autoloader
require 'vendor/autoload.php';

function sendVerificationCode($email, $verificationCode) {
    $mail = new PHPMailer(true);
    try {
        // Set up the mailer
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Gmail's SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = '21-75120@g.batstate-u.edu.ph'; // Your email
        $mail->Password = 'wvlibgarhqdfubhy'; // Avoid hardcoding, use secure storage
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        // Set sender and recipient
        $mail->setFrom('21-75120@g.batstate-u.edu.ph');
        $mail->addAddress($email);

        // Set the email subject and body content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Verification Code';
        $mail->Body = "Your verification code is: $verificationCode";

        // Send the email
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
