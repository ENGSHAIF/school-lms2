@extends('layouts.app', ['title' => 'رصد تقييم وحضور الحصة اليومية'])

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- ترويسة النموذج وبيانات المجموعة -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border border-slate-800">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold border border-emerald-400/30">
                    رصد الحصة اليومية والمشاركات 📋
                </span>
                <span class="text-xs text-amber-400 font-bold font-mono">الحصة رقم #{{ $nextSessionNumber }}</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black">رصد حضور وتقييم: {{ $group->name }}</h1>
            <p class="text-xs text-slate-300 mt-1">
                المسار: <strong>{{ $group->course?->name }}</strong> • 
                المستوى: <span class="text-amber-300 font-bold">{{ $group->level?->name }}</span> • 
                الطلاب المسكنين: <span class="text-emerald-400 font-bold font-mono">{{ $group->activeStudents->count() }} طالب</span>
            </p>
        </div>

        <a href="{{ route('admin.groups.evaluations.index', ['group_id' => $group->id]) }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs transition border border-slate-700">
            رجوع للسجل ✕
        </a>
    </div>

    <form method="POST" action="{{ route('admin.groups.evaluations.store', $group->id) }}" class="space-y-6">
        @csrf

        <!-- 1. البيانات العامة للحصة والدرس -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-2xs space-y-4">
            <h3 class="text-sm font-black text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3">
                <span>📖</span>
                <span>البيانات الأساسية للحصة ومحتوى الدرس</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">تاريخ الحصة / اللقاء:</label>
                    <input type="date" name="session_date" required value="{{ old('session_date', now()->toDateString()) }}" 
                        class="w-full border border-slate-300 rounded-xl p-2.5 font-bold text-slate-800 outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">رقم الحصة في الدورة:</label>
                    <input type="number" name="session_number" required min="1" value="{{ old('session_number', $nextSessionNumber) }}" 
                        class="w-full border border-slate-300 rounded-xl p-2.5 font-bold font-mono text-slate-800 outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">عنوان وموضوع درس اليوم:</label>
                    <input type="text" name="lesson_topic" required value="{{ old('lesson_topic') }}" 
                        placeholder="مثال: Unit 3 - Simple Present & Daily Routines"
                        class="w-full border border-slate-300 rounded-xl p-2.5 font-bold text-slate-800 outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">ملخص محتوى الدرس والشرح (Course Summary):</label>
                    <textarea name="lesson_summary" rows="2" 
                        placeholder="اكتب ملخصاً لما تم شرحه اليوم وتدريب الطلاب عليه..."
                        class="w-full border border-slate-300 rounded-xl p-2.5 text-slate-800 outline-none focus:ring-2 focus:ring-indigo-500">{{ old('lesson_summary') }}</textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">الواجب والتكليف المنزلي المطلوب (Homework):</label>
                    <textarea name="homework_assigned" rows="2" 
                        placeholder="مثال: حل تمارين صفحة 45 من كتاب التمارين وتسجيل مقطع صوتي للنص..."
                        class="w-full border border-slate-300 rounded-xl p-2.5 text-slate-800 outline-none focus:ring-2 focus:ring-indigo-500">{{ old('homework_assigned') }}</textarea>
                </div>
            </div>
        </div>

        <!-- 2. كشف حضور وتفاعل وتقييم الطلاب الفردي -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-2xs space-y-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-sm font-black text-slate-900 flex items-center gap-2">
                        <span>👥</span>
                        <span>رصد حضور الطلاب، والمشاركات الصوتية، ومكافآت النجوم ⭐</span>
                    </h3>
                    <span class="text-xs text-slate-400">حدد حالة الحضور، درجة التفاعل، وامنح النجوم التراكمية للمتميزين</span>
                </div>
                
                <!-- زر سريع لتعيين الكل حاضر -->
                <button type="button" onclick="setAllPresent()" class="text-xs font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-xl border border-emerald-200 transition">
                    تعيين جميع الطلاب حاضرين 🟢
                </button>
            </div>

            @if($group->activeStudents->isEmpty())
                <div class="p-8 text-center text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                    لا يوجد طلاب مسكنين في هذه المجموعة حتى الآن.
                </div>
            @else
                <div class="space-y-4">
                    @foreach($group->activeStudents as $idx => $student)
                        <div class="border border-slate-200 rounded-2xl p-4 bg-slate-50/50 hover:bg-white hover:border-indigo-300 transition space-y-3">
                            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
                                <div class="flex items-center gap-3">
                                    <span class="w-7 h-7 bg-indigo-50 text-indigo-700 rounded-lg flex items-center justify-center font-bold font-mono text-xs">
                                        {{ $idx + 1 }}
                                    </span>
                                    <div>
                                        <h4 class="font-bold text-sm text-slate-900">{{ $student->name }}</h4>
                                        <span class="text-[10px] text-slate-400 font-mono">
                                            {{ $student->student_code ?? $student->user_code }} • رصيد النجوم الحالي: ⭐ {{ $student->stars_balance }}
                                        </span>
                                    </div>
                                </div>

                                <!-- خيارات الحضور والغياب السريعة (Radio Pills) -->
                                <div class="flex items-center gap-1.5 bg-white p-1 rounded-xl border border-slate-200 text-xs font-bold w-full md:w-auto">
                                    <label class="flex-1 md:flex-none flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg cursor-pointer transition has-[:checked]:bg-emerald-500 has-[:checked]:text-white">
                                        <input type="radio" name="evaluations[{{ $student->id }}][attendance_status]" value="present" checked class="hidden status-present">
                                        <span>حاضر 🟢</span>
                                    </label>

                                    <label class="flex-1 md:flex-none flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg cursor-pointer transition has-[:checked]:bg-rose-500 has-[:checked]:text-white">
                                        <input type="radio" name="evaluations[{{ $student->id }}][attendance_status]" value="absent" class="hidden status-absent">
                                        <span>غائب 🔴</span>
                                    </label>

                                    <label class="flex-1 md:flex-none flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg cursor-pointer transition has-[:checked]:bg-amber-500 has-[:checked]:text-slate-950">
                                        <input type="radio" name="evaluations[{{ $student->id }}][attendance_status]" value="late" class="hidden status-late">
                                        <span>متأخر 🟡</span>
                                    </label>

                                    <label class="flex-1 md:flex-none flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg cursor-pointer transition has-[:checked]:bg-slate-700 has-[:checked]:text-white">
                                        <input type="radio" name="evaluations[{{ $student->id }}][attendance_status]" value="excused" class="hidden status-excused">
                                        <span>معذور ⚪</span>
                                    </label>
                                </div>
                            </div>

                            <!-- تفاصيل الدرجات والمشاركات والنجوم -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 pt-2 border-t border-slate-100 text-xs">
                                <div>
                                    <label class="block font-bold text-slate-600 mb-1">المشاركة والتفاعل (1-5):</label>
                                    <select name="evaluations[{{ $student->id }}][participation_score]" class="w-full border border-slate-300 rounded-xl p-2 font-bold text-indigo-700">
                                        <option value="5" selected>⭐⭐⭐⭐⭐ ممتاز (5)</option>
                                        <option value="4">⭐⭐⭐⭐ جيد جداً (4)</option>
                                        <option value="3">⭐⭐⭐ متوسط (3)</option>
                                        <option value="2">⭐⭐ ضعيف (2)</option>
                                        <option value="1">⭐ ضعيف جداً (1)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-600 mb-1">الاستيعاب والفهم (1-5):</label>
                                    <select name="evaluations[{{ $student->id }}][understanding_score]" class="w-full border border-slate-300 rounded-xl p-2 font-bold text-indigo-700">
                                        <option value="5" selected>5 - استيعاب كامل</option>
                                        <option value="4">4 - جيد جداً</option>
                                        <option value="3">3 - متوسط</option>
                                        <option value="2">2 - يحتاج دعم</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-600 mb-1">الواجب / ورقة الإجابة (1-5):</label>
                                    <select name="evaluations[{{ $student->id }}][homework_score]" class="w-full border border-slate-300 rounded-xl p-2 font-bold text-purple-700">
                                        <option value="5" selected>5 - منجز بالكامل</option>
                                        <option value="4">4 - منجز مع ملاحظات</option>
                                        <option value="3">3 - منجز جزئياً</option>
                                        <option value="1">1 - لم يسلّم</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-600 mb-1">مكافأة نجوم إضافية ⭐:</label>
                                    <input type="number" name="evaluations[{{ $student->id }}][stars_awarded]" min="0" max="20" value="2" 
                                        class="w-full border border-amber-300 bg-amber-50/40 rounded-xl p-2 font-mono font-bold text-amber-900 outline-none">
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-600 mb-1">نوع المشاركة في الحصة:</label>
                                    <div class="flex items-center gap-3 pt-2">
                                        <label class="flex items-center gap-1 cursor-pointer">
                                            <input type="checkbox" name="evaluations[{{ $student->id }}][spoke_voice]" value="1" checked class="rounded text-indigo-600">
                                            <span class="text-[11px] font-bold">🎙️ صوتي</span>
                                        </label>
                                        <label class="flex items-center gap-1 cursor-pointer">
                                            <input type="checkbox" name="evaluations[{{ $student->id }}][wrote_chat]" value="1" checked class="rounded text-indigo-600">
                                            <span class="text-[11px] font-bold">💬 كتابي</span>
                                        </label>
                                        <label class="flex items-center gap-1 cursor-pointer">
                                            <input type="checkbox" name="evaluations[{{ $student->id }}][submitted_task]" value="1" checked class="rounded text-indigo-600">
                                            <span class="text-[11px] font-bold">📝 واجب</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <input type="text" name="evaluations[{{ $student->id }}][teacher_notes]" 
                                    placeholder="ملاحظة أو توجيه خاص بالطالب (مثال: أداء متميز في قراءة النص، تدرب على نطق حرف P)..."
                                    class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 outline-none focus:border-indigo-500">
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- زر الحفظ والاعتماد النهائي -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-5 flex flex-col sm:flex-row justify-between items-center gap-4 shadow-sm">
            <span class="text-xs text-slate-500 font-bold">
                ⚡ سيتم ترحيل النجوم الممنوحة مباشرة إلى رصيد حساب كل طالب، وتحديث نسبة حضور المجموعة.
            </span>
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <a href="{{ route('admin.groups.evaluations.index', ['group_id' => $group->id]) }}" class="flex-1 sm:flex-none px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition text-center">
                    إلغاء
                </a>
                <button type="submit" class="flex-1 sm:flex-none px-8 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs shadow-lg shadow-emerald-600/30 transition flex items-center justify-center gap-2">
                    <span>💾</span>
                    <span>حفظ واعتماد تقييم اليوم</span>
                </button>
            </div>
        </div>
    </form>

</div>

<script>
function setAllPresent() {
    document.querySelectorAll('.status-present').forEach(el => el.checked = true);
}
</script>
@endsection
