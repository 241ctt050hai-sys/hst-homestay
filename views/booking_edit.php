<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chỉnh sửa đặt phòng | HST-Homestay</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏡</text></svg>">
</head>
<body>
<div class="form-wrapper">
  <div class="form-card">

    <a href="index.php" class="back-nav">← Quay lại danh sách</a>

    <div class="form-card-header">
      <h2> Chỉnh sửa đặt phòng <span style="color:var(--text-muted);font-weight:400;">#<?php echo $booking['id']; ?></span></h2>
      <p>Cập nhật thông tin phòng hoặc thay đổi ngày lưu trú</p>
    </div>

    <?php if (!empty($form_error)): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($form_error); ?></div>
    <?php endif; ?>

    <form action="index.php?action=edit&id=<?php echo $booking['id']; ?>" method="POST" novalidate>

      <p class="form-section-title">Thông tin phòng</p>

      <div class="form-group">
        <label for="room_name">Tên phòng / Homestay <span style="color:var(--danger)">*</span></label>
        <input type="text" id="room_name" name="room_name" required
               value="<?php echo htmlspecialchars($booking['room_name']); ?>"
               autofocus>
      </div>

      <div class="form-row">
        <div class="form-group col">
          <label for="check_in">Ngày Check-in <span style="color:var(--danger)">*</span></label>
          <input type="date" id="check_in" name="check_in" required
                 value="<?php echo $booking['check_in']; ?>">
        </div>
        <div class="form-group col">
          <label for="check_out">Ngày Check-out <span style="color:var(--danger)">*</span></label>
          <input type="date" id="check_out" name="check_out" required
                 value="<?php echo $booking['check_out']; ?>">
        </div>
      </div>

      <p class="form-section-title" style="margin-top:4px;">Thêm thông tin</p>

      <div class="form-row">
        <div class="form-group col">
          <label for="guests">Số khách</label>
          <select id="guests" name="guests">
            <?php for ($i = 1; $i <= 10; $i++): ?>
              <option value="<?php echo $i; ?>" <?php if (($booking['guests'] ?? 1) == $i) echo 'selected'; ?>>
                <?php echo $i; ?> khách
              </option>
            <?php endfor; ?>
          </select>
        </div>
        <div class="form-group col" style="display:flex;align-items:flex-end;padding-bottom:20px;">
          <?php
            $nights = BookingModel::calcNights($booking['check_in'], $booking['check_out']);
          ?>
          <div style="background:var(--info-bg);color:var(--info);border-radius:var(--radius-md);padding:10px 16px;font-size:14px;font-weight:600;width:100%;text-align:center;">
            📅 <?php echo $nights; ?> đêm lưu trú
          </div>
        </div>
      </div>

      <div class="form-group">
        <label for="note">Ghi chú</label>
        <textarea id="note" name="note"
                  placeholder="Yêu cầu đặc biệt..."><?php echo htmlspecialchars($booking['note'] ?? ''); ?></textarea>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-warning">💾 Lưu thay đổi</button>
        <a href="index.php" class="btn-link">Hủy bỏ</a>
      </div>
    </form>

  </div>
</div>

<script>
document.getElementById('check_in').addEventListener('change', function() {
  const cin  = this.value;
  const cout = document.getElementById('check_out');
  const next = new Date(cin);
  next.setDate(next.getDate() + 1);
  const nextStr = next.toISOString().split('T')[0];
  cout.min = nextStr;
  if (cout.value <= cin) cout.value = nextStr;
});
</script>
</body>
</html>
