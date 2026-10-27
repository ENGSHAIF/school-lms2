<?php

require_once __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\SupervisorController;
use App\Models\FinancialAccount;
use App\Models\Level;
use App\Models\StudentCourseGrade;
use App\Models\StudentEnrollment;
use App\Models\StudentInstallment;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

echo "=== STARTING REVOKE APPROVAL FLOW TEST ===\n\n";

// 1. Get Admin & Supervisor users
$admin = User::where('role', 'admin')->first();
$supervisor = User::where('user_code', 'SUP-2026-0001')->first();
$level = Level::first();

if (! $admin || ! $supervisor || ! $level) {
    echo "Prerequisites missing (Admin, Supervisor, or Level)!\n";
    exit(1);
}

echo "Admin: {$admin->name} (ID: {$admin->id})\n";
echo "Supervisor: {$supervisor->name} (ID: {$supervisor->id})\n";
echo "Level: {$level->name} (Fee: {$level->fee_amount})\n";

// 2. Create a test student
$testStudent = User::updateOrCreate(
    ['email' => 'test_revoke_student@example.com'],
    [
        'name' => 'طالب اختبار الإلغاء التجريبي',
        'role' => 'student',
        'status' => 'pending_approval',
        'password' => Hash::make('password123'),
        'phone_whatsapp' => '967770000999',
    ]
);

// Ensure student has a financial account
FinancialAccount::firstOrCreate(
    ['user_id' => $testStudent->id],
    [
        'account_code' => '1201'.str_pad($testStudent->id, 4, '0', STR_PAD_LEFT),
        'account_name' => 'حساب الطالب '.$testStudent->name,
        'account_type' => 'student',
        'account_number' => 'ACC-'.$testStudent->id,
        'current_balance' => 0,
        'currency' => 'USD',
    ]
);

echo "\n[Step 1] Student created: {$testStudent->name} (Status: {$testStudent->status})\n";

// 3. Supervisor approves the student
Auth::login($supervisor);
$supController = app(SupervisorController::class);
$req = new Request(['level_id' => $level->id]);

$supController->assignLevel($req, $testStudent->id);
$testStudent->refresh();
$enrollment = StudentEnrollment::where('user_id', $testStudent->id)->latest()->first();
$finAcc = FinancialAccount::where('user_id', $testStudent->id)->first();

echo "[Step 2] Approved by Supervisor:\n";
echo " - Student Status: {$testStudent->status}\n";
echo " - Approved By: {$testStudent->approved_by} (Approver Name: ".($testStudent->approver?->name ?? 'None').")\n";
echo " - Approved At: {$testStudent->approved_at}\n";
echo " - Enrollment Status: {$enrollment->status} (Level: {$enrollment->level?->name})\n";
echo " - Financial Balance: {$finAcc->current_balance}\n";

if ($testStudent->status !== 'active' || $testStudent->approved_by !== $supervisor->id) {
    echo "FAIL: Approval failed to set status or approved_by correctly!\n";
    exit(1);
}

// 4. Test revocation by Supervisor (student has NOT studied courses)
echo "\n[Step 3] Testing revocation for new student (has not studied):\n";
echo ' - Has Studied Any Courses: '.($testStudent->hasStudiedAnyCourses() ? 'YES' : 'NO')."\n";

$revokeReq = new Request(['reason' => 'رغبة الطالب في تبديل التخصص بعد الاستشارة']);
$revokeResponse = $supController->revokeLevelApproval($revokeReq, $testStudent->id);

$testStudent->refresh();
$enrollment->refresh();
$finAcc->refresh();

echo " - Student Status After Revocation: {$testStudent->status}\n";
echo " - Revoked By: {$testStudent->revoked_by} (Revoker Name: ".($testStudent->revoker?->name ?? 'None').")\n";
echo " - Revoked At: {$testStudent->revoked_at}\n";
echo " - Revoke Reason: {$testStudent->revoke_reason}\n";
echo " - Enrollment Status: {$enrollment->status}\n";
echo " - Financial Balance After Reversal: {$finAcc->current_balance}\n";

if ($testStudent->status === 'pending_approval' && $testStudent->revoked_by === $supervisor->id && $finAcc->current_balance == 0) {
    echo "SUCCESS: Revocation completely succeeded with full tracking & financial reversal!\n";
} else {
    echo "FAIL: Revocation state mismatch!\n";
    exit(1);
}

// 5. Test blocking revocation when student HAS studied courses
echo "\n[Step 4] Testing blocking when student HAS studied courses:\n";
// Re-approve student
$supController->assignLevel(new Request(['level_id' => $level->id]), $testStudent->id);
$testStudent->refresh();
echo " - Re-approved student status: {$testStudent->status}\n";

// Add a simulated course grade (as if student completed a course with grades)
$grade = StudentCourseGrade::create([
    'student_id' => $testStudent->id,
    'study_group_id' => 1,
    'total_score' => 90,
    'percentage' => 90,
    'grade_letter' => 'ممتاز',
    'status' => 'passed',
]);

echo ' - Added course grade. Has Studied: '.($testStudent->hasStudiedAnyCourses() ? 'YES' : 'NO')."\n";

$blockResponse = $supController->revokeLevelApproval(new Request(['reason' => 'محاولة إلغاء لطالب درس']), $testStudent->id);
$testStudent->refresh();
echo " - Student Status After Blocked Attempt: {$testStudent->status}\n";

if ($testStudent->status === 'active') {
    echo "SUCCESS: Revocation was successfully BLOCKED because student has studied courses!\n";
} else {
    echo "FAIL: Revocation should have been blocked!\n";
    exit(1);
}

// 6. Clean up
$grade->delete();
StudentEnrollment::where('user_id', $testStudent->id)->delete();
StudentInstallment::where('user_id', $testStudent->id)->delete();
FinancialAccount::where('user_id', $testStudent->id)->delete();
$testStudent->delete();

echo "\n=== ALL TESTS PASSED SUCCESSFULLY! ===\n";
