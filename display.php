    <?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "hccrmu";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Handle delete request
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
        $delete_id = $_POST['delete_id'];
        $sql_delete = "DELETE FROM responses WHERE id=?";
        $stmt = $conn->prepare($sql_delete);
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();
    }

    // Query data for the table
    $sql = "SELECT * FROM responses";
    $result = $conn->query($sql);

    // Query data for the pie chart
    $sql_chart = "SELECT faculty, COUNT(*) as total FROM responses GROUP BY faculty";
    $stmt_chart = $conn->prepare($sql_chart);
    $stmt_chart->execute();
    $result_chart = $stmt_chart->get_result();
    $report_data = array();
    while ($row_chart = $result_chart->fetch_assoc()) {
        $report_data[] = '{name:'.'"'.$row_chart['faculty'].' ('.$row_chart['total'].')'.'", y:'.$row_chart['total'].'}';
    }
    $report_data = implode(",", $report_data);
    $stmt_chart->close();

    // Query data for average scores chart
    $sql_avg_scores = "SELECT faculty, 
                            AVG(q1) AS avg_q1, 
                            AVG(q2) AS avg_q2, 
                            AVG(q3) AS avg_q3, 
                            AVG(q4) AS avg_q4, 
                            AVG(q5) AS avg_q5, 
                            AVG(q6) AS avg_q6, 
                            AVG(q7) AS avg_q7, 
                            AVG(q8) AS avg_q8, 
                            AVG(q9) AS avg_q9 
                    FROM responses 
                    GROUP BY faculty";
    $stmt_avg_scores = $conn->prepare($sql_avg_scores);
    $stmt_avg_scores->execute();
    $result_avg_scores = $stmt_avg_scores->get_result();

    $average_scores_data = array();
    while ($row_avg_scores = $result_avg_scores->fetch_assoc()) {
        $average_scores_data[] = [
            'faculty' => $row_avg_scores['faculty'],
            'data' => [
                $row_avg_scores['avg_q1'],
                $row_avg_scores['avg_q2'],
                $row_avg_scores['avg_q3'],
                $row_avg_scores['avg_q4'],
                $row_avg_scores['avg_q5'],
                $row_avg_scores['avg_q6'],
                $row_avg_scores['avg_q7'],
                $row_avg_scores['avg_q8'],
                $row_avg_scores['avg_q9'],
            ]
        ];
    }
    $stmt_avg_scores->close();
    $conn->close();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>แสดงข้อมูลคนที่ทำ PHQ-9</title>
        <link rel="stylesheet" href="styles4.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        <script src="https://code.highcharts.com/modules/export-data.js"></script>
        <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    </head>
    <body>
        <div class="container">
            <h1>PHQ-9 Responses</h1>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Q1</th>
                    <th>Q2</th>
                    <th>Q3</th>
                    <th>Q4</th>
                    <th>Q5</th>
                    <th>Q6</th>
                    <th>Q7</th>
                    <th>Q8</th>
                    <th>Q9</th>
                    <th>Total Score</th>
                    <th>คณะที่กำลังศึกษา</th>
                    <th>ชั้นปี</th>
                    <th>นักศึกษา/บุคลากร</th>
                    <th>วัน/เดือน/ปี</th>
                    <th>จัดการ</th>
                </tr>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>" . $row["id"] . "</td>
                            <td>" . $row["q1"] . "</td>
                            <td>" . $row["q2"] . "</td>
                            <td>" . $row["q3"] . "</td>
                            <td>" . $row["q4"] . "</td>
                            <td>" . $row["q5"] . "</td>
                            <td>" . $row["q6"] . "</td>
                            <td>" . $row["q7"] . "</td>
                            <td>" . $row["q8"] . "</td>
                            <td>" . $row["q9"] . "</td>
                            <td>" . $row["total_score"] . "</td>
                            <td>" . $row["faculty"] . "</td>
                            <td>" . $row["academic_year"] . "</td>
                            <td>" . $row["user_type"] . "</td>
                            <td>" . $row["assessment_date"] . "</td>
                            <td>
                                <form method='POST' action=''>
                                    <input type='hidden' name='delete_id' value='" . $row["id"] . "'>
                                    <button type='submit' class='delete-btn'>Delete</button>
                                </form>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='16'>No responses found</td></tr>";
                }
                ?>
            </table>
            <div class="button-group">
                <a href="index.html">กลับไปที่แบบทดสอบ</a>
            </div>
            
            <!-- Pie Chart -->
            <figure class="highcharts-figure">
                <div id="containerchart"></div>
            </figure>
            
            <!-- Bar Chart for Average Scores -->
            <figure class="highcharts-figure">
                <div id="containerchart2"></div>
            </figure>

            <script>
                // Pie Chart
                Highcharts.chart('containerchart', {
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                    },
                    title: {
                        text: 'ผลรวมทั้งหมด'
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                    },
                    accessibility: {
                        point: {
                            valueSuffix: '%'
                        }
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                            }
                        }
                    },
                    series: [{
                        name: 'คณะ',
                        colorByPoint: true,
                        data: [<?php echo $report_data; ?>]
                    }]
                });

                // Bar Chart for Average Scores
                Highcharts.chart('containerchart2', {
                    chart: {
                        type: 'bar'
                    },
                    title: {
                        text: 'Average Scores by Faculty'
                    },
                    xAxis: {
                        categories: <?php echo json_encode(array_column($average_scores_data, 'faculty')); ?>,
                        title: {
                            text: 'Faculty'
                        }
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Average Score',
                            align: 'high'
                        },
                        labels: {
                            overflow: 'justify'
                        }
                    },
                    tooltip: {
                        valueSuffix: ' Points'
                    },
                    plotOptions: {
                        bar: {
                            dataLabels: {
                                enabled: true
                            }
                        }
                    },
                    series: [
                        {
                            name: 'Q1',
                            data: <?php echo json_encode(array_column(array_column($average_scores_data, 'data'), 0)); ?>
                        },
                        {
                            name: 'Q2',
                            data: <?php echo json_encode(array_column(array_column($average_scores_data, 'data'), 1)); ?>
                        },
                        {
                            name: 'Q3',
                            data: <?php echo json_encode(array_column(array_column($average_scores_data, 'data'), 2)); ?>
                        },
                        {
                            name: 'Q4',
                            data: <?php echo json_encode(array_column(array_column($average_scores_data, 'data'), 3)); ?>
                        },
                        {
                            name: 'Q5',
                            data: <?php echo json_encode(array_column(array_column($average_scores_data, 'data'), 4)); ?>
                        },
                        {
                            name: 'Q6',
                            data: <?php echo json_encode(array_column(array_column($average_scores_data, 'data'), 5)); ?>
                        },
                        {
                            name: 'Q7',
                            data: <?php echo json_encode(array_column(array_column($average_scores_data, 'data'), 6)); ?>
                        },
                        {
                            name: 'Q8',
                            data: <?php echo json_encode(array_column(array_column($average_scores_data, 'data'), 7)); ?>
                        },
                        {
                            name: 'Q9',
                            data: <?php echo json_encode(array_column(array_column($average_scores_data, 'data'), 8)); ?>
                        }
                        
                    ]
                });
            </script>
        </div>
    </body>
    </html>
