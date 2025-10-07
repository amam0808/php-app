<?php
// user_db.php
require_once __DIR__ . '/db.php';

function get_users() {
    global $conn;
    $users = [];
    $sql = "SELECT id, username, mail, password, role FROM users";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    return $users;
}

function find_user($username) {
    global $conn;
    $sql = "SELECT id, username, mail, password, role FROM users WHERE username = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row;
    }
    return null;
}
