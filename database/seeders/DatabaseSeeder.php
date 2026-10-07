<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            StorageBucketSeeder::class,
            RolePermissionSeeder::class,
            UserSeeder::class,
            SchoolYearSeeder::class,
            MemberSeeder::class,
            ActivitySeeder::class,
            // SettingSeeder::class,
            AuditLogSeeder::class,
            MembershipFeeSeeder::class,
            FinancialCategorySeeder::class,
            FinancialTransactionSeeder::class,
            ClassRoomSeeder::class,
            SubjectSeeder::class,        // ← spostato qui, prima di Lesson
            StudentSeeder::class,
            StudentPaymentSeeder::class,
            AttendanceSeeder::class,
            LessonSeeder::class,
            GradeSeeder::class,
            MaterialSeeder::class,
            ServiceAssignmentSeeder::class,
            DocumentCategorySeeder::class,
            DocumentArchiveSeeder::class,
            DocumentArchiveLinkSeeder::class,
        ]);
    }
}
