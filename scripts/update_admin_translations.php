<?php

$basePath = __DIR__.'/../lang/';
$languages = ['en', 'ceb', 'fil', 'sub'];

$keys = [
    // Sidebar
    'admin_main_menu' => ['en' => 'Main Menu', 'ceb' => 'Pangunang Menu', 'fil' => 'Pangunahing Menu', 'sub' => 'Pangunang Menu'],
    'admin_dashboard' => ['en' => 'Dashboard', 'ceb' => 'Dashboard', 'fil' => 'Dashboard', 'sub' => 'Dashboard'],
    'admin_services' => ['en' => 'Services', 'ceb' => 'Mga Serbisyo', 'fil' => 'Mga Serbisyo', 'sub' => 'Mga Serbisyo'],
    'admin_requirements' => ['en' => 'Requirements', 'ceb' => 'Mga Requirements', 'fil' => 'Mga Requirements', 'sub' => 'Mga Requirements'],
    'admin_eligibility' => ['en' => 'Eligibility Questions', 'ceb' => 'Mga Pangutana sa Kwalipikasyon', 'fil' => 'Mga Tanong sa Kwalipikasyon', 'sub' => 'Mga Pangutana sa Kwalipikasyon'],
    'admin_reassessments' => ['en' => 'Reassessment Requests', 'ceb' => 'Mga Hangyo sa Reassessment', 'fil' => 'Mga Hiling sa Reassessment', 'sub' => 'Mga Hangyo sa Reassessment'],
    'admin_applications_menu' => ['en' => 'Applications', 'ceb' => 'Mga Aplikasyon', 'fil' => 'Mga Aplikasyon', 'sub' => 'Mga Aplikasyon'],
    'admin_inquiries' => ['en' => 'Inquiries', 'ceb' => 'Mga Pangutana', 'fil' => 'Mga Katanungan', 'sub' => 'Mga Pangutana'],
    'admin_reports' => ['en' => 'Reports', 'ceb' => 'Mga Report', 'fil' => 'Mga Ulat', 'sub' => 'Mga Report'],
    'admin_citizens' => ['en' => 'Citizens Registry', 'ceb' => 'Rehistro sa mga Lungsoranon', 'fil' => 'Rehistro ng mga Mamamayan', 'sub' => 'Rehistro sa mga Lungsoranon'],
    'admin_assessments' => ['en' => 'Assessments', 'ceb' => 'Mga Pagsusi', 'fil' => 'Mga Pagsusuri', 'sub' => 'Mga Pagsusi'],
    'admin_log_out' => ['en' => 'Log Out', 'ceb' => 'Mag-logout', 'fil' => 'Mag-logout', 'sub' => 'Mag-logout'],

    // Topbar
    'admin_today' => ['en' => 'Today', 'ceb' => 'Karon', 'fil' => 'Ngayon', 'sub' => 'Karon'],
    'admin_facilitator' => ['en' => 'Facilitator', 'ceb' => 'Facilitator', 'fil' => 'Tagapangasiwa', 'sub' => 'Facilitator'],

    // Dashboard
    'admin_welcome_back' => ['en' => 'Welcome back,', 'ceb' => 'Maayong pagbalik,', 'fil' => 'Maligayang pagbabalik,', 'sub' => 'Maayong pagbalik,'],
    'admin_overview' => ['en' => 'Here\'s an overview of GovAssist activity.', 'ceb' => 'Kini ang usa ka kinatibuk-ang pagtan-aw sa kalihokan sa GovAssist.', 'fil' => 'Narito ang isang pangkalahatang-ideya ng aktibidad sa GovAssist.', 'sub' => 'Kini ang usa ka kinatibuk-ang pagtan-aw sa kalihokan sa GovAssist.'],
    'admin_total_citizens' => ['en' => 'Total Citizens', 'ceb' => 'Kinatibuk-ang Lungsoranon', 'fil' => 'Kabuuang Mamamayan', 'sub' => 'Kinatibuk-ang Lungsoranon'],
    'admin_gov_services' => ['en' => 'Gov Services', 'ceb' => 'Serbisyo sa Gobyerno', 'fil' => 'Serbisyo ng Gobyerno', 'sub' => 'Serbisyo sa Gobyerno'],
    'admin_open_applications' => ['en' => 'Open Applications', 'ceb' => 'Bukas nga mga Aplikasyon', 'fil' => 'Bukas na mga Aplikasyon', 'sub' => 'Bukas nga mga Aplikasyon'],
    'admin_recent_applications' => ['en' => 'Recent Submitted Applications', 'ceb' => 'Bag-ong Gipasa nga mga Aplikasyon', 'fil' => 'Kamakailang mga Isinumiteng Aplikasyon', 'sub' => 'Bag-ong Gipasa nga mga Aplikasyon'],
    'admin_view_all' => ['en' => 'View All', 'ceb' => 'Tan-awa Tanan', 'fil' => 'Tingnan Lahat', 'sub' => 'Tan-awa Tanan'],
    'admin_citizen_name' => ['en' => 'Citizen Name', 'ceb' => 'Ngalan sa Lungsoranon', 'fil' => 'Pangalan ng Mamamayan', 'sub' => 'Ngalan sa Lungsoranon'],
    'admin_assistance_service' => ['en' => 'Assistance Service', 'ceb' => 'Serbisyo sa Tabang', 'fil' => 'Serbisyo ng Tulong', 'sub' => 'Serbisyo sa Tabang'],
    'admin_submitted_at' => ['en' => 'Submitted At', 'ceb' => 'Gipasa Kaniadtong', 'fil' => 'Isinumite Noong', 'sub' => 'Gipasa Kaniadtong'],
    'admin_status' => ['en' => 'Status', 'ceb' => 'Estado', 'fil' => 'Katayuan', 'sub' => 'Estado'],
    'admin_action' => ['en' => 'Action', 'ceb' => 'Aksyon', 'fil' => 'Aksyon', 'sub' => 'Aksyon'],
    'admin_no_applications' => ['en' => 'No recent applications submitted.', 'ceb' => 'Walay bag-ong aplikasyon nga gipasa.', 'fil' => 'Walang kamakailang aplikasyon na isinumite.', 'sub' => 'Walay bag-ong aplikasyon nga gipasa.'],
];

foreach ($languages as $lang) {
    $filePath = $basePath.$lang.'/messages.php';
    if (file_exists($filePath)) {
        $existing = require $filePath;
        foreach ($keys as $keyName => $translations) {
            $existing[$keyName] = $translations[$lang];
        }

        $content = "<?php\n\nreturn ".var_export($existing, true).";\n";
        file_put_contents($filePath, $content);
        echo "Updated $lang\n";
    }
}

echo "Done!\n";
