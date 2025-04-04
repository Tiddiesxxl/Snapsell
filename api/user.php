<?php
require_once 'db_connect.php';

// Handle CORS
header('Access-Control-Allow-Origin: http://localhost:3000'); // Match your frontend
header('Access-Control-Allow-Methods: GET, POST, OPTIONS'); // Include any other HTTP methods you use
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Credentials: true');


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Session verification function
function verifySession() {
    $headers = getallheaders();
    $auth_header = isset($headers['Authorization']) ? $headers['Authorization'] : '';
    
    if (!preg_match('/Bearer\s+(.*)$/i', $auth_header, $matches)) {
        return null;
    }

    $token = $matches[1];
    
    global $conn;
    
    // Clean up expired sessions
    $cleanup = $conn->prepare("DELETE FROM user_sessions WHERE expires_at < NOW()");
    $cleanup->execute();
    
    // Get active session with user data
    $stmt = $conn->prepare("
        SELECT u.id, u.name, u.email, u.user_type
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
        $userData = $result->fetch_assoc();
        
        // Update session expiry
        $newExpiry = date('Y-m-d H:i:s', strtotime('+24 hours'));
        $updateStmt = $conn->prepare("UPDATE user_sessions SET expires_at = ? WHERE session_token = ?");
        $updateStmt->bind_param("ss", $newExpiry, $token);
        $updateStmt->execute();
        
        return $userData;
    }
    
    return null;
}

$action = $_GET['action'] ?? '';


switch ($action) {
    case 'get_profile':
        // Get user from session
        $user = verifySession();
        
        if (!$user) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }
        
        // Get user profile data with error handling
        try {
            $stmt = $conn->prepare("
                SELECT u.*, up.profile_picture, up.bio, up.phone_number 
                FROM users u 
                LEFT JOIN user_profiles up ON u.id = up.user_id 
                WHERE u.id = ?
            ");
            
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            $stmt->bind_param('i', $user['id']);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            $userData = $result->fetch_assoc();
            
            if (!$userData) {
                throw new Exception("No user data found");
            }
            
            // Ensure we always have values for required fields
            $userData = array_merge([
                'name' => '',
                'email' => '',
                'user_type' => 'regular',
                'profile_picture' => null,
                'bio' => '',
                'phone_number' => ''
            ], $userData);
            
            echo json_encode([
                'success' => true,
                'user' => $userData
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        break;

        case 'change_password':
            $current_password = $data['current_password'] ?? '';
            $new_password = $data['new_password'] ?? '';
            
            if (empty($current_password) || empty($new_password)) {
                echo json_encode(['success' => false, 'error' => 'Missing required fields']);
                exit;
            }
            
            // Verify current password
            $stmt = $conn->prepare("SELECT password_hash FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            if (!password_verify($current_password, $user['password_hash'])) {
                echo json_encode(['success' => false, 'error' => 'Current password is incorrect']);
                exit;
            }
            
            // Update password
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->bind_param("si", $new_password_hash, $user_id);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to update password']);
            }
            break;
        
        case 'get_account_info':
            $stmt = $conn->prepare("SELECT created_at, last_login FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $account_info = $result->fetch_assoc();
            
            echo json_encode(['success' => true, 'account_info' => $account_info]);
            break;
        
        case 'deactivate_account':
            $stmt = $conn->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            
            if ($stmt->execute()) {
                // Clear user sessions
                $stmt = $conn->prepare("DELETE FROM user_sessions WHERE user_id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Failed to deactivate account']);
            }
            break;
        
        case 'delete_account':
            // Start transaction
            $conn->begin_transaction();
            
            try {
                // Delete user sessions
                $stmt = $conn->prepare("DELETE FROM user_sessions WHERE user_id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                
                // Delete user profile
                $stmt = $conn->prepare("DELETE FROM user_profiles WHERE user_id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                
                // Delete user
                $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                
                $conn->commit();
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                $conn->rollback();
                echo json_encode(['success' => false, 'error' => 'Failed to delete account']);
            }
            break; 

    case 'update_profile':
        $user = verifySession();
        
        if (!$user) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        // Update users table
        $stmt = $conn->prepare("
            UPDATE users 
            SET name = ?, email = ? 
            WHERE id = ?
        ");
        $stmt->bind_param('ssi', $data['name'], $data['email'], $user['id']);
        $stmt->execute();

        // Update or insert user_profiles
        $stmt = $conn->prepare("
            INSERT INTO user_profiles (user_id, phone_number, bio) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE phone_number = ?, bio = ?
        ");
        $stmt->bind_param('issss', 
            $user['id'], 
            $data['phone'], 
            $data['bio'], 
            $data['phone'], 
            $data['bio']
        );
        $stmt->execute();

        echo json_encode(['success' => true]);
        break;

    case 'update_profile_image':
        $user = verifySession();
        
        if (!$user) {
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            exit;
        }

        if (!isset($_FILES['profile_image'])) {
            echo json_encode(['success' => false, 'error' => 'No image uploaded']);
            exit;
        }

        $file = $_FILES['profile_image'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileError = $file['error'];

        // Validate file
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($fileTmpName);
        
        if (!in_array($fileType, $allowedTypes)) {
            echo json_encode(['success' => false, 'error' => 'Invalid file type']);
            exit;
        }

        // Generate unique filename
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = uniqid('profile_') . '.' . $extension;
        $uploadPath = '../uploads/profiles/' . $newFileName;

        // Create directory if it doesn't exist
        if (!file_exists('../uploads/profiles')) {
            mkdir('../uploads/profiles', 0777, true);
        }

        // Move file to uploads directory
        if (move_uploaded_file($fileTmpName, $uploadPath)) {
            // Update database
            $stmt = $conn->prepare("
                INSERT INTO user_profiles (user_id, profile_picture) 
                VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE profile_picture = ?
            ");
            $relativePath = 'uploads/profiles/' . $newFileName;
            $stmt->bind_param('iss', $user['id'], $relativePath, $relativePath);
            
            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true, 
                    'profile_picture' => $relativePath
                ]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Database error']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to upload file']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
}
?>
