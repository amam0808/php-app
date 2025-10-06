<?php
require_once 'lib/auth.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if (login($username, $password)) {
        header('Location: index.php');
        exit();
    } else {
        $error = 'ユーザー名またはパスワードが違います';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン | ToDoリスト</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="login-container">
        <h2>ログイン</h2>
        <?php if (!empty($error)) echo '<p style="color:red;">'.$error.'</p>'; ?>
        <form method="post" action="login.php">
            <label for="username">ユーザー名:</label>
            <input type="text" name="username" id="username" required><br>
            <label for="password">パスワード:</label>
            <input type="password" name="password" id="password" required><br>
            <button type="submit">ログイン</button>
        </form>
        <p>例: admin / pass123</p>
    </div>
</body>
</html>
