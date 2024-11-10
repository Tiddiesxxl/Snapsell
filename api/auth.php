<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods,Authorization,X-Requested-With');

// Database connection
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'snapsell';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection Failed: ' . $conn->connect_error]));
}

// Get request data
$data = json_decode(file_get_contents("php://input"));
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch($action) {
    case 'signup':
        handleSignup($conn, $data);
        break;
    case 'login':
        handleLogin($conn, $data);
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
}

function handleSignup($conn, $data) {
    // Validate input
    if(!isset($data->name) || !isset($data->email) || !isset($data->password)) {
        echo json_encode(['error' => 'Missing required fields']);
        return;
    }

    // Sanitize input
    $name = $conn->real_escape_string($data->name);
    $email = $conn->real_escape_string($data->email);
    $password = password_hash($data->password, PASSWORD_DEFAULT);

    // Check if email already exists
    $check_query = "SELECT id FROM users WHERE email = '$email'";
    $result = $conn->query($check_query);
    
    if($result->num_rows > 0) {
        echo json_encode(['error' => 'Email already exists']);
        return;
    }

    // Insert new user
    $query = "INSERT INTO users (name, email, password_hash) VALUES ('$name', '$email', '$password')";
    
    if($conn->query($query)) {
        echo json_encode([
            'success' => true,
            'message' => 'User registered successfully'
        ]);
    } else {
        echo json_encode([
            'error' => 'Registration failed: ' . $conn->error
        ]);
    }
}

function handleLogin($conn, $data) {
    // Validate input
    if(!isset($data->name) || !isset($data->password)) {
        echo json_encode(['error' => 'Missing required fields']);
        return;
    }

    // Sanitize input
    $name = $conn->real_escape_string($data->name);
    
    // Get user
    $query = "SELECT id, name, password_hash FROM users WHERE name = '$name'";
    $result = $conn->query($query);

    if($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if(password_verify($data->password, $user['password_hash'])) {
            // Generate session token
            $token = bin2hex(random_bytes(32));
            
            // Store session
            $user_id = $user['id'];
            $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
            $session_query = "INSERT INTO user_sessions (user_id, session_token, expires_at) 
                            VALUES ('$user_id', '$token', '$expires')";
            
            if($conn->query($session_query)) {
                echo json_encode([
                    'success' => true,
                    'token' => $token,
                    'user' => [
                        'id' => $user['id'],
                        'name' => $user['name']
                    ]
                ]);
            } else {
                echo json_encode(['error' => 'Session creation failed']);
            }
        } else {
            echo json_encode(['error' => 'Invalid password']);
        }
    } else {
        echo json_encode(['error' => 'User not found']);
    }
}

$conn->close();
?>