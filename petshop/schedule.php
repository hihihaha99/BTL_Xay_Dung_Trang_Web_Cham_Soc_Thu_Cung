<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/db.php';
$page_title = 'Schedule a Visit | Pet Care';

$notice = '';
$error = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $pet_name = trim($_POST['pet_name'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    if($pet_name === '' || $date === ''){
        $error = 'Vui lòng điền đầy đủ tên thú cưng và ngày hẹn.';
    } else {
        $stmt = $mysqli->prepare('INSERT INTO appointments (user_id, pet_name, visit_date, notes) VALUES (?,?,?,?)');
        $uid = $_SESSION['user']['id'] ?? null;
        $stmt->bind_param('isss', $uid, $pet_name, $date, $notes);
        if($stmt->execute()){
            $notice = 'Đặt lịch thành công! Xem trong Dashboard.';
        } else {
            $error = 'Không thể đặt lịch: ' . $mysqli->error;
        }
        $stmt->close();
    }
}
include __DIR__ . '/partials/header.php';
?>
<section class="auth">
  <h2>Schedule a Visit</h2>
  <?php if($notice): ?><div class="notice"><?= htmlspecialchars($notice) ?></div><?php endif; ?>
  <?php if($error): ?><div class="notice error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <form method="post">
    <div class="input">
      <label>Tên thú cưng</label>
      <input name="pet_name" placeholder="Ví dụ: Miu Miu" required>
    </div>
    <div class="input">
      <label>Ngày hẹn</label>
      <input type="datetime-local" name="date" required>
    </div>
    <div class="input">
      <label>Ghi chú</label>
      <input name="notes" placeholder="Triệu chứng / yêu cầu">
    </div>
    <?php if(!is_logged_in()): ?>
      <p class="smalltext">Bạn cần đăng nhập để đặt lịch.</p>
    <?php endif; ?>
    <button class="btn" type="submit" <?php if(!is_logged_in()) echo 'disabled'; ?>>Đặt lịch</button>
  </form>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>
