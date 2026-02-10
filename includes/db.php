<?php
// includes/db.php - CORRECTED VERSION
$host = "localhost";
$user = "root";
$password = "";
$database = "music_shop";

// Create connection
$conn = mysqli_connect($host, $user, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8");

// SIMPLE HELPER FUNCTIONS
function query($sql) {
    global $conn;
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        error_log("Query failed: " . mysqli_error($conn) . " | SQL: " . $sql);
        return false;
    }
    return $result;
}

function fetch_all($result) {
    if (!$result) return [];
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    return $rows;
}

function fetch_one($result) {
    if (!$result) return null;
    return mysqli_fetch_assoc($result);
}

function escape($str) {
    global $conn;
    return mysqli_real_escape_string($conn, $str);
}

function last_id() {
    global $conn;
    return mysqli_insert_id($conn);
}

// Debug function
function debug($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}
?>