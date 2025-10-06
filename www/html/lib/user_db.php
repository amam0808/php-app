<?php
// user_db.php
function get_users() {
    // 本来はDB管理。ここでは仮の配列
    return [
        ['id' => 1, 'username' => 'admin', 'password' => 'pass123', 'role' => 'admin'],
        ['id' => 2, 'username' => 'user', 'password' => 'userpass', 'role' => 'user']
    ];
}

function find_user($username) {
    foreach (get_users() as $user) {
        if ($user['username'] === $username) return $user;
    }
    return null;
}
