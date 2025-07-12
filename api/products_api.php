<?php
// Set the content type to JSON for all responses
header('Content-Type: application/json');

// Define the path to the data file
$productsFilePath = '../data/products.json';

// A helper function to read products from the JSON file
function get_products($filePath) {
    if (!file_exists($filePath)) {
        // If the file doesn't exist, return an empty array
        return [];
    }
    $json_data = file_get_contents($filePath);
    return json_decode($json_data, true);
}

// A helper function to save products to the JSON file
function save_products($filePath, $products) {
    $json_data = json_encode($products, JSON_PRETTY_PRINT);
    file_put_contents($filePath, $json_data);
}

// Get the request method (GET, POST, PUT, DELETE)
$method = $_SERVER['REQUEST_METHOD'];

// Handle the request based on the method
switch ($method) {
    case 'GET':
        // --- Handle GET request to fetch all products ---
        $products = get_products($productsFilePath);
        echo json_encode($products);
        break;

    case 'POST':
        // --- Handle POST request to add a new product ---
        $input = json_decode(file_get_contents('php://input'), true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($input['name']) || !isset($input['brand']) || !isset($input['price']) || !isset($input['stock_quantity'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Invalid input data.']);
            exit;
        }

        $products = get_products($productsFilePath);

        $new_product = [
            'id' => 'prod_' . uniqid(), // Generate a unique ID
            'name' => htmlspecialchars($input['name']),
            'brand' => htmlspecialchars($input['brand']),
            'price' => floatval($input['price']),
            'stock_quantity' => intval($input['stock_quantity']),
            'description' => isset($input['description']) ? htmlspecialchars($input['description']) : ''
        ];

        $products[] = $new_product;
        save_products($productsFilePath, $products);

        http_response_code(201); // Created
        echo json_encode($new_product);
        break;

    case 'PUT':
        // --- Handle PUT request to update an existing product ---
        $input = json_decode(file_get_contents('php://input'), true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($input['id'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Invalid input data or missing product ID.']);
            exit;
        }

        $products = get_products($productsFilePath);
        $product_found = false;

        foreach ($products as $key => &$product) {
            if ($product['id'] === $input['id']) {
                // Update fields if they are provided in the input
                if (isset($input['name'])) $product['name'] = htmlspecialchars($input['name']);
                if (isset($input['brand'])) $product['brand'] = htmlspecialchars($input['brand']);
                if (isset($input['price'])) $product['price'] = floatval($input['price']);
                if (isset($input['stock_quantity'])) $product['stock_quantity'] = intval($input['stock_quantity']);
                if (isset($input['description'])) $product['description'] = htmlspecialchars($input['description']);
                
                $product_found = true;
                $updated_product = $product;
                break;
            }
        }

        if ($product_found) {
            save_products($productsFilePath, $products);
            echo json_encode($updated_product);
        } else {
            http_response_code(404); // Not Found
            echo json_encode(['error' => 'Product not found.']);
        }
        break;

    case 'DELETE':
        // --- Handle DELETE request to remove a product ---
        // Note: For simplicity, we get the ID from a query string like ?id=prod_123
        if (!isset($_GET['id'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Product ID is required.']);
            exit;
        }
        
        $product_id_to_delete = $_GET['id'];
        $products = get_products($productsFilePath);
        
        // Filter the array, keeping only products that DON'T match the ID
        $products_after_deletion = array_filter($products, function($product) use ($product_id_to_delete) {
            return $product['id'] !== $product_id_to_delete;
        });

        // Check if a product was actually deleted
        if (count($products) === count($products_after_deletion)) {
             http_response_code(404); // Not Found
             echo json_encode(['error' => 'Product not found.']);
        } else {
            // Re-index the array to prevent it from becoming an object in JSON
            save_products($productsFilePath, array_values($products_after_deletion));
            echo json_encode(['success' => true, 'message' => 'Product deleted.']);
        }
        break;

    default:
        // --- Handle unsupported request methods ---
        http_response_code(405); // Method Not Allowed
        echo json_encode(['error' => 'Method not allowed.']);
        break;
}
?>
