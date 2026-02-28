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
    $location_en = !empty($_POST['location_en']) ? $_POST['location_en'] : $_POST['location'];
    
    // Default publish date if empty
    $publish_date = !empty($_POST['publish_date']) ? $_POST['publish_date'] : date('Y-m-d');
    $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;

    // Date validation
    if ($end_date && $end_date < $publish_date) {
        $error_msg = "خطأ: تاريخ الانتهاء لا يمكن أن يكون قبل تاريخ النشر.";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO jobs (
                title_en, title_ar, description_en, description_ar, location, location_en,
                job_type, vacancies, salary, publish_date, end_date,
                requirements, requirements_en, tasks, skills, qualifications, experience,
                work_period, languages, gender
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "sssssssissssssssssss",
            $title_en,
            $_POST['title_ar'],
            $description_en,
            $_POST['description_ar'],
            $_POST['location'],
            $location_en,
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
// Build Filter Conditions
$where_clauses = [];
$params = [];
$types = "";

if (!empty($_GET['nationality'])) {
    $where_clauses[] = "a.nationality = ?";
    $params[] = $_GET['nationality'];
    $types .= "s";
}
if (!empty($_GET['education_level'])) {
    $where_clauses[] = "a.education_level = ?";
    $params[] = $_GET['education_level'];
    $types .= "s";
}
if (isset($_GET['experience_years']) && $_GET['experience_years'] !== '') {
    $where_clauses[] = "a.experience_years = ?";
    $params[] = $_GET['experience_years'];
    $types .= "i";
}
if (!empty($_GET['status'])) {
    $where_clauses[] = "a.status = ?";
    $params[] = $_GET['status'];
    $types .= "s";
}

$where_sql = "";
if (count($where_clauses) > 0) {
    $where_sql = "WHERE " . implode(" AND ", $where_clauses);
}

// Fetch Applicants - Joining to get Arabic Job Title
$sql = "
    SELECT 
        a.id, a.first_name, a.last_name, a.email, a.phone, a.cv_file, a.status,
        j.title_ar
    FROM job_applications a
    JOIN jobs j ON a.job_id = j.id
    $where_sql
    ORDER BY a.id DESC
";

if (!empty($params)) {
    $stmt_app = $conn->prepare($sql);
    $stmt_app->bind_param($types, ...$params);
    $stmt_app->execute();
    $applicants = $stmt_app->get_result();
} else {
    $applicants = $conn->query($sql);
}

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
        <button class="tab <?= ($_GET['tab'] ?? '') !== 'applicants' ? 'active' : '' ?>" onclick="showTab(0)">➕ إضافة وظيفة</button>
        <button class="tab <?= ($_GET['tab'] ?? '') === 'applicants' ? 'active' : '' ?>" onclick="showTab(1)">📄 المتقدمين</button>
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
                        <label>Location (English)</label>
                        <input type="text" name="location_en" required style="width: 100%; padding: 10px;">
                    </div>
                    <div class="field">
                        <label>الموقع (المدينة - عربي)</label>
                        <input type="text" name="location" required style="width: 100%; padding: 10px; direction: rtl;">
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
            <h3>تصفية المتقدمين (Filters)</h3>
            <form method="GET" style="display: flex; gap: 15px; margin-bottom: 20px; align-items: flex-end; flex-wrap: wrap;">
                <input type="hidden" name="tab" value="applicants">
                
                <div style="flex: 1; min-width: 150px;">
                    <label style="font-size: 13px;">الجنسية / Nationality</label>
                    <select name="nationality" style="width: 100%; padding: 8px;">
                        <option value="">الكل (All)</option>
                        <option value="Saudi" <?= ($_GET['nationality'] ?? '') === 'Saudi' ? 'selected' : '' ?>>سعودي</option>
                        <option value="Non-Saudi" <?= ($_GET['nationality'] ?? '') === 'Non-Saudi' ? 'selected' : '' ?>>غير سعودي</option>
                    </select>
                </div>
                <div style="flex: 1; min-width: 150px;">
                    <label style="font-size: 13px;">المستوى التعليمي / Education</label>
                    <select name="education_level" style="width: 100%; padding: 8px;">
                        <option value="">الكل (All)</option>
                        <option value="High School" <?= ($_GET['education_level'] ?? '') === 'High School' ? 'selected' : '' ?>>ثانوي</option>
                        <option value="Diploma" <?= ($_GET['education_level'] ?? '') === 'Diploma' ? 'selected' : '' ?>>دبلوم</option>
                        <option value="Bachelor" <?= ($_GET['education_level'] ?? '') === 'Bachelor' ? 'selected' : '' ?>>بكالوريوس</option>
                        <option value="Master" <?= ($_GET['education_level'] ?? '') === 'Master' ? 'selected' : '' ?>>ماجستير</option>
                        <option value="PhD" <?= ($_GET['education_level'] ?? '') === 'PhD' ? 'selected' : '' ?>>دكتوراه</option>
                    </select>
                </div>
                <div style="flex: 1; min-width: 150px;">
                    <label style="font-size: 13px;">سنوات الخبرة / Experience</label>
                    <select name="experience_years" style="width: 100%; padding: 8px;">
                        <option value="">الكل (All)</option>
                        <option value="0" <?= iisset($_GET['experience_years']) && $_GET['experience_years'] === '0' ? 'selected' : '' ?>>بدون خبرة</option>
                        <option value="1" <?= ($_GET['experience_years'] ?? '') === '1' ? 'selected' : '' ?>>1 سنة</option>
                        <option value="2" <?= ($_GET['experience_years'] ?? '') === '2' ? 'selected' : '' ?>>2 سنوات</option>
                        <option value="3" <?= ($_GET['experience_years'] ?? '') === '3' ? 'selected' : '' ?>>3 سنوات</option>
                        <option value="4" <?= ($_GET['experience_years'] ?? '') === '4' ? 'selected' : '' ?>>4 سنوات</option>
                        <option value="5" <?= ($_GET['experience_years'] ?? '') === '5' ? 'selected' : '' ?>>5+ سنوات</option>
                    </select>
                </div>
                <div style="flex: 1; min-width: 150px;">
                    <label style="font-size: 13px;">الحالة / Status</label>
                    <select name="status" style="width: 100%; padding: 8px;">
                        <option value="">الكل (All)</option>
                        <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>قيد الانتظار</option>
                        <option value="review" <?= ($_GET['status'] ?? '') === 'review' ? 'selected' : '' ?>>مراجعة</option>
                        <option value="interview" <?= ($_GET['status'] ?? '') === 'interview' ? 'selected' : '' ?>>مقابلة</option>
                        <option value="accepted" <?= ($_GET['status'] ?? '') === 'accepted' ? 'selected' : '' ?>>مقبول</option>
                        <option value="rejected" <?= ($_GET['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>مرفوض</option>
                    </select>
                </div>
                <div>
                    <button type="submit" style="padding: 8px 20px; background: var(--gold); color: white; border: none; cursor: pointer; border-radius: 4px;">تصفية</button>
                    <a href="career-admin.php?tab=applicants" style="padding: 9px 20px; background: #eee; color: #333; text-decoration: none; border-radius: 4px; margin-right:5px;">إلغاء</a>
                </div>
            </form>
        </div>

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
                                    <?php 
                                        $status_map = [
                                            'pending' => 'قيد الانتظار',
                                            'review' => 'مراجعة',
                                            'interview' => 'مقابلة',
                                            'accepted' => 'مقبول',
                                            'rejected' => 'مرفوض'
                                        ];
                                        $display_status = $status_map[$a['status']] ?? $a['status'];
                                        $status_class = 'status-' . strtolower($a['status']);
                                    ?>
                                    <span class="status-badge <?= $status_class ?>"><?= htmlspecialchars($display_status) ?></span>
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
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        if(urlParams.get('tab') === 'applicants') {
            showTab(1);
        } else {
            showTab(0);
        }
    });

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

    /* Status Badges Styling */
    .status-badge {
        padding: 5px 12px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: bold;
        display: inline-block;
        white-space: nowrap;
    }
    .status-pending { background: #fdf0d5; color: #b58a3c; } 
    .status-review { background: #e0f0ff; color: #0056b3; } 
    .status-interview { background: #f0e6ff; color: #6f42c1; } 
    .status-accepted { background: #d4edda; color: #155724; } 
    .status-rejected { background: #f8d7da; color: #721c24; }
</style>

<?php include 'footer.php'; ?>