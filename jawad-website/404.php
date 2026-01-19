<?php include 'header.php'; ?>

<section class="error-404"
    style="min-height: 80vh; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; padding: 120px 20px;">
    <h1 style="font-size: 120px; color: var(--gold); margin: 0; line-height: 1;">404</h1>
    <h2 style="font-size: 32px; color: #222; margin-top: 20px;">
        <?php echo $isArabicPage ? 'عذراً، الصفحة غير موجودة' : 'Oops! Page Not Found'; ?>
    </h2>
    <p style="font-size: 18px; color: #666; max-width: 600px; margin-top: 15px;">
        <?php echo $isArabicPage
            ? 'يبدو أن الصفحة التي تبحث عنها قد تم نقلها أو حذفها.'
            : 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.';
        ?>
    </p>
    <a href="<?php echo $isArabicPage ? 'index-ar.php' : 'index.php'; ?>" class="lang-btn"
        style="margin-top: 30px; text-decoration: none; padding: 12px 30px; display: inline-block;">
        <?php echo $isArabicPage ? 'العودة للرئيسية' : 'Back to Home'; ?>
    </a>
</section>

<?php include 'footer.php'; ?>