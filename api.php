<?php
// api.php - Backend API for database operations
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database configuration
$host = 'localhost';
$dbname = 'personal_info_db';
$username = 'root'; // Change to your database username
$password = '';     // Change to your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        // Retrieve all records
        try {
            $stmt = $pdo->query("SELECT * FROM people ORDER BY created_at DESC");
            $people = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format data to match existing structure
            $formatted = array_map(function($person) {
                return [
                    'id' => $person['id'],
                    'name' => $person['full_name'],
                    'age' => $person['age'],
                    'nationality' => $person['nationality'],
                    'gender' => $person['gender'],
                    'date' => $person['birthdate'],
                    'hobbies' => $person['hobbies'],
                    'comment' => $person['comments']
                ];
            }, $people);
            
            echo json_encode($formatted);
        } catch(PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;
        
    case 'POST':
        // Add new record
        $data = json_decode(file_get_contents('php://input'), true);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO people (full_name, age, nationality, gender, birthdate, hobbies, comments) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['name'],
                $data['age'],
                $data['nationality'],
                $data['gender'],
                $data['date'],
                $data['hobbies'],
                $data['comment']
            ]);
            
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        } catch(PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;
        
    case 'DELETE':
        // Delete record
        $data = json_decode(file_get_contents('php://input'), true);
        
        try {
            $stmt = $pdo->prepare("DELETE FROM people WHERE id = ?");
            $stmt->execute([$data['id']]);
            
            echo json_encode(['success' => true]);
        } catch(PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        break;
}
?>