<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LegacyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users (keep original IDs)
        $users = [
            ['id' => 7, 'name' => 'Lanny M Cagatin', 'email' => 'jedcagat@gmail.com', 'password' => '$2y$12$tRh7hpucWDgks7qxJvEA8OP9dWIfFWemPmjEOmWSSakleYdILw/vi', 'created_at' => '2026-07-05 08:14:28', 'dob' => '1974-02-14', 'address' => 'Fatima, San Miguel, Zamboanga del Sur', 'civil_status' => 'Married', 'contact_number' => '09123129957', 'valid_id_path' => null, 'role' => 'facilitator', 'avatar' => 'uploads/profiles/profile_7_1783237197.jpg'],
            ['id' => 10, 'name' => 'Jaycer Arat Baterna', 'email' => 'baternajaycer@gmail.com', 'password' => '$2y$10$20bobluo6dlxSA00HdXjI.8XVxHjpwdRveuoCLCadGE22gHTwrfeS', 'created_at' => '2026-07-05 09:03:43', 'dob' => null, 'address' => 'Kabatan Vincenzo Sagun Zamboanga del sur', 'civil_status' => 'married', 'contact_number' => '+639667323237', 'valid_id_path' => 'uploads/ids/user_10_1783242727.jpg', 'role' => 'citizen', 'avatar' => 'uploads/profiles/profile_10_1783242727.jpg'],
            ['id' => 11, 'name' => 'Aimie villabeto', 'email' => 'aimievillabeto@gmail.com', 'password' => '$2y$10$mBLY6ww3vaTXliaE.qD2g.4TQ7Gn.QHQJqJBVClEa.jMHTwWcOW0i', 'created_at' => '2026-07-07 04:34:10', 'dob' => null, 'address' => null, 'civil_status' => null, 'contact_number' => null, 'valid_id_path' => null, 'role' => 'citizen', 'avatar' => null],
            ['id' => 16, 'name' => 'Mark Cagatin', 'email' => 'cagatinmark26@gmail.com', 'password' => '$2y$10$Xm2HMlBIqpgDMij/ZdShaug7tGciFpt3MoxhfUsxPenfyZysAzxky', 'created_at' => '2026-07-10 03:39:25', 'dob' => '2005-03-11', 'address' => 'Fatima, San Miguel, Zamboanga del Sur', 'civil_status' => 'Single', 'contact_number' => '09563559181', 'valid_id_path' => 'uploads/ids/user_16_1783923127.jpg', 'role' => 'citizen', 'avatar' => 'https://api.dicebear.com/9.x/adventurer/png?seed=GovAssistUser5'],
            ['id' => 17, 'name' => 'Mark', 'email' => 'cagatirarjed@gmail.com', 'password' => '$2y$10$BAMw8sdkFsJ3DUSBA2pH4eyH2aFHNB2pO.PgtbhW32XPr.7zw1CFC', 'created_at' => '2026-07-10 05:00:57', 'dob' => null, 'address' => null, 'civil_status' => null, 'contact_number' => null, 'valid_id_path' => null, 'role' => 'citizen', 'avatar' => null],
            ['id' => 18, 'name' => 'Mark Cagatin', 'email' => 'markjed60@gmail.com', 'password' => '$2y$10$KmhDMHPJpglug5nB39EwPOdfDiVrP9vN26.24ZY/tY1ue3WS0k0Cm', 'created_at' => '2026-07-11 13:20:10', 'dob' => null, 'address' => null, 'civil_status' => null, 'contact_number' => null, 'valid_id_path' => null, 'role' => 'citizen', 'avatar' => null],
            ['id' => 19, 'name' => 'Jaycer Arat Baterna', 'email' => 'baternajaycer529237@gmail.com', 'password' => '$2y$10$HXKXEMHRGa6oL6tSqRCKEe3MzMArTa/ZnHaAmffV1hJk236kKE7G2', 'created_at' => '2026-07-11 13:27:46', 'dob' => null, 'address' => null, 'civil_status' => null, 'contact_number' => null, 'valid_id_path' => null, 'role' => 'citizen', 'avatar' => null],
            ['id' => 20, 'name' => 'Jaycer Arat Baterna', 'email' => 'baternajaycer123@gmail.com', 'password' => '$2y$10$ytcZ20u7sCTZFGm.3VagWuYhTRsLsvkFFZLc1K.zfKS5x5JIadmJq', 'created_at' => '2026-07-11 13:38:27', 'dob' => null, 'address' => null, 'civil_status' => null, 'contact_number' => null, 'valid_id_path' => null, 'role' => 'citizen', 'avatar' => null],
            ['id' => 21, 'name' => 'jay bat', 'email' => 'jaybatmaot@gmail.com', 'password' => '$2y$10$TpdQXMUI319lr0JQ0TIiVOvLaUahmR/OMlgfuuqXXO/TlAx6.cWaC', 'created_at' => '2026-07-11 13:42:58', 'dob' => null, 'address' => 'Kabatan, Vincenzo Sagun Zamboanga del sur', 'civil_status' => 'Married', 'contact_number' => '09667323237', 'valid_id_path' => 'uploads/ids/user_21_1783778081.jpg', 'role' => 'citizen', 'avatar' => null],
            ['id' => 22, 'name' => 'Yvan Gonzalez', 'email' => 'mobazanekidlat@gmail.com', 'password' => '$2y$10$BJjQIRsuBUqC4Pb35pFK0el1OAtQY8tkgVhHJDu4DDZoTHAmjjnfe', 'created_at' => '2026-07-13 02:55:50', 'dob' => null, 'address' => null, 'civil_status' => null, 'contact_number' => null, 'valid_id_path' => null, 'role' => 'citizen', 'avatar' => 'https://api.dicebear.com/9.x/adventurer/png?seed=GovAssistUser2'],
            ['id' => 23, 'name' => 'Jaycer Arat Baterna', 'email' => 'batern@gmail.com', 'password' => '$2y$10$oMP9c4TIKsqQFkIZDecwOOO7nTs5iTMtXVjaGxeBUpEVd3wsUoCRm', 'created_at' => '2026-07-13 05:25:20', 'dob' => null, 'address' => null, 'civil_status' => null, 'contact_number' => null, 'valid_id_path' => null, 'role' => 'citizen', 'avatar' => null],
            ['id' => 24, 'name' => 'Jaycer Arat Baterna', 'email' => 'jaycer123@gmail.com', 'password' => '$2y$10$zpHPqIXC6AJ3Czu2MfGBVOCr3LtKIX1TvUr2fQ4Qp9CVBI.mErHwO', 'created_at' => '2026-07-13 05:26:42', 'dob' => null, 'address' => 'Kabatan Vincenzo Sagun Zamboanga del', 'civil_status' => 'single', 'contact_number' => '09667323237', 'valid_id_path' => 'uploads/ids/user_24_1783923134.jpg', 'role' => 'citizen', 'avatar' => 'https://api.dicebear.com/9.x/adventurer/png?seed=GovAssistUser7'],
        ];

        $userMapping = []; // Maps legacy ID to actual DB ID

        foreach ($users as $userData) {
            $legacyId = $userData['id'];
            unset($userData['id']); // Remove ID so we don't force update the primary key

            $user = DB::table('users')->where('email', $userData['email'])->first();

            if ($user) {
                // If the user exists, just update their details (except ID)
                DB::table('users')->where('id', $user->id)->update($userData);
                $userMapping[$legacyId] = $user->id;
            } else {
                // Insert new user and capture ID
                $newId = DB::table('users')->insertGetId($userData);
                $userMapping[$legacyId] = $newId;
            }
        }

        // 2. Categories
        // Map cat_1 -> 1, cat_2 -> 2
        $categories = [
            ['id' => 1, 'category_name' => 'Civil Registry'],
            ['id' => 2, 'category_name' => 'Licenses & Permits'],
        ];

        foreach ($categories as $cat) {
            DB::table('service_categories')->updateOrInsert(['id' => $cat['id']], $cat);
        }

        // 3. Services (Map string IDs to Integers)
        // 'srv_6a46493119072' => 1 (Educational)
        // 'srv_6a464f3ca6e7f' => 2 (Medical)
        // 'srv_6a4659f3260eb' => 3 (Burial)
        // 'srv_6a4660cb28dde' => 4 (Transportation)
        // 'srv_6a4660fb87c99' => 5 (Employment)
        $services = [
            ['id' => 1, 'category_id' => 1, 'service_name' => 'Educational Assistance', 'description' => 'Educational Assistance Provides financial aid, scholarships, tuition support, school supplies, or other educational benefits to eligible students to help them continue their studies and reduce the cost of education.', 'icon' => 'assets/icons/civil.png'],
            ['id' => 2, 'category_id' => 1, 'service_name' => 'Medical Assistance', 'description' => 'Provides financial assistance to eligible individuals to help cover medical expenses, including hospitalization, laboratory tests, medicines, surgical procedures, and other necessary healthcare services.', 'icon' => 'assets/icons/civil.png'],
            ['id' => 3, 'category_id' => 1, 'service_name' => 'Burial Assistance', 'description' => 'Provides financial assistance to the family or authorized representative of a deceased individual to help cover funeral, burial, and other related expenses.', 'icon' => 'assets/icons/civil.png'],
            ['id' => 4, 'category_id' => 1, 'service_name' => 'Transportation', 'description' => 'Provides financial assistance to eligible individuals who require transportation support for medical treatment, education, employment, emergencies, or other essential travel needs.', 'icon' => 'assets/icons/civil.png'],
            ['id' => 5, 'category_id' => 1, 'service_name' => 'Employment', 'description' => 'Provides assistance to qualified individuals seeking employment by supporting job application requirements and facilitating access to employment opportunities.', 'icon' => 'assets/icons/civil.png'],
        ];

        foreach ($services as $srv) {
            DB::table('government_services')->updateOrInsert(['id' => $srv['id']], $srv);
        }

        // 4. Requirements
        $requirements = [
            // Educational (srv = 1)
            ['service_id' => 1, 'requirement_text' => json_encode(['en' => 'Statement of Account (Private Schools Only)']), 'is_required' => 1],
            ['service_id' => 1, 'requirement_text' => json_encode(['en' => 'Certificate of Enrollment']), 'is_required' => 1],
            ['service_id' => 1, 'requirement_text' => json_encode(['en' => 'Certificate of Registration (COR)']), 'is_required' => 1],
            ['service_id' => 1, 'requirement_text' => json_encode(['en' => 'Barangay Certificate of Indigency']), 'is_required' => 1],
            ['service_id' => 1, 'requirement_text' => json_encode(['en' => 'School ID']), 'is_required' => 1],
            ['service_id' => 1, 'requirement_text' => json_encode(['en' => 'One (1) Valid Government-Issued ID']), 'is_required' => 1],
            ['service_id' => 1, 'requirement_text' => json_encode(['en' => 'Latest Grades']), 'is_required' => 1],
            // Medical (srv = 2)
            ['service_id' => 2, 'requirement_text' => json_encode(['en' => 'Medical Certificate']), 'is_required' => 1],
            ['service_id' => 2, 'requirement_text' => json_encode(['en' => 'Barangay Certificate of Indigency']), 'is_required' => 1],
            ['service_id' => 2, 'requirement_text' => json_encode(['en' => 'One (1) Valid Government-Issued ID of the Applicant']), 'is_required' => 1],
            ['service_id' => 2, 'requirement_text' => json_encode(['en' => 'One (1) Valid Government-Issued ID of the Patient']), 'is_required' => 1],
            ['service_id' => 2, 'requirement_text' => json_encode(['en' => 'Hospital Bill or Statement of Account']), 'is_required' => 1],
            ['service_id' => 2, 'requirement_text' => json_encode(['en' => 'Authorization Letter']), 'is_required' => 1],
            ['service_id' => 2, 'requirement_text' => json_encode(['en' => 'Letter of Request']), 'is_required' => 1],
            ['service_id' => 2, 'requirement_text' => json_encode(['en' => 'Social Case Study Report/Form (MSWDO)']), 'is_required' => 1],
            // Burial (srv = 3)
            ['service_id' => 3, 'requirement_text' => json_encode(['en' => 'Death Certificate']), 'is_required' => 1],
            ['service_id' => 3, 'requirement_text' => json_encode(['en' => 'Barangay Certificate of Indigency']), 'is_required' => 1],
            ['service_id' => 3, 'requirement_text' => json_encode(['en' => 'One (1) Valid Government-Issued ID of the Applicant']), 'is_required' => 1],
            ['service_id' => 3, 'requirement_text' => json_encode(['en' => 'Letter of Request']), 'is_required' => 1],
            ['service_id' => 3, 'requirement_text' => json_encode(['en' => 'Social Case Study Report/Form (MSWDO)']), 'is_required' => 1],
            // Employment (srv = 5)
            ['service_id' => 5, 'requirement_text' => json_encode(['en' => 'Personal Data Sheet (PDS)']), 'is_required' => 1],
            ['service_id' => 5, 'requirement_text' => json_encode(['en' => 'Resume']), 'is_required' => 1],
            ['service_id' => 5, 'requirement_text' => json_encode(['en' => 'Recommendation Letter']), 'is_required' => 1],
            ['service_id' => 5, 'requirement_text' => json_encode(['en' => 'Endorsement Letter']), 'is_required' => 1],
        ];

        DB::table('service_requirements')->insert($requirements);

        // 5. Checklists/Applications
        // Educational (1)
        if (isset($userMapping[10])) {
            DB::table('user_checklists')->updateOrInsert(['id' => 1], ['user_id' => $userMapping[10], 'service_id' => 1, 'status' => 'pending', 'created_at' => '2026-07-05 09:12:09']);
        }
        if (isset($userMapping[24])) {
            DB::table('user_checklists')->updateOrInsert(['id' => 5], ['user_id' => $userMapping[24], 'service_id' => 1, 'status' => 'pending', 'created_at' => '2026-07-13 06:12:31']);
        }

        // Employment (5)
        if (isset($userMapping[21])) {
            DB::table('user_checklists')->updateOrInsert(['id' => 2], ['user_id' => $userMapping[21], 'service_id' => 5, 'status' => 'pending', 'created_at' => '2026-07-11 13:55:27']);
        }
        if (isset($userMapping[16])) {
            DB::table('user_checklists')->updateOrInsert(['id' => 3], ['user_id' => $userMapping[16], 'service_id' => 5, 'status' => 'pending', 'created_at' => '2026-07-13 06:12:09']);
        }

        // Medical (2)
        if (isset($userMapping[24])) {
            DB::table('user_checklists')->updateOrInsert(['id' => 4], ['user_id' => $userMapping[24], 'service_id' => 2, 'status' => 'pending', 'created_at' => '2026-07-13 06:12:18']);
        }

        echo "Legacy Data Successfully Imported!\n";
    }
}
