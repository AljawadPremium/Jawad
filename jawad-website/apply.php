<?php
include 'db.php';
include 'header.php';

$lang   = $_GET['lang'] ?? 'en';
$job_id = $_GET['job_id'] ?? null;

/* ---------- Validate Job ---------- */
if (!$job_id || !is_numeric($job_id)) {
    echo "<p style='margin-top:120px;text-align:center;'>Invalid job.</p>";
    include 'footer.php';
    exit;
}

$stmt = $conn->prepare("SELECT title_en, title_ar FROM jobs WHERE id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p style='margin-top:120px;text-align:center;'>Job not found.</p>";
    include 'footer.php';
    exit;
}

$job = $result->fetch_assoc();

/* =======================
    HANDLE SUBMISSION
======================= */
$success = false;
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ---------- REQUIRED FIELDS ---------- */
    $requiredFields = [
        'first_name','last_name','phone','email','city',
        'education_level','qualification','major','experience_years'
    ];

    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            $error = $lang === 'ar' ? 'يرجى تعبئة جميع الحقول المطلوبة' : 'Please fill all required fields';
            break;
        }
    }

    /* ---------- FILE VALIDATION ---------- */
    if (!$error) {
        if (
            !isset($_FILES['cv']) || 
            $_FILES['cv']['error'] !== 0 || 
            mime_content_type($_FILES['cv']['tmp_name']) !== 'application/pdf'
        ) {
            $error = $lang === 'ar' ? 'السيرة الذاتية يجب أن تكون بصيغة PDF فقط' : 'CV must be a PDF file only';
        }
    }

    /* ---------- INSERT ---------- */
    if (!$error) {
        $cv_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['cv']['name']);
        $cv_path = 'uploads/cv/' . $cv_name;

        if (!move_uploaded_file($_FILES['cv']['tmp_name'], $cv_path)) {
            $error = 'Failed to upload CV';
        } else {
            $status = 'pending';
            $graduation_year = !empty($_POST['graduation_year']) ? (int)$_POST['graduation_year'] : null;
            $experience_years = (int)$_POST['experience_years'];

            $sql = "INSERT INTO job_applications (
                        job_id, first_name, father_name, grandfather_name, last_name,
                        birth_date, birth_place, gender, nationality, national_id,
                        city, phone, emergency_phone, email, address,
                        education_level, qualification, major, graduation_year,
                        courses, experience_details, experience_years, cv_file, status
                    ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param(
                    "isssssssssssssssssississ", 
                    $job_id,
                    $_POST['first_name'],
                    $_POST['father_name'],
                    $_POST['grandfather_name'],
                    $_POST['last_name'],
                    $_POST['birth_date'],
                    $_POST['birth_place'],
                    $_POST['gender'],
                    $_POST['nationality'],
                    $_POST['national_id'],
                    $_POST['city'],
                    $_POST['phone'],
                    $_POST['emergency_phone'],
                    $_POST['email'],
                    $_POST['address'],
                    $_POST['education_level'],
                    $_POST['qualification'],
                    $_POST['major'],
                    $graduation_year,
                    $_POST['courses'],
                    $_POST['experience_details'],
                    $experience_years,
                    $cv_path,
                    $status
                );

                if ($stmt->execute()) {
                    $success = true;
                } else {
                    $error = "Database error: Unable to save application.";
                }
                $stmt->close();
            } else {
                $error = "System error: Failed to prepare statement.";
            }
        }
    }
}
?>

<section style="margin-top:120px;" <?= $lang === 'ar' ? 'dir="rtl"' : '' ?>>
    <h2>
        <?= $lang === 'ar' ? 'التقديم على وظيفة:' : 'Apply for:' ?>
        <?= htmlspecialchars($lang === 'ar' ? $job['title_ar'] : $job['title_en']) ?>
    </h2>

    <?php if ($success): ?>
        <p style="color:green;font-weight:bold;">
            <?= $lang === 'ar' ? 'تم إرسال طلبك بنجاح' : 'Your application has been submitted successfully' ?>
        </p>
    <?php else: ?>

        <?php if ($error): ?>
            <p style="color:red;font-weight:bold;">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="application-form">
            <h3><?= $lang === 'ar' ? 'البيانات الشخصية' : 'Personal Information' ?></h3>
            <div class="form-grid">
                <input type="text" name="first_name" placeholder="<?= $lang === 'ar' ? 'الاسم الأول' : 'First Name' ?>" required>
                <input type="text" name="father_name" placeholder="<?= $lang === 'ar' ? 'اسم الأب' : 'Father Name' ?>">
                <input type="text" name="grandfather_name" placeholder="<?= $lang === 'ar' ? 'اسم الجد' : 'Grandfather Name' ?>">
                <input type="text" name="last_name" placeholder="<?= $lang === 'ar' ? 'اسم العائلة' : 'Last Name' ?>" required>
                <input type="date" name="birth_date">
                <input type="text" name="birth_place" placeholder="<?= $lang === 'ar' ? 'مكان الميلاد' : 'Birth Place' ?>">
                <input type="text" name="gender" placeholder="<?= $lang === 'ar' ? 'الجنس' : 'Gender' ?>">
                <input type="text" name="nationality" placeholder="<?= $lang === 'ar' ? 'الجنسية' : 'Nationality' ?>">
                <input type="text" name="national_id" placeholder="<?= $lang === 'ar' ? 'رقم الهوية' : 'National ID' ?>">
            </div>

            <h3><?= $lang === 'ar' ? 'معلومات التواصل' : 'Contact Information' ?></h3>
            <div class="form-grid">
                <input type="text" name="city" placeholder="<?= $lang === 'ar' ? 'المدينة' : 'City' ?>" required>
                <input type="text" name="phone" placeholder="<?= $lang === 'ar' ? 'رقم الجوال' : 'Phone' ?>" required>
                <input type="text" name="emergency_phone" placeholder="<?= $lang === 'ar' ? 'جوال الطوارئ' : 'Emergency Phone' ?>">
                <input type="email" name="email" placeholder="<?= $lang === 'ar' ? 'البريد الإلكتروني' : 'Email' ?>" required>
                <textarea class="full" name="address" placeholder="<?= $lang === 'ar' ? 'العنوان' : 'Address' ?>"></textarea>
            </div>

            <h3><?= $lang === 'ar' ? 'المؤهلات والخبرة' : 'Qualifications & Experience' ?></h3>
            <div class="form-grid">
                <input type="text" name="education_level" placeholder="<?= $lang === 'ar' ? 'المستوى التعليمي' : 'Education Level' ?>" required>
                <input type="text" name="qualification" placeholder="<?= $lang === 'ar' ? 'المؤهل' : 'Qualification' ?>" required>
                <input type="text" name="major" placeholder="<?= $lang === 'ar' ? 'التخصص' : 'Major' ?>" required>
                <input type="number" name="graduation_year" placeholder="<?= $lang === 'ar' ? 'سنة التخرج' : 'Graduation Year' ?>">
                <textarea class="full" name="courses" placeholder="<?= $lang === 'ar' ? 'الدورات' : 'Courses' ?>"></textarea>
                <textarea class="full" name="experience_details" placeholder="<?= $lang === 'ar' ? 'الخبرات السابقة' : 'Experience Details' ?>"></textarea>
                <input type="text" name="experience_years" placeholder="<?= $lang === 'ar' ? 'سنوات الخبرة' : 'Years of Experience' ?>" required>
            </div>

            <h3><?= $lang === 'ar' ? 'السيرة الذاتية' : 'Curriculum Vitae' ?></h3>
            <label class="file-label"><?= $lang === 'ar' ? 'تحميل السيرة الذاتية (PDF فقط)' : 'Upload CV (PDF only)' ?></label>
            <input type="file" name="cv" accept="application/pdf" required>

            <button type="submit" class="apply-btn">
                <?= $lang === 'ar' ? 'إرسال الطلب' : 'Submit Application' ?>
            </button>
        </form>
    <?php endif; ?>
</section>

<?php include 'footer.php'; ?>
