<?php if(!isset($page_title)) { $page_title = 'Pet Care | Mr.Luu Dat'; } ?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($page_title) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
  <div class="container header-inner">
    <!-- Brand -->
    <a class="brand" href="index.php">
      <span>🐾 Mr. Lưu Đạt</span>
    </a>

    <!-- Mobile toggle -->
    <input id="nav-toggle" type="checkbox" hidden>
    <label for="nav-toggle" class="hamburger" aria-label="Toggle navigation">
      <span></span><span></span><span></span>
    </label>

    <!-- Center nav -->
    <nav class="primary-nav">
      <ul class="nav-list">
        <li><a href="index.php">Home</a></li>
        <li><a href="services.php">Services</a></li>
        <li><a href="blog.php">Blog</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li><a href="members.php">Members</a></li>
      </ul>
    </nav>

    <!-- Actions -->
    <div class="actions">
      <a href="login.php" class="btn btn-ghost small">Log in</a>
      <a href="tel:+8466172876" class="btn btn-accent small">CALL +84-661-728-76</a>
    </div>
  </div>
</header>
<main class="container">
