<?php
require __DIR__ . '/includes/auth.php';
require_login();
require __DIR__ . '/includes/db.php';
$page_title = 'Dashboard | Pet Care';

$uid = $_SESSION['user']['id'];
$stmt = $mysqli->prepare('SELECT id, pet_name, visit_date, notes, created_at FROM appointments WHERE user_id=? ORDER BY visit_date DESC');
$stmt->bind_param('i', $uid);
$stmt->execute();
$result = $stmt->get_result();
include __DIR__ . '/partials/header.php';
?>
<section class="card" style="margin:30px 0">
  <h2>Appointments</h2>
  <?php if($result->num_rows === 0): ?>
    <p>Chưa có lịch hẹn nào. <a class="link" href="/schedule.php">Đặt lịch ngay</a>.</p>
  <?php else: ?>
    <div class="cards">
      <?php while($row = $result->fetch_assoc()): ?>
        <div class="card">
          <h3><?= htmlspecialchars($row['pet_name']) ?></h3>
          <p><strong>Thời gian:</strong> <?= htmlspecialchars($row['visit_date']) ?></p>
          <p><?= htmlspecialchars($row['notes'] ?: '—') ?></p>
          <p class="smalltext">Tạo lúc: <?= htmlspecialchars($row['created_at']) ?></p>
        </div>
      <?php endwhile; ?>
    </div>
  <?php endif; ?>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>
