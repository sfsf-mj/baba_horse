<!-- صفحة المستخدم - booking.html -->
<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>حجز طلعات الخيل</title>
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;700&display=swap");
        @import url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css");

        /* استايل عام للصفحة */
        body {
            font-family: "Poppins", sans-serif;
            background: #4b320d;
            color: #000000;
            text-align: center;
            margin: 0;
            padding: 0;
            backdrop-filter: blur(5px);
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            background: #bfae9c;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 0px 30px rgba(44, 62, 80, 0.5);
        }

        h1,
        h2 {
            font-family: "Playfair Display", serif;
            color: #000000;
        }

        h1::before {
            content: "\f6c0";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            margin-right: 10px;
            color: #d4af37;
        }

        p {
            font-size: 20px;
        }

        /* استايل النموذج */
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            background: rgba(191, 174, 156, 0.85);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(44, 62, 80, 0.3);
        }

        label {
            font-weight: bold;
            font-size: 18px;
        }

        input,
        select {
            width: 90%;
            padding: 12px;
            border-radius: 8px;
            border: 2px solid #2c3e50;
            font-size: 18px;
            text-align: center;
            background: rgba(255, 255, 255, 0.2);
            color: #000000;
            transition: 0.3s;
        }

        input:focus,
        select:focus {
            border-color: #d4af37;
            background: rgba(255, 255, 255, 0.3);
        }

        button {
            font-size: 20px;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 0 15px rgba(44, 62, 80, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        /* زر الحجز */
        .button-primary {
            background: linear-gradient(to right, #6b8e23, #556b2f);
            color: #f8f1e9;
        }

        .button-primary:hover {
            background: linear-gradient(to right, #556b2f, #6b8e23);
            transform: scale(1.05);
        }

        /* زر الإلغاء */
        .button-secondary {
            background: linear-gradient(to right, #4e342e, #3b2f2f);
            color: #f8f1e9;
        }

        .button-secondary:hover {
            background: linear-gradient(to right, #3b2f2f, #4e342e);
            transform: scale(1.05);
        }

        /* تأثيرات إضافية */
        h1,
        h2 {
            animation: fadeIn 3s ease-in-out;
        }

        button {
            animation: pulse 1.5s infinite;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-70px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.02);
            }

            100% {
                transform: scale(1);
            }
        }

        /* استايل الحقول الخاصة بالجماعة */
        #group_details {
            display: none;
            background: rgba(191, 174, 156, 0.85);
            padding: 20px;
            border-radius: 10px;
        }

        /* تحسين استايل الموبايل */
        @media (max-width: 600px) {
            .container {
                width: 95%;
                margin: 20px auto;
            }

            input,
            select {
                width: 100%;
            }
        }
    </style>
    <script defer>
        function generateGroupFields() {
            let size = document.getElementById("group_size").value;
            let container = document.getElementById("group-members");
            container.innerHTML = "";
            for (let i = 1; i <= size; i++) {
                container.innerHTML += `
                    <h4>تفاصيل الشخص ${i}</h4>
                    <label>أنواع الخيل:</label>
                    <select name="member_horse_type_${i}" id="member_horse_type_${i}" onchange="updatePrice()" required>
                        ${horse_option}
                    </select>
                    <label>مستوى الركوب:</label>
                    <select name="member_level_${i}">
                        <option value="مبتدئ">مبتدئ</option>
                        <option value="متوسط">متوسط</option>
                        <option value="محترف">محترف</option>
                    </select>
                    <label>الاسم:</label><input type="text" name="member_name_${i}" required>
                    <label>العمر:</label><input type="number" name="member_age_${i}" required>
                    <label>الجنس:</label>
                    <select name="member_gender_${i}">
                        <option value="ذكر">ذكر</option>
                        <option value="أنثى">أنثى</option>
                    </select>
                `;
            }
            updatePrice();
        }

        let horsePrices;
        document.addEventListener("DOMContentLoaded", function () {
            horsePrices = fetchHorses();
        });

        let horse_option;
        function fetchHorses() {
            let horse_select = document.getElementById("horse_type");

            let hp = {}; // كائن لتخزين الأسعار
            horse_option = `<option value="">اختر نوع الحصان</option>`;

            fetch("/admin/horses/data")
                .then(response => response.json())
                .then(data => {
                    hp["غير محدد"] = 0;

                    data.forEach(horse => {
                        hp[horse.class_types] = horse.ride_price; // تحويل السعر إلى رقم
                        horse_option += `<option value="${horse.class_types}">${horse.class_types} (${horse.ride_price} جنية)</option>`;
                    });

                    horse_select.innerHTML = horse_option; // تحديث القائمة بعد استلام البيانات
                })
                .catch(error => console.error("Error fetching horses:", error));

            return hp; // يتم إرجاع الكائن ولكن `fetch` لن يكون قد انتهى بعد
        }

        function updatePrice() {
            let totalPrice = 0;

            let horseType = document.getElementById("horse_type").value || "غير محدد";
            let booking_type = document.getElementById("booking_type").value;
            var groupSize = document.getElementById("group_size").value || 0;

            totalPrice = horsePrices[horseType];

            // console.log("group Size in updatePrice: " + groupSize);

            if (groupSize > 0) {
                for (let i = 1; i <= groupSize; i++) {
                    let member_horse_type = document.getElementById(`member_horse_type_${i}`).value ||
                        "غير محدد";
                    totalPrice = totalPrice + horsePrices[member_horse_type];
                }
            } else {
                totalPrice = horsePrices[horseType];
            }

            document.getElementById("total_price").value = totalPrice;
        }

        function toggleGroupFields() {
            let groupType = document.getElementById("booking_type").value;
            document.getElementById("group_details").style.display =
                groupType === "جماعي" ? "block" : "none";
            updatePrice();
        }
    </script>
</head>

<body>
    <div class="container">
        @if(session('success'))
        <div style="
          color: #ffffff;
          background: #06b713;
          width: max-content;
          padding: 10px;
        ">
            {{ session('success') }}
        </div>
        @endif @if(session('error'))
        <div style="
          color: red;
          border: 1px solid red;
          padding: 10px;
          margin-bottom: 10px;
        ">
            {{ session('error') }}
        </div>
        @endif

        <h1>مرحبًا بكم في موقع حجز طلعات الخيل</h1>
        <p>اختر نوع الخيل واحجز موعدك بسهولة.</p>

        <!-- تفاصيل الطلعة والإرشادات -->
        <div class="instructions">
            <h2>تفاصيل الطلعة والإرشادات</h2>
            <p>
                يرجى الحضور قبل الموعد بـ 15 دقيقة. تأكد من ارتداء الملابس المناسبة.
                يمنع التدخين في منطقة الخيول.
            </p>
        </div>

        <form id="booking-form" action="{{ route('bookings.store') }}" method="POST">
            @csrf
            <h2>أنواع الخيول المتاحة</h2>
            <select id="horse_type" name="horse_type" onchange="updatePrice()" required>
                <!-- سيتم الادراج عن طريق js -->
            </select>

            <label>مستوى الركوب:</label>
            <select name="ride_level">
                <option value="مبتدئ">مبتدئ</option>
                <option value="متوسط">متوسط</option>
                <option value="محترف">محترف</option>
            </select>

            <h2>احجز أو عدل موعدك</h2>

            <label for="name">الاسم الكامل:</label>
            <input type="text" id="name" name="name" pattern="^[^0-9]+$" title="يجب ألا يحتوي الاسم على أرقام"
                required />

            <label for="age">العمر:</label>
            <input type="number" id="age" name="age" pattern="^[^0-9]+$" title="يجب ألا يحتوي الاسم على أرقام"
                required />

            <label for="gender">الجنس:</label>
            <select id="gender" name="gender">
                <option value="ذكر">ذكر</option>
                <option value="انثى">انثى</option>
            </select>

            <label for="Whatsapp_number">رقم الواتساب:</label>
            <input type="tel" id="Whatsapp_number" name="Whatsapp_number" pattern="^\+?[0-9]{10,15}$"
                title="يرجى إدخال رقم صحيح" required />

            <label for="phone">رقم المكالمات:</label>
            <input type="tel" id="phone" name="phone" pattern="^\+?[0-9]{10,15}$" title="يرجى إدخال رقم صحيح"
                required />
            <label for="date">اختر التاريخ:</label>

            <input type="date" id="date" name="date" required />

            <label for="time">اختر الساعة:</label>
            <input type="time" id="time" name="time" required />

            <label for="booking_type">نوع الحجز:</label>
            <select id="booking_type" name="booking_type" onchange="toggleGroupFields()">
                <option value=""></option>
                <option value="فردي">فردي</option>
                <option value="جماعي">جماعي</option>
            </select>

            <div id="group_details" style="display: none">
                <label for="group_size">عدد الأفراد:</label>
                <input type="number" id="group_size" name="group_size" min="0" max="20"
                    onchange="generateGroupFields()" />
                <div id="group-members"></div>
            </div>

            <label for="total_price">التكلفة الإجمالية:</label>
            <input type="number" id="total_price" name="total_price" />
            <button type="submit">احجز الآن</button>
        </form>

        <h2>تعديل الحجز</h2>
        <form id="edit-booking-form" action="edit_booking.php" method="POST">
            <label for="booking-id">أدخل معرف الحجز:</label>
            <input type="number" id="booking-id" name="booking-id" required />
            <button type="submit">تعديل الحجز</button>
        </form>
    </div>
</body>

</html>