<?php
// Set the content type to JSON for all responses
header('Content-Type: application/json');

// Define the path to the data file
$repairsFilePath = '../data/repairs.json';

// A helper function to read data from the JSON file
function get_data($filePath) {
    if (!file_exists($filePath)) {
        return [];
    }
    $json_data = file_get_contents($filePath);
    return json_decode($json_data, true);
}

// A helper function to save data to the JSON file
function save_data($filePath, $data) {
    $json_data = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents($filePath, $json_data);
}

// Get the request method (GET, POST, PUT)
$method = $_SERVER['REQUEST_METHOD'];

// Handle the request based on the method
switch ($method) {
    case 'GET':
        // --- Handle GET request to fetch all repair jobs ---
        $repairs = get_data($repairsFilePath);
        echo json_encode($repairs);
        break;

    case 'POST':
        // --- Handle POST request to add a new repair job ---
        $input = json_decode(file_get_contents('php://input'), true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($input['customer_name']) || !isset($input['customer_contact']) || !isset($input['watch_details']) || !isset($input['issue_description'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Invalid input data.']);
            exit;
        }

        $repairs = get_data($repairsFilePath);

        $new_repair = [
            'repair_id' => 'repair_' . uniqid(), // Generate a unique ID
            'customer_name' => htmlspecialchars($input['customer_name']),
            'customer_contact' => htmlspecialchars($input['customer_contact']),
            'watch_details' => htmlspecialchars($input['watch_details']),
            'issue_description' => htmlspecialchars($input['issue_description']),
            'status' => 'Received', // Default status
            'price' => 0.00,
            'date_received' => date('Y-m-d') // Set current date
        ];

        $repairs[] = $new_repair;
        save_data($repairsFilePath, $repairs);

        http_response_code(201); // Created
        echo json_encode($new_repair);
        break;

    case 'PUT':
        // --- Handle PUT request to update an existing repair job (e.g., status or price) ---
        $input = json_decode(file_get_contents('php://input'), true);

        if (json_last_error() !== JSON_ERROR_NONE || !isset($input['repair_id'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => 'Invalid input data or missing repair ID.']);
            exit;
        }

        $repairs = get_data($repairsFilePath);
        $repair_found = false;

        foreach ($repairs as &$repair) { // Note the '&' to modify the array directly
            if ($repair['repair_id'] === $input['repair_id']) {
                // Update fields if they are provided in the input
                if (isset($input['status'])) {
                    $repair['status'] = htmlspecialchars($input['status']);
                }
                if (isset($input['price'])) {
                    $repair['price'] = floatval($input['price']);
                }
                
                $repair_found = true;
                $updated_repair = $repair;
                break;
            }
        }

        if ($repair_found) {
            save_data($repairsFilePath, $repairs);
            echo json_encode($updated_repair);
        } else {
            http_response_code(404); // Not Found
            echo json_encode(['error' => 'Repair job not found.']);
        }
        break;

    default:
        // --- Handle unsupported request methods ---
        http_response_code(405); // Method Not Allowed
        echo json_encode(['error' => 'Method not allowed.']);
        break;
}
?>
