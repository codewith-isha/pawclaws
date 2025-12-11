<?php
header('Content-Type: application/json');

// Prevent direct access
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

// Get form data
$name = $_POST['name'] ?? '';
$phone = $_POST['phone'] ?? '';
$email = $_POST['email'] ?? '';
$message_content = $_POST['message'] ?? '';

// Basic validation
if (empty($name) || empty($phone) || empty($email) || empty($message_content)) {
    echo json_encode(['error' => 'All fields are required']);
    exit;
}

// Email details
$to = "Priya@intuitivewizdom.com";
$subject = "New Contact Inquiry - Corporate Offerings";

$message = "New Inquiry Received!\n\n";
$message .= "Name: " . $name . "\n";
$message .= "Phone: " . $phone . "\n";
$message .= "Email: " . $email . "\n";
$message .= "---------------------------------\n";
$message .= "Message:\n" . $message_content . "\n";

$headers = "From: no-reply@intuitivewizdom.com\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/plain; charset=UTF-8\r\n";

// Send email
if (mail($to, $subject, $message, $headers)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Failed to send email. Please try again later.']);
}
?>