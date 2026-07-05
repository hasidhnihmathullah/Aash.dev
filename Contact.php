<?php
// ═══════════════════════════════════
//  php/contact.php
//  Receives form data → saves to MySQL
// ═══════════════════════════════════

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// Get inputs
$name    = trim(strip_tags($_POST['name']    ?? ''));
$email   = trim(strip_tags($_POST['email']   ?? ''));
$subject = trim(strip_tags($_POST['subject'] ?? ''));
$message = trim(strip_tags($_POST['message'] ?? ''));
$ip      = $_SERVER['REMOTE_ADDR'] ?? '';

// Validate
if (empty($name) || strlen($name) < 2) {
    echo json_encode(['success' => false, 'message' => 'Please enter your name.']);
    exit;
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email.']);
    exit;
}
if (empty($message) || strlen($message) < 5) {
    echo json_encode(['success' => false, 'message' => 'Message is too short.']);
    exit;
}

// Save to database
$db = getDB();

if (!$db) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed. Check config.php']);
    exit;
}

$stmt = $db->prepare(
    "INSERT INTO messages (name, email, subject, message, ip_address)
     VALUES (?, ?, ?, ?, ?)"
);
$stmt->bind_param('sssss', $name, $email, $subject, $message, $ip);

if ($stmt->execute()) {
    $id = $stmt->insert_id;
    $stmt->close();
    $db->close();
    echo json_encode([
        'success' => true,
        'message' => 'Message sent! I will reply within 24 hours.',
        'id'      => $id
    ]);
} else {
    $stmt->close();
    $db->close();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save. Please try again.']);
}
?>