<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppearanceController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BrandingController;
use App\Http\Controllers\CashBoxController;
use App\Http\Controllers\ChartOfAccountsController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\CostCenterController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExamManagementController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\GroupEvaluationController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\LevelResourceController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\PaymentRequestController;
use App\Http\Controllers\PermissionGroupController;
use App\Http\Controllers\PlacementTestController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StudentApplicationController;
use App\Http\Controllers\StudentPortalController;
use App\Http\Controllers\StudentSurveyController;
use App\Http\Controllers\StudyGroupController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\WhatsAppManagementController;
use App\Http\Controllers\YemenSoftAccountingController;
use Illuminate\Support\Facades\Route;

// الصفحات العامة والتعريفية (واجهات وتبويبات ASOTS لـ ASOTS)
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/about', [PublicController::class, 'about'])->name('about');
Route::get('/leadership', [PublicController::class, 'leadership'])->name('leadership');
Route::get('/quality', [PublicController::class, 'quality'])->name('quality');
Route::get('/translation-services', [PublicController::class, 'translationServices'])->name('translation_services');
Route::get('/consultancy-services', [PublicController::class, 'consultancyServices'])->name('consultancy_services');
Route::get('/services', [PublicController::class, 'services'])->name('services');
Route::get('/programmes', [PublicController::class, 'services'])->name('programmes');
Route::get('/general-english', [PublicController::class, 'generalEnglish'])->name('general_english');
Route::get('/curriculum', [PublicController::class, 'curriculum'])->name('curriculum');
Route::get('/adult-english', [PublicController::class, 'adultEnglish'])->name('adult_english');
Route::get('/pricing', [PublicController::class, 'pricing'])->name('pricing');
Route::get('/policies', [PublicController::class, 'policies'])->name('policies');
Route::get('/verify-certificate', [PublicController::class, 'verifyCertificate'])->name('verify_certificate');
Route::get('/licenses', [BrandingController::class, 'viewLicenses'])->name('licenses.viewer');
Route::get('/announcements', [PublicController::class, 'announcements'])->name('announcements.index');
Route::get('/announcements/{id}', [PublicController::class, 'showAnnouncement'])->name('announcements.show');
Route::get('/contact', [PublicController::class, 'contact'])->name('contact');
Route::post('/testimonials', [PublicController::class, 'storeTestimonial'])->name('testimonials.store');

// إستمارة التسجيل الرسمية للطلاب المتقدمين (ASOTS Admission Form)
Route::get('/student-registration', [StudentApplicationController::class, 'create'])->name('student.registration');
Route::post('/student-registration', [StudentApplicationController::class, 'store'])->name('student.registration.store');

// استبيانات تقييم الطلاب للمدرس والمنهج والدبلوم (Student Surveys & Feedback)
Route::get('/survey/{type?}', [StudentSurveyController::class, 'create'])->name('surveys.create');
Route::post('/survey/submit', [StudentSurveyController::class, 'store'])->name('surveys.store');

// إعدادات المظهر والـ 10 قوالب ومحول اللغة
Route::get('/settings/appearance', [AppearanceController::class, 'show'])->name('settings.appearance');
Route::post('/settings/appearance', [AppearanceController::class, 'update'])->name('settings.appearance.update');
Route::post('/settings/appearance/font', [AppearanceController::class, 'updateFont'])->name('settings.appearance.font');
Route::post('/settings/appearance/reset', [AppearanceController::class, 'reset'])->name('settings.appearance.reset');
Route::get('/lang/{locale}', [AppearanceController::class, 'switchLanguage'])->name('lang.switch');

// المصادقة والتسجيل ورحلة الانضمام الموجهة (Onboarding Wizard)
Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding');
Route::post('/onboarding', [OnboardingController::class, 'process'])->name('onboarding.process');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1')->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// استعادة كلمة المرور عبر تقديم طلب للإدارة عند نسيانها (محمي بمعدل 3 محاولات بالدقيقة)
Route::post('/password/reset-request', [AuthController::class, 'storePasswordResetRequest'])->middleware('throttle:3,1')->name('password.reset_request.store');

// إجبار المستخدم على تغيير كلمة المرور عند أول تسجيل دخول بعد إعادة تعيينها من الإدارة
Route::get('/password/force-change', [AuthController::class, 'showForceChangePassword'])->name('password.force_change');
Route::post('/password/force-change', [AuthController::class, 'updateForceChangePassword'])->name('password.force_change.update');

// المسارات المحمية بتسجيل الدخول
Route::middleware('auth')->group(function () {
    // 1. مسار اختبار تحديد المستوى الإلزامي للطالب الجديد
    Route::get('/placement-test', [PlacementTestController::class, 'show'])->name('placement.test');
    Route::post('/placement-test/submit', [PlacementTestController::class, 'submit'])->name('placement.test.submit');
    Route::get('/student/status', [PlacementTestController::class, 'status'])->name('student.status');

    // 2. بوابة الطالب المفعل (دروس وفيديوهات وكتب وكشف حساب)
    Route::get('/student/portal', [StudentPortalController::class, 'portal'])->name('student.portal');

    // 2.1 مسارات نموذج ASOTS (حجز الحصص 25 دقيقة، الفصل التفاعلي الحي، والباقات)
    Route::get('/student/booking', [BookingController::class, 'index'])->name('student.booking');
    Route::post('/student/booking', [BookingController::class, 'book'])->name('student.book.post');
    Route::get('/classroom/{roomId}', [BookingController::class, 'showClassroom'])->name('classroom.show');
    Route::get('/classroom/{roomId}/status', [BookingController::class, 'checkStatus'])->name('classroom.status');
    Route::post('/classroom/booking/{bookingId}/toggle-live', [BookingController::class, 'toggleBookingLive'])->name('classroom.booking.toggle_live');
    Route::post('/classroom/{bookingId}/report', [BookingController::class, 'submitReport'])->name('classroom.report.submit');
    Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
    Route::post('/packages/subscribe', [PackageController::class, 'subscribe'])->name('packages.subscribe');

    // 2.2 مسارات التفاعل للأنشطة والبث الحي المشترك داخل المجموعات (للطلاب والكادر)
    Route::post('/groups/{groupId}/activities', [StudyGroupController::class, 'storeActivity'])->name('groups.activities.store');
    Route::delete('/groups/activities/{activityId}', [StudyGroupController::class, 'destroyActivity'])->name('groups.activities.destroy');
    Route::post('/groups/{groupId}/live/start', [StudyGroupController::class, 'startLive'])->name('groups.live.start');
    Route::post('/groups/{groupId}/live/stop', [StudyGroupController::class, 'stopLive'])->name('groups.live.stop');
    Route::post('/groups/{groupId}/live/request', [StudyGroupController::class, 'requestLive'])->name('groups.live.request');
    Route::post('/groups/{groupId}/live/reject', [StudyGroupController::class, 'rejectLive'])->name('groups.live.reject');

    // 2.3 مسارات الطالب لأداء الاختبار واستعراض النتيجة
    Route::get('/student/exams/{id}/take/{groupId?}', [ExamController::class, 'take'])->name('student.exam.take');
    Route::post('/student/exams/{id}/submit', [ExamController::class, 'submit'])->name('student.exam.submit');
    Route::get('/student/exam-attempts/{attemptId}', [ExamController::class, 'studentResult'])->name('student.exam.result');
    Route::get('/student/exam-attempts/{attemptId}/report', [ExamController::class, 'studentErrorReport'])->name('student.exam.error_report');

    // 2.3.1 استبيانات وتقييمات الطالب اليومية والأسبوعية والدبلوم
    Route::get('/student/surveys', [StudentSurveyController::class, 'studentIndex'])->name('student.surveys.index');

    // 2.4 لوحة تحكم ولي الأمر ومتابعة الأبناء
    Route::get('/parent', [ParentController::class, 'dashboard'])->name('parent.dashboard');
    Route::post('/parent/child/store', [ParentController::class, 'storeChild'])->name('parent.child.store');
    Route::post('/parent/child/{childId}/book', [ParentController::class, 'bookForChild'])->name('parent.child.book');

    // =========================================================================
    // 3. مسارات كادر المنصة والإدارة (Staff & Admin Only)
    // محميّة عبر Staff Middleware - محجوبة قطعياً عن حسابات الطلاب وأولياء الأمور
    // =========================================================================
    Route::middleware('staff')->group(function () {
        // 3.0 لوحة تحكم الإدارة وشاشات القبول والتسجيل
        Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/students', [AdminController::class, 'students'])->name('admin.students.index');
        Route::post('/admin/students/{id}/assign-level', [AdminController::class, 'assignLevel'])->name('admin.assign_level');
        Route::post('/admin/students/{id}/revoke-approval', [AdminController::class, 'revokeLevelApproval'])->name('admin.students.revoke_approval');
        Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users.index');
        Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
        Route::post('/admin/users/{id}/update', [AdminController::class, 'updateUser'])->name('admin.users.update');
        Route::post('/admin/users/{id}/change-password', [AdminController::class, 'changeUserPassword'])->name('admin.users.change_password');
        Route::post('/admin/password-requests/{id}/handle', [AdminController::class, 'handlePasswordResetRequest'])->name('admin.password_requests.handle');
        Route::post('/admin/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus'])->name('admin.users.toggle_status');
        Route::delete('/admin/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.destroy');
        Route::post('/admin/parents/{parentId}/link-student', [AdminController::class, 'linkStudentToParent'])->name('admin.parents.link_student');
        Route::post('/admin/parents/{parentId}/unlink-student/{studentId}', [AdminController::class, 'unlinkStudentFromParent'])->name('admin.parents.unlink_student');

        // إدارة مجموعات الصلاحيات والأدوار (Permission Groups & Roles)
        Route::get('/admin/permission-groups', [PermissionGroupController::class, 'index'])->name('admin.permission_groups.index');
        Route::post('/admin/permission-groups', [PermissionGroupController::class, 'store'])->name('admin.permission_groups.store');
        Route::post('/admin/permission-groups/{id}/update', [PermissionGroupController::class, 'update'])->name('admin.permission_groups.update');
        Route::delete('/admin/permission-groups/{id}', [PermissionGroupController::class, 'destroy'])->name('admin.permission_groups.destroy');
        Route::post('/admin/permission-groups/{id}/assign-users', [PermissionGroupController::class, 'assignUsers'])->name('admin.permission_groups.assign_users');
        Route::delete('/admin/permission-groups/{id}/users/{userId}', [PermissionGroupController::class, 'removeUser'])->name('admin.permission_groups.remove_user');

        // 3.1 إدارة الدورات والمستويات والحصص والأسعار وباقات الحصص
        Route::get('/admin/courses', [AdminController::class, 'courses'])->name('admin.courses.index');
        Route::post('/admin/courses', [AdminController::class, 'storeCourse'])->name('admin.courses.store');
        Route::post('/admin/courses/{id}/update', [AdminController::class, 'updateCourse'])->name('admin.courses.update');
        Route::post('/admin/levels', [AdminController::class, 'storeLevel'])->name('admin.levels.store');
        Route::post('/admin/levels/{id}/update', [AdminController::class, 'updateLevel'])->name('admin.levels.update');
        Route::post('/admin/lessons', [AdminController::class, 'storeLesson'])->name('admin.lessons.store');
        Route::post('/admin/packages', [AdminController::class, 'storePackage'])->name('admin.packages.store');
        Route::post('/admin/packages/{id}/update', [AdminController::class, 'updatePackage'])->name('admin.packages.update');

        // 3.1.1 بنك موارد ومناهج الدورات والمستويات الأكاديمية
        Route::get('/admin/courses/{courseId}/resources', [LevelResourceController::class, 'courseResources'])->name('admin.courses.resources');
        Route::post('/admin/courses/{courseId}/resources', [LevelResourceController::class, 'storeCourseResource'])->name('admin.courses.resources.store');
        Route::get('/admin/levels/{levelId}/resources', [LevelResourceController::class, 'index'])->name('admin.levels.resources');
        Route::post('/admin/levels/{levelId}/resources', [LevelResourceController::class, 'store'])->name('admin.levels.resources.store');
        Route::post('/admin/levels/resources/{id}/update', [LevelResourceController::class, 'update'])->name('admin.levels.resources.update');
        Route::delete('/admin/levels/resources/{id}', [LevelResourceController::class, 'destroy'])->name('admin.levels.resources.destroy');
        Route::post('/groups/{groupId}/share-resource', [LevelResourceController::class, 'shareToGroup'])->name('groups.resources.share');
        Route::delete('/groups/{groupId}/resources/{resourceId}/unshare', [LevelResourceController::class, 'unshareFromGroup'])->name('groups.resources.unshare');

        // 3.1.2 نظام الكويزات والاختبارات المخصصة وبنك الأسئلة وإدارتها
        Route::get('/admin/exams', [ExamController::class, 'index'])->name('admin.exams.index');
        Route::get('/admin/exams/create', [ExamController::class, 'create'])->name('admin.exams.create');
        Route::post('/admin/exams', [ExamController::class, 'store'])->name('admin.exams.store');
        Route::get('/admin/levels/{levelId}/exams', [ExamController::class, 'index'])->name('admin.levels.exams.index');
        Route::get('/admin/levels/{levelId}/exams/create', [ExamController::class, 'create'])->name('admin.levels.exams.create');
        Route::post('/admin/levels/{levelId}/exams', [ExamController::class, 'store'])->name('admin.levels.exams.store');
        Route::get('/admin/exams/{id}/edit', [ExamController::class, 'edit'])->name('admin.exams.edit');
        Route::post('/admin/exams/{id}/update', [ExamController::class, 'update'])->name('admin.exams.update');
        Route::delete('/admin/exams/{id}', [ExamController::class, 'destroy'])->name('admin.exams.destroy');
        Route::post('/admin/exams/{id}/questions', [ExamController::class, 'addQuestion'])->name('admin.exams.questions.add');
        Route::post('/admin/exams/{id}/questions/import', [ExamController::class, 'importQuestions'])->name('admin.exams.questions.import');
        Route::post('/admin/exams/questions/{questionId}/update', [ExamController::class, 'updateQuestion'])->name('admin.exams.questions.update');
        Route::delete('/admin/exams/questions/{questionId}', [ExamController::class, 'deleteQuestion'])->name('admin.exams.questions.destroy');
        Route::post('/admin/exams/{id}/share-group', [ExamController::class, 'shareToGroup'])->name('admin.exams.share_group');
        Route::get('/admin/exams/{id}/attempts', [ExamController::class, 'attempts'])->name('admin.exams.attempts');
        Route::get('/admin/exam-attempts/{attemptId}/review', [ExamController::class, 'reviewAttempt'])->name('admin.exams.attempts.review');
        Route::get('/admin/exam-attempts/{attemptId}/review-detail', [ExamController::class, 'reviewAttempt'])->name('admin.exams.review_attempt');
        Route::post('/admin/exam-attempts/{attemptId}/publish', [ExamController::class, 'publishResult'])->name('admin.exams.attempts.publish');
        Route::post('/admin/exam-attempts/{attemptId}/publish-result', [ExamController::class, 'publishResult'])->name('admin.exams.publish_result');
        Route::post('/admin/exams/{id}/publish-all', [ExamController::class, 'publishAllResults'])->name('admin.exams.publish_all');
        Route::get('/admin/all-exam-attempts', [ExamController::class, 'allAttempts'])->name('admin.exams.all_attempts');
        Route::get('/admin/students/{studentId}/exam-attempts', [ExamController::class, 'studentAttempts'])->name('admin.students.exam_attempts');
        Route::get('/admin/exam-attempts/{attemptId}/error-report', [ExamController::class, 'errorReport'])->name('admin.exams.attempts.error_report');

        // 3.1.4 إدارة الاختبارات الشاملة وسلالم الدرجات ورصد النتائج والشهائد الأكاديمية (Exam Management & Certificates Hub)
        Route::get('/admin/exams-management', [ExamManagementController::class, 'index'])->name('admin.exams_management.index');
        Route::post('/admin/exams-management/exams', [ExamManagementController::class, 'storeExam'])->name('admin.exams_management.exams.store');
        Route::post('/admin/exams-management/exams/{id}/assign', [ExamManagementController::class, 'assignExam'])->name('admin.exams_management.exams.assign');
        Route::post('/admin/exams-management/grade-scales', [ExamManagementController::class, 'saveGradeScale'])->name('admin.exams_management.scales.save');
        Route::post('/admin/exams-management/groups/{groupId}/grades', [ExamManagementController::class, 'saveGroupGrades'])->name('admin.exams_management.grades.save');
        Route::get('/admin/exams-management/groups/{groupId}/grade-sheet', [ExamManagementController::class, 'printGradeSheet'])->name('admin.exams_management.grade_sheet.print');
        Route::post('/admin/exams-management/groups/{groupId}/certificates/bulk-generate', [ExamManagementController::class, 'bulkGenerateCertificates'])->name('admin.exams_management.certificates.bulk_generate');
        Route::post('/admin/exams-management/certificates/generate-single', [ExamManagementController::class, 'generateSingleCertificate'])->name('admin.exams_management.certificates.generate_single');
        Route::get('/admin/exams-management/certificates/{id}/print', [ExamManagementController::class, 'printCertificate'])->name('admin.exams_management.certificates.print');
        Route::get('/admin/exams-management/groups/{groupId}/certificates/print-batch', [ExamManagementController::class, 'printBatchCertificates'])->name('admin.exams_management.certificates.print_batch');
        Route::delete('/admin/exams-management/certificates/{id}', [ExamManagementController::class, 'destroyCertificate'])->name('admin.exams_management.certificates.destroy');

        // 3.2 إدارة المجموعات والدفعات وتسكين الطلاب (Study Groups & Batches)
        Route::get('/admin/groups', [StudyGroupController::class, 'index'])->name('admin.groups.index');
        Route::post('/admin/groups', [StudyGroupController::class, 'store'])->name('admin.groups.store');
        Route::post('/admin/groups/{id}/update', [StudyGroupController::class, 'update'])->name('admin.groups.update');
        Route::post('/admin/groups/{id}/enroll', [StudyGroupController::class, 'enrollStudents'])->name('admin.groups.enroll');
        Route::delete('/admin/groups/{groupId}/students/{studentId}', [StudyGroupController::class, 'removeStudent'])->name('admin.groups.students.remove');
        Route::post('/admin/groups/{groupId}/students/{studentId}/transfer', [StudyGroupController::class, 'transferStudent'])->name('admin.groups.students.transfer');
        Route::delete('/admin/groups/{id}', [StudyGroupController::class, 'destroy'])->name('admin.groups.destroy');

        // 3.2.1 تقييمات وحضور ومشاركات المجموعات الدراسية (Group Evaluations, Attendance & Diploma Grading)
        Route::get('/admin/groups/evaluations', [GroupEvaluationController::class, 'index'])->name('admin.groups.evaluations.index');
        Route::get('/admin/groups/evaluations/print', [GroupEvaluationController::class, 'printReport'])->name('admin.groups.evaluations.print');
        Route::get('/admin/groups/{groupId}/evaluations/create', [GroupEvaluationController::class, 'create'])->name('admin.groups.evaluations.create');
        Route::post('/admin/groups/{groupId}/evaluations', [GroupEvaluationController::class, 'store'])->name('admin.groups.evaluations.store');
        Route::get('/admin/groups/evaluations/session/{sessionId}', [GroupEvaluationController::class, 'showSession'])->name('admin.groups.evaluations.show');
        Route::delete('/admin/groups/evaluations/session/{sessionId}', [GroupEvaluationController::class, 'destroySession'])->name('admin.groups.evaluations.destroy');

        // 3.2.2 مركز استبيانات وتقييمات الطلاب الشامل للأستاذ والدبلوم والتقارير
        Route::get('/admin/surveys', [StudentSurveyController::class, 'index'])->name('admin.surveys.index');
        Route::get('/admin/surveys/print', [StudentSurveyController::class, 'printReport'])->name('admin.surveys.print');
        Route::delete('/admin/surveys/{id}', [StudentSurveyController::class, 'destroy'])->name('admin.surveys.destroy');

        // 3.3 مركز التقارير والإحصائيات الشامل للمنصة (Reports & Analytics Hub)
        Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
        Route::get('/admin/reports/print', [ReportController::class, 'printReport'])->name('admin.reports.print');
        Route::get('/admin/reports/student/{id}/transcript', [ReportController::class, 'studentTranscript'])->name('admin.reports.student.transcript');
        Route::get('/admin/reports/teacher/{id}/statement', [ReportController::class, 'teacherStatement'])->name('admin.reports.teacher.statement');
        Route::get('/admin/reports/term/print', [ReportController::class, 'termRosterPrint'])->name('admin.reports.term.print');

        // 4. النظام المحاسبي القياسي (سندات القبض والصرف، كشوفات الحساب)
        Route::get('/accounting', [AccountingController::class, 'dashboard'])->name('accounting.dashboard');
        Route::get('/accounting/statement/{studentId}', [AccountingController::class, 'showStatement'])->name('accounting.statement');
        Route::get('/accounting/teacher-statement/{teacherId}', [ReportController::class, 'teacherStatement'])->name('accounting.teacher.statement');
        Route::post('/accounting/receipt/store', [AccountingController::class, 'storeReceipt'])->name('accounting.receipt.store');
        Route::post('/accounting/payment/store', [AccountingController::class, 'storePayment'])->name('accounting.payment.store');
        Route::post('/accounting/voucher/{id}/update', [AccountingController::class, 'updateVoucher'])->name('accounting.voucher.update');
        Route::delete('/accounting/voucher/{id}', [AccountingController::class, 'destroyVoucher'])->name('accounting.voucher.destroy');
        Route::get('/accounting/voucher/{id}/print', [AccountingController::class, 'printVoucher'])->name('accounting.voucher.print');
        Route::get('/accounting/voucher/{id}/journal-entry', [AccountingController::class, 'getVoucherJournalEntry'])->name('accounting.voucher.journal_entry');

        // 4.0 دليل وهيكل الصناديق والخزائن المالية والحسابات البنكية والصناديق الفرعية
        Route::get('/accounting/cash-boxes', [CashBoxController::class, 'index'])->name('accounting.cash_boxes.index');
        Route::post('/accounting/cash-boxes', [CashBoxController::class, 'store'])->name('accounting.cash_boxes.store');
        Route::post('/accounting/cash-boxes/{id}/update', [CashBoxController::class, 'update'])->name('accounting.cash_boxes.update');
        Route::delete('/accounting/cash-boxes/{id}', [CashBoxController::class, 'destroy'])->name('accounting.cash_boxes.destroy');
        Route::get('/accounting/cash-boxes/{id}/statement', [CashBoxController::class, 'statement'])->name('accounting.cash_boxes.statement');
        Route::get('/accounting/cash-boxes/{id}/statement/print', [CashBoxController::class, 'printStatement'])->name('accounting.cash_boxes.statement.print');
        Route::post('/accounting/cash-boxes/{id}/reconcile', [CashBoxController::class, 'reconcile'])->name('accounting.cash_boxes.reconcile');

        // تسعيرة الدورات والخدمات التعليمية وعقود المعلمين
        Route::get('/accounting/pricing', [AccountingController::class, 'pricingAndServices'])->name('accounting.pricing.index');
        Route::post('/accounting/pricing/course/store', [AccountingController::class, 'storeCourseFromPricing'])->name('accounting.pricing.course.store');
        Route::post('/accounting/pricing/course/{id}', [AccountingController::class, 'updateCoursePricing'])->name('accounting.pricing.course.update');
        Route::post('/accounting/services', [AccountingController::class, 'storeEducationalService'])->name('accounting.services.store');
        Route::post('/accounting/services/{id}/update', [AccountingController::class, 'updateEducationalService'])->name('accounting.services.update');
        Route::delete('/accounting/services/{id}', [AccountingController::class, 'destroyEducationalService'])->name('accounting.services.destroy');
        Route::post('/accounting/teacher-contract/{teacherId}', [AccountingController::class, 'updateTeacherContract'])->name('accounting.teacher_contract.update');

        // إثبات مستحقات المعلمين وتعديلها وإلغائها وطباعتها
        Route::post('/accounting/teacher-entitlement', [AccountingController::class, 'storeTeacherEntitlement'])->name('accounting.teacher_entitlement.store');
        Route::post('/accounting/teacher-entitlement/{id}/update', [AccountingController::class, 'updateTeacherEntitlement'])->name('accounting.teacher_entitlement.update');
        Route::delete('/accounting/teacher-entitlement/{id}', [AccountingController::class, 'destroyTeacherEntitlement'])->name('accounting.teacher_entitlement.destroy');
        Route::get('/accounting/teacher-entitlement/{id}/print', [AccountingController::class, 'printEntitlement'])->name('accounting.teacher_entitlement.print');

        // 4.1 الدليل المحاسبي الشجري متعدد المستويات (Chart of Accounts)
        Route::get('/accounting/chart-of-accounts', [ChartOfAccountsController::class, 'index'])->name('accounting.chart_of_accounts.index');
        Route::post('/accounting/chart-of-accounts', [ChartOfAccountsController::class, 'store'])->name('accounting.chart_of_accounts.store');
        Route::post('/accounting/chart-of-accounts/{id}/update', [ChartOfAccountsController::class, 'update'])->name('accounting.chart_of_accounts.update');
        Route::delete('/accounting/chart-of-accounts/{id}', [ChartOfAccountsController::class, 'destroy'])->name('accounting.chart_of_accounts.destroy');

        // 4.2 شجرة مراكز التكلفة ومؤشرات الربحية (Cost Centers)
        Route::get('/accounting/cost-centers', [CostCenterController::class, 'index'])->name('accounting.cost_centers.index');
        Route::post('/accounting/cost-centers', [CostCenterController::class, 'store'])->name('accounting.cost_centers.store');
        Route::post('/accounting/cost-centers/{id}/update', [CostCenterController::class, 'update'])->name('accounting.cost_centers.update');
        Route::delete('/accounting/cost-centers/{id}', [CostCenterController::class, 'destroy'])->name('accounting.cost_centers.destroy');

        // 4.3 دورة طلبات القبض والصرف والاعتمادات الإدارية (Payment & Receipt Requests Workflow)
        Route::get('/accounting/payment-requests', [PaymentRequestController::class, 'index'])->name('accounting.payment_requests.index');
        Route::post('/accounting/payment-requests', [PaymentRequestController::class, 'store'])->name('accounting.payment_requests.store');
        Route::post('/accounting/payment-requests/{id}/approve', [PaymentRequestController::class, 'approve'])->name('accounting.payment_requests.approve');
        Route::post('/accounting/payment-requests/{id}/reject', [PaymentRequestController::class, 'reject'])->name('accounting.payment_requests.reject');
        Route::post('/accounting/payment-requests/{id}/execute', [PaymentRequestController::class, 'execute'])->name('accounting.payment_requests.execute');

        // 4.4 دفتر قيود اليومية العامة المزدوجة (General Journal Entries)
        Route::get('/accounting/journal-entries', [JournalEntryController::class, 'index'])->name('accounting.journal_entries.index');
        Route::get('/accounting/journal-entries/create', [JournalEntryController::class, 'create'])->name('accounting.journal_entries.create');
        Route::post('/accounting/journal-entries', [JournalEntryController::class, 'store'])->name('accounting.journal_entries.store');
        Route::get('/accounting/journal-entries/{id}', [JournalEntryController::class, 'show'])->name('accounting.journal_entries.show');
        Route::get('/accounting/journal-entries/{id}/print', [JournalEntryController::class, 'print'])->name('accounting.journal_entries.print');
        Route::post('/accounting/journal-entries/{id}/reverse', [JournalEntryController::class, 'reverse'])->name('accounting.journal_entries.reverse');

        // 4.5 التقارير المالية التحليلية والقوائم الختامية (Financial Reports)
        Route::get('/accounting/reports', [FinancialReportController::class, 'index'])->name('accounting.reports.index');
        Route::get('/accounting/reports/trial-balance', [FinancialReportController::class, 'trialBalance'])->name('accounting.reports.trial_balance');
        Route::get('/accounting/reports/income-statement', [FinancialReportController::class, 'incomeStatement'])->name('accounting.reports.income_statement');
        Route::get('/accounting/reports/general-ledger', [FinancialReportController::class, 'generalLedger'])->name('accounting.reports.general_ledger');
        Route::get('/accounting/reports/cost-centers', [FinancialReportController::class, 'costCentersReport'])->name('accounting.reports.cost_centers');

        // 4.6 وحدة يمن سوفت ERP (الفترات، الخيارات، الأرصدة الافتتاحية، الموازنة، الشيكات، الحوافظ، الإشعارات، جرد النقدية، الترحيل)
        Route::get('/accounting/periods', [YemenSoftAccountingController::class, 'periods'])->name('accounting.periods.index');
        Route::post('/accounting/periods/{id}/toggle', [YemenSoftAccountingController::class, 'togglePeriodStatus'])->name('accounting.periods.toggle');
        Route::get('/accounting/options', [YemenSoftAccountingController::class, 'options'])->name('accounting.options.index');
        Route::post('/accounting/options', [YemenSoftAccountingController::class, 'updateOptions'])->name('accounting.options.update');
        Route::get('/accounting/opening-balances', [YemenSoftAccountingController::class, 'openingBalances'])->name('accounting.opening_balances.index');
        Route::post('/accounting/opening-balances', [YemenSoftAccountingController::class, 'saveOpeningBalances'])->name('accounting.opening_balances.save');
        Route::get('/accounting/budgets', [YemenSoftAccountingController::class, 'budgets'])->name('accounting.budgets.index');
        Route::post('/accounting/budgets', [YemenSoftAccountingController::class, 'saveBudget'])->name('accounting.budgets.save');
        Route::get('/accounting/cheques', [YemenSoftAccountingController::class, 'cheques'])->name('accounting.cheques.index');
        Route::post('/accounting/cheques', [YemenSoftAccountingController::class, 'storeCheque'])->name('accounting.cheques.store');
        Route::post('/accounting/cheques/{id}/status', [YemenSoftAccountingController::class, 'updateChequeStatus'])->name('accounting.cheques.update_status');
        Route::get('/accounting/daily-portfolios', [YemenSoftAccountingController::class, 'dailyPortfolios'])->name('accounting.daily_portfolios.index');
        Route::post('/accounting/daily-portfolios', [YemenSoftAccountingController::class, 'storeDailyPortfolio'])->name('accounting.daily_portfolios.store');
        Route::post('/accounting/daily-portfolios/{id}/approve', [YemenSoftAccountingController::class, 'approveDailyPortfolio'])->name('accounting.daily_portfolios.approve');
        Route::post('/accounting/daily-portfolios/{id}/post-voucher', [YemenSoftAccountingController::class, 'postDailyPortfolioToVoucher'])->name('accounting.daily_portfolios.post_voucher');
        Route::get('/accounting/notes', [YemenSoftAccountingController::class, 'financialNotes'])->name('accounting.notes.index');
        Route::post('/accounting/notes', [YemenSoftAccountingController::class, 'storeFinancialNote'])->name('accounting.notes.store');
        Route::get('/accounting/cash-counts', [YemenSoftAccountingController::class, 'cashCounts'])->name('accounting.cash_counts.index');
        Route::post('/accounting/cash-counts', [YemenSoftAccountingController::class, 'storeCashCount'])->name('accounting.cash_counts.store');
        Route::get('/accounting/posting', [YemenSoftAccountingController::class, 'posting'])->name('accounting.posting.index');
        Route::post('/accounting/posting/batch', [YemenSoftAccountingController::class, 'postBatch'])->name('accounting.posting.batch');

        // 6. لوحة تحكم المشرف الأكاديمي التجريبي (مراجعة واعتماد المستويات الأكاديمية)
        Route::get('/supervisor', [SupervisorController::class, 'dashboard'])->name('supervisor.dashboard');
        Route::post('/supervisor/assign/{id}', [SupervisorController::class, 'assignLevel'])->name('supervisor.assign_level');
        Route::post('/supervisor/revoke/{id}', [SupervisorController::class, 'revokeLevelApproval'])->name('supervisor.revoke_approval');

        // 7. لوحة تحكم المعلم التجريبي (مواعيد الحصص وإطلاق الفصل الحي 25 دقيقة)
        Route::get('/teacher', [TeacherController::class, 'dashboard'])->name('teacher.dashboard');

        // 8. نظام إدارة المحتوى البصري والإعلانات (Visual CMS)
        Route::get('/admin/banners', [BannerController::class, 'index'])->name('admin.banners.index');
        Route::post('/admin/banners', [BannerController::class, 'store'])->name('admin.banners.store');
        Route::post('/admin/banners/{id}/update', [BannerController::class, 'update'])->name('admin.banners.update');
        Route::post('/admin/banners/{id}/toggle', [BannerController::class, 'toggle'])->name('admin.banners.toggle');
        Route::delete('/admin/banners/{id}', [BannerController::class, 'destroy'])->name('admin.banners.destroy');

        // 8.1 إدارة ومتابعة استمارات تسجيل الطلاب المتقدمين (Student Applications Hub)
        Route::get('/admin/student-applications', [StudentApplicationController::class, 'index'])->name('admin.student_applications.index');
        Route::post('/admin/student-applications/{id}/update', [StudentApplicationController::class, 'update'])->name('admin.student_applications.update');
        Route::delete('/admin/student-applications/{id}', [StudentApplicationController::class, 'destroy'])->name('admin.student_applications.destroy');
        Route::get('/admin/student-applications/{id}/print', [StudentApplicationController::class, 'print'])->name('admin.student_applications.print');

        // 9. إدارة الهوية البصرية وخلفيات وشعارات ونصوص الموقع (Branding & Backgrounds)
        Route::get('/admin/branding', [BrandingController::class, 'index'])->name('admin.branding.index');
        Route::post('/admin/branding/background', [BrandingController::class, 'updateBackground'])->name('admin.branding.background.update');
        Route::post('/admin/branding/background/remove', [BrandingController::class, 'removeBackground'])->name('admin.branding.background.remove');
        Route::post('/admin/branding/logo', [BrandingController::class, 'updateLogo'])->name('admin.branding.logo.update');
        Route::post('/admin/branding/logo/remove', [BrandingController::class, 'removeLogo'])->name('admin.branding.logo.remove');
        Route::post('/admin/branding/texts', [BrandingController::class, 'updateTexts'])->name('admin.branding.texts.update');
        Route::post('/admin/branding/leadership', [BrandingController::class, 'updateLeadership'])->name('admin.branding.leadership.update');
        Route::post('/admin/branding/texts/reset', [BrandingController::class, 'resetTexts'])->name('admin.branding.texts.reset');
        Route::post('/admin/branding/licenses', [BrandingController::class, 'storeLicense'])->name('admin.branding.licenses.store');
        Route::post('/admin/branding/licenses/{id}/toggle', [BrandingController::class, 'toggleLicenseStatus'])->name('admin.branding.licenses.toggle');
        Route::delete('/admin/branding/licenses/{id}', [BrandingController::class, 'destroyLicense'])->name('admin.branding.licenses.destroy');
        // 9.1 مركز إدارة الواتساب الذكي والآلي والرسائل الموجهة (Smart WhatsApp Management)
        Route::get('/admin/whatsapp', [WhatsAppManagementController::class, 'index'])->name('admin.whatsapp.index');
        Route::post('/admin/whatsapp/settings', [WhatsAppManagementController::class, 'updateSettings'])->name('admin.whatsapp.settings.update');
        Route::post('/admin/whatsapp/toggle-master', [WhatsAppManagementController::class, 'toggleMasterSwitch'])->name('admin.whatsapp.master.toggle');
        Route::post('/admin/whatsapp/users/{id}/toggle', [WhatsAppManagementController::class, 'toggleUser'])->name('admin.whatsapp.users.toggle');
        Route::post('/admin/whatsapp/users/bulk-toggle', [WhatsAppManagementController::class, 'bulkToggleUsers'])->name('admin.whatsapp.users.bulk_toggle');
        Route::post('/admin/whatsapp/test', [WhatsAppManagementController::class, 'testConnection'])->name('admin.whatsapp.test');
        Route::post('/admin/whatsapp/template/{key}', [WhatsAppManagementController::class, 'updateTemplate'])->name('admin.whatsapp.template.update');
        Route::post('/admin/whatsapp/campaign', [WhatsAppManagementController::class, 'sendCampaign'])->name('admin.whatsapp.campaign.send');
        Route::delete('/admin/whatsapp/logs/{id}', [WhatsAppManagementController::class, 'destroyLog'])->name('admin.whatsapp.logs.destroy');
        Route::delete('/admin/whatsapp/logs-clear', [WhatsAppManagementController::class, 'clearLogs'])->name('admin.whatsapp.logs.clear');

        // 9.2 مركز الرقابة والتدقيق الشامل وتتبع العمليات وتغييرات القيم (Full Surveillance & Audit Trail Center)
        Route::get('/admin/audit-logs', [AuditLogController::class, 'index'])->name('admin.audit_logs.index');
        Route::post('/admin/audit-logs/settings', [AuditLogController::class, 'updateSettings'])->name('admin.audit_logs.settings.update');
        Route::get('/admin/audit-logs/print', [AuditLogController::class, 'printReport'])->name('admin.audit_logs.print');
        Route::get('/admin/audit-logs/export-csv', [AuditLogController::class, 'exportCsv'])->name('admin.audit_logs.export_csv');
        Route::get('/admin/audit-logs/{id}', [AuditLogController::class, 'show'])->name('admin.audit_logs.show');
        Route::post('/admin/audit-logs/clear', [AuditLogController::class, 'clearOld'])->name('admin.audit_logs.clear');
    });
});

// 10. التحقق العام من صحة ومصداقية الشهائد الصادرة برقم السيريال أو الباركود QR (بدون اشتراط تسجيل الدخول)
Route::get('/verify/certificate/{serial}', [ExamManagementController::class, 'verifyCertificate'])->name('verify.certificate');

// 11. خدمة المساعد الذكي والرد الآلي لقاعدة البيانات والشات المباشر (Smart Bot & Live Assistant)
Route::post('/chatbot/message', [ChatbotController::class, 'sendMessage'])->name('chatbot.message');
Route::get('/chatbot/quick-info', [ChatbotController::class, 'quickInfo'])->name('chatbot.quick_info');

// 12. نقطة الرد التلقائي عبر تطبيق الواتساب (WhatsApp Webhook Bot)
Route::match(['get', 'post'], '/webhook/whatsapp', [ChatbotController::class, 'whatsappWebhook'])->name('webhook.whatsapp');

// 13. مركز المساعدة ودليل تشغيل واستخدام النظام الشامل (Interactive Help & Knowledge Base Center)
Route::get('/help', [HelpController::class, 'index'])->name('help.index');
