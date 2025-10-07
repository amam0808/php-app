<?php
session_start();
require_once 'lib/db.php';

$mail = isset($_POST['mail']) ? $_POST['mail'] : '';
$pass = isset($_POST['pass']) ? $_POST['pass'] : '';

$msg = '';
$link = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mail && $pass) {
        // mailで検索し、パスワードハッシュで認証
        $sql = "SELECT * FROM users WHERE mail = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $mail);
        $stmt->execute();
        $result = $stmt->get_result();
        $member = $result->fetch_assoc();
        if ($member && password_verify($pass, $member['password'])) {
            $_SESSION['id'] = $member['id'];
            $_SESSION['name'] = $member['username'];
            header('Location: index.php');
            exit();
        } else {
            $msg = 'メールアドレスもしくはパスワードが間違っています。';
            $link = '<a href="login.php">戻る</a>';
        }
    } else {
        $msg = 'メールアドレスとパスワードを入力してください。';
        $link = '<a href="login.php">戻る</a>';
    }
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ログイン</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="login-container">
        <h2>ログイン</h2>
        <?php if ($msg): ?>
            <p><?php echo htmlspecialchars($msg); ?></p>
            <?php echo $link; ?>
        <?php else: ?>
        <form method="post" action="login.php">
            <label for="mail">メールアドレス:</label>
            <input type="email" name="mail" id="mail" required><br>
            <label for="pass">パスワード:</label>
            <input type="password" name="pass" id="pass" required><br>
            <button type="submit">ログイン</button>
        </form>
        <p><a href="signup.php">新規登録はこちら</a></p>
        <?php endif; ?>
    </div>
</body>
</html>
