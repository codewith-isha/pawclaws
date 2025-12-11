<?php
require_once 'razorpay-config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$razorpay_payment_id = $input['razorpay_payment_id'] ?? null;
$razorpay_order_id = $input['razorpay_order_id'] ?? null;
$razorpay_signature = $input['razorpay_signature'] ?? null;

if (!$razorpay_payment_id || !$razorpay_order_id || !$razorpay_signature) {
    echo json_encode(['error' => 'Missing payment details']);
    exit;
}

$generated_signature = hash_hmac('sha256', $razorpay_order_id . "|" . $razorpay_payment_id, RAZORPAY_KEY_SECRET);

if ($generated_signature === $razorpay_signature) {
    // Payment Successful

    // Fetch Order Details from Razorpay to get Notes (Customer Details)
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/orders/' . $razorpay_order_id);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERPWD, RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET);
    $order_result = curl_exec($ch);
    curl_close($ch);

    $order_data = json_decode($order_result, true);
    $notes = $order_data['notes'] ?? [];

    $name = $notes['customer_name'] ?? 'N/A';
    $email = $notes['customer_email'] ?? 'N/A';
    $phone = $notes['customer_phone'] ?? 'N/A';
    $address = $notes['customer_address'] ?? 'N/A';

    // Send Email
    $to = "Priya@intuitivewizdom.com";
    $subject = "New Order Received - " . $razorpay_order_id;

    $message = "New Payment Received!\n\n";
    $message .= "Order ID: " . $razorpay_order_id . "\n";
    $message .= "Payment ID: " . $razorpay_payment_id . "\n";
    $message .= "---------------------------------\n";
    $message .= "Customer Details:\n";
    $message .= "Name: " . $name . "\n";
    $message .= "Email: " . $email . "\n";
    $message .= "Phone: " . $phone . "\n";
    $message .= "Address: " . $address . "\n";

    $headers = "From: no-reply@intuitivewizdom.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/plain; charset=UTF-8\r\n";

    $admin_mail_sent = mail($to, $subject, $message, $headers);

    // Send Email to Customer
    $customer_subject = "Order Confirmation - " . $razorpay_order_id;
    $customer_message = "Dear " . $name . ",\n\n";
    $customer_message .= "Thank you for your order! We have received your payment.\n\n";
    $customer_message .= "Order ID: " . $razorpay_order_id . "\n";
    $customer_message .= "Payment ID: " . $razorpay_payment_id . "\n";
    $customer_message .= "---------------------------------\n";
    $customer_message .= "We will process your order shortly.\n\n";
    $customer_message .= "Best Regards,\nIntuitive Wizdom";

    $customer_headers = "From: no-reply@intuitivewizdom.com\r\n";
    $customer_headers .= "MIME-Version: 1.0\r\n";
    $customer_headers .= "Content-type: text/plain; charset=UTF-8\r\n";

    $customer_mail_sent = false;
    if ($email !== 'N/A') {
        $customer_mail_sent = mail($email, $customer_subject, $customer_message, $customer_headers);
    }

    echo json_encode([
        'success' => true,
        'debug' => [
            'extracted_email' => $email,
            'admin_mail_sent' => $admin_mail_sent,
            'customer_mail_sent' => $customer_mail_sent
        ]
    ]);
} else {
    echo json_encode(['error' => 'Payment verification failed']);
}
?>