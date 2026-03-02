<?php
include 'db.php';
include 'header.php';

$lang = $_GET['lang'] ?? 'en';
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
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ---------- REQUIRED FIELDS ---------- */
    $requiredFields = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'city',
        'education_level',
        'major',
        'experience_years'
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
            $graduation_year = !empty($_POST['graduation_year']) ? (int) $_POST['graduation_year'] : null;
            $experience_years = (int) $_POST['experience_years'];

            $sql = "INSERT INTO job_applications (
                        job_id, first_name, father_name, grandfather_name, last_name,
                        birth_date, birth_place, gender, nationality,
                        city, phone, emergency_phone, email, address,
                        education_level, major, graduation_year,
                        courses, experience_details, expected_salary, last_salary, notice_period,
                        experience_years, cv_file, status
                    ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param(
                    "isssssssssssssssisssssiss",
                    $job_id,
                    $_POST['first_name'],
                    $_POST['father_name'],
                    $_POST['grandfather_name'],
                    $_POST['last_name'],
                    $_POST['birth_date'],
                    $_POST['birth_place'],
                    $_POST['gender'],
                    $_POST['nationality'],
                    $_POST['city'],
                    $_POST['phone'],
                    $_POST['emergency_phone'],
                    $_POST['email'],
                    $_POST['address'],
                    $_POST['education_level'],
                    $_POST['major'],
                    $graduation_year,
                    $_POST['courses'],
                    $_POST['experience_details'],
                    $_POST['expected_salary'],
                    $_POST['last_salary'],
                    $_POST['notice_period'],
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
                <input type="text" name="first_name" placeholder="<?= $lang === 'ar' ? 'الاسم الأول' : 'First Name' ?>"
                    required>
                <input type="text" name="father_name" placeholder="<?= $lang === 'ar' ? 'اسم الأب' : 'Father Name' ?>">
                <input type="text" name="grandfather_name"
                    placeholder="<?= $lang === 'ar' ? 'اسم الجد' : 'Grandfather Name' ?>">
                <input type="text" name="last_name" placeholder="<?= $lang === 'ar' ? 'اسم العائلة' : 'Last Name' ?>"
                    required>
                <input type="date" name="birth_date">
                <input type="text" name="birth_place" placeholder="<?= $lang === 'ar' ? 'مكان الميلاد' : 'Birth Place' ?>">
                <input type="text" name="gender" placeholder="<?= $lang === 'ar' ? 'الجنس' : 'Gender' ?>">
                <select name="nationality" required>
                    <option value="" disabled selected><?= $lang === 'ar' ? 'الجنسية' : 'Nationality' ?></option>
                    <option value="Saudi"><?= $lang === 'ar' ? 'سعودي' : 'Saudi' ?></option>
                    <option value="UAE"><?= $lang === 'ar' ? 'إماراتي' : 'UAE' ?></option>
                    <option value="Kuwaiti"><?= $lang === 'ar' ? 'كويتي' : 'Kuwaiti' ?></option>
                    <option value="Qatari"><?= $lang === 'ar' ? 'قطري' : 'Qatari' ?></option>
                    <option value="Bahraini"><?= $lang === 'ar' ? 'بحريني' : 'Bahraini' ?></option>
                    <option value="Omani"><?= $lang === 'ar' ? 'عماني' : 'Omani' ?></option>
                    <option value="Egyptian"><?= $lang === 'ar' ? 'مصري' : 'Egyptian' ?></option>
                    <option value="Jordanian"><?= $lang === 'ar' ? 'أردني' : 'Jordanian' ?></option>
                    <option value="Syrian"><?= $lang === 'ar' ? 'سوري' : 'Syrian' ?></option>
                    <option value="Lebanese"><?= $lang === 'ar' ? 'لبناني' : 'Lebanese' ?></option>
                    <option value="Palestinian"><?= $lang === 'ar' ? 'فلسطيني' : 'Palestinian' ?></option>
                    <option value="Yemeni"><?= $lang === 'ar' ? 'يمني' : 'Yemeni' ?></option>
                    <option value="Sudanese"><?= $lang === 'ar' ? 'سوداني' : 'Sudanese' ?></option>
                    <option value="Moroccan"><?= $lang === 'ar' ? 'مغربي' : 'Moroccan' ?></option>
                    <option value="Tunisian"><?= $lang === 'ar' ? 'تونسي' : 'Tunisian' ?></option>
                    <option value="Algerian"><?= $lang === 'ar' ? 'جزائري' : 'Algerian' ?></option>
                    <option value="Indian"><?= $lang === 'ar' ? 'هندي' : 'Indian' ?></option>
                    <option value="Pakistani"><?= $lang === 'ar' ? 'باكستاني' : 'Pakistani' ?></option>
                    <option value="Filipino"><?= $lang === 'ar' ? 'فلبيني' : 'Filipino' ?></option>
                    <option value="Bangladeshi"><?= $lang === 'ar' ? 'بنجلاديشي' : 'Bangladeshi' ?></option>
                    <option value="Other"><?= $lang === 'ar' ? 'أخرى' : 'Other' ?></option>
                </select>
            </div>

            <h3><?= $lang === 'ar' ? 'معلومات التواصل' : 'Contact Information' ?></h3>
            <div class="form-grid">
                <input type="text" name="city" placeholder="<?= $lang === 'ar' ? 'المدينة' : 'City' ?>" required>
                <input type="text" name="phone" placeholder="<?= $lang === 'ar' ? 'رقم الجوال' : 'Phone' ?>" required>
                <input type="text" name="emergency_phone"
                    placeholder="<?= $lang === 'ar' ? 'جوال الطوارئ' : 'Emergency Phone' ?>">
                <input type="email" name="email" placeholder="<?= $lang === 'ar' ? 'البريد الإلكتروني' : 'Email' ?>"
                    required>
                <textarea class="full" name="address"
                    placeholder="<?= $lang === 'ar' ? 'العنوان' : 'Address' ?>"></textarea>
            </div>

            <h3><?= $lang === 'ar' ? 'المؤهلات والخبرة' : 'Qualifications & Experience' ?></h3>
            <div class="form-grid">
                <select name="education_level" required>
                    <option value="" disabled selected><?= $lang === 'ar' ? 'المستوى التعليمي' : 'Education Level' ?>
                    </option>
                    <option value="High School"><?= $lang === 'ar' ? 'ثانوي' : 'High School' ?></option>
                    <option value="Diploma"><?= $lang === 'ar' ? 'دبلوم' : 'Diploma' ?></option>
                    <option value="Bachelor"><?= $lang === 'ar' ? 'بكالوريوس' : 'Bachelor' ?></option>
                    <option value="Master"><?= $lang === 'ar' ? 'ماجستير' : 'Master' ?></option>
                    <option value="PhD"><?= $lang === 'ar' ? 'دكتوراه' : 'PhD' ?></option>
                </select>
                <input type="text" name="major" placeholder="<?= $lang === 'ar' ? 'التخصص' : 'Major' ?>" required>
                <input type="number" name="graduation_year"
                    placeholder="<?= $lang === 'ar' ? 'سنة التخرج' : 'Graduation Year' ?>">
                <textarea class="full" name="courses"
                    placeholder="<?= $lang === 'ar' ? 'الدورات' : 'Courses' ?>"></textarea>
                <textarea class="full" name="experience_details"
                    placeholder="<?= $lang === 'ar' ? 'آخر وظيفة سابقة - اسم المنظمة والمسمى الوظيفي' : 'Previous last job (Organization and Title)' ?>"></textarea>

                <input type="text" name="expected_salary"
                    placeholder="<?= $lang === 'ar' ? 'الراتب المتوقع' : 'Expected Salary' ?>">
                <input type="text" name="last_salary" placeholder="<?= $lang === 'ar' ? 'آخر راتب' : 'Last Salary' ?>">
                <input type="text" name="notice_period"
                    placeholder="<?= $lang === 'ar' ? 'فترة الإنذار' : 'Notice Period' ?>">

                <select name="experience_years" required>
                    <option value="" disabled selected><?= $lang === 'ar' ? 'سنوات الخبرة' : 'Years of Experience' ?>
                    </option>
                    <option value="0"><?= $lang === 'ar' ? 'بدون خبرة' : 'No Experience' ?></option>
                    <option value="1"><?= $lang === 'ar' ? 'سنة واحدة' : '1 Year' ?></option>
                    <option value="2"><?= $lang === 'ar' ? 'سنتين' : '2 Years' ?></option>
                    <option value="3"><?= $lang === 'ar' ? '3 سنوات' : '3 Years' ?></option>
                    <option value="4"><?= $lang === 'ar' ? '4 سنوات' : '4 Years' ?></option>
                    <option value="5"><?= $lang === 'ar' ? '5 سنوات فأكثر' : '5+ Years' ?></option>
                </select>
            </div>

            <h3><?= $lang === 'ar' ? 'السيرة الذاتية' : 'Curriculum Vitae' ?></h3>
            <label
                class="file-label"><?= $lang === 'ar' ? 'تحميل السيرة الذاتية (PDF فقط)' : 'Upload CV (PDF only)' ?></label>
            <input type="file" name="cv" accept="application/pdf" required>

            <button type="submit" class="apply-btn">
                <?= $lang === 'ar' ? 'إرسال الطلب' : 'Submit Application' ?>
            </button>
        </form>
    <?php endif; ?>
</section>

<?php include 'footer.php'; ?>