<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\StudentInstallment;
use App\Models\SystemAuditLog;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CurrencyController extends Controller
{
    /**
     * شاشة تهيئة بيانات العملات (مطابقة لمعايير المتكامل بلاس ERP)
     */
    public function index()
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->hasPermission('finance.currencies') && ! $user->hasPermission('manage_accounting')) {
            abort(403, 'عذراً، ليس لديك صلاحية الوصول إلى شاشة تهيئة العملات.');
        }

        $currencies = Currency::orderByDesc('is_local')->orderBy('id')->get();
        $localCurrency = Currency::localCurrency();
        $foreignCurrenciesCount = Currency::where('is_local', false)->count();
        $activeCurrenciesCount = Currency::where('is_active', true)->where('is_stopped', false)->count();

        return view('accounting.currencies.index', compact(
            'currencies',
            'localCurrency',
            'foreignCurrenciesCount',
            'activeCurrenciesCount'
        ));
    }

    /**
     * حفظ عملة جديدة في النظام
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->hasPermission('finance.currencies')) {
            abort(403, 'غير مصرح لك بإضافة عملات جديدة.');
        }

        $validated = $request->validate([
            'currency_code' => 'required|string|max:10|unique:currencies,currency_code',
            'name' => 'required|string|max:100',
            'name_en' => 'nullable|string|max:100',
            'symbol' => 'required|string|max:10',
            'fraction_name' => 'required|string|max:50',
            'is_local' => 'nullable|boolean',
            'exchange_rate' => 'required|numeric|min:0.0001',
            'max_exchange_rate' => 'nullable|numeric',
            'min_exchange_rate' => 'nullable|numeric',
            'valuation_rate' => 'nullable|numeric',
            'buy_rate' => 'nullable|numeric',
            'max_buy_rate' => 'nullable|numeric',
            'sell_rate' => 'nullable|numeric',
            'min_sell_rate' => 'nullable|numeric',
            'is_default_transfer' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'is_stopped' => 'nullable|boolean',
            'denominations_str' => 'nullable|string',
            'notes' => 'nullable|string|max:500',
        ]);

        $code = strtoupper(trim($validated['currency_code']));
        $isLocal = $request->boolean('is_local', false);

        // إذا تم تحديد العملة على أنها محلية، يتم إلغاء المحلية عن باقي العملات (عملة محلية واحدة للمنشأة)
        if ($isLocal) {
            Currency::where('is_local', true)->update(['is_local' => false]);
            $exchangeRate = 1.0000;
        } else {
            $exchangeRate = (float) $validated['exchange_rate'];
        }

        // تحويل فئات العملة إلى مصفوفة JSON
        $denominations = null;
        if (! empty($validated['denominations_str'])) {
            $denominations = array_values(array_filter(array_map('trim', explode(',', $validated['denominations_str']))));
        }

        $currency = Currency::create([
            'currency_code' => $code,
            'name' => trim($validated['name']),
            'name_en' => $validated['name_en'] ?? null,
            'symbol' => trim($validated['symbol']),
            'fraction_name' => trim($validated['fraction_name']),
            'is_local' => $isLocal,
            'exchange_rate' => $exchangeRate,
            'max_exchange_rate' => $validated['max_exchange_rate'] ?? null,
            'min_exchange_rate' => $validated['min_exchange_rate'] ?? null,
            'valuation_rate' => $validated['valuation_rate'] ?? $exchangeRate,
            'buy_rate' => $validated['buy_rate'] ?? null,
            'max_buy_rate' => $validated['max_buy_rate'] ?? null,
            'sell_rate' => $validated['sell_rate'] ?? null,
            'min_sell_rate' => $validated['min_sell_rate'] ?? null,
            'is_default_transfer' => $request->boolean('is_default_transfer', false),
            'is_active' => $request->boolean('is_active', true),
            'is_stopped' => $request->boolean('is_stopped', false),
            'denominations' => $denominations,
            'notes' => $validated['notes'] ?? null,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        SystemAuditLog::record(
            'currency.created',
            "إضافة عملة جديدة: {$currency->name} ({$currency->currency_code}) بسعر صرف {$currency->exchange_rate}",
            $currency
        );

        return back()->with('success', "تم إضافة عملة [{$currency->name} - {$currency->currency_code}] بنجاح 🪙");
    }

    /**
     * تحديث بيانات عملة موجودة وأسعار الصرف والحدود
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! $user->hasPermission('finance.currencies')) {
            abort(403, 'غير مصرح لك بتعديل بيانات العملات.');
        }

        $currency = Currency::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'name_en' => 'nullable|string|max:100',
            'symbol' => 'required|string|max:10',
            'fraction_name' => 'required|string|max:50',
            'is_local' => 'nullable|boolean',
            'exchange_rate' => 'required|numeric|min:0.0001',
            'max_exchange_rate' => 'nullable|numeric',
            'min_exchange_rate' => 'nullable|numeric',
            'valuation_rate' => 'nullable|numeric',
            'buy_rate' => 'nullable|numeric',
            'max_buy_rate' => 'nullable|numeric',
            'sell_rate' => 'nullable|numeric',
            'min_sell_rate' => 'nullable|numeric',
            'is_default_transfer' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'is_stopped' => 'nullable|boolean',
            'denominations_str' => 'nullable|string',
            'notes' => 'nullable|string|max:500',
        ]);

        $isLocal = $request->boolean('is_local', false);

        if ($isLocal && ! $currency->is_local) {
            Currency::where('id', '!=', $currency->id)->update(['is_local' => false]);
            $exchangeRate = 1.0000;
        } else {
            $exchangeRate = $currency->is_local ? 1.0000 : (float) $validated['exchange_rate'];
        }

        // فئات العملة
        $denominations = $currency->denominations;
        if ($request->has('denominations_str')) {
            $denominations = array_values(array_filter(array_map('trim', explode(',', $request->input('denominations_str', '')))));
        }

        $currency->update([
            'name' => trim($validated['name']),
            'name_en' => $validated['name_en'] ?? null,
            'symbol' => trim($validated['symbol']),
            'fraction_name' => trim($validated['fraction_name']),
            'is_local' => $isLocal,
            'exchange_rate' => $exchangeRate,
            'max_exchange_rate' => $validated['max_exchange_rate'] ?? null,
            'min_exchange_rate' => $validated['min_exchange_rate'] ?? null,
            'valuation_rate' => $validated['valuation_rate'] ?? $exchangeRate,
            'buy_rate' => $validated['buy_rate'] ?? null,
            'max_buy_rate' => $validated['max_buy_rate'] ?? null,
            'sell_rate' => $validated['sell_rate'] ?? null,
            'min_sell_rate' => $validated['min_sell_rate'] ?? null,
            'is_default_transfer' => $request->boolean('is_default_transfer', false),
            'is_active' => $request->boolean('is_active', true),
            'is_stopped' => $request->boolean('is_stopped', false),
            'denominations' => $denominations,
            'notes' => $validated['notes'] ?? null,
            'updated_by' => $user->id,
        ]);

        SystemAuditLog::record(
            'currency.updated',
            "تحديث بيانات عملة: {$currency->name} ({$currency->currency_code}) وسعر الصرف إلى {$currency->exchange_rate}",
            $currency
        );

        return back()->with('success', "تم تحديث بيانات وأسعار صرف عملة [{$currency->name}] بنجاح ✅");
    }

    /**
     * تحديث سريع لسعر صرف العملة (سعر التحويل والبيع والشراء)
     */
    public function quickRateUpdate(Request $request, $id)
    {
        $currency = Currency::findOrFail($id);

        if ($currency->is_local) {
            return back()->with('error', 'العملة المحلية للمنشأة سعرها ثابت دائماً (1.0000).');
        }

        $request->validate([
            'exchange_rate' => 'required|numeric|min:0.0001',
            'buy_rate' => 'nullable|numeric',
            'sell_rate' => 'nullable|numeric',
        ]);

        $newRate = (float) $request->exchange_rate;

        // التحقق من حدود أعلى وأدنى سعر تحويل إن وُجدت وفق معايير المتكامل بلاس
        if ($currency->max_exchange_rate && $newRate > (float) $currency->max_exchange_rate) {
            return back()->with('error', "سعر التحويل المدخل ({$newRate}) يتجاوز الحد الأعلى المسموح به للعملة ({$currency->max_exchange_rate})!");
        }

        if ($currency->min_exchange_rate && $newRate < (float) $currency->min_exchange_rate) {
            return back()->with('error', "سعر التحويل المدخل ({$newRate}) أقل من الحد الأدنى المسموح به للعملة ({$currency->min_exchange_rate})!");
        }

        $currency->update([
            'exchange_rate' => $newRate,
            'valuation_rate' => $newRate,
            'buy_rate' => $request->filled('buy_rate') ? (float) $request->buy_rate : $currency->buy_rate,
            'sell_rate' => $request->filled('sell_rate') ? (float) $request->sell_rate : $currency->sell_rate,
            'updated_by' => Auth::id(),
        ]);

        SystemAuditLog::record(
            'currency.rate_changed',
            "تغيير سريع لسعر صرف عملة {$currency->name} إلى: {$newRate}",
            $currency
        );

        return back()->with('success', "تم تعديل سعر صرف عملة [{$currency->name}] فورياً إلى: {$newRate} بنجاح ⚡");
    }

    /**
     * إيقاف أو تنشيط التعامل بالعملة
     */
    public function toggleStatus($id)
    {
        $currency = Currency::findOrFail($id);
        if ($currency->is_local) {
            return back()->with('error', 'لا يمكن إيقاف العملة المحلية الأساسية للمنشأة.');
        }

        $currency->is_stopped = ! $currency->is_stopped;
        $currency->save();

        $statusText = $currency->is_stopped ? 'إيقاف التعامل بالعملة 🚫' : 'تنشيط التعامل بالعملة 🟢';

        return back()->with('success', "تم {$statusText} بنجاح.");
    }

    /**
     * حذف عملة إن لم تكن مستخدمة في أي عمليات مالية
     */
    public function destroy($id)
    {
        $currency = Currency::findOrFail($id);

        if ($currency->is_local) {
            return back()->with('error', 'لا يمكن حذف العملة المحلية الأساسية للمنشأة.');
        }

        $code = $currency->currency_code;

        // التحقق من وجود حركات مرتبطة بهذه العملة
        $hasVouchers = Voucher::where('currency', $code)->exists();
        $hasInstallments = StudentInstallment::where('currency', $code)->exists();

        if ($hasVouchers || $hasInstallments) {
            return back()->with('error', "لا يمكن حذف عملة [{$currency->name}] لوجود سندات قبض أو رسوم مسجلة بها في النظام، يمكنك تحديد خيار (إيقاف العملة) بدلاً من الحذف منعاً لخلل الحسابات.");
        }

        $currency->delete();

        return back()->with('success', "تم حذف العملة [{$currency->name}] بنجاح.");
    }
}
