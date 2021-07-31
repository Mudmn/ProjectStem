<?php
// error_reporting(0);
session_start();

$config = new controller();
class controller{

		function __construct(){
			if (isset($_GET['mod'])) {
				$conn = $this->open();
				$action = $this->valdata($conn, $_GET['mod']);

				switch ($action) {

					//---------------- START BASIC PART ----------------
					case 'overviewDashboard':
						$this->overviewDashboard($conn);
						break;

					case 'forceExit':
						$this->forceExit($conn);
						break;

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

		public function sendNotificationTelegram($method, $data){

			$BOT_TOKEN = "";
	
			$url = "https://api.telegram.org/bot$BOT_TOKEN/$method";
		
			if(!$curld = curl_init()){
				exit;
			}
			curl_setopt($curld, CURLOPT_POST, true);
			curl_setopt($curld, CURLOPT_POSTFIELDS, $data);
			curl_setopt($curld, CURLOPT_URL, $url);
			curl_setopt($curld, CURLOPT_RETURNTRANSFER, true);
			$output = curl_exec($curld);
			curl_close($curld);
			return $output;
		}

		public function overviewDashboard($conn){

			$rows = array();
			$sql = "SELECT logs.*, students.name AS name, students.class AS class, students.rfid AS rfid FROM logs LEFT JOIN students ON (logs.student_id = students.id) WHERE CAST(log_date AS DATE) = CAST( curdate() AS DATE) AND exit_time IS NULL";
			$stmt = $conn->prepare($sql);
			$stmt->execute();
			while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
				$rows[] = $row;
			}

			$totalStudent = $this->getCount($conn, 'students');
			$totalActiveNotification = $this->getCount($conn, 'students', 'WHERE tele_id IS NOT NULL');
			$totalAttend = $this->getCountAttend($conn);
			if($totalAttend['total'] <= 0){
				$totalAbsent = 0;
			} else {
				$totalAbsent = $totalStudent['total'] - $totalAttend['total'];
			}

			$data = [
				'listAttend' => $rows,
				'totalStudent' => $totalStudent,
				'totalActiveNotification' => $totalActiveNotification,
				'totalAttend' => $totalAttend,
				'totalAbsent' => $totalAbsent
			];

			echo json_encode($data);
		}

		public function forceExit($conn){
			$id = $this->valdata($conn, $_GET['id']);
			$user = $this->getAuth($conn);

			$remark = "Exit by " . $user['name'];
			$updateSql = "UPDATE logs SET exit_time = NOW(), remark = ? WHERE id = ?";
			$updateStmt = $conn->prepare($updateSql);
			$updateRs = $updateStmt->execute([$remark, $id]);

			$student = $this->getOneData($conn, "SELECT students.* FROM logs LEFT JOIN students ON (logs.student_id = students.id) WHERE logs.id = $id");
			
			if($student['tele_id'] != null){

				$message = "Hi for your information, " . $student['name'] . " from class ". $student['class'] ." didnt scan for exit time. So sistem will update the data for exit time on : " . date("h:i:s a") . ", By ". $user['name'];
				// $message = "asdas";
				$param = array(
					"chat_id" => $student['tele_id'],
					"text" => $message,
					"parse_mode" => "HTML"
				);

				$this->sendNotificationTelegram("sendMessage", $param);
			}

			$this->redirect('index.php', 'successful force exit');
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

				// If already set for notification
				if($student['tele_id'] != null){

					$message = "Hi, " . $student['name'] . " from class ". $student['class'] ." has exit from school at : " . date("h:i:s a");

					$param = array(
						"chat_id" => $student['tele_id'],
						"text" => $message,
						"parse_mode" => "HTML"
					);

					print_r($param);

					$this->sendNotificationTelegram("sendMessage", $param);
				}
			} else {
				$insertSql = "INSERT INTO logs (student_id, enter_time) VALUES (?,NOW())";
				$insertStmt = $conn->prepare($insertSql);
				$insertRs = $insertStmt->execute([$student['id']]);

				// If already set for notification
				if($student['tele_id'] != null){

					$message = "Hi, " . $student['name'] . " from class ". $student['class'] ." arrived at school at : " . date("h:i:s a");

					$param = array(
						"chat_id" => $student['tele_id'],
						"text" => $message,
						"parse_mode" => "HTML"
					);

					print_r($param);

					$this->sendNotificationTelegram("sendMessage", $param);
				}
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
            $rfid = $this->valdata($conn, $_POST['rfid']);
            $tele_id = $this->valdata($conn, $_POST['tele_id']);
            $id = $this->valdata($conn, $_POST['id']);

            $sql = "UPDATE students SET name = ?, class = ?, rfid = ?, tele_id = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $rs = $stmt->execute([$name, $class, $rfid, $tele_id, $id]);

            $this->redirect('students.php', 'Successfully Updated');
        }

        public function addStudent($conn){
            $name = $this->valdata($conn, $_POST['name']);
            $class = $this->valdata($conn, $_POST['class']);
            $rfid = $this->valdata($conn, $_POST['rfid']);
            $tele_id = $this->valdata($conn, $_POST['tele_id']);

            $sql = "INSERT INTO students (name,class,rfid, tele_id) VALUES (?,?,?,?)";
            $stmt = $conn->prepare($sql);
            $rs = $stmt->execute([$name, $class, $rfid, $tele_id]);

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

		public function getCountAttend($conn){
			$sql = "SELECT count(DISTINCT student_id) as total FROM logs WHERE CAST(log_date AS DATE) = CAST( curdate() AS DATE)";
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

				$message = "Hai ". $user['name'] .", Welcome to SMKWMS2 Attandance System";
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
			$dbname = "";
			$username = "";
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