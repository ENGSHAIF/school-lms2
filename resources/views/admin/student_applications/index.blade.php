@extends('layouts.app', ['title' => 'إدارة استمارات وطلبات قبول الطلاب - ASOTS'])

@section('content')
<div class="space-y-8 max-w-7xl mx-auto my-4">

    <!-- شريط رأس الصفحة -->
    <div class="bg-gradient-to-r from-slate-900 via-[#004e7c] to-[#0066A1] text-white p-7 sm:p-9 rounded-3xl shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border border-slate-800">
        <div class="space-y-2">
            <div class="inline-flex items-center gap-2 bg-[#F58220]/25 text-[#F58220] text-xs px-3.5 py-1 rounded-full font-bold border border-[#F58220]/40">
                <span>📋</span>
                <span>مركز القبول والتسجيل وإدارة استمارات الطلاب (Admissions Hub)</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black">إدارة استمارات وطلبات تسجيل الطلاب</h1>
            <p class="text-xs text-sky-100 max-w-2xl leading-relaxed">
                متابعة المتقدمين عبر إستمارة التسجيل الرسمية، رصد بيانات الإدارة (Term, Academic Number, Course)، والتواصل المباشر مع الطلاب وأولياء الأمور عبر الواتساب.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('student.registration') }}" target="_blank"
                class="bg-white/10 hover:bg-white/20 text-white border border-white/25 font-bold text-xs px-4 py-3 rounded-2xl transition flex items-center gap-2 whitespace-nowrap">
                <span>👁️</span>
                <span>معاينة الاستمارة العامة بالمنصة</span>
            </a>
        </div>
    </div>

    <!-- بطاقات الإحصائيات -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <a href="{{ route('admin.student_applications.index') }}" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:border-[#0066A1] transition block">
            <span class="text-[11px] font-bold text-slate-400 block">إجمالي الاستمارات</span>
            <strong class="text-2xl font-black text-slate-900 font-mono">{{ $counts['all'] }}</strong>
            <span class="text-[10px] text-slate-500 block mt-0.5">كافة المتقدمين</span>
        </a>

        <a href="{{ route('admin.student_applications.index', ['status' => 'pending']) }}" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:border-amber-400 transition block">
            <span class="text-[11px] font-bold text-amber-600 block">قيد المراجعة ⏳</span>
            <strong class="text-2xl font-black text-amber-600 font-mono">{{ $counts['pending'] }}</strong>
            <span class="text-[10px] text-slate-500 block mt-0.5">تحتاج تدقيق وتواصل</span>
        </a>

        <a href="{{ route('admin.student_applications.index', ['status' => 'contacted']) }}" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:border-blue-400 transition block">
            <span class="text-[11px] font-bold text-blue-600 block">تم التواصل 📞</span>
            <strong class="text-2xl font-black text-blue-600 font-mono">{{ $counts['contacted'] }}</strong>
            <span class="text-[10px] text-slate-500 block mt-0.5">جارٍ التسكين</span>
        </a>

        <a href="{{ route('admin.student_applications.index', ['status' => 'approved']) }}" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:border-emerald-400 transition block">
            <span class="text-[11px] font-bold text-emerald-600 block">معتمد ومقبول ✅</span>
            <strong class="text-2xl font-black text-emerald-600 font-mono">{{ $counts['approved'] }}</strong>
            <span class="text-[10px] text-slate-500 block mt-0.5">تم منح رقم أكاديمي</span>
        </a>

        <a href="{{ route('admin.student_applications.index', ['status' => 'rejected']) }}" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm hover:border-rose-400 transition block">
            <span class="text-[11px] font-bold text-rose-600 block">مرفوض ❌</span>
            <strong class="text-2xl font-black text-rose-600 font-mono">{{ $counts['rejected'] }}</strong>
            <span class="text-[10px] text-slate-500 block mt-0.5">ملغى أو غير مطابق</span>
        </a>
    </div>

    <!-- جدول وقائمة الاستمارات التفاعلية -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden space-y-4 p-6 sm:p-8">
        
        <!-- شريط البحث والتصفية -->
        <div class="flex flex-col md:flex-row justify-between items-stretch md:items-center gap-4 border-b border-slate-100 pb-4">
            <form action="{{ route('admin.student_applications.index') }}" method="GET" class="flex items-center gap-2 flex-1 max-w-md">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث بالاسم، الهاتف، أو الرقم الأكاديمي..."
                    class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs font-bold outline-none focus:bg-white focus:ring-1 focus:ring-[#0066A1]">
                <button type="submit" class="bg-[#0066A1] hover:bg-[#004e7c] text-white font-bold text-xs px-4 py-2.5 rounded-xl transition">
                    بحث 🔍
                </button>
            </form>

            <div class="text-xs text-slate-500 font-mono">
                يعرض {{ $applications->count() }} من أصل {{ $applications->total() }} طلب
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-right text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 font-black border-b border-slate-200">
                        <th class="p-3.5">#</th>
                        <th class="p-3.5">اسم الطالب (عربي / إنجليزي)</th>
                        <th class="p-3.5">الميلاد والمستوى التعليمي</th>
                        <th class="p-3.5">هواتف التواصل (واتساب)</th>
                        <th class="p-3.5">تحديد مستوى</th>
                        <th class="p-3.5">بيانات الإدارة (Term / Academic No / Course)</th>
                        <th class="p-3.5">الحالة</th>
                        <th class="p-3.5 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($applications as $app)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="p-3.5 font-mono text-slate-400 font-bold">#{{ $app->id }}</td>
                            
                            <td class="p-3.5">
                                <strong class="text-sm font-black text-slate-900 block">{{ $app->name_ar }}</strong>
                                @if($app->name_en)
                                    <span class="text-[11px] text-slate-500 font-sans block" dir="ltr">{{ $app->name_en }}</span>
                                @else
                                    <span class="text-[10px] text-slate-400 italic">لم يُدخل اسماً إنجليزياً</span>
                                @endif
                                <span class="text-[10px] text-slate-400 font-mono block mt-0.5">📅 {{ $app->created_at ? $app->created_at->format('Y-m-d H:i') : '' }}</span>
                            </td>

                            <td class="p-3.5 space-y-1">
                                <div>
                                    <span class="text-slate-400 text-[10px]">الميلاد:</span>
                                    <span class="text-slate-700 font-bold">{{ $app->birth_place ?: '—' }} {{ $app->birth_date ? '(' . $app->birth_date->format('Y-m-d') . ')' : '' }}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 text-[10px]">المستوى:</span>
                                    <span class="text-[#0066A1] font-bold">{{ $app->educational_level ?: '—' }}</span>
                                </div>
                            </td>

                            <td class="p-3.5 space-y-1 font-mono">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[10px] text-emerald-700 font-bold">الدراسة:</span>
                                    <a href="https://wa.me/{{ $app->clean_study_phone }}" target="_blank" class="text-emerald-700 font-bold hover:underline" dir="ltr">
                                        {{ $app->study_phone_whatsapp }} 💬
                                    </a>
                                </div>
                                @if($app->parent_phone)
                                    <div class="flex items-center gap-1.5 text-slate-500 text-[11px]">
                                        <span class="text-[10px]">ولي الأمر:</span>
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $app->parent_phone) }}" target="_blank" class="hover:underline" dir="ltr">
                                            {{ $app->parent_phone }}
                                        </a>
                                    </div>
                                @endif
                            </td>

                            <td class="p-3.5">
                                @if($app->needs_placement_test)
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-900 border border-amber-300">
                                        يحتاج اختبار ✅
                                    </span>
                                @else
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        لا يحتاج
                                    </span>
                                @endif
                            </td>

                            <td class="p-3.5 space-y-0.5 font-mono text-[11px]">
                                <div>
                                    <span class="text-slate-400 text-[10px]">Term:</span>
                                    <strong class="text-slate-900">{{ $app->term }}</strong>
                                </div>
                                <div>
                                    <span class="text-slate-400 text-[10px]">Academic No:</span>
                                    <strong class="text-[#0066A1]">{{ $app->academic_number ?: 'لم يُحدد' }}</strong>
                                </div>
                                <div>
                                    <span class="text-slate-400 text-[10px]">Course:</span>
                                    <span class="text-slate-700 font-sans font-bold">{{ $app->course_assigned ?: 'قيد التسكين' }}</span>
                                </div>
                            </td>

                            <td class="p-3.5">
                                <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-black border {{ $app->status_badge }}">
                                    {{ $app->status_label }}
                                </span>
                            </td>

                            <td class="p-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" onclick="openAppEditModal({{ json_encode($app) }})" 
                                        class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 p-2 rounded-xl font-bold transition" title="تعديل بيانات الإدارة والتسكين">
                                        ✏️
                                    </button>

                                    <a href="{{ route('admin.student_applications.print', $app->id) }}" target="_blank"
                                        class="bg-slate-100 hover:bg-slate-200 text-slate-700 p-2 rounded-xl font-bold transition" title="طباعة الاستمارة الرسمية">
                                        🖨️
                                    </a>

                                    <form action="{{ route('admin.student_applications.destroy', $app->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الاستمارة نهائياً؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-600 p-2 rounded-xl font-bold transition" title="حذف الاستمارة">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-10 text-center text-slate-400">
                                لا توجد استمارات تسجيل مطابقة لبحثك حالياً.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-slate-100">
            {{ $applications->links() }}
        </div>
    </div>

</div>

<!-- مودال تعديل بيانات الإدارة والتسكين للاستمارة -->
<div id="modalEditApplication" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-8 max-w-lg w-full shadow-2xl space-y-5">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="font-black text-sm sm:text-base text-slate-900" id="modalStudentName">بيانات الإدارة والتسكين للاستمارة</h3>
            <button type="button" onclick="document.getElementById('modalEditApplication').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 font-bold text-xl px-2">✕</button>
        </div>

        <form id="formEditApplication" method="POST" class="space-y-4 text-xs">
            @csrf

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">الترم الدراسي (Term) *</label>
                    <input type="text" name="term" id="app_term" required 
                        class="w-full border border-slate-300 rounded-xl p-2.5 font-bold outline-none focus:ring-1 focus:ring-[#0066A1]">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">الرقم الأكاديمي (Academic No)</label>
                    <input type="text" name="academic_number" id="app_academic_number" placeholder="مثال: ASOTS-2026-0412" 
                        class="w-full border border-slate-300 rounded-xl p-2.5 font-mono outline-none focus:ring-1 focus:ring-[#0066A1]">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">الدورة والمستوى المقترح (Course Assigned)</label>
                <input type="text" name="course_assigned" id="app_course_assigned" placeholder="مثال: دبلوم اللغة الإنجليزية - Level 1" 
                    class="w-full border border-slate-300 rounded-xl p-2.5 font-bold outline-none focus:ring-1 focus:ring-[#0066A1]">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">مدة البرنامج المعتمدة</label>
                    <input type="text" name="program_duration" id="app_program_duration" 
                        class="w-full border border-slate-300 rounded-xl p-2.5 outline-none focus:ring-1 focus:ring-[#0066A1]">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">حالة الاستمارة *</label>
                    <select name="status" id="app_status" class="w-full border border-slate-300 rounded-xl p-2.5 font-bold outline-none focus:ring-1 focus:ring-[#0066A1]">
                        <option value="pending">قيد المراجعة ⏳</option>
                        <option value="contacted">تم التواصل والتنسيق 📞</option>
                        <option value="approved">معتمد ومقبول رسمياً ✅</option>
                        <option value="rejected">مرفوض أو ملغى ❌</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">ملاحظات الإدارة والقسم المختص</label>
                <textarea name="admin_notes" id="app_admin_notes" rows="3" placeholder="سجل أي ملاحظات بخصوص الطالب أو وقت الدوام أو التخفيض..."
                    class="w-full border border-slate-300 rounded-xl p-2.5 outline-none focus:ring-1 focus:ring-[#0066A1]"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="document.getElementById('modalEditApplication').classList.add('hidden')" class="px-4 py-2 text-slate-500 font-bold">إلغاء</button>
                <button type="submit" class="bg-[#0066A1] hover:bg-[#004e7c] text-white font-black px-6 py-2.5 rounded-xl shadow transition">
                    حفظ بيانات الإدارة 💾
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAppEditModal(app) {
        document.getElementById('formEditApplication').action = '/admin/student-applications/' + app.id + '/update';
        document.getElementById('modalStudentName').innerText = 'تسكين وبيانات الإدارة: ' + app.name_ar + ' (طلب #' + app.id + ')';
        document.getElementById('app_term').value = app.term || 'Term 40';
        document.getElementById('app_academic_number').value = app.academic_number || '';
        document.getElementById('app_course_assigned').value = app.course_assigned || '';
        document.getElementById('app_program_duration').value = app.program_duration || 'شهر واحد — 5 أيام أسبوعياً (السبت إلى الأربعاء)';
        document.getElementById('app_status').value = app.status || 'pending';
        document.getElementById('app_admin_notes').value = app.admin_notes || '';
        document.getElementById('modalEditApplication').classList.remove('hidden');
    }
</script>
@endsection
