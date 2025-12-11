<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $product = $_POST['product_name'];
    $price = $_POST['product_price'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = nl2br($_POST['address']);

    $to = "YOUR_EMAIL@example.com"; // ← Change this
    $subject = "New Product Order - $product";

    $message = "
      <h2>New Order Received</h2>
      <p><b>Product:</b> $product</p>
      <p><b>Price:</b> ₹$price</p>
      <hr>
      <p><b>Name:</b> $name</p>
      <p><b>Email:</b> $email</p>
      <p><b>Phone:</b> $phone</p>
      <p><b>Address:</b><br>$address</p>
    ";

    $headers  = "From: Website Order <no-reply@yourwebsite.com>\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    if (mail($to, $subject, $message, $headers)) {
        echo "<h2>Order Sent Successfully!</h2>";
    } else {
        echo "<h2>Failed to send order. Try again.</h2>";
    }
}

?>
