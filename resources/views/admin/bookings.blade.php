<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>لوحة تحكم الأدمن</title>
    <script src="https://kit.fontawesome.com/6a1016d9de.js" crossorigin="anonymous"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            direction: rtl;
            text-align: center;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .search_and_filter{
            display: flex;
            justify-content: space-between;
        }
        .search_div{
            position: relative;
            display: flex;
            justify-content: center;
            width: 60%;
        }
        .search_div input[type="text"], .search_and_filter select{
            width: 100%;
            border: none;
            background-color: #ffffff;
            padding: 10px;
            padding-left: 40px;
            border-radius: 15px;
            outline: 3px solid rgb(204 204 204);
        }
        .search_div i{
            color: #b1b1b1;
            width: 40px;
            height: 41px;
            left: 0%;
            display: flex;
            align-items: center;
            position: absolute;
            margin-right: -10px;
            justify-content: center;
        }
        .search_and_filter select{
            width: 30%;
            padding-left: 0px;
        }
        .search_div input:focus, .search_and_filter select:focus{
            outline: 3px solid #007bff;
        }
        .search_div input:focus ~ i{
            color: #007bff;
        }
        .content-2 .search button img {
            width: 30px;
            height: 30px;
        }

        h1,
        h2 {
            color: #333;
        }

        .stats {
            display: flex;
            justify-content: space-around;
            background: #007bff;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .stats p {
            font-size: 18px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 10px;
            text-align: center;
        }

        th {
            background: #007bff;
            color: white;
        }

        tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        tbody tr:hover {
            background: #f1f1f1;
        }

        button {
            background: red;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
            border-radius: 5px;
        }

        button:hover {
            background:rgba(174, 174, 174, 0.51);
        }

        form {
            margin-top: 20px;
        }

        input[type="number"] {
            padding: 8px;
            width: 100px;
            margin-right: 10px;
        }

        button[type="submit"] {
            background: green;
            padding: 10px 15px;
            border-radius: 5px;
        }

        button[type="submit"]:hover {
            background: darkgreen;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.getElementById('searchInput').addEventListener('input', fetchBookingsSearch);
            document.getElementById('statusFilter').addEventListener('change', fetchBookingsSearch);
            fetchBookings();
        });

        function fetchBookings() {
            fetch("/admin/bookings/data")
            .then(response => response.json())
            .then(data => {

                viewRecords(data);

            })
            .catch(error => console.error("Error fetching bookings:", error));
        }

        let searchTimeout;
        function fetchBookingsSearch() {
            clearTimeout(searchTimeout); // مسح المؤقت السابق

            searchTimeout = setTimeout(() => {
                let query = document.getElementById('searchInput').value;
                let status = document.getElementById('statusFilter').value;

                if(query === "" && status === ""){
                    fetchBookings();
                } else {
                    fetch(`/api/bookings/search?query=${query}&status=${status}`)
                    .then(response => response.json())
                    .then(data => {
                        viewRecords(data);
                    })
                    .catch(error => console.error('Error fetching data:', error));
                }
            }, 2000); // تأخير البحث بمقدار نصف ثانية
        }

        function viewRecords(data){
            let tableBody = document.getElementById("bookings-list");
            tableBody.innerHTML = ""; // تفريغ الجدول قبل إعادة تحميل البيانات

            let bookings_total = document.getElementById("bookings_total");
            bookings_total.innerHTML = data.length;

            let d_total = document.getElementById("d_total");
            d_total.innerHTML = calculateD(data);
            
            let r_total = document.getElementById("r_total");
            r_total.innerHTML = calculateR(data);

            if (data.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="15">لا توجد نتائج</td></tr>';
                return;
            }

            let row = "";
            data.forEach(booking => {
                row += `<tr id="booking-${booking.id}">
                    <td>
                        <button onclick="updateStatus(${booking.id}, 'ملغي')" style="background: #ff5656;">إلغاء</button>
                        <button onclick="updateStatus(${booking.id}, 'تم الركوب')" style="background: #6acc71;">تم الركوب</button>
                    </td>
                    <td>${booking.id}</td>
                    <td>${booking.horse_type}</td>
                    <td>${booking.ride_level}</td>
                    <td>${booking.name}</td>
                    <td>${booking.age}</td>
                    <td>${booking.gender}</td>
                    <td id="status-${booking.id}">${booking.status}</td>
                    <td><a href="tel:${booking.Whatsapp_number}">${booking.Whatsapp_number}</a></td>
                    <td><a href="tel:${booking.phone}">${booking.phone}</a></td>
                    <td>${booking.date}</td>
                    <td>${booking.time}</td>
                    <td>${booking.offer}</td>
                    <td>${booking.booking_type}</td>
                    <td>${booking.group_size}</td>
                    <td>${booking.total_price}</td>
                    <td>${new Date(booking.updated_at).toLocaleString()}</td>
                    <td>${new Date(booking.created_at).toLocaleString()}</td>
                </tr>`;
            });

            tableBody.innerHTML = row;
        }

        function updateStatus(bookingId, newStatus) {
            if (!confirm(`هل أنت متأكد من تغيير الحالة إلى "${newStatus}"؟`)) {
                return;
            }

            fetch("/api/bookings/update-status", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    booking_id: bookingId,
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`status-${bookingId}`).innerText = data.new_status;
                    alert("تم تحديث الحالة بنجاح!");
                } else {
                    alert("حدث خطأ أثناء تحديث الحالة!");
                }
            })
            .catch(error => console.error("Error updating status:", error));
        }

        function getHorses(){
            fetch("/admin/horses/data")
            .then(response => response.json())
            .then(horses => {
                horses_data = horses;
                console.log("in fetch method: ", horses_data);
                return horses;
            })
            .catch(error => console.error("Error fetching bookings:", error));
        }

        function calculateD(data){
            var horses_data = getHorses();

            

            console.log("out fetch method: ", horses_data);

            return data.length;
        }

        function calculateR(data){
            return data.length;
        }
    </script>
</head>

<body>
    <div class="container">
        <h1>لوحة تحكم الأدمن</h1>

        <h2>إحصائيات الحجوزات</h2>
        <div class="stats">
            <p>إجمالي الحجوزات: <span id="bookings_total"></span></p>
            <p>إجمالي الدخل: <span id="d_total"></span> جنيه</p>
            <p>إجمالي الربح: <span id="r_total"></span> جنيه</p>
        </div>

        <h2>إدارة الحجوزات</h2>

        <div class="search_and_filter">
            <div class="search_div">
                <input type="text" id="searchInput" placeholder="ابحث عن حجز...">
                <i class="fas fa-search"></i>
            </div>
            <select id="statusFilter">
                <option value="">تحديد الكل</option>
                <option value="مبدئي">مبدئي</option>
                <option value="ملغي">ملغي</option>
                <option value="تم الركوب">تم الركوب</option>
            </select>
        </div>

        <div style="overflow-x: scroll; width: auto;">
            <table>
                <thead>
                    <tr>
                        <th>الاجراء</th>
                        <th>المعرف</th>
                        <th>نوع الحصان</th>
                        <th>مستوى الركوب</th>
                        <th>الاسم</th>
                        <th>العمر</th>
                        <th>الجنس</th>
                        <th>الحالة</th>
                        <th>رقم الواتس</th>
                        <th>رقم التلفون</th>
                        <th>التاريخ</th>
                        <th>الزمن</th>
                        <th>العرض</th>
                        <th>نوع الحجز</th>
                        <th>عدد الأفراد</th>
                        <th>مجمل السعر</th>
                        <th>تاريخ التحديث</th>
                        <th>تاريخ الإنشاء</th>
                    </tr>
                </thead>
                <tbody id="bookings-list">

                </tbody>
            </table>

        </div>

        <h2>تعديل سعر العرض الأسبوعي</h2>
        <form id="update-offer-form" action="update_offer.php" method="POST">
            <label for="offer-price">سعر العرض الجديد:</label>
            <input type="number" id="offer-price" name="offer-price" required>
            <button type="submit">تحديث السعر</button>
        </form>
    </div>
</body>

</html>