<?php
require_once 'razorpay-config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

// Get raw POST data
$input = json_decode(file_get_contents('php://input'), true);
$productId = $input['product_id'] ?? null;
$name = $input['name'] ?? '';
$email = $input['email'] ?? '';
$phone = $input['phone'] ?? '';
$address = $input['address'] ?? '';

if (!$productId) {
    echo json_encode(['error' => 'Product ID is required']);
    exit;
}

// Load products to get the real price
$productsJson = file_get_contents('../shop/products.json');
$products = json_decode($productsJson, true);

$product = null;
foreach ($products as $p) {
    if ($p['id'] == $productId) {
        $product = $p;
        break;
    }
}

if (!$product) {
    echo json_encode(['error' => 'Product not found']);
    exit;
}

// Razorpay Order Creation
$api_key = RAZORPAY_KEY_ID;
$api_secret = RAZORPAY_KEY_SECRET;

$amount = $product['price'] * 100; // Amount in paise
$currency = 'INR';
$receipt = 'order_rcptid_' . time();

$data = [
    'amount' => $amount,
    'currency' => $currency,
    'receipt' => $receipt,
    'payment_capture' => 1,
    'notes' => [
        'customer_name' => $name,
        'customer_email' => $email,
        'customer_phone' => $phone,
        'customer_address' => $address
    ]
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/orders');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_USERPWD, $api_key . ':' . $api_secret);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$result = curl_exec($ch);
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if (curl_errno($ch)) {
    echo json_encode(['error' => 'Curl error: ' . curl_error($ch)]);
    exit;
}

curl_close($ch);

if ($http_status === 200) {
    $order = json_decode($result, true);
    echo json_encode([
        'order_id' => $order['id'],
        'amount' => $amount,
        'currency' => $currency,
        'key_id' => RAZORPAY_KEY_ID,
        'product_name' => $product['name'],
        'description' => $product['description'],
        'image' => $product['image'],
        'prefill_name' => $name,
        'prefill_email' => $email,
        'prefill_contact' => $phone
    ]);
} else {
    echo json_encode(['error' => 'Razorpay Error: ' . $result]);
}
?>