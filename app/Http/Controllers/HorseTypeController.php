<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HorseType;
use Illuminate\Database\QueryException;

class HorseTypeController extends Controller
{
    // عرض جميع الأنواع من الجدول الموجود
    public function index()
    {
        return view('admin.horses');
    }

    // عرض فورم إضافة نوع جديد
    // public function create()
    // {
    //     return view('horses.create');
    // }

    // تخزين البيانات الجديدة في الجدول الحالي
    public function store(Request $request)
    {
        try {
            // التحقق من البيانات
            $request->validate([
                'class_types' => 'required|string|max:255',
                'price' => 'required|numeric',
                'ride_price' => 'required|numeric',
            ]);
    
            // إدخال البيانات في الجدول
            HorseType::create([
                'class_types' => $request->class_types,
                'price' => $request->price,
                'ride_price' => $request->ride_price,
            ]);
    
            // رسالة نجاح
            return redirect()->back()->with('success', 'تم إضافة الحصان بنجاح!');
    
        } catch (QueryException $e) {
            // في حالة وجود خطأ في قاعدة البيانات، سيتم تخزينه في الجلسة
            return redirect()->back()->with('error', 'خطأ في الإدخال: ' . $e->getMessage());
        }
    }

    public function getHorses()
    {
        $horses = HorseType::all();
        return response()->json($horses);
    }

    public function update(Request $request, $id)
    {
        // التحقق من صحة البيانات
        $request->validate([
            'class_types' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'ride_price' => 'sometimes|numeric|min:0',
        ]);

        // العثور على الحصان المطلوب
        $horse = HorseType::findOrFail($id);

        // تحديث الحقل المحدد فقط
        $horse->update($request->only(['class_types', 'price', 'ride_price']));

        // إعادة استجابة JSON للنجاح
        return response()->json(['success' => true, 'message' => 'تم تحديث الحصان بنجاح']);
    }

}
