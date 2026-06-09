<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Cập nhật mật khẩu MySQL nếu có
define('DB_NAME', 'hst_homestay');

function getDBConnection(): PDO {
    static $conn = null;
    if ($conn !== null) return $conn;
    try {
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
        return $conn;
    } catch (PDOException $e) {
        die("<div style='padding:24px;background:#fee2e2;color:#b91c1c;border-radius:10px;font-family:sans-serif;max-width:600px;margin:40px auto'>
                <strong> Lỗi kết nối CSDL:</strong><br>" . htmlspecialchars($e->getMessage()) .
             "<br><br><small>Vui lòng kiểm tra lại cấu hình trong <code>config/database.php</code></small>
             </div>");
    }
}
