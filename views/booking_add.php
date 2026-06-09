<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đặt phòng mới | HST-Homestay</title>
  <link rel="stylesheet" href="assets/style.css">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🏡</text></svg>">
</head>
<body>
<div class="form-wrapper">
  <div class="form-card">

    <a href="index.php" class="back-nav">← Quay lại danh sách</a>

    <div class="form-card-header">
      <h2>🏨 Đặt phòng mới</h2>
      <p>Điền thông tin bên dưới để đặt chỗ lưu trú của bạn</p>
    </div>

    <?php if (!empty($form_error)): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($form_error); ?></div>
    <?php endif; ?>

    <form action="index.php?action=add" method="POST" novalidate>

      <p class="form-section-title">Thông tin phòng</p>

      <div class="form-group">
        <label for="room_name">Tên phòng / Homestay <span style="color:var(--danger)">*</span></label>
        <input type="text" id="room_name" name="room_name" required
               placeholder="Vd: Villa hướng Biển – Căn C05"
               value="<?php echo htmlspecialchars($old['room_name'] ?? ''); ?>"
               autofocus>
      </div>

      <div class="form-row">
        <div class="form-group col">
          <label for="check_in">Ngày nhận phòng (Check-in) <span style="color:var(--danger)">*</span></label>
          <input type="date" id="check_in" name="check_in" required
                 value="<?php echo $old['check_in'] ?? date('Y-m-d'); ?>"
                 min="<?php echo date('Y-m-d'); ?>">
        </div>
        <div class="form-group col">
          <label for="check_out">Ngày trả phòng (Check-out) <span style="color:var(--danger)">*</span></label>
          <input type="date" id="check_out" name="check_out" required
                 value="<?php echo $old['check_out'] ?? date('Y-m-d', strtotime('+1 day')); ?>"
                 min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
        </div>
      </div>

      <p class="form-section-title" style="margin-top:4px;">Thêm thông tin (tuỳ chọn)</p>

      <div class="form-row">
        <div class="form-group col">
          <label for="guests">Số lượng khách</label>
          <select id="guests" name="guests">
            <?php for ($i = 1; $i <= 10; $i++): ?>
              <option value="<?php echo $i; ?>" <?php if (($old['guests'] ?? 1) == $i) echo 'selected'; ?>>
                <?php echo $i; ?> khách
              </option>
            <?php endfor; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="note">Ghi chú đặc biệt</label>
        <textarea id="note" name="note"
                  placeholder="Yêu cầu phòng, giờ nhận phòng sớm, v.v..."><?php echo htmlspecialchars($old['note'] ?? ''); ?></textarea>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">✅ Xác nhận đặt phòng</button>
        <a href="index.php" class="btn-link">Hủy bỏ</a>
      </div>
    </form>

  </div>
</div>

<script>
// Tự động cập nhật min check_out khi thay đổi check_in
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
