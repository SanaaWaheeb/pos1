<?php
// Define the file where data will be stored
$file = 'data.txt';

// Get the current timestamp
$date = date('Y-m-d H:i:s');

// Capture GET and POST data
$data = [
    'timestamp' => $date,
    'method' => $_SERVER['REQUEST_METHOD'],
    'params' => ($_SERVER['REQUEST_METHOD'] === 'POST') ? $_POST : $_GET
];

// Convert data to JSON format for easy reading
$json_data = json_encode($data, JSON_PRETTY_PRINT);

// Append the data to the file
file_put_contents($file, $json_data . PHP_EOL, FILE_APPEND);

// Response to confirm data has been saved
echo "Data saved successfully.";
?>
