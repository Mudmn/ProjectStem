<?php

include 'controller.php';

if(!isset($_SESSION['user_id'])){
    session_destroy();
    echo "<script>window.location = 'login.php'</script>";
}

$controller = new controller();
$conn = $controller->open();

$students = $controller->getListData($conn, "SELECT * FROM students");



?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Students Of SMKWMS2</title>

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
                    <h1 class="h3 mb-2 text-gray-800">Students</h1>
                    <p class="mb-4">List of students in SMK Wangsa Maju Seksyen 2.</p>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4 border-left-primary">
                        <div class="card-header py-3">
                            <button class="btn btn-primary btn-sm float-right" type="button" data-toggle="modal" data-target="#exampleModal"><i class="fas fa-user-plus"></i> Add Student</button>
                            <h6 class="m-0 font-weight-bold text-primary">Students Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>Kelas</th>
                                            <th>Form</th>
                                            <th>Kod RFID</th>
                                            <th>Action</th>
                                            
                                        </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                            <th>Nama</th>
                                            <th>Kelas</th>
                                            <th>Form</th>
                                            <th>Kod RFID</th>
                                            <th>Action</th>
                                            
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        
                                        <?php
                                        if($students != null){
                                            foreach($students as $student){ ?>
                                            <tr>
                                            <td><?= $student['name'] ?></td>
                                            <td><?= $student['class'] ?></td>
                                            <td><?= $student['form'] ?></td>
                                            <td><?= $student['rfid'] ?></td>
                                            
                                            
                                            <td>
                                                <a class="btn btn-warning btn-sm" href="student-details.php?id=<?= $student['id'] ?>"><i class="fas fa-user-cog"></i></a>
                                                <a class="btn btn-danger btn-sm" href="controller.php?mod=deleteStudent&id=<?=$student['id']?>" onclick="return confirm('Are You Sure?')"><i class="fas fa-trash"></i></a>                                                
                                            </td>
                                            
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

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">New message</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form method="post" action="controller.php?mod=addStudent">
      <div class="modal-body">      
          <div class="form-group">
            <label class="col-form-label">Name:</label>
            <input required name="name"type="text" class="form-control" placeholder="Enter Name">
          </div>
          <div class="form-group">
            <label class="col-form-label">Class:</label>
            <input required  name="class" type="text" class="form-control" placeholder="Enter Class">        
          </div>
          <div class="form-group">
            <label class="col-form-label">Form:</label>
            <select required  name="form"d id="" class="form-control">
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            </select>
          </div>
          <div class="form-group">
            <label class="col-form-label">RFID Code:</label>
            <input required  name="rfid" type="text" class="form-control" placeholder="Enter RFID Code">
          </div>               
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
      </form>
    </div>
  </div>
</div>

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