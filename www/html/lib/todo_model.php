<?php
function create_table($conn) {
    $conn->query("CREATE TABLE IF NOT EXISTS posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        content TEXT NOT NULL,
        deadline DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
}

function add_todo($conn, $content, $deadline = null) {
    $content = $conn->real_escape_string($content);
    $deadline_sql = $deadline ? "'".$conn->real_escape_string($deadline)."'" : "NULL";
    $conn->query("INSERT INTO posts (content, deadline) VALUES ('$content', $deadline_sql)");
}

function update_todo($conn, $id, $content, $deadline = null) {
    $content = $conn->real_escape_string($content);
    $deadline_sql = $deadline ? "'".$conn->real_escape_string($deadline)."'" : "NULL";
    $conn->query("UPDATE posts SET content='$content', deadline=$deadline_sql WHERE id=$id");
}

function delete_todo($conn, $id) {
    $conn->query("DELETE FROM posts WHERE id=$id");
}

function get_todo($conn, $id) {
    return $conn->query("SELECT * FROM posts WHERE id=$id");
}

function get_todos($conn, $order) {
    return $conn->query("SELECT * FROM posts ORDER BY $order");
}
?>
