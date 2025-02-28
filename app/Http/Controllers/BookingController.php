<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\GroupMember;
use Illuminate\Database\QueryException;

class BookingController extends Controller
{
    public function index()
    {
        return view('admin.bookings');
    }

    public function store(Request $request)
    {
        try {
            // فحص البيانات المرسلة قبل أي عمليات أخرى
            // dd($request->all());

            $status = "مبدئي";
            $payment = "لم يتم";

            $request->merge(['group_size' => $request->group_size ?? 0]);

            // تحقق من صحة البيانات المدخلة
            $validated = $request->validate([
                'horse_type' => 'required|string',
                'ride_level' => 'required|string',
                'name' => 'required|string',
                'age' => 'required|integer',
                'gender' => 'required|string',
                'Whatsapp_number' => 'required|string',
                'phone' => 'required|string',
                'date' => 'required|date',
                'time' => 'required',
                'booking_type' => 'required|string',
                'group_size' => 'nullable|integer|min:0',
                'total_price' => 'required|numeric',
            ]);

            // إدراج الحالة في البيانات بعد التحقق
            $validated['status'] = $status;
            $validated['offer'] = $payment;

            // إنشاء الحجز الأساسي
            Booking::create($validated);

            // إضافة أعضاء المجموعة إذا كان الحجز جماعيًا
            if ($request->booking_type === "جماعي" && $request->group_size > 0) {
                for ($i = 1; $i <= $request->group_size; $i++) {
                    Booking::create([
                        'horse_type' => $request->input("member_horse_type_$i"),
                        'ride_level' => $request->input("member_level_$i"),
                        'name' => $request->input("member_name_$i"),
                        'age' => $request->input("member_age_$i"),
                        'gender' => $request->input("member_gender_$i"),
                        'Whatsapp_number' => $request->Whatsapp_number,
                        'phone' => $request->phone,
                        'date' => $request->date,
                        'time' => $request->time,
                        'offer' => $payment,
                        'booking_type' => $request->booking_type,
                        'group_size' => $request->group_size,
                        'total_price' => $request->total_price,
                        'status' => $status,
                    ]);
                }
            }

            // رسالة نجاح
            return redirect()->back()->with('success', 'تم الحجز بنجاح!');

        } catch (QueryException $e) {
            return redirect()->back()->with('error', 'خطأ في الإدخال: ' . $e->getMessage());
        }
    }

    public function getBookings()
    {
        $bookings = Booking::all();
        return response()->json($bookings);
    }

    public function getBookingsRide()
    {
        $bookings = Booking::where('status', 'مبدئي')->get();
        return response()->json($bookings);
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'status' => 'required|string|in:ملغي,تم الركوب,تم الدفع'
        ]);

        $booking = Booking::find($request->booking_id);

        if($request->status === "ملغي" || $request->status === "تم الركوب"){
            $booking->status = $request->status;
        } else {
            $booking->offer = $request->status;
        }
        
        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الحالة بنجاح',
            'new_status' => $booking->status
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('query'); // النص المُدخل في البحث
        $status = $request->input('status'); // الفلترة بالحالة

        // جلب الحجوزات مع البحث والفلترة
        $bookings = Booking::query()
        ->when($query, function ($q) use ($query) {
            $q->where(function ($subQuery) use ($query) {
                $subQuery->where('horse_type', 'like', "%{$query}%")
                        ->orWhere('ride_level', 'like', "%{$query}%")
                        ->orWhere('name', 'like', "%{$query}%")
                        ->orWhere('age', 'like', "%{$query}%")
                        ->orWhere('gender', 'like', "%{$query}%")
                        ->orWhere('status', 'like', "%{$query}%")
                        ->orWhere('Whatsapp_number', 'like', "%{$query}%")
                        ->orWhere('phone', 'like', "%{$query}%")
                        ->orWhere('date', 'like', "%{$query}%")
                        ->orWhere('time', 'like', "%{$query}%")
                        ->orWhere('offer', 'like', "%{$query}%")
                        ->orWhere('booking_type', 'like', "%{$query}%")
                        ->orWhere('group_size', 'like', "%{$query}%")
                        ->orWhere('total_price', 'like', "%{$query}%");
            });
        })
        ->when(!empty($status), function ($q) use ($status) {
            $q->where('status', $status);
        })
        ->get();

        return response()->json($bookings);
    }

    public function ride()
    {
        return view('admin.ride');
    }
}
