<?php
$servername = "db";
$username = "appuser";
$password = "apppass";
$dbname = "appdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("DB接続失敗: " . $conn->connect_error);
}

// 開発用: テーブルを一度削除して作り直す
// $conn->query("DROP TABLE IF EXISTS posts");
// 投稿テーブル作成（初回のみ） 締め切りカラム追加
$conn->query("CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL,
    deadline DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// 投稿処理
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 新規投稿
    if (isset($_POST["content"]) && $_POST["content"] !== "" && (!isset($_POST["edit_id"]) || $_POST["edit_id"] === "")) {
        $content = $conn->real_escape_string($_POST["content"]);
        $deadline = (isset($_POST["deadline"]) && $_POST["deadline"] !== "") ? $conn->real_escape_string($_POST["deadline"]) : null;
        $conn->query("INSERT INTO posts (content, deadline) VALUES ('$content', " . ($deadline ? "'$deadline'" : "NULL") . ")");
        header('Location: index.php');
        exit();
    }
    // 編集
    if (isset($_POST["edit_id"]) && $_POST["edit_id"] !== "" && isset($_POST["content"]) && $_POST["content"] !== "") {
        $edit_id = intval($_POST["edit_id"]);
        $content = $conn->real_escape_string($_POST["content"]);
        $deadline = (isset($_POST["deadline"]) && $_POST["deadline"] !== "") ? $conn->real_escape_string($_POST["deadline"]) : null;
        $conn->query("UPDATE posts SET content='$content', deadline=" . ($deadline ? "'$deadline'" : "NULL") . " WHERE id=$edit_id");
        header('Location: index.php');
        exit();
    }
    // 削除
    if (isset($_POST["delete_id"]) && $_POST["delete_id"] !== "") {
        $delete_id = intval($_POST["delete_id"]);
        $conn->query("DELETE FROM posts WHERE id=$delete_id");
        header('Location: index.php');
        exit();
    }
}

// 編集フォーム表示
$edit_content = "";
$edit_id = "";
$edit_deadline = "";
if (!empty($_GET["edit"])) {
    $edit_id = intval($_GET["edit"]);
    $result = $conn->query("SELECT content, deadline FROM posts WHERE id=$edit_id");
    if ($row = $result->fetch_assoc()) {
        $edit_content = $row["content"];
        $edit_deadline = $row["deadline"];
    }
}

// 並び替え機能の追加
$order = 'created_at DESC'; // デフォルトは作成日が新しい順
if (!empty($_GET['sort']) && $_GET['sort'] === 'deadline') {
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
            <form method="post">
                <textarea name="content" rows="4" cols="40" placeholder="内容" required><?php echo htmlspecialchars($edit_content); ?></textarea><br>
                締め切り: <input type="date" name="deadline" value="<?php echo htmlspecialchars($edit_deadline); ?>"><br>
                <?php if ($edit_id): ?>
                    <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
                    <button type="submit">編集</button>
                    <a href="index.php">キャンセル</a>
                <?php else: ?>
                    <button type="submit">追加</button>
                <?php endif; ?>
            </form>
        </div>
        <div style="flex: 2; min-width: 300px;">
            <h3>ToDoリスト</h3>
            <div class="sort-links">
                <a href="index.php?sort=deadline">締め切り近い順</a> |
                <a href="index.php?sort=created_at">作成日が新しい順</a>
            </div>
            <div style="max-height: 400px; overflow-y: auto; background: #fff; border-radius: 10px; border: 1px solid #f3e7d3; padding: 8px 0;">
                <ul style="margin:0;">
                    <?php
                    $today = date('Y-m-d');
                    $result = $conn->query("SELECT * FROM posts ORDER BY $order");
                    while ($row = $result->fetch_assoc()) {
                        $deadline = isset($row["deadline"]) ? $row["deadline"] : null;
                        $remind = "";
                        if ($deadline) {
                            if ($deadline < $today) {
                                $remind = '<span class="remind" style="color:#d9a78c;">【期限切れ】</span>';
                            } elseif ($deadline == $today) {
                                $remind = '<span class="remind" style="color:#e5b97a;">【本日締切】</span>';
                            } elseif ((strtotime($deadline) - strtotime($today)) <= 86400 * 3) {
                                $remind = '<span class="remind" style="color:#8ab6a9;">【締切間近】</span>';
                            }
                        }
                        echo "<li>";
                        echo '<div class="task-content">' . htmlspecialchars($row["content"]);
                        if ($deadline) {
                            echo " <small>（締切: " . htmlspecialchars($deadline) . "）</small> ";
                        }
                        echo $remind . "</div>";
                        echo '<div class="task-actions">';
                        echo '<form method="post" style="display:inline;">
                                <input type="hidden" name="delete_id" value="' . $row["id"] . '">
                                <button type="submit" onclick="return confirm(\'本当に削除しますか？\');">削除</button>
                              </form> ';
                        echo '<a href="index.php?edit=' . $row["id"] . '">編集</a>';
                        echo "</div></li>";
                    }
                    $conn->close();
                    ?>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>