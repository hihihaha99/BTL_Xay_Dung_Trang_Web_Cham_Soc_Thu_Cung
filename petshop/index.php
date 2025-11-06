<?php
require __DIR__ . '/includes/auth.php';
$page_title = 'Pet Care — House Call Veterinarian';
include __DIR__ . '/partials/header.php';
?>
<section class="hero">
  <div class="hero-text">
    <h1 class="hero-title"><span>Pet</span><span>Care</span></h1>
    <div class="cta">
      <a href="#" class="btn">SCHEDULE A VISIT</a>
      <span class="badge">Trusted by 1,000+ pet parents</span>
    </div>
  </div>
<div class="hero-figure">
  <img class="hero-img"
       src="assets/img/dog.gif"
       alt="Happy dog"
       onerror="this.src='assets/img/hero-dog.jpg'"><!-- fallback khi GIF lỗi -->
  </div>

</section>

<section class="cards">
  <div class="card">
    <h3>House Calls</h3>
    <p>Exams, vaccinations and wellness visits without leaving home.</p>
  </div>
  <div class="card">
    <h3>Urgent Care</h3>
    <p>Same-day visits for minor emergencies & concerns.</p>
  </div>
  <div class="card">
    <h3>TeleVet</h3>
    <p>Fast advice over video for common issues and follow‑ups.</p>
  </div>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>
