<?php
session_start();
require_once 'config/database.php';
require_once 'models/UserModel.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = getDBConnection();
    $userModel = new UserModel($db);

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu.";
    } else {
        $user = $userModel->login($username, $password);
        if ($user) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['full_name'] = $user['full_name'] ?? $user['username'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Tên đăng nhập hoặc mật khẩu không chính xác.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng nhập | HST-Homestay</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏡</text></svg>">
</head>
<body class="auth-body">
  <div class="auth-container">
    <a href="#" class="brand-logo">HST<span>Homestay</span></a>
    <h2>Chào mừng trở lại!</h2>
    <p class="auth-subtitle">Đăng nhập để quản lý lịch đặt phòng của bạn</p>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST" novalidate>
      <div class="form-group">
        <label for="username">Tên đăng nhập</label>
        <input type="text" id="username" name="username"
               placeholder="Nhập tên đăng nhập..."
               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
               required autocomplete="username" autofocus>
      </div>
      <div class="form-group">
        <label for="password">Mật khẩu</label>
        <input type="password" id="password" name="password"
               placeholder="Nhập mật khẩu..."
               required autocomplete="current-password">
      </div>
      <button type="submit" class="btn btn-primary btn-block">
         Đăng nhập
      </button>
    </form>

    <hr class="auth-divider">
    <div class="auth-footer">
      <span>Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></span>
    </div>
  </div>
</body>
</html>
