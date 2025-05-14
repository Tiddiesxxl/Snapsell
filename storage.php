<?php
require_once 'config.php';
require_once 'auth_middleware.php';

// Verify user is authenticated
$user_id = verifyToken();
if (!$user_id) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Initialize R2 client
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

$s3 = new S3Client([
    'version' => 'latest',
    'region'  => 'auto',
    'endpoint' => 'https://<account-id>.r2.cloudflarestorage.com',
    'credentials' => [
        'key'    => R2_ACCESS_KEY_ID,
        'secret' => R2_SECRET_ACCESS_KEY,
    ],
]);

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'upload':
        if (!isset($_FILES['file'])) {
            echo json_encode(['success' => false, 'error' => 'No file provided']);
            exit;
        }

        $file = $_FILES['file'];
        $path = $_POST['path'] ?? '';
        $filename = uniqid() . '_' . basename($file['name']);
        $fullPath = trim($path . '/' . $filename, '/');

        try {
            // Upload to R2
            $result = $s3->putObject([
                'Bucket' => R2_BUCKET_NAME,
                'Key'    => $fullPath,
                'Body'   => fopen($file['tmp_name'], 'rb'),
                'ACL'    => 'public-read',
                'ContentType' => $file['type']
            ]);

            echo json_encode([
                'success' => true,
                'url' => $result['ObjectURL']
            ]);
        } catch (AwsException $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        break;

    case 'delete':
        $data = json_decode(file_get_contents('php://input'), true);
        $fileUrl = $data['fileUrl'] ?? '';
        
        if (empty($fileUrl)) {
            echo json_encode(['success' => false, 'error' => 'No file URL provided']);
            exit;
        }

        try {
            // Extract key from URL
            $key = basename($fileUrl);
            
            // Delete from R2
            $s3->deleteObject([
                'Bucket' => R2_BUCKET_NAME,
                'Key'    => $key
            ]);

            echo json_encode(['success' => true]);
        } catch (AwsException $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
}