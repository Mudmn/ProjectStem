<?php
error_reporting(0);
session_start();

$config = new controller();
class controller{

		function __construct(){
			if (isset($_GET['mod'])) {
				$conn = $this->open();
				$action = $this->valdata($conn, $_GET['mod']);

				switch ($action) {

					//---------------- START BASIC PART ----------------
					case 'rfidTrigger':
						$this->rfidTrigger($conn);
						break;

                    case 'deleteStudent':
                        $this->deleteStudent($conn);
                        break;

                    case 'updateStudent':
                        $this->updateStudent($conn);
                        break;
                    
                    case 'addStudent':
						$this->addStudent($conn);
						break;

					case 'login':
						$this->login($conn);
						break;

					case 'logout':
						$this->logout($conn);
						break;

					//---------------- END BASIC PART ----------------
					
				}
			}
		}

		public function rfidTrigger($conn){
			if(!isset($_GET['rfid'])){
				return false;
				exit;
			}

			$rfid = $this->valdata($conn, $_GET['rfid']);
			$student = $this->getOneData($conn, "SELECT * FROM students WHERE rfid = '$rfid'");
			if($student == null){
				return false;
				exit;
			}

			$sql = "SELECT * FROM logs WHERE student_id = ? AND exit_time IS NULL ORDER BY log_date DESC LIMIT 1";
			$stmt = $conn->prepare($sql);
			$stmt->execute([$student['id']]);
			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			if($row){
				$updateSql = "UPDATE logs SET exit_time = NOW() WHERE id = ?";
				$updateStmt = $conn->prepare($updateSql);
				$updateRs = $updateStmt->execute([$row['id']]);
			} else {
				$insertSql = "INSERT INTO logs (student_id, enter_time) VALUES (?,NOW())";
				$insertStmt = $conn->prepare($insertSql);
				$insertRs = $insertStmt->execute([$student['id']]);
			}

			return true;
		}

        public function deleteStudent($conn){
            $id = $this->valdata($conn, $_GET['id']);
            
            $sql = "DELETE FROM students WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$id]);

            $this->redirect('students.php','Successfully Deleted');
        }

        public function updateStudent($conn){
            $name = $this->valdata($conn, $_POST['name']);
            $class = $this->valdata($conn, $_POST['class']);
            $form = $this->valdata($conn, $_POST['form']);
            $rfid = $this->valdata($conn, $_POST['rfid']);
            $id = $this->valdata($conn, $_POST['id']);

            $sql = "UPDATE students SET name = ?, class = ?, form = ?, rfid = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $rs = $stmt->execute([$name, $class, $form, $rfid, $id]);

            $this->redirect('students.php', 'Successfully Updated');
        }

        public function addStudent($conn){
            $name = $this->valdata($conn, $_POST['name']);
            $class = $this->valdata($conn, $_POST['class']);
            $form = $this->valdata($conn, $_POST['form']);
            $rfid = $this->valdata($conn, $_POST['rfid']);

            $sql = "INSERT INTO students (name,class,form,rfid) VALUES (?,?,?,?)";
            $stmt = $conn->prepare($sql);
            $rs = $stmt->execute([$name, $class, $form, $rfid]);

            $this->redirect('students.php', 'Successfully added');
        }

		public function getOneData($conn, $query){
			
			$sql = "$query";
			$stmt = $conn->prepare($sql);
			$stmt->execute();
			
			if ($stmt) {
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
					return $row;	
				}
			} else {
				return 0;
			}
		}

		public function getListData($conn, $query){
			
			$sql = "$query";
			$stmt = $conn->prepare($sql);
			$stmt->execute();
			
			if ($stmt) {
				while($row = $stmt->fetchAll()){
					return $row;
				}
			} else {
				return 0;
			}
		}

		public function getCount($conn, $tableName, $where = null){
			$sql = "SELECT count(id) as total FROM $tableName $where";
	        $stmt = $conn->prepare($sql);
	        $stmt->execute();
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				return $row;	
			}
        }
        
        public function getAuth($conn){

			if(isset($_SESSION['user_id'])){
				$id = $_SESSION['user_id'];
				$sql = "SELECT * FROM users WHERE id = :id";
				
				$stmt = $conn->prepare($sql);
				$stmt->bindparam(':id', $id);
				$stmt->execute();
				while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
					return $row;	
				}
			}  else {
				return 0;
			}
		}

		public function login($conn){
			$email = $this->valdata($conn,$_POST['email']);
			$encrypted = md5($this->valdata($conn,$_POST['password']));

			//for admin
			$sql = "SELECT * FROM users WHERE email = :email AND password = :encrypted";
	        $stmt = $conn->prepare($sql);
	        $stmt->bindparam(':email', $email);
	        $stmt->bindparam(':encrypted', $encrypted);
	        $stmt->execute();
			$user = $stmt->fetch(PDO::FETCH_ASSOC);
			
			if($user){
				$_SESSION['user_id'] = $user['id'];

				$message = "Hai ". $user['nama'] .", Welcome to";
				$this->redirect('index.php', $message);
			} else {
				
				$message = "Your email or password is invalid, please try again.";
				$this->redirect('login.php', $message);
			}
		}

		// Validation Data / Input
		public function valdata($conn, $inputpost) {
			if (is_array($inputpost) && count($inputpost) > 0) {
				foreach ($inputpost as $input) {
					$inputpost[] = trim($input);
					$inputpost[] = stripslashes($input);
					$inputpost[] = htmlspecialchars($input);
				}
				return $inputpost;
			} else {
				$inputpost = trim($inputpost);
				$inputpost = stripslashes($inputpost);
				$inputpost = htmlspecialchars($inputpost);
				return $inputpost;
			}
		}

		// Destory Session
		public function logout($conn){
			session_destroy();
            $this->redirect('login.php');
		}

		// Redirection
        public function redirect($url, $message = null){

            if($message != null){
                echo "<script type='text/javascript'>alert('$message');</script>";
            }
    
            echo "<script type='text/javascript'>window.location='$url';</script>";
        }

		// Connection With Datbase
		public function open(){
			date_default_timezone_set("Asia/Kuala_Lumpur");

			$conn = "";
			$servername = "localhost";
			$dbname = "ProjectStem";
			$username = "root";
			$password = "";

			try {
			    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
			    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			    return $conn;
			}
			catch(PDOException $e)
			    {
			    echo "Connection failed: " . $e->getMessage();
			}
		}
	}
?>