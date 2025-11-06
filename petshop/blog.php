<?php
require __DIR__ . '/includes/auth.php';
$page_title = 'Blog | Pet Care';
include __DIR__ . '/partials/header.php';

/* ========= CẤU HÌNH & HELPERS ẢNH ========= */

/* BASE_URL tự tính theo thư mục script (vd: /petshop); nếu ở root thì rỗng */
$BASE_URL = rtrim(str_replace('\\','/', dirname($_SERVER['SCRIPT_NAME'])), '/');
if ($BASE_URL === '/') $BASE_URL = '';

/* Chuẩn hoá nối đường dẫn hệ thống */
function _fs(string $webPath): string {
  return rtrim($_SERVER['DOCUMENT_ROOT'], '/') . $webPath;
}

/**
 * Trả về URL ảnh dùng trực tiếp trong <img src="...">.
 * Hỗ trợ:
 *  - https://... (giữ nguyên)
 *  - /tuyet-doi-tren-web (giữ nguyên nếu file tồn tại)
 *  - tên file hoặc đường dẫn tương đối (tự dò thư mục ảnh)
 */
function img_url(string $path): string {
  global $BASE_URL;

  $path = trim($path);

  // 1) URL ngoài
  if (preg_match('#^https?://#i', $path)) return $path;

  // 2) Đường dẫn tuyệt đối /... theo web root
  if (strpos($path, '/') === 0) {
    return is_file(_fs($path)) ? $path : placeholder_data_uri();
  }

  // 3) Các khả năng tương đối: thử lần lượt các base-folders
  $candidates = [];

  // Nếu user đã gõ kiểu "img/xxx.jpg" hay "blog/xxx.jpg"
  $candidates[] = $BASE_URL . '/' . ltrim($path, '/');

  // Thư mục blog & img chuẩn của dự án
  $candidates[] = $BASE_URL . '/assets/img/blog/' . ltrim($path, '/');
  $candidates[] = $BASE_URL . '/assets/img/' . ltrim($path, '/');

  foreach ($candidates as $web) {
    if (is_file(_fs($web))) return $web;
  }

  // Không tìm thấy -> placeholder (nếu có)
  $ph = $BASE_URL . '/assets/img/placeholder.jpg';
  return is_file(_fs($ph)) ? $ph : placeholder_data_uri();
}

/* Trả về 1x1 gif rỗng nếu không có ảnh */
function placeholder_data_uri(): string {
  return 'data:image/gif;base64,R0lGODlhAQABAAAAACw='; // transparent pixel
}

/* Ảnh nền (đặt sẵn file trong assets/img/ hoặc assets/img/blog/) */
$BG_IMAGE = img_url('pet-bg.jpg');

/* ===== DỮ LIỆU DEMO (cover = tên file local) ===== */
$posts = [
  1 => [
    'title'   => '5 cách chăm sóc chó trong mùa nóng',
    'cover'   => 'pet-hot.jpg', // file nằm ở assets/img/ hoặc assets/img/blog/
    'excerpt' => 'Bật mí cách giúp thú cưng của bạn luôn mát mẻ và khỏe mạnh trong mùa hè.',
    'content' => "1️⃣ Luôn có nước mát.\n2️⃣ Tránh vận động giữa trưa.\n3️⃣ Bổ sung điện giải.\n4️⃣ Không để trong xe kín.\n5️⃣ Nhận biết và xử trí sốc nhiệt kịp thời.",
    'link'    => 'https://www.aspca.org/pet-care/dog-care/hot-weather-safety-tips'
  ],
  2 => [
    'title'   => 'Mèo của bạn có đang thiếu vitamin không?',
    'cover'   => 'vitamin-meo.jpeg', // ví dụ dùng file khác: hero-dog.jpg
    'excerpt' => 'Tìm hiểu những dấu hiệu cho thấy mèo cần bổ sung dinh dưỡng.',
    'content' => "🐱 Dấu hiệu: lông xấu, mệt mỏi, chậm lớn.\nNhóm vitamin A, B, D, E rất quan trọng.\n⚠️ Không nên bổ sung quá liều, cần theo chỉ dẫn thú y.",
    'link'    => 'https://www.hillspet.com/cat-care/nutrition-feeding/vitamins-for-cats'
  ],
  3 => [
    'title'   => 'Chọn thức ăn phù hợp cho từng loại thú cưng',
    'cover'   => 'thucan.jpg',
    'excerpt' => 'Tư vấn chọn thực phẩm theo độ tuổi và giống loài để đảm bảo dinh dưỡng tốt nhất.',
    'content' => "📘 Cân bằng protein, chất béo, và carb.\n🍖 Chú ý dị ứng và hệ tiêu hoá.\n🐾 Đọc kỹ nhãn mác và chọn sản phẩm uy tín.",
    'link'    => 'https://www.purina.com/articles/pet-food'
  ],
  4 => [
    'title'   => 'Những bệnh thường gặp ở thú cưng và cách phòng ngừa',
    'cover'   => 'benhthucung.jpg',
    'excerpt' => 'Hướng dẫn nhận biết dấu hiệu sớm và phòng bệnh hiệu quả cho thú cưng.',
    'content' => "💉 Tiêm phòng định kỳ.\n🐜 Tẩy giun, ve, rận thường xuyên.\n🏡 Giữ môi trường sống sạch sẽ.\n👀 Quan sát biểu hiện bất thường để xử lý kịp thời.",
    'link'    => 'https://www.petmd.com/dog/general-health/common-diseases-dogs'
  ],
  5 => [
    'title'   => 'Chải lông đúng cách cho thú cưng',
    'cover'   => 'chailongchothucung.jpg',
    'excerpt' => 'Giữ cho bộ lông của bé luôn sạch sẽ, mềm mượt và tránh rụng lông nhiều.',
    'content' => "🪮 Chọn lược phù hợp từng loại lông.\n🧴 Dưỡng ẩm da và lông định kỳ.\n🫧 Kết hợp tắm nhẹ nhàng 2–4 lần/tuần.",
    'link'    => 'https://www.akc.org/expert-advice/grooming/how-to-groom-your-dog/'
  ],
  6 => [
    'title'   => 'Du lịch an toàn cùng thú cưng',
    'cover'   => 'dulichthucung.jpg',
    'excerpt' => 'Chuẩn bị hành lý, thức ăn và lồng di chuyển cho chuyến đi hoàn hảo.',
    'content' => "✈️ Mang theo sổ tiêm & thuốc cơ bản.\n🧳 Đảm bảo lồng di chuyển thoáng khí.\n🌳 Chọn khách sạn và địa điểm pet-friendly.",
    'link'    => 'https://www.petmd.com/dog/travel'
  ],
];

$id       = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post     = $posts[$id] ?? null;
$isDetail = (bool)$post;
?>

<!-- Font tiếng Việt đẹp, nhanh -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;600;700;800&display=swap" rel="stylesheet">

<style>
:root{ --brand:#ff6f61; --brand-2:#ff7f73; --text:#0f172a; --muted:#475569; --glass: rgba(255,255,255,.92); }
*{box-sizing:border-box}
html,body{height:100%}
body{ margin:0; color:var(--text); font-family:'Be Vietnam Pro',system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif; background:#fff; -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale; }

/* BG local */
.bg-stack{position:fixed; inset:0; z-index:0; pointer-events:none}
.bg-photo{position:absolute; inset:0; background:url('<?= htmlspecialchars($BG_IMAGE) ?>') center/cover no-repeat fixed; filter:saturate(1.05)}
.bg-gradient{position:absolute; inset:0; background:linear-gradient(to right, rgba(255,255,255,.92), rgba(255,255,255,.84) 35%, rgba(255,255,255,.78) 60%, rgba(255,255,255,.90))}
.bg-vignette{position:absolute; inset:0; background:radial-gradient(1200px 800px at 50% 50%, rgba(0,0,0,0) 60%, rgba(0,0,0,.06) 100%)}

.main{position:relative; z-index:2; width:92%; max-width:1200px; margin:64px auto 80px}
.header{text-align:center; margin-bottom:22px}
.header h1{font-size:38px; font-weight:800; margin:0 0 4px}
.header p{margin:0; color:var(--muted)}

.grid{display:grid; grid-template-columns:repeat(auto-fit,minmax(330px,1fr)); gap:28px}
.card{background:var(--glass); border:1px solid rgba(255,255,255,.7); border-radius:22px; overflow:hidden; box-shadow:0 14px 34px rgba(0,0,0,.14); transition:.35s; transform-style:preserve-3d; perspective:1000px}
.card:hover{transform:translateY(-8px) rotateX(.5deg) rotateY(-.5deg); box-shadow:0 20px 44px rgba(0,0,0,.18)}
.card img{width:100%; height:220px; object-fit:cover; display:block}
.card-body{padding:20px 20px 22px}
.card h3{margin:0 0 10px; font-size:20px; font-weight:800; color:#111827}
.card p{margin:0; color:var(--muted); line-height:1.6; font-size:15.5px}

.actions{display:flex; gap:12px; margin-top:16px; flex-wrap:wrap}
.btn{
  --glow:0 10px 26px rgba(255,111,97,.36);
  position:relative; display:inline-block; border:none; cursor:pointer; text-decoration:none;
  border-radius:999px; padding:12px 22px; font-weight:800; font-size:15px; letter-spacing:.2px;
  color:#fff; background:linear-gradient(135deg,var(--brand) 0%,var(--brand-2) 100%);
  box-shadow:var(--glow); transition:transform .2s, box-shadow .2s, background-position .35s; background-size:200% 200%
}
.btn:hover{transform:translateY(-3px); box-shadow:0 16px 34px rgba(255,111,97,.42); background-position:100% 50%}
.btn::after{content:""; position:absolute; inset:0; border-radius:inherit; background:linear-gradient(120deg,rgba(255,255,255,0) 0%,rgba(255,255,255,.35) 50%,rgba(255,255,255,0) 100%); transform:translateX(-150%); transition:.6s}
.btn:hover::after{transform:translateX(150%)}
.btn:active{transform:scale(.97); box-shadow:0 6px 18px rgba(255,111,97,.28)}

.detail{background:var(--glass); border:1px solid rgba(255,255,255,.7); border-radius:24px; padding:28px; box-shadow:0 16px 40px rgba(0,0,0,.16)}
.cover{width:100%; max-height:460px; object-fit:cover; border-radius:20px; box-shadow:0 14px 34px rgba(0,0,0,.18)}
.title{font-size:34px; font-weight:800; margin:18px 0 8px}
.meta{color:#6b7280; margin-bottom:16px}
.content{white-space:pre-line; font-size:17px; line-height:1.8; color:#1f2937; background:rgba(255,255,255,.98); border-radius:18px; padding:18px; box-shadow:0 8px 22px rgba(0,0,0,.08)}
.detail .actions{margin-top:18px}

@media (max-width:992px){ .header h1{font-size:30px} }
</style>

<!-- Background local -->
<div class="bg-stack">
  <div class="bg-photo"></div>
  <div class="bg-gradient"></div>
  <div class="bg-vignette"></div>
</div>

<main class="main">
  <?php if (!$isDetail): ?>
    <header class="header">
      <h1>🐾 Blog Thú Cưng – Pet Care</h1>
      <p>Mẹo hay, dinh dưỡng chuẩn, chăm sóc yêu thương mỗi ngày.</p>
    </header>

    <section class="grid" aria-label="Danh sách bài viết">
      <?php foreach ($posts as $pid => $p): ?>
        <article class="card">
          <img src="<?= htmlspecialchars(img_url($p['cover'])) ?>" alt="" loading="lazy" decoding="async">
          <div class="card-body">
            <h3><?= htmlspecialchars($p['title']) ?></h3>
            <p><?= htmlspecialchars($p['excerpt']) ?></p>
            <div class="actions">
              <a class="btn" href="blog.php?id=<?= $pid ?>">Đọc thêm</a>
              <a class="btn" href="<?= htmlspecialchars($p['link']) ?>" target="_blank" rel="noopener">Chi tiết</a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </section>

  <?php else: ?>
    <section class="detail" aria-label="Chi tiết bài viết">
      <img class="cover" src="<?= htmlspecialchars(img_url($post['cover'])) ?>" alt="" loading="eager">
      <h1 class="title"><?= htmlspecialchars($post['title']) ?></h1>
      <div class="meta">Cập nhật gần đây • Pet Care Blog</div>
      <div class="content"><?= htmlspecialchars($post['content']) ?></div>
      <div class="actions">
        <a class="btn" href="<?= htmlspecialchars($post['link']) ?>" target="_blank" rel="noopener">Mở bài tham khảo gốc</a>
        <a class="btn" href="blog.php">← Quay lại danh sách</a>
      </div>
    </section>
  <?php endif; ?>
</main>

<?php include __DIR__ . '/partials/footer.php'; ?>
