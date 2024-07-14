<?php
require_once __DIR__ . '/vendor/autoload.php'; // Adjust the path if necessary


use PHPSupabase\Service;

// Initialize the Supabase service
$service = new Service(
    "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InprdHN4dW10eWVyeHRza21henNrIiwicm9sZSI6ImFub24iLCJpYXQiOjE3MjA4ODc0MDYsImV4cCI6MjAzNjQ2MzQwNn0.lB9X4Z_a2W4fkOvbTVD81q-AP4Tlde_IrC88CKqAIBw",
    "https://zktsxumtyerxtskmazsk.supabase.co"
);

// Initialize the database instance
$db = $service->initializeDatabase('employees', 'id');

// Extract the department-id query parameter
$departmentIds = isset($_GET['department-id']) ? explode(',', $_GET['department-id']) : [];

// Construct the query
$query = [
    'select' => '*',
    'from'   => 'employees',
    'join'   => [
        [
            'table' => 'departments',
            'tablekey' => 'id' //we dont neet to define 'on' => 'employees.department_id = departments.id' cause Supabase database schema is set up correctly with the necessary relationships
        ]
    ],

];


// Add the department filter if department IDs are provided
if (!empty($departmentIds)) {
    $departmentIdsStr = implode(',', array_map('intval', $departmentIds)); // Convert array to a comma-separated string
    $query['where'] = [
        'department_id' => "in.($departmentIdsStr)" // Use the string representation of the array
    ];
}

try {
    // Execute the query
    $result = $db->createCustomQuery($query)->getResult();

    // Convert the result to JSON format
    $employees = json_encode($result, JSON_UNESCAPED_UNICODE);

    // Output employees as JSON with proper content type
    header('Content-Type: application/json; charset=utf-8');
    echo $employees;
} catch (Exception $e) {
    // Handle any exceptions
    echo 'Error: ' . $e->getMessage();
}
