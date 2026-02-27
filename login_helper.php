<?php

/**
 * BellaVella Client Auto-Login Script
 * 
 * This script automates the OTP flow and returns a JWT token for testing.
 * Run this from your terminal: php login_helper.php
 */

$mobile = "9832036595";
$baseUrl = "http://localhost/bellavellaa/public/api/client";

echo "--- BellaVella Login Helper ---\n";
echo "Mobile: $mobile\n";
echo "1. Requesting OTP...\n";

// Step 1: Send OTP
$ch = curl_init("$baseUrl/auth/send-otp");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['mobile' => $mobile]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($response, true);

if ($httpCode !== 200 || !isset($data['success']) || !$data['success']) {
    die("Error requesting OTP: " . ($data['message'] ?? 'Unknown error') . "\n");
}

$otp = $data['data']['otp_debug'] ?? null;

if (!$otp) {
    die("Error: No debug OTP found in response. Ensure APP_DEBUG=true or environment is not production.\n");
}

echo "Detected OTP (Debug): $otp\n";
echo "2. Verifying OTP and fetching Token...\n";

// Step 2: Verify OTP
$ch = curl_init("$baseUrl/auth/verify-otp");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['mobile' => $mobile, 'otp' => $otp]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Accept: application/json']);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$authData = json_decode($response, true);

if ($httpCode !== 200 || !isset($authData['success']) || !$authData['success']) {
    die("Error verifying OTP: " . ($authData['message'] ?? 'Unknown error') . "\n");
}

$token = $authData['data']['access_token'] ?? null;

if (!$token) {
    die("Error: Token not found in verification response.\n");
}

echo "\n--- SUCCESS ---\n";
echo "JWT Token:\n\n$token\n\n";
echo "You can now use this Bearer Token in Postman.\n";
echo "---------------------------\n";
