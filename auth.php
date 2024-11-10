<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    require_once 'db_connect.php';
    require_once 'email_config.php';

    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Log incoming request data
        error_log('Incoming request: ' . file_get_contents('php://input'));
        
        $data = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON data: ' . json_last_error_msg());
        }

        $action = isset($_GET['action']) ? $_GET['action'] : '';
        
        // Log the action
        error_log('Action: ' . $action);

        switch($action) {
            case 'signup':
                handleSignup($conn, $data);
                break;
            case 'verify_signup':
                verifySignupCode($conn, $data);
                break;
            case 'login':
                handleLogin($conn, $data);
                break;
            case 'verify_login':
                verifyLoginCode($conn, $data);
                break;
            default:
                throw new Exception('Invalid action specified');
        }
    } else {
        throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    error_log('Error in auth.php: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => 'Server error occurred: ' . $e->getMessage()
    ]);
}

function handleSignup($conn, $data) {
    try {
        if (!isset($data['name']) || !isset($data['email']) || !isset($data['password'])) {
            throw new Exception('Missing required fields');
        }

        $name = trim($data['name']);
        $email = trim($data['email']);
        $password = $data['password'];

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }

        // Validate name length
        if (strlen($name) < 2) {
            throw new Exception('Name is too short');
        }

        $password = password_hash($password, PASSWORD_DEFAULT);
        
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        if (!$stmt) {
            throw new Exception('Database prepare error: ' . $conn->error);
        }

        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            throw new Exception('Database execute error: ' . $stmt->error);
        }

        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo json_encode(['success' => false, 'error' => 'Email already exists']);
            return;
        }

        // Generate verification code
        $verificationCode = sprintf("%06d", mt_rand(100000, 999999));
        $_SESSION['verification_code'] = $verificationCode;
        $_SESSION['temp_user_data'] = [
            'name' => $name,
            'email' => $email,
            'password' => $password
        ];

        // Send verification email
        if (sendVerificationEmail($email, $verificationCode)) {
            echo json_encode(['success' => true, 'message' => 'Verification code sent']);
        } else {
            throw new Exception('Failed to send verification email');
        }

    } catch (Exception $e) {
        error_log('Error in handleSignup: ' . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function handleLogin($conn, $data) {
    try {
        if (!isset($data['name']) || !isset($data['password'])) {
            throw new Exception('Missing required fields');
        }

        $name = trim($data['name']);
        $password = $data['password'];
        
        $stmt = $conn->prepare("SELECT id, email, password FROM users WHERE name = ?");
        if (!$stmt) {
            throw new Exception('Database prepare error: ' . $conn->error);
        }

        $stmt->bind_param("s", $name);
        if (!$stmt->execute()) {
            throw new Exception('Database execute error: ' . $stmt->error);
        }

        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Generate verification code
                $verificationCode = sprintf("%06d", mt_rand(100000, 999999));
                $_SESSION['login_verification_code'] = $verificationCode;
                $_SESSION['temp_user_id'] = $user['id'];

                // Send verification email
                if (sendVerificationEmail($user['email'], $verificationCode)) {
                    echo json_encode(['success' => true, 'message' => 'Verification code sent']);
                } else {
                    throw new Exception('Failed to send verification email');
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Invalid credentials']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'User not found']);
        }

    } catch (Exception $e) {
        error_log('Error in handleLogin: ' . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function verifySignupCode($conn, $data) {
    $inputCode = $data['code'];
    
    if (!isset($_SESSION['verification_code']) || !isset($_SESSION['temp_user_data'])) {
        echo json_encode(['success' => false, 'error' => 'Verification session expired']);
        return;
    }

    if ($inputCode === $_SESSION['verification_code']) {
        $userData = $_SESSION['temp_user_data'];
        
        // Insert user into database
        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $userData['name'], $userData['email'], $userData['password']);
        
        if ($stmt->execute()) {
            // Clear session data
            unset($_SESSION['verification_code']);
            unset($_SESSION['temp_user_data']);
            
            echo json_encode(['success' => true, 'message' => 'Registration successful']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Database error']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid verification code']);
    }
}

function verifyLoginCode($conn, $data) {
    $inputCode = $data['code'];
    
    if (!isset($_SESSION['login_verification_code']) || !isset($_SESSION['temp_user_id'])) {
        echo json_encode(['success' => false, 'error' => 'Verification session expired']);
        return;
    }

    if ($inputCode === $_SESSION['login_verification_code']) {
        $userId = $_SESSION['temp_user_id'];
        
        // Generate JWT token or session token here
        $token = bin2hex(random_bytes(32)); // Simple token generation, consider using JWT
        
        // Clear session data
        unset($_SESSION['login_verification_code']);
        unset($_SESSION['temp_user_id']);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Login successful',
            'token' => $token
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid verification code']);
    }
} 