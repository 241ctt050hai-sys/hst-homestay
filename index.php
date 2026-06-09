<?php

session_start();
require_once 'config/database.php';
require_once 'models/BookingModel.php';
require_once 'models/UserModel.php';

// Bảo vệ: bắt buộc đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db           = getDBConnection();
$bookingModel = new BookingModel($db);
$userModel    = new UserModel($db);

$user_id      = (int)$_SESSION['user_id'];
$username     = $_SESSION['username'];
$display_name = $_SESSION['full_name'] ?? $username;
$action       = $_GET['action'] ?? 'list';


switch ($action) {

    
    case 'add':
        $form_error = '';
        $old = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $room_name = trim($_POST['room_name'] ?? '');
            $check_in  = $_POST['check_in']  ?? '';
            $check_out = $_POST['check_out'] ?? '';
            $guests    = max(1, (int)($_POST['guests'] ?? 1));
            $note      = trim($_POST['note'] ?? '');
            $old       = compact('room_name', 'check_in', 'check_out', 'guests', 'note');

            if (empty($room_name)) {
                $form_error = "Vui lòng nhập tên phòng.";
            } else {
                $err = BookingModel::validateDates($check_in, $check_out);
                if ($err) {
                    $form_error = $err;
                } else {
                    $bookingModel->createBooking($user_id, $room_name, $check_in, $check_out, $guests, $note);
                    header("Location: index.php?status=success_add");
                    exit;
                }
            }
        }
        include 'views/booking_add.php';
        break;

    
    case 'edit':
        $id      = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $booking = $bookingModel->getBookingById($id, $user_id);

        if (!$booking) {
            header("Location: index.php");
            exit;
        }

        $form_error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $room_name = trim($_POST['room_name'] ?? '');
            $check_in  = $_POST['check_in']  ?? '';
            $check_out = $_POST['check_out'] ?? '';
            $guests    = max(1, (int)($_POST['guests'] ?? 1));
            $note      = trim($_POST['note'] ?? '');

            if (empty($room_name)) {
                $form_error = "Vui lòng nhập tên phòng.";
            } else {
                $err = BookingModel::validateDates($check_in, $check_out);
                if ($err) {
                    $form_error = $err;
                } else {
                    $bookingModel->updateBooking($id, $user_id, $room_name, $check_in, $check_out, $guests, $note);
                    header("Location: index.php?status=success_edit");
                    exit;
                }
            }

            // Giữ lại giá trị đã nhập khi có lỗi
            $booking = array_merge($booking, compact('room_name', 'check_in', 'check_out', 'guests', 'note'));
        }
        include 'views/booking_edit.php';
        break;

    case 'delete':
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id > 0) {
            $bookingModel->deleteBooking($id, $user_id);
        }
        header("Location: index.php?status=success_delete");
        exit;

    case 'profile':
        $user           = $userModel->getUserById($user_id);
        $profile_error  = '';
        $profile_success = '';
        $pw_error       = '';

        // Cập nhật thông tin cá nhân
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
            $full_name = trim($_POST['full_name'] ?? '');
            $email     = trim($_POST['email']     ?? '');
            $phone     = trim($_POST['phone']     ?? '');

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $profile_error = "Địa chỉ email không hợp lệ.";
            } elseif ($userModel->emailExists($email, $user_id)) {
                $profile_error = "Email này đã được sử dụng bởi tài khoản khác.";
            } else {
                $userModel->updateProfile($user_id, $full_name, $phone, $email);
                $_SESSION['full_name'] = $full_name ?: $username;
                header("Location: index.php?status=profile_saved");
                exit;
            }
            $user = array_merge($user, compact('full_name', 'email', 'phone'));
        }

        // Đổi mật khẩu
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
            $current_pw = $_POST['current_pw'] ?? '';
            $new_pw     = $_POST['new_pw']     ?? '';
            $confirm_pw = $_POST['confirm_pw'] ?? '';

            if (empty($current_pw) || empty($new_pw) || empty($confirm_pw)) {
                $pw_error = "Vui lòng điền đầy đủ thông tin mật khẩu.";
            } elseif (!$userModel->verifyPassword($user_id, $current_pw)) {
                $pw_error = "Mật khẩu hiện tại không chính xác.";
            } elseif (strlen($new_pw) < 6) {
                $pw_error = "Mật khẩu mới phải có ít nhất 6 ký tự.";
            } elseif ($new_pw !== $confirm_pw) {
                $pw_error = "Xác nhận mật khẩu mới không khớp.";
            } else {
                $userModel->changePassword($user_id, $new_pw);
                header("Location: index.php?status=pw_changed");
                exit;
            }
        }

        include 'views/profile.php';
        break;

    case 'logout':
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit;


    case 'list':
    default:
        $search   = trim($_GET['search'] ?? '');
        $filter   = $_GET['filter'] ?? '';
        $bookings = $bookingModel->getAllBookings($user_id, $search, $filter);
        $stats    = $bookingModel->getStats($user_id);
        include 'views/home.php';
        break;
}
