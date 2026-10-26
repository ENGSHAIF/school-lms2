@extends('layouts.app', ['title' => 'بنك أسئلة وتصميم الاختبار: ' . $exam->title])

@section('content')
<div class="space-y-6">

    <!-- شريط الترويسة الرئيسي والأزرار الإدارية السريعة -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden border border-indigo-900/50">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
            <div>
                <div class="flex items-center gap-2 text-xs text-amber-400 font-bold mb-2 flex-wrap">
                    <a href="{{ route('admin.exams.index') }}" class="hover:underline flex items-center gap-1">
                        <span>←</span>
                        <span>بنك الاختبارات والكويزات</span>
                    </a>
                    <span>•</span>
                    <span class="text-indigo-200">
                        دورة: {{ $exam->course?->displayName ?: ($exam->level?->course?->name ?: 'عام') }}
                    </span>
                    @if($exam->level)
                        <span>•</span>
                        <span class="text-amber-200">مستوى: {{ $exam->level->name }}</span>
                    @else
                        <span>•</span>
                        <span class="text-slate-300">عام لكافة المستويات</span>
                    @endif
                    @if($exam->term)
                        <span>•</span>
                        <span class="bg-indigo-800 text-indigo-200 px-2 py-0.5 rounded text-[10px]">{{ $exam->term }}</span>
                    @endif
                </div>

                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl sm:text-3xl font-black text-white">{{ $exam->title }}</h1>
                    <span class="px-3 py-1 rounded-full text-xs font-black border {{ $exam->quiz_period_badge }}">
                        {{ $exam->quiz_period_label }}
                    </span>
                    @if($exam->is_active)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            مفعل ونشط ✓
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-500/20 text-slate-400 border border-slate-500/30">
                            معطل
                        </span>
                    @endif

                    <!-- زر تعديل الاسم والإعدادات الفوري -->
                    <button onclick="openEditSettingsModal()" class="px-3 py-1 bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-400/40 rounded-xl text-xs font-bold transition flex items-center gap-1">
                        <span>✏️</span>
                        <span>تعديل الاسم والدورة</span>
                    </button>
                </div>

                <p class="text-xs text-slate-300 mt-2 max-w-2xl leading-relaxed">
                    {{ $exam->description ?: 'إدارة بنك أسئلة الاختبار، تعديل الأسئلة والخيارات والإجابات الصحيحة، إضافة أسئلة يدوياً أو استيرادها دفعة واحدة من الوورد.' }}
                </p>
            </div>

            <!-- أزرار الإجراءات السريعة في الترويسة -->
            <div class="flex items-center gap-2 flex-wrap">
                <!-- زر استعراض محاولات ونتائج الطلاب -->
                <a href="{{ route('admin.exams.attempts', $exam->id) }}" class="px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-black text-xs transition flex items-center gap-1.5 shadow-md">
                    <span>📋</span>
                    <span>محاولات ونتائج الطلاب ({{ $exam->attempts->count() }})</span>
                </a>

                <!-- زر مشاركة للمجموعة -->
                <button onclick="openShareModal()" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs transition flex items-center gap-1.5 shadow-md">
                    <span>🚀</span>
                    <span>مشاركة للمجموعة كنشاط</span>
                </button>

                <!-- زر معاينة الاختبار كطالب -->
                <a href="{{ route('student.exam.take', $exam->id) }}" target="_blank" class="px-3.5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-xs transition flex items-center gap-1.5">
                    <span>👁️</span>
                    <span>معاينة كطالب</span>
                </a>

                <!-- زر تعديل الإعدادات والاسم -->
                <button onclick="openEditSettingsModal()" class="px-3.5 py-2.5 rounded-xl bg-amber-500 text-slate-950 font-black text-xs shadow-md transition flex items-center gap-1.5">
                    <span>⚙️</span>
                    <span>تعديل الإعدادات</span>
                </button>
            </div>
        </div>

        <!-- بطاقات المؤشرات الإحصائية للاختبار -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-indigo-900/50 text-xs">
            <div class="bg-slate-800/60 p-3 rounded-2xl border border-slate-700/60 text-center">
                <span class="text-slate-400 font-bold text-[10px] block">أسئلة بنك الاختبار</span>
                <span class="text-xl font-black text-white font-mono">{{ $exam->questions->count() }}</span>
                <span class="text-[10px] text-slate-400 block">سؤال متاح في البنك</span>
            </div>

            <div class="bg-slate-800/60 p-3 rounded-2xl border border-slate-700/60 text-center">
                <span class="text-slate-400 font-bold text-[10px] block">الأسئلة المعروضة للطالب</span>
                <span class="text-xl font-black text-amber-400 font-mono">
                    {{ $exam->random_questions_count ?: 'كل الأسئلة (' . $exam->questions->count() . ')' }}
                </span>
                <span class="text-[10px] text-amber-300 block">عشوائية لكل طالب 🎲</span>
            </div>

            <div class="bg-slate-800/60 p-3 rounded-2xl border border-slate-700/60 text-center">
                <span class="text-slate-400 font-bold text-[10px] block">إجمالي درجات البنك</span>
                <span class="text-xl font-black text-emerald-400 font-mono">{{ $exam->total_points }}</span>
                <span class="text-[10px] text-emerald-300 block">درجة النجاح: {{ $exam->passing_score }}%</span>
            </div>

            <div class="bg-slate-800/60 p-3 rounded-2xl border border-slate-700/60 text-center">
                <span class="text-slate-400 font-bold text-[10px] block">زمن الاختبار وإعلان النتيجة</span>
                <span class="text-xl font-black text-blue-400 font-mono">{{ $exam->duration_minutes }} د</span>
                <span class="text-[10px] {{ $exam->show_result_immediately ? 'text-emerald-300' : 'text-amber-300' }} block font-bold">
                    {{ $exam->show_result_immediately ? 'إظهار فوري للنتيجة' : 'حجب للمراجعة والاعتماد' }}
                </span>
            </div>
        </div>
    </div>

    <!-- رسائل التنبيه والنجاح -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <span class="text-lg">✅</span>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 font-black">✕</button>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2">
                <span class="text-lg">⚠️</span>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 font-black">✕</button>
        </div>
    @endif

    <!-- قسمين رئيسيين: 1. إضافة الأسئلة يدوياً أو استيرادها  2. قائمة الأسئلة الحالية وتعديلها -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- العمود الجانبي الأيمن: إضافة سؤال يدوي + الاستيراد السريع من Word/Excel -->
        <div class="space-y-6">

            <!-- بطاقة الاستيراد السريع من الوورد أو الإكسل -->
            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-3xl border border-indigo-200 p-5 space-y-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">⚡</span>
                        <div>
                            <h3 class="font-black text-sm text-indigo-950">استيراد سريع للأسئلة</h3>
                            <span class="text-[11px] text-indigo-700">نسخ ولصق من الوورد أو رفع ملف</span>
                        </div>
                    </div>
                </div>

                <p class="text-[11px] text-slate-600 leading-relaxed">
                    وفر وقتك! يمكنك نسخ أسئلتك المجهزة في ملف Word ولصقها هنا مباشرة، وسيتعرف النظام عليها ويضيفها للبنك فوراً.
                </p>

                <button onclick="openBulkImportModal()" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs rounded-xl shadow-md transition flex items-center justify-center gap-2">
                    <span>📄</span>
                    <span>فتح نافذة الاستيراد من الوورد / الإكسل</span>
                </button>
            </div>

            <!-- بطاقة إضافة سؤال يدوي مع مربعات الاختيار (Radio Buttons) لتحديد الإجابة الصحيحة -->
            <div class="bg-white rounded-3xl border border-slate-200 p-6 space-y-4 shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="font-black text-sm text-slate-900 flex items-center gap-2">
                        <span>➕</span>
                        <span>إضافة سؤال جديد يدوياً</span>
                    </h3>
                    <span class="text-[10px] text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">حدد الإجابة الصحيحة</span>
                </div>

                <form action="{{ route('admin.exams.questions.add', $exam->id) }}" method="POST" class="space-y-4 text-xs">
                    @csrf
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">نوع السؤال</label>
                        <select name="type" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                            <option value="mcq">🔘 اختيارات من متعدد (Multiple Choice)</option>
                            <option value="reading">📖 قراءة وفهم مقطوعة (Reading Comprehension)</option>
                            <option value="listening">🎧 سؤال استماع (Listening)</option>
                            <option value="speaking_ai">🎙️ اختبار نطق وتحدث صوتي (Speaking)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">نص السؤال <span class="text-rose-500">*</span></label>
                        <textarea name="question_text" required rows="2" placeholder="اكتب نص السؤال هنا..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-bold text-xs"></textarea>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">قطعة قراءة أو نص مساعد (اختياري)</label>
                        <textarea name="context_passage" rows="2" placeholder="إن كان السؤال يعتمد على قطعة قراءة..." class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none text-xs"></textarea>
                    </div>

                    <!-- الخيارات الأربعة مع زر راديو لتحديد الإجابة الصحيحة -->
                    <div class="space-y-2">
                        <label class="block font-bold text-slate-700">خيارات الإجابة (اضغط الدائرة لتحديد الإجابة الصحيحة): <span class="text-rose-500">*</span></label>

                        <div class="space-y-2" id="manualOptionsContainer">
                            @foreach(['A' => 'الخيار الأول (A)', 'B' => 'الخيار الثاني (B)', 'C' => 'الخيار الثالث (C)', 'D' => 'الخيار الرابع (D)'] as $key => $placeholder)
                                <div class="flex items-center gap-2 bg-slate-50 p-2 rounded-xl border border-slate-200 hover:border-emerald-400 transition option-row">
                                    <input type="radio" name="correct_answer_radio" value="{{ $loop->index }}" {{ $loop->first ? 'checked' : '' }} onchange="updateCorrectAnswerInput({{ $loop->index }})" class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer" title="حدد كإجابة صحيحة">
                                    <span class="w-6 h-6 rounded-lg bg-slate-200 text-slate-700 font-mono font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ $key }}
                                    </span>
                                    <input type="text" name="options[]" id="opt_{{ $loop->index }}" required placeholder="{{ $placeholder }}" oninput="syncRadioValue({{ $loop->index }})" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none text-xs bg-white font-medium">
                                </div>
                            @endforeach
                        </div>

                        <!-- الحقل الخفي لتمرير الإجابة الصحيحة -->
                        <input type="hidden" name="correct_answer" id="manualCorrectAnswerInput" value="">
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-2">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">درجة السؤال</label>
                            <input type="number" name="points" value="1.0" step="0.5" min="0.5" max="100" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono font-bold">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" onclick="prepareManualSubmit()" class="w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white font-black rounded-xl shadow-md transition flex items-center justify-center gap-1.5">
                                <span>➕</span>
                                <span>إضافة للبنك</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>

        <!-- العمود الأيسر: بنك الأسئلة الحالي وترتيبها وتعديلها وحذفها -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 flex-wrap gap-2">
                    <div>
                        <h3 class="font-black text-base text-slate-900 flex items-center gap-2">
                            <span>📚</span>
                            <span>بنك أسئلة الاختبار الحالية</span>
                        </h3>
                        <span class="text-xs text-slate-400 font-bold">إجمالي: {{ $exam->questions->count() }} سؤال • مجموع الدرجات: {{ $exam->total_points }} نقطة</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <button onclick="openBulkImportModal()" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-xs font-bold border border-indigo-200 transition">
                            ⚡ استيراد وورد
                        </button>
                    </div>
                </div>

                @if($exam->questions->isEmpty())
                    <div class="text-center py-12 bg-slate-50 rounded-2xl border border-dashed border-slate-200 text-slate-400 space-y-2">
                        <span class="text-4xl block">📝</span>
                        <h4 class="font-black text-slate-700 text-sm">بنك الأسئلة فارغ حالياً</h4>
                        <p class="text-xs max-w-sm mx-auto">
                            أضف سؤالك الأول من النموذج الجانبي، أو استخدم زر "استيراد سريع" للصق كافة الأسئلة والخيارات من ملف الوورد دفعة واحدة.
                        </p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($exam->questions as $idx => $q)
                            <div class="p-4 rounded-2xl border border-slate-200 hover:border-indigo-300 bg-white transition space-y-3 relative group" id="questionCard_{{ $q->id }}">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3">
                                        <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-700 font-mono font-black text-xs flex items-center justify-center shrink-0">
                                            #{{ $idx + 1 }}
                                        </span>
                                        <div>
                                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">
                                                    {{ $q->type === 'mcq' ? 'اختيارات' : ($q->type === 'reading' ? 'قراءة' : 'نطق صوتي') }}
                                                </span>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    ⭐ {{ $q->points }} درجة
                                                </span>
                                            </div>
                                            <h4 class="font-black text-sm text-slate-900 leading-snug">{{ $q->question_text }}</h4>
                                            @if($q->context_passage)
                                                <p class="text-[11px] text-slate-500 bg-slate-50 p-2 rounded-lg mt-1.5 border border-slate-100">{{ $q->context_passage }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- أزرار التعديل والحذف للسؤال -->
                                    <div class="flex items-center gap-1 shrink-0">
                                        <!-- زر تعديل السؤال والخيارات والإجابة الصحيحة -->
                                        <button onclick="openEditQuestionModal({{ $q->id }})" class="p-2 text-indigo-600 hover:text-indigo-800 rounded-xl hover:bg-indigo-50 transition border border-indigo-200/60 flex items-center gap-1 text-xs font-bold" title="تعديل السؤال والخيارات والإجابة الصحيحة">
                                            <span>✏️</span>
                                            <span>تعديل</span>
                                        </button>

                                        <!-- زر حذف السؤال -->
                                        <form action="{{ route('admin.exams.questions.destroy', $q->id) }}" method="POST" onsubmit="return confirm('هل تريد بالتأكيد حذف هذا السؤال من بنك الاختبار؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-rose-500 hover:text-rose-700 rounded-xl hover:bg-rose-50 transition border border-rose-200/60" title="حذف السؤال">
                                                🗑️
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- خيارات الإجابة واستعراض الإجابة الصحيحة -->
                                @if(is_array($q->options))
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-2 border-t border-slate-100">
                                        @php
                                            $letters = ['A', 'B', 'C', 'D', 'E'];
                                        @endphp
                                        @foreach($q->options as $optIdx => $opt)
                                            @php
                                                $isCorrect = (trim(mb_strtolower($opt)) === trim(mb_strtolower($q->correct_answer)));
                                            @endphp
                                            <div class="flex items-center gap-2 p-2 rounded-xl text-xs {{ $isCorrect ? 'bg-emerald-50 border border-emerald-300 text-emerald-950 font-bold' : 'bg-slate-50 text-slate-700' }}">
                                                <span class="w-5 h-5 rounded-md {{ $isCorrect ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-600' }} font-mono font-bold text-[10px] flex items-center justify-center shrink-0">
                                                    {{ $letters[$optIdx] ?? ($optIdx + 1) }}
                                                </span>
                                                <span class="flex-1">{{ $opt }}</span>
                                                @if($isCorrect)
                                                    <span class="text-[10px] bg-emerald-200 text-emerald-900 px-1.5 py-0.5 rounded font-bold">✓ الإجابة الصحيحة</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <!-- نافذة تعديل السؤال والإجابات الصحيحة المنبثقة لكل سؤال -->
                            <div id="editQuestionModal_{{ $q->id }}" class="fixed inset-0 z-50 hidden bg-slate-950/75 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4">
                                <div class="bg-white rounded-3xl w-full max-w-xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[92vh]">
                                    <div class="px-6 py-4 bg-gradient-to-r from-slate-900 to-indigo-950 text-white flex justify-between items-center shrink-0">
                                        <h3 class="font-black text-sm flex items-center gap-2">
                                            <span>✏️</span>
                                            <span>تعديل السؤال #{{ $idx + 1 }} والخيارات والإجابة الصحيحة</span>
                                        </h3>
                                        <button onclick="closeEditQuestionModal({{ $q->id }})" class="text-white/70 hover:text-white text-lg">✕</button>
                                    </div>

                                    <form action="{{ route('admin.exams.questions.update', $q->id) }}" method="POST" id="editQForm_{{ $q->id }}" class="p-6 space-y-4 overflow-y-auto text-xs">
                                        @csrf
                                        <div>
                                            <label class="block font-bold text-slate-700 mb-1">نص السؤال <span class="text-rose-500">*</span></label>
                                            <textarea name="question_text" required rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-bold text-xs">{{ $q->question_text }}</textarea>
                                        </div>

                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block font-bold text-slate-700 mb-1">نوع السؤال</label>
                                                <select name="type" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                                                    <option value="mcq" {{ $q->type === 'mcq' ? 'selected' : '' }}>🔘 اختيارات من متعدد</option>
                                                    <option value="reading" {{ $q->type === 'reading' ? 'selected' : '' }}>📖 قراءة وفهم</option>
                                                    <option value="listening" {{ $q->type === 'listening' ? 'selected' : '' }}>🎧 استماع</option>
                                                    <option value="speaking_ai" {{ $q->type === 'speaking_ai' ? 'selected' : '' }}>🎙️ تحدث صوتي</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block font-bold text-slate-700 mb-1">درجة السؤال *</label>
                                                <input type="number" name="points" value="{{ $q->points }}" step="0.5" min="0.5" max="100" required class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono font-bold">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block font-bold text-slate-700 mb-1">قطعة قراءة أو نص توضيحي (اختياري)</label>
                                            <textarea name="context_passage" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none text-xs">{{ $q->context_passage }}</textarea>
                                        </div>

                                        <!-- خيارات الإجابة مع الراديو للإجابة الصحيحة -->
                                        <div class="space-y-2 pt-2 border-t border-slate-100">
                                            <label class="block font-bold text-slate-900">
                                                خيارات الإجابة (اضغط الدائرة لتحديد الإجابة الصحيحة) <span class="text-rose-500">*</span>:
                                            </label>

                                            <div class="space-y-2">
                                                @php
                                                    $existingOpts = is_array($q->options) ? $q->options : [];
                                                    while (count($existingOpts) < 4) {
                                                        $existingOpts[] = '';
                                                    }
                                                @endphp
                                                @foreach($existingOpts as $optIndex => $optVal)
                                                    @php
                                                        $isThisCorrect = (trim(mb_strtolower($optVal)) === trim(mb_strtolower($q->correct_answer))) || ($optIndex === 0 && empty($q->correct_answer));
                                                    @endphp
                                                    <div class="flex items-center gap-2 bg-slate-50 p-2 rounded-xl border border-slate-200 hover:border-emerald-400 transition">
                                                        <input type="radio" name="edit_radio_{{ $q->id }}" value="{{ $optIndex }}" {{ $isThisCorrect ? 'checked' : '' }}
                                                               onchange="syncEditCorrectAnswer({{ $q->id }}, {{ $optIndex }})"
                                                               class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 cursor-pointer" title="حدد كإجابة صحيحة">
                                                        <span class="w-6 h-6 rounded-lg bg-slate-200 text-slate-700 font-mono font-bold text-xs flex items-center justify-center shrink-0">
                                                            {{ $letters[$optIndex] ?? ($optIndex + 1) }}
                                                        </span>
                                                        <input type="text" name="options[]" id="edit_opt_{{ $q->id }}_{{ $optIndex }}" value="{{ $optVal }}" required
                                                               oninput="onEditOptionChange({{ $q->id }}, {{ $optIndex }})"
                                                               placeholder="الخيار {{ $letters[$optIndex] ?? ($optIndex + 1) }}"
                                                               class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none text-xs bg-white font-medium">
                                                    </div>
                                                @endforeach
                                            </div>

                                            <!-- حقل الإجابة الصحيحة الفعلي -->
                                            <input type="hidden" name="correct_answer" id="edit_correct_answer_{{ $q->id }}" value="{{ $q->correct_answer }}">
                                        </div>

                                        <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                                            <button type="button" onclick="closeEditQuestionModal({{ $q->id }})" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                                                إلغاء
                                            </button>
                                            <button type="submit" onclick="prepareEditQuestionSubmit({{ $q->id }})" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow-md transition flex items-center gap-1.5">
                                                <span>💾</span>
                                                <span>حفظ تعديل السؤال والإجابة</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>

<!-- ======================================================= -->
<!-- نافذة منبثقة: استيراد الأسئلة من الوورد أو الإكسل (Bulk Import Modal) -->
<!-- ======================================================= -->
<div id="bulkImportModal" class="fixed inset-0 z-50 hidden bg-slate-950/75 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-3xl w-full max-w-2xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[92vh]">
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-900 to-purple-950 text-white flex justify-between items-center shrink-0">
            <h3 class="font-black text-sm flex items-center gap-2">
                <span>📄</span>
                <span>استيراد الأسئلة دفعة واحدة من الوورد أو الإكسل</span>
            </h3>
            <button onclick="closeBulkImportModal()" class="text-white/70 hover:text-white text-lg">✕</button>
        </div>

        <form action="{{ route('admin.exams.questions.import', $exam->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 overflow-y-auto text-xs flex-1">
            @csrf

            <!-- نصائح التنسيق -->
            <div class="bg-amber-50 p-4 rounded-2xl border border-amber-200 text-amber-950 space-y-1.5 leading-relaxed">
                <span class="font-black text-xs block">💡 الصيغة المدعومة للاستيراد السريع:</span>
                <p class="text-[11px]">يمكنك كتابة أو نسخ الأسئلة بالشكل الطبيعي، مثلاً:</p>
                <pre class="bg-white p-2.5 rounded-xl border border-amber-200/80 font-mono text-[10px] text-slate-800 text-left overflow-x-auto" dir="ltr">1. What is the past form of 'take'?
A) taken
B) took
C) taking
D) takes
Answer: B
Points: 2

2. He _____ Arabic and English.
A) speak
B) speaks
C) speaking
Answer: B</pre>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">الصق نص الأسئلة المنسوخ من ملف Word هنا:</label>
                <textarea name="raw_text" rows="8" placeholder="الصق الأسئلة والخيارات والإجابة هنا..." class="w-full p-3 rounded-2xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-mono text-xs"></textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">أو ارفع ملف الأسئلة (.docx أو .txt أو .csv):</label>
                <input type="file" name="import_file" accept=".docx,.txt,.csv" class="w-full text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer">
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeBulkImportModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                    إلغاء
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs shadow-md transition flex items-center gap-1.5">
                    <span>⚡</span>
                    <span>معالجة واستيراد الأسئلة فوراً</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================= -->
<!-- نافذة منبثقة: مشاركة الاختبار لمجموعة دراسية (Share to Group) -->
<!-- ======================================================= -->
<div id="shareGroupModal" class="fixed inset-0 z-50 hidden bg-slate-950/75 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[92vh]">
        <div class="px-6 py-4 bg-emerald-900 text-white flex justify-between items-center shrink-0">
            <h3 class="font-black text-sm flex items-center gap-2">
                <span>🚀</span>
                <span>إرسال ومشاركة الاختبار لمجموعة طلابية</span>
            </h3>
            <button onclick="closeShareModal()" class="text-white/70 hover:text-white text-lg">✕</button>
        </div>

        <form action="{{ route('admin.exams.share_group', $exam->id) }}" method="POST" class="p-6 space-y-4 overflow-y-auto text-xs">
            @csrf
            <div>
                <label class="block font-bold text-slate-700 mb-1">اختر المجموعة الدراسية المراد اختبار طلابها <span class="text-rose-500">*</span></label>
                <select name="study_group_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 outline-none bg-white font-bold text-xs">
                    <option value="">-- حدد المجموعة --</option>
                    @foreach($studyGroups as $grp)
                        <option value="{{ $grp->id }}">👥 {{ $grp->name }} ({{ $grp->students->count() }} طالب)</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">تعليمات أو ملاحظة موجهة لطلاب المجموعة</label>
                <textarea name="custom_instructions" rows="2" placeholder="مثال: يرجى حل الكويز اليوم قبل الساعة 10 مساءً، بالتوفيق للجميع..." class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 outline-none text-xs"></textarea>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">موعد التسليم الأخير (Deadline)</label>
                <input type="date" name="due_date" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-emerald-500 outline-none text-xs">
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeShareModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                    إلغاء
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs shadow-md transition">
                    مشاركة في حائط المجموعة الآن 🚀
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================= -->
<!-- نافذة منبثقة: تعديل إعدادات واسم وتصنيف ودورة الاختبار -->
<!-- ======================================================= -->
<div id="editSettingsModal" class="fixed inset-0 z-50 hidden bg-slate-950/75 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[92vh]">
        <div class="px-6 py-4 bg-slate-900 text-white flex justify-between items-center shrink-0">
            <h3 class="font-black text-sm flex items-center gap-2">
                <span>⚙️</span>
                <span>تعديل اسم وتصنيف ودورة وإعدادات الاختبار</span>
            </h3>
            <button onclick="closeEditSettingsModal()" class="text-white/70 hover:text-white text-lg">✕</button>
        </div>

        <form action="{{ route('admin.exams.update', $exam->id) }}" method="POST" class="p-6 space-y-4 overflow-y-auto text-xs">
            @csrf

            <!-- تسميات سريعة للاسم -->
            <div>
                <label class="block font-bold text-slate-700 mb-1.5">اختيار تسمية سريعة:</label>
                <div class="flex flex-wrap gap-1.5">
                    <button type="button" onclick="setEditTitlePreset('كويز أسبوعي', 'weekly')" class="px-2.5 py-1 rounded-lg bg-purple-50 text-purple-800 text-[11px] font-bold border border-purple-200 hover:bg-purple-100">
                        كويز أسبوعي
                    </button>
                    <button type="button" onclick="setEditTitlePreset('اختبار نصفي (Midterm)', 'midterm')" class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-800 text-[11px] font-bold border border-blue-200 hover:bg-blue-100">
                        اختبار نصفي
                    </button>
                    <button type="button" onclick="setEditTitlePreset('اختبار نهائي (Final Exam)', 'final')" class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-800 text-[11px] font-bold border border-rose-200 hover:bg-rose-100">
                        اختبار نهائي
                    </button>
                    <button type="button" onclick="setEditTitlePreset('كويز قصير', 'pop_quiz')" class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 text-[11px] font-bold border border-amber-200 hover:bg-amber-100">
                        كويز قصير
                    </button>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">اسم الكويز أو الاختبار <span class="text-rose-500">*</span></label>
                <input type="text" name="title" id="editModalTitleInput" required value="{{ $exam->title }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-bold">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <!-- تحديد الدورة التدريبية -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">الدورة التدريبية</label>
                    <select name="course_id" id="editModalCourseSelect" onchange="filterEditModalLevels(this.value)" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                        <option value="">-- بدون دورة محددة --</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ $exam->course_id == $c->id ? 'selected' : '' }}>
                                {{ $c->displayName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- تحديد المستوى الأكاديمي -->
                <div>
                    <label class="block font-bold text-slate-700 mb-1">المستوى الأكاديمي</label>
                    <select name="level_id" id="editModalLevelSelect" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                        <option value="">-- عام لكافة مستويات الدورة --</option>
                        @foreach($levels as $lvl)
                            <option value="{{ $lvl->id }}" data-course-id="{{ $lvl->course_id }}" {{ $exam->level_id == $lvl->id ? 'selected' : '' }}>
                                {{ $lvl->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">نوع الاختبار / الفترة</label>
                    <select name="quiz_period" id="editModalQuizPeriod" required class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none bg-white font-bold">
                        <option value="weekly" {{ $exam->quiz_period === 'weekly' ? 'selected' : '' }}>كويز أسبوعي</option>
                        <option value="midterm" {{ $exam->quiz_period === 'midterm' ? 'selected' : '' }}>اختبار نصفي</option>
                        <option value="final" {{ $exam->quiz_period === 'final' ? 'selected' : '' }}>اختبار نهائي</option>
                        <option value="pop_quiz" {{ $exam->quiz_period === 'pop_quiz' ? 'selected' : '' }}>كويز مفاجئ</option>
                        <option value="placement" {{ $exam->quiz_period === 'placement' ? 'selected' : '' }}>اختبار قياس مستوى</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">الترم</label>
                    <input type="text" name="term" value="{{ $exam->term }}" placeholder="مثال: الترم الأول 2026" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:ring-2 focus:ring-indigo-500 outline-none font-bold">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">المدة بالدقائق</label>
                    <input type="number" name="duration_minutes" value="{{ $exam->duration_minutes }}" min="1" max="300" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono font-bold">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">درجة النجاح %</label>
                    <input type="number" name="passing_score" value="{{ $exam->passing_score }}" min="1" max="100" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono font-bold">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">عدد الأسئلة المعروضة للطالب من البنك (اختياري)</label>
                <input type="number" name="random_questions_count" value="{{ $exam->random_questions_count }}" placeholder="اتركه فارغاً لعرض كل الأسئلة" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono">
            </div>

            <div class="space-y-2 pt-2 border-t border-slate-100">
                <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-800">
                    <input type="checkbox" name="shuffle_questions" value="1" {{ $exam->shuffle_questions ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500">
                    <span>خلط ترتيب الأسئلة عشوائياً لكل طالب</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-800">
                    <input type="checkbox" name="shuffle_options" value="1" {{ $exam->shuffle_options ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500">
                    <span>خلط خيارات الإجابة A, B, C, D عشوائياً</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer font-bold text-amber-900 bg-amber-50 p-2 rounded-xl border border-amber-200">
                    <input type="checkbox" name="show_result_immediately" value="1" {{ $exam->show_result_immediately ? 'checked' : '' }} class="rounded text-amber-600 focus:ring-amber-500">
                    <span>إظهار النتيجة للطالب فوراً (إلغاء التحديد يحجبها حتى اعتماد الإدارة)</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer font-bold text-slate-800">
                    <input type="checkbox" name="is_active" value="1" {{ $exam->is_active ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500">
                    <span>الاختبار متاح ونشط</span>
                </label>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeEditSettingsModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold transition">
                    إلغاء
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold transition">
                    حفظ التعديلات 💾
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // مزامنة الإجابة الصحيحة للنموذج اليدوي
    function updateCorrectAnswerInput(idx) {
        const input = document.getElementById('opt_' + idx);
        if (input) {
            document.getElementById('manualCorrectAnswerInput').value = input.value.trim();
        }
    }

    function syncRadioValue(idx) {
        const checkedRadio = document.querySelector('input[name="correct_answer_radio"]:checked');
        if (checkedRadio && checkedRadio.value == idx) {
            updateCorrectAnswerInput(idx);
        }
    }

    function prepareManualSubmit() {
        const checkedRadio = document.querySelector('input[name="correct_answer_radio"]:checked');
        if (checkedRadio) {
            updateCorrectAnswerInput(checkedRadio.value);
        }
    }

    // مزامنة تعديل السؤال والإجابة الصحيحة
    function syncEditCorrectAnswer(qId, optIdx) {
        const input = document.getElementById('edit_opt_' + qId + '_' + optIdx);
        if (input) {
            document.getElementById('edit_correct_answer_' + qId).value = input.value.trim();
        }
    }

    function onEditOptionChange(qId, optIdx) {
        const checkedRadio = document.querySelector('input[name="edit_radio_' + qId + '"]:checked');
        if (checkedRadio && checkedRadio.value == optIdx) {
            syncEditCorrectAnswer(qId, optIdx);
        }
    }

    function prepareEditQuestionSubmit(qId) {
        const checkedRadio = document.querySelector('input[name="edit_radio_' + qId + '"]:checked');
        if (checkedRadio) {
            syncEditCorrectAnswer(qId, checkedRadio.value);
        }
    }

    // فتح وإغلاق نافذة تعديل السؤال
    function openEditQuestionModal(qId) {
        document.getElementById('editQuestionModal_' + qId).classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeEditQuestionModal(qId) {
        document.getElementById('editQuestionModal_' + qId).classList.add('hidden');
        document.body.style.overflow = '';
    }

    // نوافذ منبثقة
    function openBulkImportModal() {
        document.getElementById('bulkImportModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeBulkImportModal() {
        document.getElementById('bulkImportModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function openShareModal() {
        document.getElementById('shareGroupModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeShareModal() {
        document.getElementById('shareGroupModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function openEditSettingsModal() {
        document.getElementById('editSettingsModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeEditSettingsModal() {
        document.getElementById('editSettingsModal').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function setEditTitlePreset(title, period) {
        document.getElementById('editModalTitleInput').value = title;
        document.getElementById('editModalQuizPeriod').value = period;
    }

    function filterEditModalLevels(courseId) {
        const lvlSelect = document.getElementById('editModalLevelSelect');
        const opts = lvlSelect.querySelectorAll('option');
        opts.forEach(opt => {
            if (!opt.value) {
                opt.style.display = 'block';
                return;
            }
            const cId = opt.getAttribute('data-course-id');
            if (!courseId || cId === courseId) {
                opt.style.display = 'block';
            } else {
                opt.style.display = 'none';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const initialCourse = document.getElementById('editModalCourseSelect')?.value;
        if (initialCourse) {
            filterEditModalLevels(initialCourse);
        }
        // تجهيز الإجابة الصحيحة للنموذج الأولي
        prepareManualSubmit();
    });
</script>
@endsection
