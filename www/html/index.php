<?php
require_once 'lib/auth.php';
require_once 'lib/db.php';
require_once 'lib/todo_model.php';
require_once 'lib/todo_form.php';
require_once 'lib/todo_list.php';

// if (!is_logged_in()) {
//     // ログインフォーム表示
//     include 'templates/login.html';
//     exit();
// }

create_table($conn);

// ユーザー名とログアウトボタン表示
if (isset($_SESSION['id'])) {
    echo '<div class="header-user">';
    echo 'ユーザー: ' . htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8');
    echo ' <a href="logout.php" class="logout-link">ログアウト</a>';
    echo '</div>';
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["content"]) && !isset($_POST["edit_id"])) {
        add_todo($conn, $_POST["content"], isset($_POST["deadline"]) ? $_POST["deadline"] : "");
        header('Location: index.php');
        exit();
    }
    if (isset($_POST["edit_id"]) && isset($_POST["content"])) {
        update_todo($conn, intval($_POST["edit_id"]), $_POST["content"], isset($_POST["deadline"]) ? $_POST["deadline"] : "");
        header('Location: index.php');
        exit();
    }
    if (isset($_POST["delete_id"])) {
        delete_todo($conn, intval($_POST["delete_id"]));
        header('Location: index.php');
        exit();
    }
}

$edit_content = "";
$edit_id = "";
$edit_deadline = "";

if (isset($_GET["edit"])) {
    $edit_id = intval($_GET["edit"]);
    $result = get_todo($conn, $edit_id);
    if ($row = $result->fetch_assoc()) {
        $edit_content = $row["content"];
        $edit_deadline = $row["deadline"];
    }
}

$order = 'created_at DESC';
if (isset($_GET['sort']) && $_GET['sort'] === 'deadline') {
    $order = 'CASE WHEN deadline IS NULL THEN 1 ELSE 0 END, deadline ASC, created_at DESC';
}

include 'templates/main.html';
?>
