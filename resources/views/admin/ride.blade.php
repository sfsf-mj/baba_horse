<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>واجهة الركوب</title>
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

        h1{
            color: #333;
        }

        .search_div{
            position: relative;
            display: flex;
            justify-content: center;
            width: 100%;
            margin-top: 25px;
        }
        .search_div input[type="text"]{
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
        .search_div input:focus{
            outline: 3px solid #007bff;
        }
        .search_div input:focus ~ i{
            color: #007bff;
        }
        .content-2 .search button img {
            width: 30px;
            height: 30px;
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
            color:rgb(0, 0, 0);
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
            fetchBookings();
        });

        function fetchBookings() {
            fetch("/api/bookings")
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
                let status = "";

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

            let bookings_date = document.getElementById("bookings_date");
            bookings_date.innerHTML = data[0].date;

            if (data.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="15">لا توجد نتائج</td></tr>';
                return;
            }

            let row = "";
            data.forEach(booking => {
                if(booking.offer === "لم يتم" && booking.status === "مبدئي"){
                    row += `<tr id="booking-${booking.id}">
                        <td>
                            <button onclick="updateStatus(${booking.id}, 'ملغي')" style="background: #dd8e8e;">إلغاء</button>
                            <button onclick="updateStatus(${booking.id}, 'تم الدفع')" style="background: #dcdd8e;">تم الدفع</button>
                        </td>
                        <td>${booking.horse_type}</td>
                        <td>${booking.name}</td>
                        <td>${booking.total_price}</td>
                    </tr>`;
                } else if(booking.offer === "تم الدفع" && booking.status === "مبدئي"){
                    row += `<tr id="booking-${booking.id}">
                        <td>
                            <button onclick="updateStatus(${booking.id}, 'ملغي')" style="background: #dd8e8e;">إلغاء</button>
                            <button onclick="updateStatus(${booking.id}, 'تم الركوب')" style="background: #8edd94;">تم الركوب</button>
                        </td>
                        <td>${booking.horse_type}</td>
                        <td>${booking.name}</td>
                        <td>${booking.total_price}</td>
                    </tr>`;
                } else {
                    row += `<tr id="booking-${booking.id}">
                        <td>${booking.status}</td>
                        <td>${booking.horse_type}</td>
                        <td>${booking.name}</td>
                        <td>${booking.total_price}</td>
                    </tr>`;
                }
                
                
                    // <td id="status-${booking.id}">${booking.status}</td>
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
                    // document.getElementById(`status-${bookingId}`).innerText = data.new_status;
                    alert("تم تحديث الحالة بنجاح!");
                } else {
                    alert("حدث خطأ أثناء تحديث الحالة!");
                }
            })
            .catch(error => console.error("Error updating status:", error));

            // جلب الحجوزات مرة اخرى لتحديث الازرار
            fetchBookings();
        }
    </script>
</head>

<body>
    <div class="container">
        <h1>حجوزات يوم<br><span id="bookings_date"></span></h1>

        <div class="stats">
            <p>إجمالي الحجوزات: <span id="bookings_total"></span></p>
        </div>

        <div class="search_div">
            <input type="text" id="searchInput" placeholder="ابحث عن حجز...">
            <i class="fas fa-search"></i>
        </div>

        <div style="overflow-x: scroll; width: auto;">
            <table>
                <thead>
                    <tr>
                        <th>الاجراء</th>
                        <th>نوع الحصان</th>
                        <th>الاسم</th>
                        <th>مجمل السعر</th>
                    </tr>
                </thead>
                <tbody id="bookings-list">

                </tbody>
            </table>
        </div>
    </div>
</body>

</html>