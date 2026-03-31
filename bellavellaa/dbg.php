<?php
$url = 'http://127.0.0.1:8000/api/professional/register';
$data = [
    'mobile' => '9999999999',
    'name' => 'Test User',
    'category' => 'Massage',
    'city' => 'Mumbai'
];

$options = [
    'http' => [
        'method'  => 'POST',
        'header'  => "Content-Type: application/json\r\n" .
                     "Accept: application/json\r\n",
        'content' => json_encode($data),
        'ignore_errors' => true
    ]
];

$context  = stream_context_create($options);
$result = @file_get_contents($url, false, $context);
$status_line = $http_response_header[0];
echo "Status: $status_line\n";
echo "Response: $result\n";
