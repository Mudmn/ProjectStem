<?php

include 'controller.php';

if(!isset($_SESSION['user_id'])){
    session_destroy();
    echo "<script>window.location = 'login.php'</script>";
}

$controller = new controller();
$conn = $controller->open();

$logs = $controller->getListData($conn, "SELECT logs.*,students.*,DATE_FORMAT(log_date, '%d %M %Y') AS logDate FROM logs LEFT JOIN students on (logs.student_id = students.id) WHERE logs.exit_time IS NOT NULL ORDER BY logs.log_date DESC");
?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Students Logs</title>

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <?php include('sidebar.php');?>
        
        <!-- foot -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">


                <?php include('topbar.php');?>
                
                <!-- Begin Page Content -->

                <div class="container-fluid">

                

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Logs And Activities</h1>
                    <p class="mb-4">All students information and activities such as time they arrived and exit from school will be recorded here.</p>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4 border-left-primary">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Student Logs</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Class</th>
                                            <th>RFID Code</th>
                                            <th>Date</th>
                                            <th>Enter Time</th>
                                            <th>Exit Time</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                            <th>Name</th>
                                            <th>Class</th>
                                            <th>RFID Code</th>
                                            <th>Date</th>
                                            <th>Enter Time</th>
                                            <th>Exit Time</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                    <?php
                                        if($logs != null){
                                            foreach($logs as $log){ ?>
                                         <tr>
                                            <td><?= $log['name'] ?></td>
                                            <td><?= $log['class'] ?></td>
                                            <td><?= $log['rfid'] ?></td>
                                            <td><?= $log['logDate'] ?></td>
                                            <td><?= $log['enter_time'] ?></td>
                                            <td><?= $log['exit_time'] ?></td>
                                            <td><?= $log['remark'] ?></td>
                                        </tr>                                       
                                <?php } } ?>
            
                                    </tbody>
                                </table>
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
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>

</body>

</html>