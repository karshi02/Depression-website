document.addEventListener("DOMContentLoaded", function () {
    let currentQuestion = 1;
    const totalQuestions = 9; // จำนวนคำถามทั้งหมดใน PHQ-9
    const form = document.getElementById("phq9-form");

    // ซ่อนคำถามทั้งหมดนอกจากคำถามแรก
    for (let i = 2; i <= totalQuestions; i++) {
      //  document.getElementsByName("q" + i)[0].parentElement.style.display = "none";
    }
    document.getElementsByName("faculty")[0].parentElement.style.display = "none";
  document.getElementsByName("academic_year")[0].parentElement.style.display = "none";
   หdocument.getElementsByName("user_type")[0].parentElement.style.display = "none";

    form.addEventListener("change", function (event) {
        const target = event.target;

        // ถ้าคำถามที่ตอบเป็นคำถามปัจจุบัน
        if (target.tagName === 'SELECT' && target.name === 'q' + currentQuestion) {
            // ซ่อนคำถามปัจจุบัน
           // document.getElementsByName('q' + currentQuestion)[0].parentElement.style.display = "none";

            // เพิ่มจำนวนคำถามปัจจุบัน
            currentQuestion++;

            // ถ้าคำถามปัจจุบันน้อยกว่าหรือเท่ากับจำนวนคำถามทั้งหมด
            if (currentQuestion <= totalQuestions) {
                // แสดงคำถามถัดไป
               // document.getElementsByName('q' + currentQuestion)[0].parentElement.style.display = "block";
            } else {
                // แสดงคำถามเกี่ยวกับคณะ ชั้นปี และประเภทผู้ใช้
               // document.getElementsByName("faculty")[0].parentElement.style.display = "block";
               //document.getElementsByName("academic_year")[0].parentElement.style.display = "block";
               // document.getElementsByName("user_type")[0].parentElement.style.display = "block";
            }
        }
    });
});
function updateSliderColor(slider) {
    const value = slider.value;
    let color = '';

    if (value == 0) {
        color = '#4CAF50'; // สีเขียว
    } else if (value == 1) {
        color = '#FFEB3B'; // สีเหลือง
    } else if (value == 2) {
        color = '#FF9800'; // สีส้ม
    } else if (value == 3) {
        color = '#F44336'; // สีแดง
    }

    slider.style.background = `linear-gradient(to right, ${color} ${value * 33.33}%, #ddd ${value * 33.33}%)`;
    slider.nextElementSibling.value = value;
}
