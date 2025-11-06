<?php
session_start();

/* ================== CẤU HÌNH ================== */
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "123456789";       // ĐỔI theo máy bạn
$DB_NAME = "pet_shop_db";

$BRAND_NAME   = "PetCare";    // tên thương hiệu
$ADDRESS_TXT  = "41 Phố Hải Thanh, Hồng Hải, TP. Hạ Long, Quảng Ninh";
$ADDRESS_MAP  = "https://www.google.com/maps/search/?api=1&query=41+Pho+Hai+Thanh+Ha+Long+Quang+Ninh";
$HOTLINE      = "+84 32 855 3886";
$EMAIL_TO     = "info@petshop.vn";

/* ================== KẾT NỐI DB + TẠO BẢNG ================== */
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
  $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
  $conn->set_charset("utf8mb4");
  // tự tạo bảng nếu chưa có
  $conn->query("
    CREATE TABLE IF NOT EXISTS contact_messages (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(80) NOT NULL,
      email VARCHAR(120) NOT NULL,
      subject VARCHAR(150) NOT NULL,
      message TEXT NOT NULL,
      status ENUM('new','seen','closed') NOT NULL DEFAULT 'new',
      ip VARCHAR(45),
      user_agent VARCHAR(255),
      created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      INDEX idx_created_at (created_at),
      INDEX idx_email (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
  ");
} catch (Throwable $e) {
  die("Lỗi DB: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

/* ================== CSRF TOKEN ================== */
if (empty($_SESSION['contact_csrf'])) {
  $_SESSION['contact_csrf'] = bin2hex(random_bytes(32));
}

/* ================== XỬ LÝ SUBMIT ================== */
$successMsg = $errorMsg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (!empty($_POST['website'])) {
    $errorMsg = "Không hợp lệ."; // honeypot
  } elseif (!hash_equals($_SESSION['contact_csrf'] ?? '', $_POST['csrf'] ?? '')) {
    $errorMsg = "Phiên gửi không hợp lệ. Vui lòng tải lại trang.";
  } else {
    try {
      $name    = trim($_POST['name'] ?? "");
      $email   = trim($_POST['email'] ?? "");
      $phone   = trim($_POST['phone'] ?? "");
      $service = trim($_POST['service'] ?? "");
      $contact = trim($_POST['contact_method'] ?? "");
      $timepref= trim($_POST['time_pref'] ?? "");
      $subject = trim($_POST['subject'] ?? "");
      $message = trim($_POST['message'] ?? "");
      $agree   = isset($_POST['agree']);

      if (mb_strlen($name) < 2 || mb_strlen($name) > 80)   throw new Exception("Tên phải 2–80 ký tự.");
      if (!filter_var($email, FILTER_VALIDATE_EMAIL))      throw new Exception("Email không hợp lệ.");
      if (mb_strlen($subject) < 2 || mb_strlen($subject) > 150) throw new Exception("Chủ đề 2–150 ký tự.");
      if (mb_strlen($message) < 10 || mb_strlen($message) > 5000) throw new Exception("Nội dung 10–5000 ký tự.");
      if (!$agree)                                         throw new Exception("Bạn cần đồng ý cho chúng tôi liên hệ lại.");

      // Ghép thông tin bổ sung vào message để không cần đổi schema
      $extra = [];
      if ($phone)   $extra[] = "📞 Điện thoại: " . $phone;
      if ($service) $extra[] = "🐾 Dịch vụ quan tâm: " . $service;
      if ($contact) $extra[] = "💬 Cách liên hệ mong muốn: " . $contact;
      if ($timepref)$extra[] = "🕒 Khung giờ mong muốn: " . $timepref;

      $compiledMessage = $message . (count($extra) ? "\n\n--- Thông tin bổ sung ---\n" . implode("\n", $extra) : "");

      $sql = "INSERT INTO contact_messages (name,email,subject,message,status,ip,user_agent,created_at)
              VALUES (?,?,?,?, 'new', ?, ?, NOW())";
      $stmt = $conn->prepare($sql);
      $ip = $_SERVER['REMOTE_ADDR'] ?? null;
      $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250);
      $stmt->bind_param("ssssss", $name, $email, $subject, $compiledMessage, $ip, $ua);
      $stmt->execute();

      $successMsg = "Cảm ơn bạn! 🧡 Yêu cầu đã được gửi. Chúng tôi sẽ liên hệ sớm nhất.";
      $_SESSION['contact_csrf'] = bin2hex(random_bytes(32)); // reset token
    } catch (Throwable $e) {
      $errorMsg = "❌ " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
  }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <title>Liên hệ | <?= htmlspecialchars($BRAND_NAME) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
  <style>
    :root{
      --y-bg:#FFF7EA; --y-accent:#F7A65A; --y-accent-2:#FFCD90;
      --txt:#2b2b2b; --muted:#6b6b6b; --ok:#1AAE6F; --err:#E34D5C;
      --r:20px; --shadow:0 14px 40px rgba(0,0,0,.08);
    }
    *{box-sizing:border-box}
    body{ background:var(--y-bg); color:var(--txt); }
    header,.site-header,.navbar{position:relative;z-index:9999}
    .crisp-client{z-index:2147483647!important} /* nếu sau này bạn bật chat */
    .hero{
      position:relative; overflow:hidden;
      padding:72px 16px 40px;
      background: radial-gradient(1200px 520px at 85% -10%, rgba(247,166,90,.22), transparent 60%);
    }
    .wrap{ max-width:1150px; margin:0 auto; }
    .eyebrow{ display:inline-flex; gap:8px; align-items:center;
      font-weight:800; letter-spacing:.08em; color:var(--y-accent);
      background:rgba(247,166,90,.18); padding:8px 14px; border-radius:999px; }
    .eyebrow:before{content:"🐾"}
    .title{ font-size:clamp(28px,4.4vw,56px); line-height:1.08; margin:14px 0 10px; }
    .sub{ color:var(--muted); max-width:740px; }

    .grid{ display:grid; gap:22px; margin-top:28px; grid-template-columns:2fr 1.05fr; }
    @media (max-width: 992px){ .grid{ grid-template-columns:1fr; } }

    .card{ background:#fff; border:0; border-radius:var(--r); box-shadow:var(--shadow); }
    .card-form{ padding:22px 22px; }
    .card-info{ padding:22px; height:100%; }

    .badge-soft{background:rgba(26,174,111,.12); color:var(--ok); padding:.35rem .6rem; border-radius:999px; font-weight:700; font-size:.85rem}

    .paw-sep{
      --c:rgba(247,166,90,.25);
      height:10px; background:linear-gradient(90deg,var(--c) 20%,transparent 0) 0 0/18px 2px repeat-x;
      opacity:.8; margin:10px 0 18px;
    }

    .info-item{ display:flex; gap:12px; align-items:flex-start; margin-bottom:14px; }
    .info-ico{ width:44px; height:44px; display:grid; place-items:center; border-radius:12px;
               background:rgba(247,166,90,.15); font-size:20px; color:#c46b1f; }

    .form-control,.form-select{ border-radius:14px; padding:.7rem .9rem; }
    .btn-primary{
      background: linear-gradient(180deg, var(--y-accent), #e98f40);
      border:none; font-weight:800; letter-spacing:.02em; border-radius:14px;
      box-shadow:0 10px 24px rgba(247,166,90,.45);
    }
    .btn-primary:hover{ filter:brightness(.98) saturate(1.05); }
    .btn-ghost{
      border:2px solid rgba(247,166,90,.5); color:var(--y-accent); font-weight:800; border-radius:14px;
      background:linear-gradient(180deg,#fff,#fff7);
    }
    .map-embed{ border-radius:16px; overflow:hidden; box-shadow:var(--shadow); }

    /* Toast */
    .toast-wrap{position:fixed; right:18px; bottom:18px; z-index:9999}
    .toast{border:0; box-shadow:var(--shadow); border-radius:12px}

    /* FAQ */
    .faq .accordion-button{font-weight:700}
  </style>
</head>
<body>
<section class="hero">
  <div class="wrap">
    <span class="eyebrow">LIÊN HỆ</span>
    <h1 class="title">Rất vui khi nhận được tin nhắn từ bạn 🧡</h1>
    <p class="sub">Điền thông tin bên dưới, chọn dịch vụ quan tâm và cách liên hệ thuận tiện. Chúng tôi phản hồi trong giờ làm việc.</p>

    <?php if ($successMsg): ?>
      <div class="alert alert-success mt-3">
        <i class="bi bi-check-circle-fill me-2"></i><?= $successMsg ?>
        <span class="badge-soft ms-2">Đã nhận</span>
      </div>
    <?php elseif ($errorMsg): ?>
      <div class="alert alert-danger mt-3"><i class="bi bi-x-circle-fill me-2"></i><?= $errorMsg ?></div>
    <?php endif; ?>

    <div class="grid">
      <!-- FORM -->
      <div class="card card-form">
        <form method="POST" class="needs-validation" novalidate>
          <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['contact_csrf']) ?>">
          <input type="text" name="website" style="display:none">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Họ và tên</label>
              <input type="text" name="name" class="form-control" maxlength="80" required>
              <div class="invalid-feedback">Vui lòng nhập họ tên (≤ 80 ký tự).</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" maxlength="120" required>
              <div class="invalid-feedback">Email không hợp lệ.</div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Số điện thoại <span class="text-muted">(không bắt buộc)</span></label>
              <input type="tel" name="phone" class="form-control" maxlength="20" placeholder="Ví dụ: 09xx xxx xxx">
            </div>
            <div class="col-md-6">
              <label class="form-label">Dịch vụ quan tâm</label>
              <select class="form-select" name="service">
                <option value="">— Chọn dịch vụ —</option>
                <option>Khám tại nhà</option>
                <option>Tiêm phòng / Tẩy giun</option>
                <option>Khám cấp cứu nhẹ</option>
                <option>Tư vấn TeleVet</option>
                <option>Chăm sóc sau phẫu thuật</option>
                <option>Khác</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Cách liên hệ mong muốn</label>
              <select class="form-select" name="contact_method">
                <option value="">Bất kỳ</option>
                <option>Gọi điện</option>
                <option>Zalo</option>
                <option>Facebook</option>
                <option>Email</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Khung giờ thuận tiện</label>
              <select class="form-select" name="time_pref">
                <option value="">Bất kỳ</option>
                <option>Sáng (08:00–11:00)</option>
                <option>Chiều (13:30–17:30)</option>
                <option>Tối (18:30–21:00)</option>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label">Chủ đề</label>
              <input type="text" name="subject" class="form-control" maxlength="150" required placeholder="Ví dụ: Đặt lịch khám cho bé mèo">
              <div class="invalid-feedback">Nhập chủ đề (≤ 150 ký tự).</div>
            </div>

            <div class="col-12">
              <label class="form-label">Nội dung</label>
              <textarea name="message" class="form-control" rows="6" maxlength="5000" required placeholder="Hãy mô tả ngắn tình trạng, giống loài, tuổi, cân nặng..."></textarea>
              <div class="invalid-feedback">Vui lòng nhập nội dung (≥ 10 ký tự).</div>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="agree" name="agree" required>
                <label class="form-check-label" for="agree">
                  Tôi đồng ý cho <?= htmlspecialchars($BRAND_NAME) ?> liên hệ lại theo thông tin đã cung cấp.
                </label>
                <div class="invalid-feedback">Bạn cần đồng ý để tiếp tục.</div>
              </div>
            </div>

            <div class="col-12 d-flex gap-2 align-items-center">
              <button type="submit" class="btn btn-primary px-4" id="btnSend">
                <i class="bi bi-send-fill me-1"></i> Gửi liên hệ
              </button>
              <button type="button" class="btn btn-ghost px-3" id="btnNearest">
                <i class="bi bi-geo-alt-fill me-1"></i> Tìm chi nhánh gần nhất
              </button>
              <span class="ms-auto badge-soft"><i class="bi bi-clock-history me-1"></i>Giờ làm việc: 8:00–21:00 (hàng ngày)</span>
            </div>
          </div>
        </form>

        <div class="paw-sep"></div>

        <!-- FAQ ngắn gọn -->
        <div class="faq accordion" id="faq">
          <div class="accordion-item">
            <h2 class="accordion-header" id="q1">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a1">
                Tôi có thể đặt lịch trong ngày không?
              </button>
            </h2>
            <div id="a1" class="accordion-collapse collapse" data-bs-parent="#faq">
              <div class="accordion-body">Nếu còn slot trống, chúng tôi hỗ trợ đặt lịch **trong ngày** cho trường hợp nhẹ. Cấp cứu xin gọi ngay <a href="tel:<?= preg_replace('/\s+/', '', $HOTLINE) ?>"><?= htmlspecialchars($HOTLINE) ?></a>.</div>
            </div>
          </div>
          <div class="accordion-item">
            <h2 class="accordion-header" id="q2">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#a2">
                TeleVet hoạt động thế nào?
              </button>
            </h2>
            <div id="a2" class="accordion-collapse collapse" data-bs-parent="#faq">
              <div class="accordion-body">Chúng tôi tư vấn nhanh qua video cho các vấn đề thường gặp và theo dõi sau khám. Trường hợp khẩn cấp vẫn cần **khám trực tiếp**.</div>
            </div>
          </div>
        </div>
      </div>

      <!-- THÔNG TIN LIÊN HỆ -->
      <aside class="card card-info">
        <h5 class="mb-3">Thông tin liên hệ</h5>
        <div class="info-item">
          <div class="info-ico"><i class="bi bi-telephone-fill"></i></div>
          <div><div class="fw-semibold">Hotline</div>
            <a class="link-underline link-underline-opacity-0" href="tel:<?= preg_replace('/\s+/', '', $HOTLINE) ?>">
              <?= htmlspecialchars($HOTLINE) ?>
            </a>
          </div>
        </div>
        <div class="info-item">
          <div class="info-ico"><i class="bi bi-envelope-fill"></i></div>
          <div><div class="fw-semibold">Email</div>
            <a href="mailto:<?= htmlspecialchars($EMAIL_TO) ?>"><?= htmlspecialchars($EMAIL_TO) ?></a>
          </div>
        </div>
        <div class="info-item">
          <div class="info-ico"><i class="bi bi-geo-alt-fill"></i></div>
          <div>
            <div class="fw-semibold">Địa chỉ</div>
            <div><?= htmlspecialchars($ADDRESS_TXT) ?></div>
            <a class="small" target="_blank" href="<?= htmlspecialchars($ADDRESS_MAP) ?>">Xem bản đồ →</a>
          </div>
        </div>
        <div class="map-embed mt-3">
          <iframe src="https://www.google.com/maps?q=<?= urlencode($ADDRESS_TXT) ?>&output=embed"
                  width="100%" height="240" style="border:0;" loading="lazy" allowfullscreen></iframe>
        </div>

        <div class="d-flex gap-2 mt-3">
          <a class="btn btn-ghost w-100" href="tel:<?= preg_replace('/\s+/', '', $HOTLINE) ?>"><i class="bi bi-telephone-outbound me-1"></i>Gọi ngay</a>
          <a class="btn btn-ghost w-100" href="mailto:<?= htmlspecialchars($EMAIL_TO) ?>"><i class="bi bi-envelope-paper-heart me-1"></i>Gửi email</a>
        </div>
      </aside>
    </div>
  </div>
</section>

<!-- Toast feedback -->
<div class="toast-wrap">
  <div id="toastOK" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex"><div class="toast-body"><i class="bi bi-check2-circle me-2"></i>Đã gửi yêu cầu thành công!</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
  <div id="toastERR" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex"><div class="toast-body"><i class="bi bi-exclamation-octagon me-2"></i>Gửi thất bại. Kiểm tra lại thông tin!</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Validate + tránh double submit + toast
  (function(){
    const forms = document.querySelectorAll('.needs-validation');
    const toastOK  = new bootstrap.Toast(document.getElementById('toastOK'));
    const toastERR = new bootstrap.Toast(document.getElementById('toastERR'));
    Array.from(forms).forEach(form=>{
      form.addEventListener('submit', e=>{
        if(!form.checkValidity()){
          e.preventDefault(); e.stopPropagation(); toastERR.show();
        } else {
          document.getElementById('btnSend').disabled = true;
          // để server xử lý submit thật; nếu muốn AJAX thì mình có thể chuyển sang fetch()
        }
        form.classList.add('was-validated');
      });
    });
    // Hiển thị toast theo kết quả server
    <?php if ($successMsg): ?> toastOK.show(); <?php endif; ?>
    <?php if ($errorMsg): ?>  toastERR.show(); <?php endif; ?>
  })();

  // Tìm thú y gần nhất
  document.getElementById('btnNearest')?.addEventListener('click', () => {
    if (!navigator.geolocation) { window.open('https://www.google.com/maps/search/thú+y+gần+đây', '_blank'); return; }
    navigator.geolocation.getCurrentPosition(
      pos => {
        const { latitude, longitude } = pos.coords;
        window.open(`https://www.google.com/maps/search/thú+y+gần+đây/@${latitude},${longitude},15z`, '_blank');
      },
      () => window.open('https://www.google.com/maps/search/thú+y+gần+đây','_blank'),
      { enableHighAccuracy:true, timeout:6000 }
    );
  });
</script>
</body>
</html>
