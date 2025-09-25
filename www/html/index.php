<?php
require_once "db.php";
require_once "todo_model.php";
require_once "todo_form.php";
require_once "todo_list.php";

// テーブル作成
create_table($conn);

// 投稿処理
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["content"]) && $_POST["content"] !== "" && (!isset($_POST["edit_id"]) || $_POST["edit_id"] === "")) {
        add_todo($conn, $_POST["content"], $_POST["deadline"] ?? "");
        header('Location: index.php');
        exit();
    }
    if (isset($_POST["edit_id"]) && $_POST["edit_id"] !== "" && isset($_POST["content"]) && $_POST["content"] !== "") {
        update_todo($conn, intval($_POST["edit_id"]), $_POST["content"], $_POST["deadline"] ?? "");
        header('Location: index.php');
        exit();
    }
    if (isset($_POST["delete_id"]) && $_POST["delete_id"] !== "") {
        delete_todo($conn, intval($_POST["delete_id"]));
        header('Location: index.php');
        exit();
    }
}

// 編集フォーム表示
$edit_content = "";
$edit_id = "";
$edit_deadline = "";
if (isset($_GET["edit"]) && $_GET["edit"] !== "") {
    $edit_id = intval($_GET["edit"]);
    $result = get_todo($conn, $edit_id);
    if ($row = $result->fetch_assoc()) {
        $edit_content = $row["content"];
        $edit_deadline = $row["deadline"];
    }
}

// 並び替え機能
$order = 'created_at DESC';
if (isset($_GET['sort']) && $_GET['sort'] === 'deadline') {
    $order = 'CASE WHEN deadline IS NULL THEN 1 ELSE 0 END, deadline ASC, created_at DESC';
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ToDoアプリ</title>
    <style>
        body {
            background: #f9f6f2;
            color: #5c4326;
            font-family: 'Segoe UI', 'Hiragino Sans', 'Meiryo', sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1000px;
            margin: 40px auto;
            background: #fffaf5;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(180, 140, 90, 0.08);
            padding: 32px 28px 24px 28px;
        }
        h3 {
            color: #b89b72;
            margin-top: 0;
            letter-spacing: 1px;
        }
        form {
            margin-bottom: 18px;
        }
        textarea, input[type="date"] {
            border: 1px solid #e5d6c2;
            border-radius: 6px;
            background: #fff;
            padding: 8px;
            font-size: 1em;
            color: #5c4326;
            margin-bottom: 8px;
            width: 100%;
            box-sizing: border-box;
        }
        button {
            background: #e5d6c2;
            color: #5c4326;
            border: none;
            border-radius: 6px;
            padding: 8px 20px;
            font-size: 1em;
            cursor: pointer;
            margin-right: 8px;
            transition: background 0.2s;
        }
        button:hover {
            background: #f3e7d3;
        }
        a {
            color: #b89b72;
            text-decoration: none;
            margin-left: 8px;
        }
        a:hover {
            text-decoration: underline;
        }
        .sort-links {
            margin-bottom: 18px;
            font-size: 0.98em;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            background: #fff;
            border: 1px solid #f3e7d3;
            border-radius: 8px;
            margin-bottom: 12px;
            padding: 14px 12px 10px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(180, 140, 90, 0.04);
        }
        .task-content {
            flex: 1;
            min-width: 0;
        }
        .task-actions {
            margin-left: 12px;
            white-space: nowrap;
        }
        .remind {
            margin-left: 8px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container" style="display: flex; gap: 32px; align-items: flex-start;">
        <div style="flex: 1; min-width: 220px;">
            <h3>ToDo追加</h3>
            <?php render_todo_form($edit_content, $edit_deadline, $edit_id); ?>
        </div>
        <div style="flex: 2; min-width: 300px;">
            <h3>ToDoリスト</h3>
            <div class="sort-links">
                <a href="index.php?sort=deadline">締め切り近い順</a> |
                <a href="index.php?sort=created_at">作成日が新しい順</a>
            </div>
            <div style="max-height: 400px; overflow-y: auto; background: #fff; border-radius: 10px; border: 1px solid #f3e7d3; padding: 8px 0;">
                <?php
                $today = date('Y-m-d');
                $result = get_todos($conn, $order);
                render_todo_list($result, $today);
                $conn->close();
                ?>
            </div>
        </div>
    </div>
</body>
</html>