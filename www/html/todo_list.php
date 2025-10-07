<?php
// ToDoリスト表示部品
function render_todo_list($result, $today) {
?>
<ul style="margin:0;">
<?php
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
?>
</ul>
<?php
}
