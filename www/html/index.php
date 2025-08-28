<?php
$servername = "db";
$username = "appuser";
$password = "apppass";
$dbname = "appdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("DB接続失敗: " . $conn->connect_error);
}

// 投稿テーブル作成（初回のみ）
$conn->query("CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// 投稿処理
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 新規投稿
    if (!empty($_POST["content"]) && empty($_POST["edit_id"])) {
        $content = $conn->real_escape_string($_POST["content"]);
        $conn->query("INSERT INTO posts (content) VALUES ('$content')");
        // echo "<p>投稿しました！</p>";
    }
    // 編集
    if (!empty($_POST["edit_id"]) && !empty($_POST["content"])) {
        $edit_id = intval($_POST["edit_id"]);
        $content = $conn->real_escape_string($_POST["content"]);
        $conn->query("UPDATE posts SET content='$content' WHERE id=$edit_id");
        // echo "<p>編集しました！</p>";
    }
    // 削除
    if (!empty($_POST["delete_id"])) {
        $delete_id = intval($_POST["delete_id"]);
        $conn->query("DELETE FROM posts WHERE id=$delete_id");
        // echo "<p>削除しました！</p>";
    }
}

// 編集フォーム表示
$edit_content = "";
$edit_id = "";
if (!empty($_GET["edit"])) {
    $edit_id = intval($_GET["edit"]);
    $result = $conn->query("SELECT content FROM posts WHERE id=$edit_id");
    if ($row = $result->fetch_assoc()) {
        $edit_content = $row["content"];
    }
}
?>
<h3>ToDo</h3>
<form method="post">
    <textarea name="content" rows="4" cols="40" placeholder="内容"><?php echo htmlspecialchars($edit_content); ?></textarea><br>
    <?php if ($edit_id): ?>
        <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
        <button type="submit">編集</button>
        <a href="index.php">キャンセル</a>
    <?php else: ?>
        <button type="submit">追加</button>
    <?php endif; ?>
</form>
<hr>
<h3>ToDoリスト</h3>
<ul>
<?php
$result = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
while ($row = $result->fetch_assoc()) {
    echo "<li>" . htmlspecialchars($row["content"]) . " <small>(" . $row["created_at"] . ")</small> ";
    echo '<form method="post" style="display:inline;">
            <input type="hidden" name="delete_id" value="' . $row["id"] . '">
            <button type="submit" onclick="return confirm(\'本当に削除しますか？\');">削除</button>
          </form> ';
    echo '<a href="index.php?edit=' . $row["id"] . '">編集</a>';
    echo "</li>";
}
$conn->close();
?>
</ul>