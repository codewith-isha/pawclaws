<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $to = "Priya@intuitivewizdom.com"; 

    $subject = "Wee Wonderland Registration Form Submission";

    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name  = htmlspecialchars(trim($_POST['last_name']));
    $email      = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $phone      = htmlspecialchars(trim($_POST['phone']));

    $message = "You have received a new registration:\n\n";
    $message .= "First Name: $first_name\n";
    $message .= "Last Name: $last_name\n";
    $message .= "Email: $email\n";
    $message .= "Phone: $phone\n";

    // Use your domain email as From to reduce spam
    $headers = "From: Priya@intuitivewizdom.com\r\n";
    $headers .= "Reply-To: $email\r\n"; // user’s email
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/plain; charset=UTF-8\r\n";

    if (mail($to, $subject, $message, $headers)) {
        echo "success";
    } else {
        echo "error";
    }
}
?>
