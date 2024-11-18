<?php
header('Access-Control-Allow-Origin: http://localhost:3000'); // Match your frontend
header('Access-Control-Allow-Methods: GET, POST, OPTIONS'); // Include any other HTTP methods you use
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
date_default_timezone_set('Africa/Nairobi');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
function errorHandler($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'type' => 'PHP Error',
        'message' => $errstr,
        'file' => basename($errfile),
        'line' => $errline
    ]);
    exit;
}
set_error_handler("errorHandler");

register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== NULL && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        http_response_code(500);
        echo json_encode([
            'error' => true,
            'type' => 'Fatal Error',
            'message' => $error['message'],
            'file' => basename($error['file']),
            'line' => $error['line']
        ]);
    }
});



// Database connection
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'snapsell';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'type' => 'Database Error',
        'message' => 'Connection Failed: ' . $conn->connect_error
    ]);
    exit;
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
    case 'verify':
            verifyCode($conn, $data);
            break;
    default:
        echo json_encode(['error' => 'Invalid action']);
}

function sendVerificationEmail($email, $code) {
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp-relay.brevo.com'; // Update with your SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = '802019002@smtp-brevo.com'; // Update with your email
        $mail->Password = 'MWCzwmIrqJYQLaU6'; // Use app password for Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('ayub@ayubxxl.site', 'SnapSell');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your SnapSell Verification Code';
        $mail->Body = "Your verification code is: <b>$code</b>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function handleSignup($conn, $data) {
    if(!isset($data->name) || !isset($data->email) || !isset($data->password)) {
        http_response_code(400);
        echo json_encode([
            'error' => true,
            'type' => 'Validation Error',
            'message' => 'Missing required fields'
        ]);
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
    if(!isset($data->name) || !isset($data->password)) {
        http_response_code(400);
        echo json_encode([
            'error' => true,
            'type' => 'Validation Error',
            'message' => 'Missing required fields'
        ]);
        return;
    }

    // Sanitize input
    $name = $conn->real_escape_string($data->name);
    
    // Use prepared statement
    $query = "SELECT id, name, email, password_hash FROM users WHERE name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if(password_verify($data->password, $user['password_hash'])) {
            // Generate verification code
            $verificationCode = sprintf("%06d", random_int(0, 999999));
            
            // Store verification code in database
            $expires = date('Y-m-d H:i:s', strtotime('+5 minutes'));
            $userId = $user['id'];
            
            $storeCode = $conn->prepare("INSERT INTO verification_codes (user_id, code, expires_at) VALUES (?, ?, ?)");
            $storeCode->bind_param("iss", $userId, $verificationCode, $expires);
            
            if($storeCode->execute() && sendVerificationEmail($user['email'], $verificationCode)) {
                echo json_encode([
                    'requiresVerification' => true,
                    'message' => 'Verification code sent to your email',
                    'userId' => $user['id']
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to send verification code']);
            }
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid password']);
        }
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
    }
    
    $stmt->close();
}

// Add new verification endpoint
function verifyCode($conn, $data) {
    if(!isset($data->userId) || !isset($data->code)) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        return;
    }

    $stmt = $conn->prepare("SELECT * FROM verification_codes 
                           WHERE user_id = ? AND code = ? AND expires_at > NOW() 
                           AND used = 0 ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("is", $data->userId, $data->code);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        // Mark code as used
        $updateStmt = $conn->prepare("UPDATE verification_codes SET used = 1 WHERE user_id = ? AND code = ?");
        $updateStmt->bind_param("is", $data->userId, $data->code);
        $updateStmt->execute();

        // Generate session token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        $sessionStmt = $conn->prepare("INSERT INTO user_sessions (user_id, session_token, expires_at) VALUES (?, ?, ?)");
        $sessionStmt->bind_param("iss", $data->userId, $token, $expires);
        
        if($sessionStmt->execute()) {
            // Get user details
            $userStmt = $conn->prepare("SELECT id, name FROM users WHERE id = ?");
            $userStmt->bind_param("i", $data->userId);
            $userStmt->execute();
            $user = $userStmt->get_result()->fetch_assoc();

            echo json_encode([
                'success' => true,
                'token' => $token,
                'user' => $user
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create session']);
        }
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid or expired verification code']);
    }
}

$conn->close();
?>