<?php
function render_todo_form($edit_content, $edit_deadline, $edit_id) {
?>
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
<?php
}
?>
