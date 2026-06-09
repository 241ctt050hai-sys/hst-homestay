<?php
class BookingModel {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Lấy tất cả đặt phòng của user, có thể lọc/tìm kiếm
     */
    public function getAllBookings(int $user_id, string $search = '', string $filter = ''): array {
        $sql = "SELECT * FROM bookings WHERE user_id = ?";
        $params = [$user_id];

        if (!empty($search)) {
            $sql .= " AND room_name LIKE ?";
            $params[] = "%$search%";
        }

        if ($filter === 'upcoming') {
            $sql .= " AND check_in > CURDATE()";
        } elseif ($filter === 'active') {
            $sql .= " AND check_in <= CURDATE() AND check_out >= CURDATE()";
        } elseif ($filter === 'past') {
            $sql .= " AND check_out < CURDATE()";
        }

        $sql .= " ORDER BY id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Lấy chi tiết một đặt phòng (bảo vệ theo user_id)
     */
    public function getBookingById(int $id, int $user_id): array|false {
        $stmt = $this->db->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$id, $user_id]);
        return $stmt->fetch();
    }

    /**
     * Tạo đặt phòng mới
     */
    public function createBooking(int $user_id, string $room_name, string $check_in, string $check_out, int $guests = 1, string $note = ''): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO bookings (user_id, room_name, check_in, check_out, guests, note) VALUES (?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$user_id, $room_name, $check_in, $check_out, $guests, $note]);
    }

    /**
     * Cập nhật thông tin đặt phòng
     */
    public function updateBooking(int $id, int $user_id, string $room_name, string $check_in, string $check_out, int $guests = 1, string $note = ''): bool {
        $stmt = $this->db->prepare(
            "UPDATE bookings SET room_name = ?, check_in = ?, check_out = ?, guests = ?, note = ? WHERE id = ? AND user_id = ?"
        );
        return $stmt->execute([$room_name, $check_in, $check_out, $guests, $note, $id, $user_id]);
    }

    /**
     * Xóa / Hủy đặt phòng
     */
    public function deleteBooking(int $id, int $user_id): bool {
        $stmt = $this->db->prepare("DELETE FROM bookings WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $user_id]);
    }

    /**
     * Thống kê nhanh cho dashboard
     */
    public function getStats(int $user_id): array {
        $today = date('Y-m-d');

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $total = (int)$stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ? AND check_in > ?");
        $stmt->execute([$user_id, $today]);
        $upcoming = (int)$stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ? AND check_in <= ? AND check_out >= ?");
        $stmt->execute([$user_id, $today, $today]);
        $active = (int)$stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ? AND check_out < ?");
        $stmt->execute([$user_id, $today]);
        $past = (int)$stmt->fetchColumn();

        return compact('total', 'upcoming', 'active', 'past');
    }

    /**
     * Tính số đêm lưu trú
     */
    public static function calcNights(string $check_in, string $check_out): int {
        $diff = (strtotime($check_out) - strtotime($check_in)) / 86400;
        return max(0, (int)$diff);
    }

    /**
     * Validate ngày check-in/out
     */
    public static function validateDates(string $check_in, string $check_out): string {
        if (!strtotime($check_in) || !strtotime($check_out)) {
            return "Ngày không hợp lệ.";
        }
        if ($check_out <= $check_in) {
            return "Ngày Check-out phải sau ngày Check-in.";
        }
        return '';
    }
}
