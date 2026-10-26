<?php

namespace App\Http\Controllers;

use App\Models\CostCenter;
use App\Models\SystemAuditLog;
use Illuminate\Http\Request;

class CostCenterController extends Controller
{
    /**
     * عرض شجرة مراكز التكلفة ومؤشرات الربحية
     */
    public function index()
    {
        $rootCenters = CostCenter::with(['children.children'])
            ->whereNull('parent_id')
            ->orderBy('code')
            ->get();

        $allCenters = CostCenter::orderBy('code')->get();
        $totalCostCenters = CostCenter::count();

        return view('accounting.cost_centers.index', compact(
            'rootCenters',
            'allCenters',
            'totalCostCenters'
        ));
    }

    /**
     * حفظ مركز تكلفة جديد
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'code'        => 'nullable|string|unique:cost_centers,code',
            'parent_id'   => 'nullable|exists:cost_centers,id',
            'description' => 'nullable|string',
        ]);

        $parentId = $request->parent_id ?: null;
        $parent = $parentId ? CostCenter::find($parentId) : null;

        $code = $request->code;
        if (empty($code)) {
            $prefix = $parent ? $parent->code : '1';
            $count = CostCenter::where('parent_id', $parentId)->count() + 1;
            $code = $prefix . str_pad($count, 2, '0', STR_PAD_LEFT);
            while (CostCenter::where('code', $code)->exists()) {
                $count++;
                $code = $prefix . str_pad($count, 2, '0', STR_PAD_LEFT);
            }
        }

        if ($parent && !$parent->is_parent) {
            $parent->update(['is_parent' => true]);
        }

        $cc = CostCenter::create([
            'parent_id'   => $parentId,
            'code'        => $code,
            'name'        => $request->name,
            'level'       => $parent ? ($parent->level + 1) : 1,
            'is_parent'   => false,
            'description' => $request->description,
            'is_active'   => true,
        ]);

        SystemAuditLog::record(
            'cost_center.create',
            "إضافة مركز تكلفة جديد: [{$code}] {$cc->name}",
            $cc,
            ['code' => $code, 'parent_id' => $parentId]
        );

        return back()->with('success', "تم إضافة مركز التكلفة [{$code}] {$cc->name} بنجاح.");
    }

    /**
     * تحديث مركز تكلفة
     */
    public function update(Request $request, $id)
    {
        $cc = CostCenter::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active'   => 'boolean',
        ]);

        $cc->update([
            'name'        => $request->name,
            'description' => $request->description,
            'is_active'   => $request->boolean('is_active'),
        ]);

        return back()->with('success', "تم تحديث بيانات مركز التكلفة [{$cc->code}] بنجاح.");
    }

    /**
     * حذف أو تعطيل مركز تكلفة
     */
    public function destroy($id)
    {
        $cc = CostCenter::findOrFail($id);

        if ($cc->children()->exists()) {
            return back()->with('error', "لا يمكن حذف هذا المركز لأنه يضم مراكز تكلفة فرعية تحته.");
        }

        if ($cc->journalLines()->exists() || $cc->paymentRequests()->exists()) {
            $cc->update(['is_active' => false]);
            return back()->with('success', "تم تعطيل مركز التكلفة [{$cc->code}] بنجاح لوجود قيود وحركات مرتبطة به.");
        }

        $cc->delete();
        return back()->with('success', "تم حذف مركز التكلفة بنجاح.");
    }
}
