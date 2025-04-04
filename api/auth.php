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
    case 'verify-session':  // Add this case
        verifySession($conn);
        break;
    case 'logout':  // Add this case
        handleLogout($conn);
        break;
    case 'send-otp':
        sendOtp($conn,$data);
        break;
    case 'verify-otp':
        verifyOtp($conn,$data);
        break;       
   case 'reset-password':
        resetPassword($conn, $data);
        break;
    default:
        echo json_encode(['error' => 'Invalid action']);
}

function base64url_encode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data) {
    return base64_decode(strtr($data, '-_', '+/'));
}

function verifyToken() {
    // Get headers
    $headers = getallheaders();
    $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

    // Check if token exists
    if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        return null;
    }

    $token = $matches[1];
    $secret = "your_jwt_secret_key"; // In production, this should be in a secure configuration file

    try {
        // Split token into parts
        $tokenParts = explode('.', $token);
        if (count($tokenParts) != 3) {
            return null;
        }

        // Decode token parts
        $header = json_decode(base64url_decode($tokenParts[0]), true);
        $payload = json_decode(base64url_decode($tokenParts[1]), true);

        // Verify signature
        $signature = $tokenParts[2];
        $validSignature = base64url_encode(
            hash_hmac('sha256', $tokenParts[0] . "." . $tokenParts[1], $secret, true)
        );

        if ($signature !== $validSignature) {
            return null;
        }

        // Check if token is expired
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return null;
        }

        // Get user from database
        global $conn;
        $userId = $payload['user_id'];
        $stmt = $conn->prepare("SELECT id, name, email, user_type FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        return $user;

    } catch (Exception $e) {
        return null;
    }
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
    if(!isset($data->email) || !isset($data->password)) {
        http_response_code(400);
        echo json_encode([
            'error' => true,
            'type' => 'Validation Error',
            'message' => 'Missing required fields'
        ]);
        return;
    }

    try {
        // Sanitize input
        $email = $conn->real_escape_string($data->email);
        
        // Use prepared statement with email instead of name
        $query = "SELECT id, name, email, password_hash FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

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
                if (!$storeCode) {
                    throw new Exception("Prepare store code failed: " . $conn->error);
                }

                $storeCode->bind_param("iss", $userId, $verificationCode, $expires);
                
                if($storeCode->execute() && sendVerificationEmail($user['email'], $verificationCode)) {
                    echo json_encode([
                        'requiresVerification' => true,
                        'message' => 'Verification code sent to your email',
                        'userId' => $user['id']
                    ]);
                    return;
                } else {
                    throw new Exception("Failed to store code or send email");
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
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'error' => true,
            'type' => 'Server Error',
            'message' => 'An error occurred during login',
            'debug_message' => $e->getMessage() // Remove this in production
        ]);
    }
}

function sendOtp($conn, $data) {
    if (!isset($data->email)) {
        http_response_code(400);
        echo json_encode(['error' => 'Email is required']);
        return;
    }

    $email = $conn->real_escape_string($data->email);

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $verificationCode = sprintf("%06d", random_int(0, 999999));
        $expires = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        // Store verification code in database
        $storeCode = $conn->prepare("INSERT INTO verification_codes (user_id, code, expires_at) VALUES (?, ?, ?)");
        $storeCode->bind_param("iss", $user['id'], $verificationCode, $expires);

        if ($storeCode->execute() && sendVerificationEmail($email, $verificationCode)) {
            echo json_encode(['success' => true, 'message' => 'Verification code sent']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to send verification code']);
        }
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Email not found']);
    }
}

function verifyOtp($conn, $data) {
    if (!isset($data->email) || !isset($data->code) || empty($data->email) || empty($data->code)) {
        http_response_code(400);
        echo json_encode(['error' => 'Email and verification code are required']);
        return;
    }

    $email = $conn->real_escape_string($data->email);
    $code = $conn->real_escape_string($data->code);
    $isPasswordReset = isset($data->isPasswordReset) ? $data->isPasswordReset : false;

    // First get the user ID from email
    $userStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $userStmt->bind_param("s", $email);
    $userStmt->execute();
    $userResult = $userStmt->get_result();

    if ($userResult->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
        return;
    }

    $user = $userResult->fetch_assoc();
    $userId = $user['id'];

    // Verify the code
    $stmt = $conn->prepare("
        SELECT * FROM verification_codes 
        WHERE user_id = ? 
        AND code = ? 
        AND expires_at > NOW() 
        AND used = 0 
        ORDER BY created_at DESC 
        LIMIT 1
    ");
    $stmt->bind_param("is", $userId, $code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Mark code as used
        $updateStmt = $conn->prepare("UPDATE verification_codes SET used = 1 WHERE user_id = ? AND code = ?");
        $updateStmt->bind_param("is", $userId, $code);
        $updateStmt->execute();

        // If this is not a password reset, generate session token
        if (!$isPasswordReset) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
            
            $sessionStmt = $conn->prepare("INSERT INTO user_sessions (user_id, session_token, expires_at) VALUES (?, ?, ?)");
            $sessionStmt->bind_param("iss", $userId, $token, $expires);
            
            if ($sessionStmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'token' => $token,
                    'message' => 'Code verified successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create session']);
            }
        } else {
            // For password reset flow
            echo json_encode([
                'success' => true,
                'message' => 'Code verified successfully'
            ]);
        }
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid or expired verification code']);
    }
}

function resetPassword($conn, $data) {
    if (!isset($data->email) || !isset($data->newPassword)) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        return;
    }

    $email = $conn->real_escape_string($data->email);
    $newPassword = password_hash($data->newPassword, PASSWORD_DEFAULT);

    // Update the user's password
    $updatePasswordStmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
    $updatePasswordStmt->bind_param("ss", $newPassword, $email);
    
    if ($updatePasswordStmt->execute()) {
        // Mark all verification codes for this user as used
        $markCodesUsed = $conn->prepare("
            UPDATE verification_codes vc 
            JOIN users u ON vc.user_id = u.id 
            SET vc.used = 1 
            WHERE u.email = ?
        ");
        $markCodesUsed->bind_param("s", $email);
        $markCodesUsed->execute();
        
        echo json_encode(['success' => true, 'message' => 'Password reset successful']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to reset password']);
    }
}

// Add new verification endpoint
function verifyCode($conn, $data) {
    if(!isset($data->email) || !isset($data->code)) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        return;
    }

    // First get the user ID from email
    $userStmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $userStmt->bind_param("s", $data->email);
    $userStmt->execute();
    $userResult = $userStmt->get_result();

    if($userResult->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'User not found']);
        return;
    }

    $user = $userResult->fetch_assoc();
    $userId = $user['id'];

    $stmt = $conn->prepare("SELECT * FROM verification_codes 
                           WHERE user_id = ? AND code = ? AND expires_at > NOW() 
                           AND used = 0 ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("is", $userId, $data->code);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        // Mark code as used
        $updateStmt = $conn->prepare("UPDATE verification_codes SET used = 1 WHERE user_id = ? AND code = ?");
        $updateStmt->bind_param("is", $userId, $data->code);
        $updateStmt->execute();

        // Generate session token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        // Get client information
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        
        $sessionStmt = $conn->prepare("INSERT INTO user_sessions (user_id, session_token, expires_at, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
        $sessionStmt->bind_param("issss", $userId, $token, $expires, $ip_address, $user_agent);
        
        if($sessionStmt->execute()) {
            // Get user details
            $userStmt = $conn->prepare("SELECT id, name, email FROM users WHERE id = ?");
            $userStmt->bind_param("i", $userId);
            $userStmt->execute();
            $user = $userStmt->get_result()->fetch_assoc();

            echo json_encode([
                'success' => true,
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email']
                ]
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

function verifySession($conn) {
    $headers = apache_request_headers();
    $auth_header = isset($headers['Authorization']) ? $headers['Authorization'] : '';
    
    if (!preg_match('/Bearer\s+(.*)$/i', $auth_header, $matches)) {
        http_response_code(401);
        echo json_encode(['valid' => false, 'error' => 'No token provided']);
        return;
    }

    $token = $matches[1];
    
    // Add token validation and cleanup
    $stmt = $conn->prepare("DELETE FROM user_sessions WHERE expires_at < NOW()");
    $stmt->execute();
    
    $stmt = $conn->prepare("
        SELECT us.*, u.name, u.email, u.id as user_id
        FROM user_sessions us
        JOIN users u ON us.user_id = u.id
        WHERE us.session_token = ? 
        AND us.expires_at > NOW()
        LIMIT 1
    ");
    
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $session = $result->fetch_assoc();
        
        // Update session expiry
        $newExpiry = date('Y-m-d H:i:s', strtotime('+24 hours'));
        $updateStmt = $conn->prepare("UPDATE user_sessions SET expires_at = ? WHERE session_token = ?");
        $updateStmt->bind_param("ss", $newExpiry, $token);
        $updateStmt->execute();
        
        echo json_encode([
            'valid' => true,
            'user' => [
                'id' => $session['user_id'],
                'name' => $session['name'],
                'email' => $session['email']
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode(['valid' => false, 'error' => 'Invalid or expired session']);
    }
}

function handleLogout($conn) {
    $headers = getallheaders();
    $auth_header = isset($headers['Authorization']) ? $headers['Authorization'] : '';
    
    if (preg_match('/Bearer\s+(.*)$/i', $auth_header, $matches)) {
        $token = $matches[1];
        
        $stmt = $conn->prepare("DELETE FROM user_sessions WHERE session_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'No token provided']);
    }
}

$conn->close();
?>