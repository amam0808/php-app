<?php
// ToDoのDB操作（CRUD）
function create_table($conn) {
    $conn->query("CREATE TABLE IF NOT EXISTS posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        content TEXT NOT NULL,
        deadline DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
}
function add_todo($conn, $content, $deadline) {
    $content = $conn->real_escape_string($content);
    $deadline = ($deadline !== "") ? $conn->real_escape_string($deadline) : null;
    $conn->query("INSERT INTO posts (content, deadline) VALUES ('$content', " . ($deadline ? "'$deadline'" : "NULL") . ")");
}
function update_todo($conn, $id, $content, $deadline) {
    $content = $conn->real_escape_string($content);
    $deadline = ($deadline !== "") ? $conn->real_escape_string($deadline) : null;
    $conn->query("UPDATE posts SET content='$content', deadline=" . ($deadline ? "'$deadline'" : "NULL") . " WHERE id=$id");
}
function delete_todo($conn, $id) {
    $conn->query("DELETE FROM posts WHERE id=$id");
}
function get_todos($conn, $order) {
    return $conn->query("SELECT * FROM posts ORDER BY $order");
}
function get_todo($conn, $id) {
    return $conn->query("SELECT content, deadline FROM posts WHERE id=$id");
}
