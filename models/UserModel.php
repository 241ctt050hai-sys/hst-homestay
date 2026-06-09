<?php
class UserModel {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Đăng ký tài khoản mới
     * @throws PDOException nếu username/email đã tồn tại
     */
    public function register(string $username, string $password, string $email, string $full_name = '', string $phone = ''): bool {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $this->db->prepare(
            "INSERT INTO users (username, email, password, full_name, phone) VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$username, $email, $hash, $full_name, $phone]);
    }

    /**
     * Xác thực đăng nhập
     */
    public function login(string $username, string $password): array|false {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    /**
     * Lấy thông tin user theo ID
     */
    public function getUserById(int $id): array|false {
        $stmt = $this->db->prepare("SELECT id, username, email, full_name, phone, created_at FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Cập nhật thông tin hồ sơ
     */
    public function updateProfile(int $id, string $full_name, string $phone, string $email): bool {
        $stmt = $this->db->prepare(
            "UPDATE users SET full_name = ?, phone = ?, email = ? WHERE id = ?"
        );
        return $stmt->execute([$full_name, $phone, $email, $id]);
    }

    /**
     * Đổi mật khẩu
     */
    public function changePassword(int $id, string $new_password): bool {
        $hash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$hash, $id]);
    }

    /**
     * Kiểm tra mật khẩu hiện tại
     */
    public function verifyPassword(int $id, string $password): bool {
        $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row && password_verify($password, $row['password']);
    }

    /**
     * Kiểm tra username đã tồn tại chưa
     */
    public function usernameExists(string $username): bool {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return (bool)$stmt->fetch();
    }

    /**
     * Kiểm tra email đã tồn tại chưa
     */
    public function emailExists(string $email, int $exclude_id = 0): bool {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $exclude_id]);
        return (bool)$stmt->fetch();
    }
}
