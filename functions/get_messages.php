<?php
// Establish a database connection (replace with your database credentials)
$mysqli = new mysqli("localhost", "root", "", "sharkawi_muc");
// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Retrieve messages from the database based on the specified category
$channel_id = isset($_GET['channel_id']) ? $_GET['channel_id'] : 'akjshdksajdlksad';
$since = isset($_GET['since']) ? $_GET['since'] : null;

// Modify the SQL query to include a condition for the timestamp
$query = "SELECT sender, channel_id, message, timestamp FROM messages WHERE channel_id = ?";
if ($since) {
    $query .= " AND timestamp > ?";
}

$stmt = $mysqli->prepare($query);

if (!$stmt) {
    die("Error in preparing statement: " . $mysqli->error);
}

// If the 'since' parameter is provided, bind it to the SQL statement
if ($since) {
    $stmt->bind_param("ss", $channel_id, $since);
} else {
    $stmt->bind_param("s", $channel_id);
}

$stmt->execute();

if ($stmt->error) {
    die("Error in executing statement: " . $stmt->error);
}

$stmt->bind_result($sender, $channel_id, $message, $timestamp);

$messages = array();

while ($stmt->fetch()) {
    $messages[] = array(
        'sender' => $sender,
        'channel_id' => $channel_id,
        'message' => $message,
        'timestamp' => $timestamp,
    );
}

$stmt->close();
$mysqli->close();

header('Content-Type: application/json');
echo json_encode($messages);
