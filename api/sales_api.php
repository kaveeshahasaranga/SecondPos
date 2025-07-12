<?php
// Set the content type to JSON for all responses
header('Content-Type: application/json');

// Define file paths
$salesFilePath = '../data/sales.json';
$productsFilePath = '../data/products.json';

// A helper function to read data from a JSON file
function get_data($filePath) {
    if (!file_exists($filePath)) {
        // Return an empty array if the file doesn't exist
        return [];
    }
    $json_data = file_get_contents($filePath);
    // Return an empty array if decoding fails or file is empty
    return json_decode($json_data, true) ?: [];
}

// A helper function to save data to a JSON file
function save_data($filePath, $data) {
    $json_data = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents($filePath, $json_data);
}

// This API only accepts POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Method not allowed. Only POST is accepted.']);
    exit;
}

// --- Handle POST request to create a new sale ---
$input = json_decode(file_get_contents('php://input'), true);

// Validate the incoming data
if (json_last_error() !== JSON_ERROR_NONE || !isset($input['items']) || !isset($input['total_amount']) || !is_array($input['items'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid input data. "items" array and "total_amount" are required.']);
    exit;
}

// Load existing data
$sales = get_data($salesFilePath);
$products = get_data($productsFilePath);

// Create the new sale record
$new_sale = [
    'sale_id' => 'sale_' . uniqid(),
    'timestamp' => date('c'), // ISO 8601 date format
    'items' => $input['items'],
    'total_amount' => floatval($input['total_amount'])
];

// --- Update product stock quantities ---
// Create a map of products for faster lookup
$productMap = [];
foreach ($products as &$product) {
    $productMap[$product['id']] = &$product;
}

foreach ($new_sale['items'] as $item) {
    if (isset($item['product_id']) && isset($productMap[$item['product_id']])) {
        // Decrease the stock quantity
        $productMap[$item['product_id']]['stock_quantity'] -= $item['quantity'];
    }
}
// Unset the reference to avoid potential issues
unset($product); 

// Add the new sale to the sales array
$sales[] = $new_sale;

// Save both updated arrays back to their files
save_data($salesFilePath, $sales);
save_data($productsFilePath, $products);

// Respond with success
http_response_code(201); // Created
echo json_encode($new_sale);

?>
