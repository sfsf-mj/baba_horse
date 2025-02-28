// function toggleGroupFields() {
//     let groupType = document.getElementById("booking_type").value;
//     document.getElementById("group-details").style.display = (groupType === "جماعي") ? "block" : "none";
// }

function generateGroupFields() {
    let size = document.getElementById("group-size").value;
    let container = document.getElementById("group-members");
    container.innerHTML = "";
    for (let i = 1; i <= size; i++) {
        container.innerHTML += `
            <h4>تفاصيل الشخص ${i}</h4>
            <label>أنواع الخيل:</label>
            <select name="member_horse_type_${i}" id="member_horse_type_${i}" onchange="updatePrice()" required>
                <option value=""></option>
                <option value="عادي">عادي</option>
                <option value="سرعة">سرعة</option>
                <option value="رقص">رقص</option>
                <option value="تكبيش">تكبيش</option>
                <option value="طيران">طيران</option>
                <option value="جمال">جمال</option>
                <option value="درجات صحراوية">درجات صحراوية</option>
                <option value="حنطور">حنطور (يسمح بثلاثة أفراد فقط)</option>
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
// document.addEventListener("DOMContentLoaded", function () {
//     document.getElementById("horse-type").addEventListener("change", updatePrice);
//     document.getElementById("offer").addEventListener("change", updatePrice);
//     document.getElementById("booking_type").addEventListener("change", toggleGroupFields);
//     document.getElementById("group-size").addEventListener("input", generateGroupFields);
// });

const horsePrices = {
    "غير محدد": 0,
    "عادي": 100,
    "سرعة": 150,
    "رقص": 200,
    "تكبيش": 250,
    "طيران": 300,
    "جمال": 120,
    "درجات صحراوية": 180,
    "حنطور": 250 // سعر الحنطور يشمل 3 أفراد
};

function updatePrice() {
    let totalPrice = 0;

    let horseType = document.getElementById("horse-type").value || "غير محدد";
    let isOffer = document.getElementById("offer").value === "نعم";
    let booking_type = document.getElementById("booking_type").value;
    var groupSize = document.getElementById("group-size").value || 0;

    totalPrice = horsePrices[horseType];

    console.log("group Size in updatePrice: "+groupSize);

    if(groupSize > 0){
        for (let i = 1; i <= groupSize; i++) {
            let member_horse_type = document.getElementById(`member_horse_type_${i}`).value || "غير محدد";
            totalPrice = totalPrice + horsePrices[member_horse_type];
        }
    } else {
        totalPrice = horsePrices[horseType];
    }
    // let totalPrice = basePrice * groupSize;

    // إذا كان الحجز ضمن العرض، حساب السعر بناءً على العرض
    // if (isOffer) {
    //     totalPrice = 100 * groupSize; // العرض يحسب الفرد بـ 100 جنيه
    // }

    document.getElementById("total-price").value = totalPrice;
}

function toggleGroupFields() {
    let groupType = document.getElementById("booking_type").value;
    document.getElementById("group-details").style.display = (groupType === "جماعي") ? "block" : "none";
    updatePrice();
}

// function generateGroupFields() {
//     let size = document.getElementById("group-size").value;
//     let container = document.getElementById("group-members");
//     container.innerHTML = "";

//     for (let i = 1; i <= size; i++) {
//         container.innerHTML += `
//             <h4>تفاصيل الشخص ${i}</h4>
//             <label>الاسم:</label><input type="text" name="member_name_${i}" required>
//             <label>العمر:</label><input type="number" name="member_age_${i}" required>
//             <label>الجنس:</label>
//             <select name="member_gender_${i}">
//                 <option value="ذكر">ذكر</option>
//                 <option value="أنثى">أنثى</option>
//             </select>
//             <label>مستوى الركوب:</label>
//             <select name="member_level_${i}">
//                 <option value="مبتدئ">مبتدئ</option>
//                 <option value="متوسط">متوسط</option>
//                 <option value="محترف">محترف</option>
//             </select>
//         `;
//     }
//     updatePrice();
// }
console.log("booking.js loaded successfully!");
