<?php

header('Content-Type: application/json');

$data = [
    'success' => true,
    'message' => 'Rest API is working!',
    'timestamp' => time()
];

echo json_encode($data);