<?php

include 'controller.php';

if(!isset($_SESSION['user_id'])){
    session_destroy();
    echo "<script>window.location = 'login.php'</script>";
}

$controller = new controller();
$conn = $controller->open();
$logs = $controller->getListData($conn, "SELECT logs.*, students.name AS name, students.class AS class, students.form AS form, students.rfid AS rfid FROM LOGS LEFT JOIN students ON (LOGS.student_id = students.id) WHERE CAST(log_date AS DATE) = CAST( curdate() AS DATE) AND exit_time IS NULL");
$totalStudent = $controller->getCount($conn, 'students');
$totalActiveNotification = $controller->getCount($conn, 'students', 'WHERE tele_id IS NOT NULL');
$totalAttend = $controller->getCountAttend($conn);
$totalAbsent = $totalStudent['total'] - $totalAttend['total'];
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>STEM Dashboard</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <?php include('sidebar.php'); ?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <?php include('topbar.php'); ?>

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Total Student</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"> <span
                                                    id="totalStudent"></span></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-border-all"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Students Attandance (Today)</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><span
                                                    id="totalAttend"></span></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-book"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                Total Absent</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><span
                                                    id="totalAbsent"></span>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-exclamation-circle"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Requests Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Parents Actived Notifications</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><span
                                                    id="totalActiveNotification"></span></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-comment"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Row -->

                    <div class="row">

                        <div class="col-lg-8">
                            <div class="card shadow mb-4 border-left-success">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-success">Student Logs : <?= date("d/m/Y") ?>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Class</th>
                                                    <th>RFID Code</th>
                                                    <th>Form</th>
                                                    <th>Enter Time</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Class</th>
                                                    <th>RFID Code</th>
                                                    <th>Form</th>
                                                    <th>Enter Time</th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                            <tbody class="tables-student">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pie Chart -->
                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4 border-left-primary">
                                <!-- Card Header - Dropdown -->
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Log Overview</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                            aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Dropdown Header:</div>
                                            <a class="dropdown-item" href="#">Action</a>
                                            <a class="dropdown-item" href="#">Another action</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#">Something else here</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-2">
                                        <canvas id="myPieChart"></canvas>
                                    </div>
                                    <div class="mt-4 text-center small">
                                        <span class="mr-2">
                                            <i class="fas fa-circle text-success"></i> Attend
                                        </span>
                                        <span class="mr-2">
                                            <i class="fas fa-circle text-danger"></i> Absent
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <?php include('footer.php'); ?>
        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>
    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/datatables-demo.js"></script>
    <script>
    window.onload = function() {
        var overviewDashboard = function() {
            $.ajax({
                url: "controller.php?mod=overviewDashboard",
                type: "GET",
                dataType: "json",
                success: function(response) {

                    // For List Attend
                    var listAttend = response.listAttend;
                    console.log(listAttend);
                    var getlenght = listAttend.length - 1;
                    html = '';
                    for (var i = getlenght; i >= 0; i--) {
                        html += '<tr>';
                        html += '<td>' + listAttend[i].name + '</td>';
                        html += '<td>' + listAttend[i].class + '</td>';
                        html += '<td>' + listAttend[i].rfid + '</td>';
                        html += '<td>' + listAttend[i].form + '</td>';
                        html += '<td>' + listAttend[i].enter_time + '</td>';
                        html +=
                            '<td><a class="btn btn-danger btn-sm" href="controller.php?mod=forceExit&id=' +
                            listAttend[i].id +
                            '"><i class="fa fa-check-circle" style="font-size:24px"></i></a></td>';
                        html += '<tr>';
                    }

                    html += '';
                    $('.tables-student').html(html);
                    $('#totalStudent').html(response.totalStudent.total);
                    $('#totalActiveNotification').html(response.totalActiveNotification.total);
                    $('#totalAttend').html(response.totalAttend.total);
                    $('#totalAbsent').html(response.totalAbsent);
                    // End For List Attend
                    // Set new default font family and font color to mimic Bootstrap's default styling
                    Chart.defaults.global.defaultFontFamily = 'Nunito',
                        '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
                    Chart.defaults.global.defaultFontColor = '#858796';

                    // Pie Chart Example
                    var ctx = document.getElementById("myPieChart");
                    var myPieChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ["Attend", "Absent"],
                            datasets: [{
                                data: [response.totalAttend.total, response
                                    .totalAbsent
                                ],
                                backgroundColor: ['#1cc88a', '#E02D1B'],
                                hoverBackgroundColor: ['#17a673', '#cc1e0e'],
                                hoverBorderColor: "rgba(234, 236, 244, 1)",
                            }],
                        },
                        options: {
                            animation: {
                                duration: 0
                            },
                            maintainAspectRatio: false,
                            tooltips: {
                                backgroundColor: "rgb(255,255,255)",
                                bodyFontColor: "#858796",
                                borderColor: '#dddfeb',
                                borderWidth: 1,
                                xPadding: 15,
                                yPadding: 15,
                                displayColors: false,
                                caretPadding: 10,
                            },
                            legend: {
                                display: false
                            },
                            cutoutPercentage: 80,
                        },
                    });
                }
            });
        }

        setInterval(function() {
            overviewDashboard();
        }, 2000);
    };
    </script>
</body>

</html>