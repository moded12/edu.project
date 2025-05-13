<?php
require_once '../core/db.php';  // Make sure the path is correct to your db connection file

// Check if the application_id is provided
if (isset($_GET['application_id'])) {
    $application_id = (int)$_GET['application_id']; // Casting to int for safety

    // Query to fetch classes based on application_id
    $query = "SELECT id, name FROM classes WHERE application_id = ? ORDER BY name";
    
    if ($stmt = $conn->prepare($query)) {
        // Bind the application_id to the query
        $stmt->bind_param("i", $application_id);
        
        // Execute the query
        $stmt->execute();
        
        // Bind result variables
        $stmt->bind_result($id, $name);
        
        // Prepare an array to hold the results
        $classes = [];
        
        // Fetch the results
        while ($stmt->fetch()) {
            $classes[] = [
                'id' => $id,
                'name' => $name
            ];
        }
        
        // Close the statement
        $stmt->close();
        
        // Return the result as JSON
        echo json_encode($classes);
    } else {
        // If query preparation fails
        echo json_encode(['error' => 'Failed to prepare the query']);
    }
} else {
    // If application_id is not provided
    echo json_encode(['error' => 'No application_id provided']);
}
?>
