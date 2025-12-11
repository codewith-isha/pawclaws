<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = "Priya@intuitivewizdom.com"; 

    $subject = $_POST["subject"] ?: "Intuitive Wizdom Contact Form Message";

    $name    = htmlspecialchars(trim($_POST['name']));
    $email   = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone   = htmlspecialchars(trim($_POST['phone']));
    $userMsg = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : "";

    $message = "Name: $name\n";
    $message .= "Email: $email\n";
    $message .= "Phone: $phone\n\n";
    $message .= "Message:\n$userMsg\n";

    // Use your domain email in From to avoid spam
    $headers = "From: Priya@intuitivewizdom.com\r\n";   // domain email
    $headers .= "Reply-To: $email\r\n";                 // user email
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/plain; charset=UTF-8\r\n";

    if (mail($to, $subject, $message, $headers)) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
