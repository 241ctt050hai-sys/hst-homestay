<?php
session_start();
require_once 'config/database.php';
require_once 'models/UserModel.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error   = '';
$success = '';
$old     = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db        = getDBConnection();
    $userModel = new UserModel($db);

    $username  = trim($_POST['username']  ?? '');
    $email     = trim($_POST['email']     ?? '');
    $password  = $_POST['password']       ?? '';
    $confirm   = $_POST['confirm']        ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    $phone     = trim($_POST['phone']     ?? '');
    $old       = compact('username', 'email', 'full_name', 'phone');

    // Validate
    if (empty($username) || empty($email) || empty($password)) {
        $error = "Vui lòng điền đầy đủ các trường bắt buộc.";
    } elseif (strlen($username) < 4 || strlen($username) > 30) {
        $error = "Tên đăng nhập phải từ 4–30 ký tự.";
    } elseif (!preg_match('/^[a-zA-Z0-9_.]+$/', $username)) {
        $error = "Tên đăng nhập chỉ được chứa chữ cái, số, dấu chấm và gạch dưới.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Địa chỉ email không hợp lệ.";
    } elseif (strlen($password) < 6) {
        $error = "Mật khẩu phải có ít nhất 6 ký tự.";
    } elseif ($password !== $confirm) {
        $error = "Xác nhận mật khẩu không khớp.";
    } elseif ($userModel->usernameExists($username)) {
        $error = "Tên đăng nhập này đã được sử dụng.";
    } elseif ($userModel->emailExists($email)) {
        $error = "Email này đã được đăng ký.";
    } else {
        try {
            $userModel->register($username, $password, $email, $full_name, $phone);
            $success = "Tài khoản đã được tạo thành công! Bạn có thể đăng nhập ngay.";
            $old = [];
        } catch (Exception $e) {
            $error = "Đã có lỗi xảy ra. Vui lòng thử lại.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đăng ký | HST-Homestay</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏡</text></svg>">
</head>
<body class="auth-body">
  <div class="auth-container">
    <a href="login.php" class="brand-logo">HST<span>Homestay</span></a>
    <h2>Tạo tài khoản mới</h2>
    <p class="auth-subtitle">Tham gia để nhận những dịch vụ lưu trú tốt nhất</p>

    <?php if ($error): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if (!$success): ?>
    <form action="register.php" method="POST" novalidate>
      <div class="form-row">
        <div class="form-group col">
          <label for="username">Tên đăng nhập <span style="color:var(--danger)">*</span></label>
          <input type="text" id="username" name="username"
                 placeholder="vd: nguyenvana"
                 value="<?php echo htmlspecialchars($old['username'] ?? ''); ?>"
                 required autocomplete="username" autofocus>
        </div>
        <div class="form-group col">
          <label for="full_name">Họ và tên</label>
          <input type="text" id="full_name" name="full_name"
                 placeholder="Nguyễn Văn A"
                 value="<?php echo htmlspecialchars($old['full_name'] ?? ''); ?>">
        </div>
      </div>

      <div class="form-group">
        <label for="email">Địa chỉ Email <span style="color:var(--danger)">*</span></label>
        <input type="email" id="email" name="email"
               placeholder="example@email.com"
               value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>"
               required autocomplete="email">
      </div>

      <div class="form-group">
        <label for="phone">Số điện thoại</label>
        <input type="tel" id="phone" name="phone"
               placeholder="0901 234 567"
               value="<?php echo htmlspecialchars($old['phone'] ?? ''); ?>">
      </div>

      <div class="form-row">
        <div class="form-group col">
          <label for="password">Mật khẩu <span style="color:var(--danger)">*</span></label>
          <input type="password" id="password" name="password"
                 placeholder="Tối thiểu 6 ký tự" required>
        </div>
        <div class="form-group col">
          <label for="confirm">Xác nhận mật khẩu <span style="color:var(--danger)">*</span></label>
          <input type="password" id="confirm" name="confirm"
                 placeholder="Nhập lại mật khẩu" required>
        </div>
      </div>

      <button type="submit" class="btn btn-success btn-block">
        Đăng ký tài khoản
      </button>
    </form>
    <?php endif; ?>

    <hr class="auth-divider">
    <div class="auth-footer">
      <span>Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a></span>
    </div>
  </div>
</body>
</html>
