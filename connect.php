<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Depression Risk Assessment Result</title>
    <?php
    // แก้ไขชื่อฐานข้อมูลเป็น "hccrmu"
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "hccrmu";

    // สร้างการเชื่อมต่อ
    $conn = new mysqli($servername, $username, $password, $dbname);

    // ตรวจสอบการเชื่อมต่อ
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $message = ""; // เริ่มต้นข้อความเป็นค่าว่าง

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // รับและตรวจสอบค่าจากฟอร์ม
        $q1 = isset($_POST['q1']) ? intval($_POST['q1']) : 0;
        $q2 = isset($_POST['q2']) ? intval($_POST['q2']) : 0;
        $q3 = isset($_POST['q3']) ? intval($_POST['q3']) : 0;
        $q4 = isset($_POST['q4']) ? intval($_POST['q4']) : 0;
        $q5 = isset($_POST['q5']) ? intval($_POST['q5']) : 0;
        $q6 = isset($_POST['q6']) ? intval($_POST['q6']) : 0;
        $q7 = isset($_POST['q7']) ? intval($_POST['q7']) : 0;
        $q8 = isset($_POST['q8']) ? intval($_POST['q8']) : 0;
        $q9 = isset($_POST['q9']) ? intval($_POST['q9']) : 0;
        $faculty = $conn->real_escape_string($_POST['faculty'] ?? '');
        $academic_year = isset($_POST['academic_year']) ? intval($_POST['academic_year']) : 0;
        $user_type = $conn->real_escape_string($_POST['user_type'] ?? '');
        $total_score = $q1 + $q2 + $q3 + $q4 + $q5 + $q6 + $q7 + $q8 + $q9;
        
        // กำหนดวันที่และเวลาปัจจุบัน
        $assessment_date = date('Y-m-d H:i:s');

        // บันทึกข้อมูลลงฐานข้อมูล
        $sql = "INSERT INTO responses (q1, q2, q3, q4, q5, q6, q7, q8, q9, total_score, faculty, academic_year, user_type, assessment_date) 
                VALUES ($q1, $q2, $q3, $q4, $q5, $q6, $q7, $q8, $q9, $total_score, '$faculty', $academic_year, '$user_type', '$assessment_date')";

        if ($conn->query($sql) === TRUE) {
            // ข้อความแสดงผลตามช่วงคะแนน
            $message = "ระดับคะเเนนในช่วง $total_score.<br>";
            if ($total_score <= 4) {
                $message .= "<strong class='level-low'>ระดับคะแนนช่วง 0 - 3 (ไม่มีเลย หรือมีเล็กน้อย)</strong>";
                $message .= "<strong class='level-low'>บ่งบอกภาวะว่าท่านไม่มีอาการซึมเศร้าหรือมีก็เพียงเล็กน้อยเท่านั้น</strong>";
            } elseif ($total_score <= 9) {
                $message .= "<strong class='level-medium'>ระดับคะแนนช่วง 4 - 9 (ปานกลาง)</strong>";
                $message .= "<strong class='level-medium'>ข้อแนะนำในการดูแล: ควรพักผ่อนให้เพียงพอ นอนหลับให้ได้ 6-8 ชั่วโมง ออกกำลังกายสม่ำเสมอ ทำกิจกรรมที่ทำให้ผ่อนคลาย พบปะเพื่อนฝูง ควรขอคำปรึกษาช่วยเหลือจากผู้ที่ไว้วางใจ ไม่จมอยู่กับปัญหา มองหาหนทางคลี่คลาย หากอาการที่ท่านเป็นมีผลกระทบต่อการทำงานหรือการเข้าสังคม (อาการซึมเศร้าทำให้ท่านมีปัญหาในการทำงาน การดูแลสิ่งต่าง ๆ ในบ้าน หรือการเข้ากับผู้คน ในระดับมากถึงมากที่สุด) หรือหากท่านมีอาการระดับนี้มานาน 1-2 สัปดาห์แล้วยังไม่ดีขึ้น ควรพบแพทย์เพื่อรับการช่วยเหลือรักษา (ปานกลาง)</strong>";
            } elseif ($total_score <= 14) {
                $message .= "<strong class='level-high'>ระดับคะแนนช่วง 9 - 14 (ค่อนข้างรุนแรง)</strong>";
                $message .= "<strong class='level-high'>ข้อแนะนำในการดูแล: ควรพบแพทย์เพื่อประเมินอาการและให้การรักษาระหว่างนี้ควรพักผ่อนให้เพียงพอ นอนหลับให้ได้ 6-8 ชั่วโมง ออกกำลังกายเบาๆ ทำกิจกรรมที่ทำให้ผ่อนคลาย ไม่เก็บตัว และควรขอคำปรึกษาช่วยเหลือจากผู้ใกล้ชิด</strong>";
            } elseif ($total_score <= 19) {
                $message .= "<strong class='level-severe'>ระดับคะแนนช่วง 14 - 19 (รุนแรงมาก)</strong>";
                $message .= "<strong class='level-severe'>ข้อแนะนำในการดูแล: ต้องพบแพทย์เพื่อประเมินอาการและให้การรักษาโดยเร็ว ไม่ควรปล่อยทิ้งไว้</strong>";
            } else {
                $message .= "<strong class='level-extreme'>ระดับคะแนนช่วง 19 - 27 (อาการซึมเศร้าแรงมาก)</strong>";
                $message .= "<strong class='level-extreme'>ข้อแนะนำในการดูแล: ห้ามอยู่คนเดียว ต้องพบแพทย์เพื่อรักษาโดยเร็ว ไม่งั้นอาจเป็นอันตรายต่อตัวเองได้</strong>";
            }
        } else {
            $message = "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    $conn->close();
    ?>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            text-align: center;
        }
        h1 {
            margin-bottom: 20px;
            color: #333333;
        }  
        .message {
            font-size: 18px;
            color: #333333;
            margin-bottom: 20px;
        }
        .message strong {
            display: block;
            margin-top: 10px;
            font-size: 20px;
        }
        .level-low {
            color: #28a745; /* สีเขียวอ่อน */
        }
        .level-medium {
            color: #ffc107; /* สีเหลือง */
        }
        .level-high {
            color: #fd7e14; /* สีส้ม */
        }
        .level-severe {
            color: #dc3545; /* สีแดง */
        }
        .level-extreme {
            color: #6610f2; /* สีม่วงเข้ม */
        }
        .button-group {
            margin-top: 20px;
        }
        .button-group a {
            display: inline-block;
            padding: 10px 20px;
            color: #ffffff;
            background-color: #007bff;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .button-group a:hover {
            background-color: #0056b3;
        }
        .button-group button {
            display: inline-block;
            padding: 10px 20px;
            color: #ffffff;
            background-color: #28a745; /* สีเขียว */
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .button-group button:hover {
            background-color: #218838; /* สีเขียวเข้ม */
        }
    </style>
</head>
<body>
      <!-- เพิ่มคำถามอื่น ๆ ที่เหลือที่นี่ -->
    <div class="question">
    <div class="button-group">
    <form id="hccrmu-form" action="connect.php" method="post">
        <!-- ฟิลด์อื่น ๆ ที่มีอยู่เดิม -->
    <div class="question">
    <div class="button-group">
    </div>
    </form>
    <div id="result"></div>
    <head>
    <!-- Other meta and style tags -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>
    </div>
    <div class="container">
        <h1>ผลการประเมินความเสี่ยงภาวะโรคซึมเศร้า</h1>
        <div class="message">
            <?php echo $message; ?>
        </div>
        <div class="button-group">
            <a href="index.html">กลับไปที่แบบทดสอบ</a>
            <!-- ฟอร์มการเชื่อมต่อไปยัง connect.php -->
            <form action="connect.php" method="post" style="display: inline;">          <!-- ใส่ข้อมูลที่ต้องส่งต่อหากต้องการ -->
            </form>
        </div>
    </div>
</body>
</body>
</html>
