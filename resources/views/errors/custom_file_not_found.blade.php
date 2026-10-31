@extends('layouts.app')

@section('title', $title ?? 'الملف غير موجود')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl border border-slate-200 p-6 sm:p-10 max-w-lg w-full text-center shadow-xl space-y-6">
        <div class="w-20 h-20 bg-rose-50 text-rose-600 rounded-3xl flex items-center justify-center text-4xl mx-auto shadow-inner border border-rose-100">
            📁
        </div>

        <div class="space-y-2">
            <h1 class="text-xl sm:text-2xl font-black text-slate-900">
                {{ $title ?? 'الملف غير موجود على الخادم' }}
            </h1>
            @if(isset($resourceTitle))
                <p class="text-sm font-bold text-indigo-700 bg-indigo-50 px-3 py-1 rounded-xl inline-block">
                    {{ $resourceTitle }}
                </p>
            @endif
        </div>

        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
            {{ $message ?? 'عذراً، الملف المطلوب غير متوفر حالياً على مسار الخادم. قد يكون لم يتم رفع الفيديو بالكامل بسبب انقطاع الاتصال أو تجاوز الحد المسموح، أو تم حذفه.' }}
        </p>

        @if(isset($filePath) && auth()->check() && auth()->user()->isAdmin())
            <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200 text-right">
                <span class="text-[10px] text-slate-400 font-bold block mb-1">المسار المسجل في النظام (خاص بالإدارة):</span>
                <code class="text-[11px] text-slate-700 font-mono break-all dir-ltr block text-left bg-white p-2 rounded-lg border border-slate-200">{{ $filePath }}</code>
            </div>
        @endif

        <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ $backUrl ?? url()->previous() }}" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs transition shadow-sm">
                الرجوع للصفحة السابقة ↩️
            </a>
            @if(auth()->check() && auth()->user()->isAdmin())
                <a href="{{ route('admin.courses.index') }}" class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                    لوحة المناهج والدورات 📚
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
