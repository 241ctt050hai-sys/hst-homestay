<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hồ sơ cá nhân | HST-Homestay</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏡</text></svg>">
</head>
<body>
<div class="profile-wrapper">
  <a href="index.php" class="back-nav">← Quay lại Dashboard</a>

  <!-- Profile Header -->
  <div class="profile-card">
    <div class="profile-header">
      <div class="avatar"><?php echo strtoupper(mb_substr($user['full_name'] ?: $user['username'], 0, 1)); ?></div>
      <div>
        <h2><?php echo htmlspecialchars($user['full_name'] ?: $user['username']); ?></h2>
        <p>@<?php echo htmlspecialchars($user['username']); ?> · Thành viên từ <?php echo date('d/m/Y', strtotime($user['created_at'])); ?></p>
      </div>
    </div>

    <div class="profile-body">
      <?php if (!empty($profile_error)): ?>
        <div class="alert alert-danger">⚠️ <?php echo htmlspecialchars($profile_error); ?></div>
      <?php endif; ?>
      <?php if (!empty($profile_success)): ?>
        <div class="alert alert-success">✅ <?php echo htmlspecialchars($profile_success); ?></div>
      <?php endif; ?>

      <p class="form-section-title">Thông tin cá nhân</p>

      <form action="index.php?action=profile" method="POST" novalidate>
        <input type="hidden" name="update_profile" value="1">
        <div class="form-row">
          <div class="form-group col">
            <label>Tên đăng nhập</label>
            <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled style="opacity:.6;cursor:not-allowed;">
          </div>
          <div class="form-group col">
            <label for="full_name">Họ và tên</label>
            <input type="text" id="full_name" name="full_name"
                   placeholder="Nguyễn Văn A"
                   value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col">
            <label for="email">Email <span style="color:var(--danger)">*</span></label>
            <input type="email" id="email" name="email" required
                   value="<?php echo htmlspecialchars($user['email']); ?>">
          </div>
          <div class="form-group col">
            <label for="phone">Số điện thoại</label>
            <input type="tel" id="phone" name="phone"
                   placeholder="0901 234 567"
                   value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
          </div>
        </div>
        <div class="form-actions">
          <button type="submit" class="btn btn-primary">💾 Lưu thông tin</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Change Password -->
  <div class="profile-card">
    <div class="profile-body">
      <p class="form-section-title">Đổi mật khẩu</p>

      <?php if (!empty($pw_error)): ?>
        <div class="alert alert-danger">⚠️ <?php echo htmlspecialchars($pw_error); ?></div>
      <?php endif; ?>

      <form action="index.php?action=profile" method="POST" novalidate>
        <input type="hidden" name="change_password" value="1">
        <div class="form-group">
          <label for="current_pw">Mật khẩu hiện tại <span style="color:var(--danger)">*</span></label>
          <input type="password" id="current_pw" name="current_pw"
                 placeholder="Nhập mật khẩu hiện tại..." required>
        </div>
        <div class="form-row">
          <div class="form-group col">
            <label for="new_pw">Mật khẩu mới <span style="color:var(--danger)">*</span></label>
            <input type="password" id="new_pw" name="new_pw"
                   placeholder="Tối thiểu 6 ký tự" required>
          </div>
          <div class="form-group col">
            <label for="confirm_pw">Xác nhận mật khẩu mới <span style="color:var(--danger)">*</span></label>
            <input type="password" id="confirm_pw" name="confirm_pw"
                   placeholder="Nhập lại mật khẩu mới" required>
          </div>
        </div>
        <div class="form-actions">
          <button type="submit" class="btn btn-warning">🔒 Đổi mật khẩu</button>
        </div>
      </form>
    </div>
  </div>

</div>
</body>
</html>
