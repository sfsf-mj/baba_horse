<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الخيول</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            direction: rtl;
            text-align: right;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #ddd;
        }

        form {
            margin-top: 20px;
        }

        input,
        button {
            padding: 10px;
            margin: 5px;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            fetchHorses();
        });

        function fetchHorses() {
            fetch("/admin/horses/data")
            .then(response => response.json())
            .then(data => {
                let tableBody = document.getElementById("bookings-list");
                tableBody.innerHTML = ""; // تفريغ الجدول قبل إعادة تحميل البيانات

                if (data.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="15">لا توجد نتائج</td></tr>';
                    return;
                }

                let row = "";
                data.forEach(horse => {
                    row += `<tr data-id="${horse.id}">
                        <td>${horse.id}</td>
                        <td class="editable" data-field="class_types">${horse.class_types}</td>
                        <td class="editable" data-field="price">${horse.price}</td>
                        <td class="editable" data-field="ride_price">${horse.ride_price}</td>
                        <td>${new Date(horse.created_at).toLocaleString()}</td>
                        <td>${new Date(horse.updated_at).toLocaleString()}</td>
                    </tr>`;
                });

                tableBody.innerHTML = row;

                // console.log(hp); // اختبار المخرجات
                updateField();
            })
            .catch(error => console.error("Error fetching horses:", error));
        }

        function updateField() {
            document.querySelectorAll(".editable").forEach(function (cell) {
                cell.addEventListener("click", function () {
                    let currentValue = this.innerText;
                    let field = this.getAttribute("data-field");
                    let row = this.closest("tr");
                    let id = row.getAttribute("data-id");

                    let input = document.createElement("input");
                    input.type = field === "price" || field === "ride_price" ? "number" : "text";
                    input.value = currentValue;

                    let saveButton = document.createElement("button");
                    saveButton.innerText = "حفظ";
                    saveButton.style.marginRight = "5px";

                    let spinner = document.createElement("span");
                    spinner.innerText = "🔄"; // رمز تحميل مؤقت
                    spinner.style.display = "none"; // نخفيه في البداية

                    this.innerHTML = "";
                    this.appendChild(input);
                    this.appendChild(saveButton);
                    this.appendChild(spinner);
                    input.focus();

                    saveButton.addEventListener("click", function () {
                        let newValue = input.value;
                        if (newValue !== currentValue) {
                            spinner.style.display = "inline"; // إظهار رمز التحميل
                            
                            fetch(`/horses/${id}`, {
                                method: "PUT",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },
                                body: JSON.stringify({ [field]: newValue })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    cell.innerText = newValue;
                                } else {
                                    cell.innerText = currentValue;
                                }
                            })
                            .catch(() => {
                                cell.innerText = currentValue;
                            })
                            .finally(() => {
                                spinner.style.display = "none"; // إخفاء التحميل بعد انتهاء العملية
                            });
                        } else {
                            cell.innerText = currentValue;
                        }
                    });
                });
            });
        }

    </script>
</head>

<body>

    <h2>إضافة حصان جديد</h2>

    @if(session('success'))
    <div style="color: #ffffff; background: #06b713; width: max-content; padding: 10px;">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 10px;">
        {{ session('error') }}
    </div>
    @endif


    <form action="{{ route('horses.store') }}" method="POST">
        @csrf
        <input type="text" name="class_types" placeholder="اسم الحصان" required>
        <input type="number" name="price" placeholder="سعر الحصان" required>
        <input type="number" name="ride_price" placeholder="سعر الركوب" required>
        <button type="submit">إضافة</button>
    </form>

    <h2>قائمة الخيول</h2>
    <table>
        <thead>
            <tr>
                <th>الرقم</th>
                <th>الاسم</th>
                <th>السعر</th>
                <th>سعر الركوب</th>
                <th>تاريخ الانشاء</th>
                <th>تاريخ التعديل</th>
            </tr>
        </thead>

        <tbody id="bookings-list">

        </tbody>

    </table>

</body>

</html>