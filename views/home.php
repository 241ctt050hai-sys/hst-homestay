<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | HST-Homestay</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏡</text></svg>">
</head>
<body>
<div class="dashboard-wrapper">

  <!-- HEADER -->
  <header class="main-header">
    <div class="header-brand">
      <div class="brand-logo">HST<span>Homestay</span></div>
      <p class="welcome-text">Xin chào, <strong><?php echo htmlspecialchars($display_name ?? $_SESSION['username'] ?? 'Khách'); ?></strong> 👋</p>
    </div>
    <div class="header-actions">
      <a href="index.php?action=add" class="btn btn-primary">
         Đặt phòng mới
      </a>
      <a href="index.php?action=profile" class="btn btn-outline">
        👤 Hồ sơ
      </a>
      <a href="index.php?action=logout" class="btn btn-outline-danger"
         onclick="return confirm('Bạn muốn đăng xuất khỏi hệ thống?')">
        Đăng xuất
      </a>
    </div>
  </header>

  <!-- ALERT MESSAGES -->
  <?php if (isset($_GET['status'])): ?>
    <?php $statuses = [
      'success_add'    => ['success', '🎉 Đặt phòng thành công! Chúng tôi đã ghi nhận lịch lưu trú của bạn.'],
      'success_edit'   => ['success', '💾 Cập nhật lịch đặt phòng thành công!'],
      'success_delete' => ['success', '🗑️ Đã hủy đặt phòng thành công.'],
      'profile_saved'  => ['success', '✅ Thông tin hồ sơ đã được lưu.'],
      'pw_changed'     => ['success', '🔒 Mật khẩu đã được thay đổi thành công.'],
    ]; ?>
    <?php if (isset($statuses[$_GET['status']])): ?>
      <?php [$type, $msg] = $statuses[$_GET['status']]; ?>
      <div class="alert alert-<?php echo $type; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>
  <?php endif; ?>

  <!-- STAT CARDS -->
  <?php
  // Đảm bảo $stats luôn tồn tại dù controller có truyền hay không
  $stats = $stats ?? ['total' => 0, 'upcoming' => 0, 'active' => 0, 'past' => 0];
  ?>
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon purple">🏨</div>
      <div class="stat-info">
        <div class="stat-num"><?php echo (int)$stats['total']; ?></div>
        <div class="stat-label">Tổng đặt phòng</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon blue">📅</div>
      <div class="stat-info">
        <div class="stat-num"><?php echo (int)$stats['upcoming']; ?></div>
        <div class="stat-label">Sắp diễn ra</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon green">✅</div>
      <div class="stat-info">
        <div class="stat-num"><?php echo (int)$stats['active']; ?></div>
        <div class="stat-label">Đang lưu trú</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon gray">📋</div>
      <div class="stat-info">
        <div class="stat-num"><?php echo (int)$stats['past']; ?></div>
        <div class="stat-label">Đã hoàn thành</div>
      </div>
    </div>
  </div>

  <!-- TABLE SECTION -->
  <main>
    <div class="section-title-bar">
      <h3>Danh sách đặt phòng của bạn</h3>
      <div class="filter-bar">
        <form method="GET" action="index.php" style="display:flex;gap:8px;align-items:center;">
          <input type="hidden" name="action" value="list">
          <input type="search" name="search" placeholder="🔍 Tìm phòng..."
                 value="<?php echo htmlspecialchars($search ?? ''); ?>">
          <select name="filter">
            <option value=""         <?php if (($filter ?? '') === '')         echo 'selected'; ?>>Tất cả</option>
            <option value="active"   <?php if (($filter ?? '') === 'active')   echo 'selected'; ?>>Đang lưu trú</option>
            <option value="upcoming" <?php if (($filter ?? '') === 'upcoming') echo 'selected'; ?>>Sắp diễn ra</option>
            <option value="past"     <?php if (($filter ?? '') === 'past')     echo 'selected'; ?>>Đã hoàn thành</option>
          </select>
          <button type="submit" class="btn btn-outline btn-sm">Lọc</button>
          <?php if (!empty($search) || !empty($filter)): ?>
            <a href="index.php" class="btn btn-sm" style="background:#f1f5f9;color:var(--text-muted);">✕ Xóa lọc</a>
          <?php endif; ?>
        </form>
        <span class="badge badge-info"><?php echo count($bookings ?? []); ?> đơn</span>
      </div>
    </div>

    <div class="card-table">
      <div class="table-responsive">
        <table>
          <thead>
            <tr>
              <th style="width:70px; text-align:center;">Mã đơn</th>
              <th>Tên phòng / Homestay</th>
              <th>Check-in</th>
              <th>Check-out</th>
              <th>Số đêm</th>
              <th>Khách</th>
              <th style="text-align:center;">Trạng thái</th>
              <th style="width:130px; text-align:center;">Hành động</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($bookings)): ?>
              <tr class="empty-row">
                <td colspan="8">
                  <div class="empty-state">
                    <div class="empty-icon">🏖️</div>
                    <p><?php echo !empty($search) ? 'Không tìm thấy kết quả phù hợp.' : 'Bạn chưa có lịch đặt phòng nào.'; ?></p>
                    <a href="index.php?action=add" class="btn btn-primary btn-sm">Đặt phòng ngay</a>
                  </div>
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($bookings as $b):
                $today  = date('Y-m-d');
                $nights = BookingModel::calcNights($b['check_in'], $b['check_out']);
                if ($today < $b['check_in']) {
                    $status_text = 'Sắp diễn ra'; $status_class = 'badge-warning';
                } elseif ($today >= $b['check_in'] && $today <= $b['check_out']) {
                    $status_text = 'Đang lưu trú'; $status_class = 'badge-success';
                } else {
                    $status_text = 'Đã hoàn thành'; $status_class = 'badge-muted';
                }
              ?>
              <tr>
                <td style="text-align:center; font-weight:700; color:var(--text-muted);">
                  #<?php echo (int)$b['id']; ?>
                </td>
                <td>
                  <div class="room-name-cell"><?php echo htmlspecialchars($b['room_name']); ?></div>
                  <?php if (!empty($b['note'])): ?>
                    <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">
                      📝 <?php echo htmlspecialchars(mb_strimwidth($b['note'], 0, 50, '...')); ?>
                    </div>
                  <?php endif; ?>
                </td>
                <td><span class="date-text"><?php echo date('d/m/Y', strtotime($b['check_in'])); ?></span></td>
                <td><span class="date-text"><?php echo date('d/m/Y', strtotime($b['check_out'])); ?></span></td>
                <td><span class="nights-text"><?php echo $nights; ?> đêm</span></td>
                <td><?php echo (int)($b['guests'] ?? 1); ?> 👤</td>
                <td style="text-align:center;">
                  <span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                </td>
                <td style="text-align:center;">
                  <div class="action-buttons">
                    <a href="index.php?action=edit&id=<?php echo (int)$b['id']; ?>"
                       class="btn-action btn-edit" title="Chỉnh sửa"> Sửa</a>
                    <a href="index.php?action=delete&id=<?php echo (int)$b['id']; ?>"
                       class="btn-action btn-delete" title="Hủy đặt phòng"
                       onclick="return confirm('Hủy đơn đặt phòng #<?php echo (int)$b['id']; ?>?\nHành động này không thể hoàn tác.')">
                        Hủy
                    </a>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

</div>
</body>
</html>
