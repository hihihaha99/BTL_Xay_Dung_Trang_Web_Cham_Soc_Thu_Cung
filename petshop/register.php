<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/db.php';
$page_title = 'Register | Pet Care';

$error = ''; $notice = '';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if(strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6){
        $error = 'Thông tin không hợp lệ (mật khẩu ≥ 6 ký tự).';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare('INSERT INTO users (name, email, password_hash) VALUES (?,?,?)');
        $stmt->bind_param('sss', $name, $email, $hash);
        if($stmt->execute()){
            $notice = 'Tạo tài khoản thành công. Hãy đăng nhập!';
        } else {
            if($mysqli->errno === 1062) $error = 'Email đã tồn tại.';
            else $error = 'Lỗi: ' . $mysqli->error;
        }
        $stmt->close();
    }
}
include __DIR__ . '/partials/header.php';
?>

<!-- Chỉ thêm div này để canh giữa -->
<div class="center-screen">
  <section class="auth">
    <h2>Register</h2>
    <?php if($notice): ?><div class="notice"><?= htmlspecialchars($notice) ?></div><?php endif; ?>
    <?php if($error): ?><div class="notice error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <form method="post">
      <div class="input">
        <label>Full Name</label>
        <input name="name" required placeholder="Donald Trump">
      </div>

      <div class="input">
        <label>Email</label>
        <input type="email" name="email" required placeholder="you@example.com">
      </div>

      <div class="input">
        <label>Password</label>
        <input type="password" name="password" required placeholder="Minimum 6 characters">
      </div>

      <button class="btn" type="submit">Register</button>
    </form>
  </section>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
