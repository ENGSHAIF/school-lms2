<?php

namespace App\Http\Controllers;

use App\Models\FinancialAccount;
use App\Models\Package;
use App\Models\StudentWallet;
use App\Models\Voucher;
use App\Models\WhatsAppLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::where('is_active', true)->get();
        $user = Auth::user();
        $wallet = StudentWallet::where('user_id', $user->id)->first();

        return view('packages.index', compact('packages', 'wallet'));
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:packages,id',
            'payment_method' => 'required|string',
        ]);

        $user = Auth::user();
        $package = Package::findOrFail($request->package_id);

        // 1. شحن رصيد الحصص في محفظة الطالب
        $wallet = StudentWallet::firstOrCreate(
            ['user_id' => $user->id],
            ['total_lessons' => 0, 'used_lessons' => 0, 'remaining_lessons' => 0]
        );

        $wallet->increment('total_lessons', $package->lessons_count);
        $wallet->increment('remaining_lessons', $package->lessons_count);

        // 2. إصدار سند قبض رسمي (المعايير المحاسبية المعتمدة)
        $studentAccount = FinancialAccount::firstOrCreate(
            ['user_id' => $user->id],
            ['account_code' => '21' . str_pad($user->id, 4, '0', STR_PAD_LEFT), 'account_name' => 'حساب الطالب: ' . $user->name, 'account_type' => 'student', 'currency' => $package->currency]
        );
        $cashBox = FinancialAccount::where('account_type', 'cash_box')->first();

        $count = Voucher::count() + 1;
        $voucherNo = 'REC-' . date('Y') . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);

        $voucher = Voucher::create([
            'voucher_no' => $voucherNo,
            'voucher_type' => 'receipt',
            'account_id' => $studentAccount->id,
            'cash_or_bank_account_id' => $cashBox?->id ?? 1,
            'amount' => $package->price,
            'currency' => $package->currency,
            'exchange_rate' => 1.0000,
            'amount_local' => $package->price,
            'amount_in_words' => "فقط {$package->price} {$package->currency} لا غير",
            'payment_method' => $request->payment_method,
            'statement' => "شراء {$package->title} ({$package->lessons_count} حصة حية 1-on-1)",
            'created_by' => 1,
        ]);

        if ($cashBox) {
            $cashBox->increment('current_balance', $package->price);
        }

        // 3. إشعار الواتساب الفوري
        WhatsAppLog::create([
            'user_id' => $user->id,
            'phone_number' => $user->phone_whatsapp ?? 'unknown',
            'message_type' => 'receipt_voucher',
            'message_body' => "تم شحن باقتك بنجاح! 🚀\nتمت إضافة {$package->lessons_count} حصة حية 1-on-1 إلى محفظتك.\nسند القبض رقم ({$voucherNo}) بمبلغ \${$package->price} معتمد في حسابك.",
            'status' => 'sent',
        ]);

        return redirect()->route('student.booking')->with('success', "تهانينا! تم تفعيل {$package->title} وإضافة {$package->lessons_count} حصة إلى محفظتك بنجاح، وإصدار سند القبض الرسمي.");
    }
}
