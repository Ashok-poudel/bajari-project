<?php
// Start session if needed
session_start();

// Your Khalti secret key (from Khalti dashboard)
$secretKey = 'test_secret_key_dc74b1b5a0e64f3bb1c2c3c1b8e89f0a'; // replace with your own

// Read the payload from POST (sent by JS after frontend payment success)
$token = $_POST['token'] ?? '';
$amount = $_POST['amount'] ?? 0; // amount in paisa

if (!$token || !$amount) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit();
}

// Khalti verification API endpoint
$verify_url = "https://khalti.com/api/v2/payment/verify/";

// Prepare data for POST
$data = [
    'token' => $token,
    'amount' => $amount
];

$ch = curl_init($verify_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Key $secretKey"
]);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

// Check if payment is verified
if(isset($result['state']) && $result['state']['name'] === 'Completed') {
    // Clear cart if using session
    if(isset($_SESSION['cart'])) {
        unset($_SESSION['cart']);
    }

    // Return success to frontend
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'fail', 'message' => $result]);
}
?>