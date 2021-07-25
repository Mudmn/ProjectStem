<?php

include 'controller.php';

if(!isset($_SESSION['user_id'])){
    session_destroy();
    echo "<script>window.location = 'login.php'</script>";
}

$controller = new controller();
$conn = $controller->open();

$student = "";
if(isset($_GET['id'])){
    $student = $controller->getOneData($conn, "SELECT * FROM students WHERE id= ". $_GET['id']);

    if($student == null){
        echo "<script>window.location = 'students.php</script>";
    }

} else {
    echo "<script>window.location = 'students.php'</script>";
}



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
                            
                            <h6 class="m-0 font-weight-bold text-primary">Students Information : </h6>
                        </div>
                        <div class="card-body">
                            <div class="col-lg-6">
                            <form method="post" action="controller.php?mod=updateStudent">
    <div class="form-group col-md-12">
      <label for="inputEmail4">Name</label>
      <input name="name" type="text" class="form-control" id="inputEmail4" placeholder="Name...." value="<?= $student['name'] ?>">
    </div>

  </div>
  <div class="form-group">
    <label for="inputAddress">Rfid Code</label>
    <input name="rfid" type="text" class="form-control" id="inputAddress" placeholder="Enter Rfid...." value="<?= $student['rfid'] ?>" >
  </div>
  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="inputCity">Class</label>
      <input name="class" type="text" class="form-control" id="inputCity">
    </div>
    <div class="form-group col-md-6">
      <label for="inputState">Form</label>
      <select name="form" id="inputState" class="form-control">
      <option <?= ($student['form'] == 1) ? 'selected' : '' ?> value="1">1</option>
      <option <?= ($student['form'] == 2) ? 'selected' : '' ?> value="2">2</option>
      <option <?= ($student['form'] == 3) ? 'selected' : '' ?> value="3">3</option>
      <option <?= ($student['form'] == 4) ? 'selected' : '' ?> value="4">4</option>
      <option <?= ($student['form'] == 5) ? 'selected' : '' ?> value="5">5</option>
    
      </select>
    </div>
    
  </div>
  <div class="form-group">
  </div>
  <button type="submit" class="btn btn-primary">Update</button>
</form></div>                           
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
          <input type="hidden" name="id" value="<?= $student['id'] ?>">
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