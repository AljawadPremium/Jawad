<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

/* =======================
    HANDLE ADD JOB
======================= */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_job'])) {
    // Basic fallbacks for English if missing
    $title_en = !empty($_POST['title_en']) ? $_POST['title_en'] : $_POST['title_ar'];
    $description_en = !empty($_POST['description_en']) ? $_POST['description_en'] : $_POST['description_ar'];
    $requirements_en = !empty($_POST['requirements_en']) ? $_POST['requirements_en'] : $_POST['requirements'];
    
    // Default publish date if empty
    $publish_date = !empty($_POST['publish_date']) ? $_POST['publish_date'] : date('Y-m-d');
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;

    // Date validation
    if ($end_date && $end_date < $publish_date) {
        $error_msg = "خطأ: تاريخ الانتهاء لا يمكن أن يكون قبل تاريخ النشر.";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO jobs (
                title_en, title_ar, description_en, description_ar, location,
                job_type, vacancies, salary, publish_date, end_date,
                requirements, requirements_en, tasks, skills, qualifications, experience,
                work_period, languages, gender
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "ssssssissssssssssss",
            $title_en,
            $_POST['title_ar'],
            $description_en,
            $_POST['description_ar'],
            $_POST['location'],
            $_POST['job_type'],
            $_POST['vacancies'],
            $_POST['salary'],
            $publish_date,
            $end_date,
            $_POST['requirements'],
            $requirements_en,
            $_POST['tasks'],
            $_POST['skills'],
            $_POST['qualifications'],
            $_POST['experience'],
            $_POST['work_period'],
            $_POST['languages'],
            $_POST['gender']
        );

        if ($stmt->execute()) {
            $success_msg = "تمت إضافة الوظيفة بنجاح.";
        } else {
            $error_msg = "خطأ في إضافة الوظيفة: " . $conn->error;
        }
        $stmt->close();
    }
}

/* =======================
    FETCH DATA
======================= */
// Fetch Applicants - Joining to get Arabic Job Title
$applicants = $conn->query("
    SELECT 
        a.id, a.first_name, a.last_name, a.email, a.phone, a.cv_file, a.status,
        j.title_ar
    FROM job_applications a
    JOIN jobs j ON a.job_id = j.id
    ORDER BY a.id DESC
");

// Fetch Existing Jobs in Arabic
$jobs_list = $conn->query("SELECT id, title_ar FROM jobs ORDER BY id DESC");

include 'header.php';
?>

<section class="career-admin" dir="rtl" style="text-align: right; padding: 40px 20px;">

    <h2 style="color: var(--gold); margin-bottom: 30px;">لوحة تحكم التوظيف</h2>

    <?php if (isset($error_msg)): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <?= $error_msg ?>
        </div>
    <?php endif; ?>

    <?php if (isset($success_msg)): ?>
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <?= $success_msg ?>
        </div>
    <?php endif; ?>

    <div class="admin-tabs" style="margin-bottom: 20px; display: flex; gap: 10px;">
        <button class="tab active" onclick="showTab(0)">➕ إضافة وظيفة</button>
        <button class="tab" onclick="showTab(1)">📄 المتقدمين</button>
    </div>

    <div id="tab-jobs">
        <div class="admin-card">
            <h3>إضافة وظيفة جديدة</h3>
            <form method="POST" class="admin-form">
                <input type="hidden" name="add_job" value="1">
                <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="field">
                        <label>Job Title (English)</label>
                        <input type="text" name="title_en" required style="width: 100%; padding: 10px;">
                    </div>
                    <div class="field">
                        <label>عنوان الوظيفة (عربي)</label>
                        <input type="text" name="title_ar" required style="width: 100%; padding: 10px; direction: rtl;">
                    </div>
                    <div class="field full" style="grid-column: span 2;">
                        <label>Job Description (English)</label>
                        <textarea name="description_en" required style="width: 100%; height: 100px;"></textarea>
                    </div>
                    <div class="field full" style="grid-column: span 2;">
                        <label>وصف الوظيفة (عربي)</label>
                        <textarea name="description_ar" required
                            style="width: 100%; height: 100px; direction: rtl;"></textarea>
                    </div>
                    <div class="field">
                        <label>الموقع (المدينة)</label>
                        <input type="text" name="location">
                    </div>
                    <div class="field">
                        <label>نوع العمل</label>
                        <select name="job_type" style="width: 100%; padding: 10px;">
                            <option value="Full-time">دوام كامل (Full-time)</option>
                            <option value="Part-time">دوام جزئي (Part-time)</option>
                            <option value="Contract">عقد (Contract)</option>
                            <option value="Remote">عمل عن بعد (Remote)</option>
                            <option value="Internship">تدريب (Internship)</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>عدد الشواغر</label>
                        <input type="number" name="vacancies">
                    </div>
                    <div class="field">
                        <label>الراتب</label>
                        <input type="text" name="salary">
                    </div>
                    <div class="field">
                        <label>تاريخ النشر</label>
                        <input type="date" name="publish_date">
                    </div>
                    <div class="field">
                        <label>تاريخ الانتهاء</label>
                        <input type="date" name="end_date">
                    </div>
                    <div class="field full" style="grid-column: span 2;">
                        <label>Requirements (English) - each point in a new line</label>
                        <textarea name="requirements_en" style="width: 100%; height: 80px;"></textarea>
                    </div>
                    <div class="field full" style="grid-column: span 2;">
                        <label>المتطلبات (عربي) - ضع كل نقطة في سطر جديد</label>
                        <textarea name="requirements" style="width: 100%; height: 80px;"></textarea>
                    </div>
                </div>
                <button type="submit" class="admin-btn"
                    style="background: var(--gold); color: white; padding: 12px 30px; border: none; margin-top: 20px; cursor: pointer; border-radius: 5px;">حفظ
                    الوظيفة</button>
            </form>
        </div>

        <div class="admin-card">
            <h3>الوظائف الحالية</h3>
            <?php if ($jobs_list->num_rows === 0): ?>
                <p>لا يوجد وظائف حالياً.</p>
            <?php else: ?>
                <?php while ($job = $jobs_list->fetch_assoc()): ?>
                    <div class="job-row"
                        style="display:flex; justify-content:space-between; padding:15px; border-bottom:1px solid #eee;">
                        <span><?= htmlspecialchars($job['title_ar']) ?></span>
                        <div class="actions">
                            <a href="career-edit.php?id=<?= $job['id'] ?>" style="color: blue;">تعديل</a> |
                            <a href="career-delete.php?id=<?= $job['id'] ?>" style="color:red;"
                                onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>

    <div id="tab-applicants" style="display:none;">
        <div class="admin-card">
            <h3>قائمة المتقدمين</h3>
            <?php if (!$applicants || $applicants->num_rows === 0): ?>
                <p>لا يوجد طلبات توظيف حالياً.</p>
            <?php else: ?>
                <table class="admin-table" style="width:100%; border-collapse: collapse; margin-top: 15px;">
                    <thead>
                        <tr style="background:#f4f4f4; text-align:right;">
                            <th style="padding: 10px;">الاسم</th>
                            <th>الوظيفة</th>
                            <th>السيرة الذاتية</th>
                            <th>الحالة</th>
                            <th>الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($a = $applicants->fetch_assoc()): ?>
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 10px;"><?= htmlspecialchars($a['first_name'] . ' ' . $a['last_name']) ?></td>
                                <td><?= htmlspecialchars($a['title_ar']) ?></td>
                                <td><a href="<?= htmlspecialchars($a['cv_file']) ?>" target="_blank"
                                        style="color: var(--gold);">تحميل CV</a></td>
                                <td>
                                    <span class="status-badge"><?= htmlspecialchars($a['status']) ?></span>
                                </td>
                                <td><a href="applicant-view.php?id=<?= $a['id'] ?>" class="view-btn">عرض التفاصيل</a></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

</section>

<script>
    function showTab(index) {
        const jobsTab = document.getElementById('tab-jobs');
        const applicantsTab = document.getElementById('tab-applicants');
        const tabs = document.querySelectorAll('.tab');

        tabs.forEach(t => t.classList.remove('active'));
        tabs[index].classList.add('active');

        if (index === 0) {
            jobsTab.style.display = 'block';
            applicantsTab.style.display = 'none';
        } else {
            jobsTab.style.display = 'none';
            applicantsTab.style.display = 'block';
        }
    }
</script>

<style>
    .tab.active {
        background-color: var(--gold);
        color: white;
        border-radius: 5px;
        border: none;
    }

    .tab {
        cursor: pointer;
        padding: 10px 25px;
        border: 1px solid #ddd;
        background: #f8f9fa;
        font-weight: bold;
    }

    .admin-card {
        margin-top: 20px;
        border: 1px solid #ddd;
        padding: 25px;
        border-radius: 12px;
        background: white;
    }

    .view-btn {
        background: #eee;
        padding: 5px 10px;
        border-radius: 4px;
        text-decoration: none;
        color: #333;
        font-size: 14px;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        color: #555;
    }
</style>

<?php include 'footer.php'; ?>