<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin'])) {
    die("Unauthorized access");
}

/* =======================
    REPLICATE FILTER LOGIC
   ======================= */
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
        a.*, j.title_ar
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

/* =======================
    SET EXCEL HEADERS
   ======================= */
$filename = "applicants_export_" . date('Y-m-d_H-i-s') . ".xls";
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// Status Mapping
$status_map = [
    'pending' => 'قيد الانتظار',
    'review' => 'مراجعة',
    'interview' => 'مقابلة',
    'accepted' => 'مقبول',
    'rejected' => 'مرفوض'
];

/* =======================
    OUTPUT DATA AS TABLE
   ======================= */
echo "<html>";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">";
echo "<body>";
echo "<table border='1'>";
echo "<thead>";
echo "<tr style='background-color: #f2f2f2; font-weight: bold;'>";
echo "<th>ID</th>";
echo "<th>First Name / الاسم</th>";
echo "<th>Last Name / العائلة</th>";
echo "<th>Email / البريد</th>";
echo "<th>Phone / الجوال</th>";
echo "<th>Nationality / الجنسية</th>";
echo "<th>Age / العمر</th>";
echo "<th>Job Title / الوظيفة</th>";
echo "<th>City / المدينة</th>";
echo "<th>Education / التعليم</th>";
echo "<th>Major / التخصص</th>";
echo "<th>Experience Years / سنوات الخبرة</th>";
echo "<th>Expected Salary / الراتب المتوقع</th>";
echo "<th>Last Salary / آخر راتب</th>";
echo "<th>Notice Period / فترة الإنذار</th>";
echo "<th>Experience Details / آخر وظيفة سابقة</th>";
echo "<th>Status / الحالة</th>";
echo "<th>CV Link</th>";
echo "</tr>";
echo "</thead>";
echo "<tbody>";

while ($a = $applicants->fetch_assoc()) {
    $display_status = $status_map[$a['status']] ?? $a['status'];
    $cv_full_link = "https://" . $_SERVER['HTTP_HOST'] . "/" . $a['cv_file'];

    // Nationality Map
    $nat_map = [
        'Saudi' => 'سعودي',
        'UAE' => 'إماراتي',
        'Kuwaiti' => 'كويتي',
        'Qatari' => 'قطري',
        'Bahraini' => 'بحريني',
        'Omani' => 'عماني',
        'Egyptian' => 'مصري',
        'Jordanian' => 'أردني',
        'Syrian' => 'سوري',
        'Lebanese' => 'لبناني',
        'Palestinian' => 'فلسطيني',
        'Yemeni' => 'يمني',
        'Sudanese' => 'سوداني',
        'Moroccan' => 'مغربي',
        'Tunisian' => 'تونسي',
        'Algerian' => 'جزائري',
        'Indian' => 'هندي',
        'Pakistani' => 'باكستاني',
        'Filipino' => 'فلبيني',
        'Bangladeshi' => 'بنجلاديشي',
        'Other' => 'أخرى'
    ];
    $nationality_ar = $nat_map[$a['nationality']] ?? $a['nationality'];

    // Age Calculation
    $age = 'N/A';
    if (!empty($a['birth_date'])) {
        $birthDate = new DateTime($a['birth_date']);
        $today = new DateTime('today');
        $age = $birthDate->diff($today)->y;
    }

    echo "<tr>";
    echo "<td>" . $a['id'] . "</td>";
    echo "<td>" . htmlspecialchars($a['first_name']) . "</td>";
    echo "<td>" . htmlspecialchars($a['last_name']) . "</td>";
    echo "<td>" . htmlspecialchars($a['email']) . "</td>";
    echo "<td>" . htmlspecialchars($a['phone']) . "</td>";
    echo "<td>" . htmlspecialchars($nationality_ar) . "</td>";
    echo "<td>" . htmlspecialchars($age) . "</td>";
    echo "<td>" . htmlspecialchars($a['title_ar']) . "</td>";
    echo "<td>" . htmlspecialchars($a['city']) . "</td>";
    echo "<td>" . htmlspecialchars($a['education_level']) . "</td>";
    echo "<td>" . htmlspecialchars($a['major']) . "</td>";
    echo "<td>" . htmlspecialchars($a['experience_years']) . "</td>";
    echo "<td>" . htmlspecialchars($a['expected_salary']) . "</td>";
    echo "<td>" . htmlspecialchars($a['last_salary']) . "</td>";
    echo "<td>" . htmlspecialchars($a['notice_period']) . "</td>";
    echo "<td>" . htmlspecialchars($a['experience_details']) . "</td>";
    echo "<td>" . htmlspecialchars($display_status) . "</td>";
    echo "<td>" . htmlspecialchars($cv_full_link) . "</td>";
    echo "</tr>";
}

echo "</tbody>";
echo "</table>";
echo "</body>";
echo "</html>";

$conn->close();
exit;
