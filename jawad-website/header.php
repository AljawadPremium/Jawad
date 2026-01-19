<?php
// Detect current page language
$isArabicPage = (
    strpos($_SERVER['PHP_SELF'], 'index-ar') !== false ||
    strpos($_SERVER['PHP_SELF'], 'career-ar') !== false ||
    (isset($_GET['lang']) && $_GET['lang'] == 'ar')
);

// Detect home page (index only)
$isHomePage = (
    strpos($_SERVER['PHP_SELF'], 'index.php') !== false ||
    strpos($_SERVER['PHP_SELF'], 'index-ar.php') !== false
);

// --- Language Switch Logic ---
$currentFile = basename($_SERVER['PHP_SELF']);
$queryParams = $_GET;

if ($currentFile == 'index.php') {
    $targetFile = 'index-ar.php';
} elseif ($currentFile == 'index-ar.php') {
    $targetFile = 'index.php';
} elseif ($currentFile == 'career.php') {
    $targetFile = 'career-ar.php';
} elseif ($currentFile == 'career-ar.php') {
    $targetFile = 'career.php';
} else {
    $targetFile = $currentFile;
    if ($isArabicPage) {
        unset($queryParams['lang']);
    } else {
        $queryParams['lang'] = 'ar';
    }
}

$queryString = http_build_query($queryParams);
$switchLangUrl = $targetFile . ($queryString ? '?' . $queryString : '');
?>

<!DOCTYPE html>
<html lang="<?php echo $isArabicPage ? 'ar' : 'en'; ?>" <?php echo $isArabicPage ? 'dir="rtl"' : ''; ?>>

<head>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" href="/Img/iPhone.png">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title>
        <?php echo $isArabicPage
            ? 'الجواد بريميوم | خدمات الضيافة، توريد الأغذية والخدمات اللوجستية في السعودية'
            : 'Aljawad Premium | Hospitality, Catering & Food Supply Solutions in Saudi Arabia';
        ?>
    </title>

    <meta name="description" content="<?php echo $isArabicPage
        ? 'شركة جواد الضيافة (الجواد بريميوم) متخصصة في خدمات الضيافة وتوريد الأغذية والخدمات اللوجستية في السعودية. نقدم حلولاً متكاملة للقطاعين الحكومي والخاص.'
        : 'Aljawad Premium specialized in Hospitality, Catering, and Food Supply Solutions in Saudi Arabia. Providing integrated logistics and food services for govt & private sectors.';
    ?>" />

    <!-- Social Media Meta Tags (OG) -->
    <meta property="og:title" content="Aljawad Premium | Hospitality & Food Supply" />
    <meta property="og:description"
        content="Your trusted partner in hospitality, catering, and food supply services across Saudi Arabia." />
    <meta property="og:image" content="/Img/logo.png" />
    <meta property="og:url" content="https://aljawad.com" />
    <meta property="og:type" content="website" />

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Aljawad Premium | Hospitality & Food Supply" />
    <meta name="twitter:description"
        content="Your trusted partner in catering and food supply services across Saudi Arabia." />
    <meta name="twitter:image" content="/Img/logo.png" />

    <!-- Main global styles with versioning for cache control -->
    <link rel="stylesheet" href="/style.css?v=<?php echo filemtime('style.css'); ?>">

    <!-- Career public pages -->
    <?php
    if (
        strpos($_SERVER['PHP_SELF'], 'career.php') !== false ||
        strpos($_SERVER['PHP_SELF'], 'career-ar.php') !== false
    ):
        ?>
        <link rel="stylesheet" href="/careerStyle.css?v=10">
    <?php endif; ?>

    <!-- Career admin page -->
    <?php
    if (strpos($_SERVER['PHP_SELF'], 'career-admin.php') !== false):
        ?>
        <link rel="stylesheet" href="/careerAdminStyle.css?v=10">
    <?php endif; ?>

    <?php
    if (strpos($_SERVER['PHP_SELF'], 'apply.php') !== false):
        ?>
        <link rel="stylesheet" href="/applicationStyle.css?v=10">
    <?php endif; ?>


    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-0BQ05GZ7XE"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'G-0BQ05GZ7XE');
    </script>
</head>

<body class="<?php echo $isArabicPage ? 'rtl' : ''; ?>">

    <nav>
        <div class="nav-container">

            <!-- Logo -->
            <img src="Img/logo.png" class="nav-logo" alt="Logo">

            <!-- Navigation -->
            <ul>

                <!-- Home / Careers -->
                <?php if ($isArabicPage): ?>
                    <li><a href="index-ar.php">الرئيسية</a></li>
                    <li><a href="career-ar.php">الوظائف</a></li>
                <?php else: ?>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="career.php">Careers</a></li>
                <?php endif; ?>

                <!-- Landing sections (home only) -->
                <?php if ($isHomePage && !$isArabicPage): ?>
                    <li><a href="#about">Who We Are</a></li>
                    <li><a href="#presence">Our Presence</a></li>
                    <li><a href="#logistics">Logistics</a></li>
                    <li><a href="#licenses">Licenses</a></li>
                    <li><a href="#factory">Factory</a></li>
                    <li><a href="#brands">Brands</a></li>
                    <li><a href="#partners">Partners</a></li>
                <?php endif; ?>

                <?php if ($isHomePage && $isArabicPage): ?>
                    <li><a href="#about">من نحن</a></li>
                    <li><a href="#presence">توسعنا</a></li>
                    <li><a href="#logistics">المنظومة اللوجستية</a></li>
                    <li><a href="#licenses">تراخيصنا</a></li>
                    <li><a href="#factory">مصنع الأغذية</a></li>
                    <li><a href="#brands">العلامات التجارية</a></li>
                    <li><a href="#partners">شركاؤنا</a></li>
                <?php endif; ?>

                <!-- Store -->
                <li>
                    <a href="#" class="store-btn">
                        <?php echo $isArabicPage ? 'المتجر' : 'Store'; ?>
                    </a>
                </li>

                <!-- Language switch -->
                <li><a href="<?= $switchLangUrl ?>"
                        class="lang-btn"><?php echo $isArabicPage ? 'English' : 'عربي'; ?></a></li>


            </ul>
        </div>
    </nav>

    <div class="page-wrapper">