<?php

namespace App\Http\Controllers;

use App\Services\SystemHandbookService;
use Illuminate\Http\Request;

class HelpController extends Controller
{
    /**
     * عرض مركز المساعدة ودليل تشغيل واستخدام النظام الشامل بحسب الصلاحيات
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $userRole = $user ? $user->role : 'guest';

        // جلب المقالات المسموحة لهذا المستخدم فقط بحسب رتبته
        $userGuides = SystemHandbookService::getGuidesForUser($user);

        // هل المستخدم من كادر الإدارة أو التعليم (يحق له استعراض كافة أقسام الدليل)؟
        $isStaff = $user && ($user->isAdmin() || $user->isSupervisor() || $user->isTeacher() || $user->isAccountant() || ($user->permissionGroup && $user->permissionGroup->isAdminGroup()));

        return view('help.index', [
            'guides' => $userGuides,
            'userRole' => $userRole,
            'isStaff' => $isStaff,
            'currentUser' => $user,
        ]);
    }
}
