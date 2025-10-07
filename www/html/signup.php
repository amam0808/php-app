<?php
// signup.php
session_start();
require_once 'lib/db.php';

// mailカラムがなければ追加（重複エラー回避）
$result = $conn->query("SHOW COLUMNS FROM users LIKE 'mail'");
if ($result && $result->num_rows === 0) {
    $conn->query("ALTER TABLE users ADD COLUMN mail VARCHAR(255) UNIQUE AFTER username");
}

$msg = '';
$link = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $mail = isset($_POST['mail']) ? trim($_POST['mail']) : '';
    $pass = isset($_POST['pass']) ? $_POST['pass'] : '';
    $role = 'user';
    if ($username && $mail && $pass) {
        // ユーザー重複チェック
        $sql = "SELECT id FROM users WHERE username = ? OR mail = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $username, $mail);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->fetch_assoc()) {
            $msg = '既に登録済みのユーザー名またはメールアドレスです。';
            $link = '<a href="signup.php">戻る</a>';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, mail, password, role) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssss', $username, $mail, $hash, $role);
            if ($stmt->execute()) {
                $msg = '登録が完了しました。ログインしてください。';
                $link = '<a href="login.php">ログイン</a>';
            } else {
                $msg = '登録に失敗しました。';
                $link = '<a href="signup.php">戻る</a>';
            }
        }
    } else {
        $msg = '全ての項目を入力してください。';
        $link = '<a href="signup.php">戻る</a>';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>新規登録</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="login-container">
        <h2>新規登録</h2>
        <?php if ($msg): ?>
            <p><?php echo htmlspecialchars($msg); ?></p>
            <?php echo $link; ?>
        <?php else: ?>
        <form method="post" action="signup.php">
            <label for="username">ユーザー名:</label>
            <input type="text" name="username" id="username" required><br>
            <label for="mail">メールアドレス:</label>
            <input type="email" name="mail" id="mail" required><br>
            <label for="pass">パスワード:</label>
            <input type="password" name="pass" id="pass" required><br>
            <button type="submit">登録</button>
        </form>
        <p><a href="login.php">ログイン画面へ</a></p>
        <?php endif; ?>
    </div>
</body>
</html>
