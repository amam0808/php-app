<?php
require_once 'lib/auth.php';
require_once 'lib/db.php';
require_once 'lib/todo_model.php';
require_once 'lib/todo_form.php';
require_once 'lib/todo_list.php';

if (!is_logged_in()) {
    // ログインフォーム表示
    include 'templates/login.html';
    exit();
}

create_table($conn);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!empty($_POST["content"]) && empty($_POST["edit_id"])) {
        add_todo($conn, $_POST["content"], $_POST["deadline"] ?? "");
        header('Location: index.php');
        exit();
    }
    if (!empty($_POST["edit_id"]) && !empty($_POST["content"])) {
        update_todo($conn, intval($_POST["edit_id"]), $_POST["content"], $_POST["deadline"] ?? "");
        header('Location: index.php');
        exit();
    }
    if (!empty($_POST["delete_id"])) {
        delete_todo($conn, intval($_POST["delete_id"]));
        header('Location: index.php');
        exit();
    }
}

$edit_content = "";
$edit_id = "";
$edit_deadline = "";

if (!empty($_GET["edit"])) {
    $edit_id = intval($_GET["edit"]);
    $result = get_todo($conn, $edit_id);
    if ($row = $result->fetch_assoc()) {
        $edit_content = $row["content"];
        $edit_deadline = $row["deadline"];
    }
}

$order = 'created_at DESC';
if (!empty($_GET['sort']) && $_GET['sort'] === 'deadline') {
    $order = 'CASE WHEN deadline IS NULL THEN 1 ELSE 0 END, deadline ASC, created_at DESC';
}

include 'templates/main.html';
?>
