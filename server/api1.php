<?php
header('Access-Control-Allow-Origin: *');

	require_once("Rest.inc.php");
	//require_once('generatePDF.php');
	//include('simple_html_dom.php');
	//require_once('CaptureService.php');
	//require_once('config/config.php');
	//require 'QueryPath/qp.php';

	class API extends REST {

		public $data = "";

		const DB_SERVER = "localhost";
		const DB_USER = "root";
		const DB_PASSWORD = "";
	  const DB = "rda_123";
		// adding table names
		const usersTable = "users";
		const planTable = "buiding_plan";
		const planUploadPath = "buildingPlan/";



		private $db = NULL;
		private $proxy = NULL;
		private $storeApiLogin = false;


		public $errorCodes = array(
				"200" => "The client request has succeeded",
				"201" => "Created",
				"202" => "Accepted",
				"203" => 	"Non-authoritative information.",
				"204" => 	"No content",
				"205" => 	"Reset content",
				"206" => 	"Partial content",

				"302" => "Object moved",
				"304" => "Not modified",
				"307" => "Temporary redirect",

				"400" => "Bad request",
				"401" => "Access denied",
				"402" => "Payment Required",
				"403" => "Forbidden",
				"404" => "Not found",
				"405" => "HTTP verb used to access this page is not allowed",
				"406" => "Client browser does not accept the MIME type of the requested page",
				"407" => "Proxy authentication required",
				"412" => "Precondition failed",
				"413" => "Request entity too large",
				"414" => "Request-URL too long",
				"415" => "Unsupported media type",
				"416" => "Requested range not satisfiable",
				"417" => "Execution failed",
				"423" => "Locked error",

				"500" => "Internal server error",
				"501" => "Header values specify a configuration that is not implemented",
				"502" => "Bad Gateway",
				"503" => "Service unavailable",
				"504" => "Gateway timeout",
				"505" => "HTTP version not supported"

		);
		public $messages = array(
				"operationNotDefined" => "Operation not Defined",
				"dataFetched" => "data fetched success",
				"userCreated" => "User created successfully",
				"deleted" => "data deleted successfully",
				"userUpdated" => "data updated successfully",
				"loginSuccess" => "successfully Logedin",
				"userLogout" => "Successfully log out",
				"changedPassword" => "Successfully Changed your password",
				"dataSaved" => "Data saved successfully"
		);

		public function __construct(){
			parent::__construct();
			$this->dbConnect();
		}

		private function dbConnect(){

			$this->db = mysql_connect(self::DB_SERVER,self::DB_USER,self::DB_PASSWORD);
			if($this->db)
                mysql_select_db(self::DB, $this->db) or die('ERRROR:'.mysql_error());
			else
				echo "db not exists";
		}

		public function processApi(){

			$func='';
			if(isset($_REQUEST['service']))
				$func = strtolower(trim(str_replace("/", "", $_REQUEST['service'])));
			else if(isset($_REQUEST['reqmethod']))
				$func = strtolower(trim(str_replace("/", "", $_REQUEST['reqmethod'])));

			if($func){

				//if(function_exists($func))
				if (method_exists($this, $func)) {
					$this->$func();
				} else{
					// $this->log('invalid service:'.$func, true, 'log_invalid.txt');
					$this->log('invalid service:'.$func." at ".date("Y-m-d H:i:s"));
					$this->response('invalid service', 406);
				}
			}
			else
				echo "invalid function";
		}
		/*
		* This is used to make a log of the error in the server end
		* (Yet to implemented) A log class that will create logs in the different files according to the type of the logs
		* like for logs for the server errors , information logs will be store in the different files
		*/
		public function log($logText,$type = 3 ,$destFile= 'error_log.txt'){
			error_log("\n".$logText,$type,$destFile);
		}
		public function json($data){
        if(is_array($data))
        {
              $formatted= json_encode($data);
              return $this->formatJson($formatted);
        }
				else {
					return $data;
				}
    }
    private function formatJson($jsonData){
        $formatted = $jsonData;
        $formatted = str_replace('"{', '{', $formatted);
        $formatted = str_replace('}"', '}', $formatted);
        $formatted = str_replace('\\', '', $formatted);
        return $formatted;
    }
		private function isValidCall($apiKey)
		{
			$flag=false;
			$apiKey = mysql_real_escape_string($apiKey);

			$sql="SELECT api_key  FROM ".self::TABLE_API_DATA." WHERE api_key ='$apiKey' ";
			$result = mysql_query($sql, $this->db);
			if(mysql_num_rows($result) > 0) {
          $rows =  mysql_fetch_array($result,MYSQL_ASSOC);
          $apiKeyDB = $rows['api_key'];
          $flag = true;
			}
			return $flag;
		}
		public function generateRandomString($length = 60) {
		    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		    $charactersLength = strlen($characters);
		    $randomString = '';
		    for ($i = 0; $i < $length; $i++) {
		        $randomString .= $characters[rand(0, $charactersLength - 1)];
		    }
		    return $randomString;
		}
		/*
		START  :: 28.2.15 :: Rajendra kumar sahoo
		*/
    	public function executeGenericDQLQuery($query){
          try{
              if(!$this->db)
              {
                  $this->db = mysql_connect(self::DB_SERVER,self::DB_USER,self::DB_PASSWORD);
              }
              $result = mysql_query($query, $this->db);
              /* if(mysqli_errno($con) != 0){
                  throw new Exception("Error   :".mysqli_errno($con)."   :  ".mysqli_error($con));
              } */

              $rows = array();
              while($row = mysql_fetch_array($result)){
                  array_push($rows,$row);
              }
              //mysqli_close($con);
              return $rows;

          }
          catch(Exception $e){
              $response = array();
              $response['status'] = false;
              $response['message'] = $e->getMessage();
              $this->response($this->json($response), 200);
          }
        }
        public function executeGenericDMLQuery($query){
            try{
                $result = mysql_query($query, $this->db);
                if(mysql_errno($this->db) != 0){
                    throw new Exception("Error   :".mysql_errno($this->db)."   :  ".mysql_error($this->db));
                }
								return mysql_affected_rows(); // return the affected rows
            }
            catch(Exception $e){
                $response = array();
                $response['status'] = false;
                $response['message'] = $e->getMessage();
                //echo json_encode($response);
                $this->response($this->json($response), 200);
            }
        }
        public function executeGenericInsertQuery($query){
            try{
                $result = mysql_query($query, $this->db);
                if(mysql_errno($this->db) != 0){
                    throw new Exception("Error   :".mysql_errno($this->db)."   :  ".mysql_error($this->db));
                }
                return mysql_insert_id($this->db);
            }
            catch(Exception $e){
                $response = array();
                $response['status'] = false;
                $response['message'] = $e->getMessage();
                //echo json_encode($response);
                $this->response($this->json($response), 200);
            }
        }
				public function sendResponse($statusCode,$status,$message = null ,$data = null){
					$response = array();
					$response['statusCode'] = $statusCode;
					$response['status'] = $status;
					$response['message'] = $message;
					$response['data'] = $data;
					$this->response($this->json($response), 200);
        }
				public function sendResponse2($statusCode,$message = null ,$data = null){
					$response = array();
					$response[$statusCode] = $this->errorCodes[$statusCode];
					$response['message'] = $message;
					$response['data'] = $data;
					$this->response($this->json($response), 200);
        }
        public function clearArray($arr){
            unset($arr);
            $arr = array();
            return $arr;
        }
        public function getUsers(){
        	  $sql = "SELECT * FROM ".self::usersTable;
       			$rows = $this->executeGenericDQLQuery($sql);
						$users = array();
						for($i=0;$i<sizeof($rows);$i++)
	    			{
	    				$users[$i]['id'] = $rows[$i]['id'];
	    				$users[$i]['user_type'] = $rows[$i]['user_type'];
	    				$users[$i]['user_name'] = $rows[$i]['user_name'];
	    				$users[$i]['mobile'] = $rows[$i]['mobile'];
	    				$users[$i]['email'] = $rows[$i]['email'];
	    				$users[$i]['first_name'] = $rows[$i]['first_name'];
	    				$users[$i]['last_name'] = $rows[$i]['last_name'];
	    				$users[$i]['token'] = $rows[$i]['token'];
	    				$users[$i]['status'] = $rows[$i]['status'];
	    			}
    				$this->sendResponse(200,"success","",$users);
        }
				public function getUserById(){
        	  $sql = "SELECT * FROM ".self::usersTable;
        	  $sql .= " where id=".$this->_request['id'];
       			$rows = $this->executeGenericDQLQuery($sql);
						$user = array();
    				$user['id'] = $rows[0]['id'];
    				$user['user_type'] = $rows[0]['user_type'];
    				$user['user_name'] = $rows[0]['user_name'];
    				$user['mobile'] = $rows[0]['mobile'];
    				$user['email'] = $rows[0]['email'];
    				$user['first_name'] = $rows[0]['first_name'];
    				$user['last_name'] = $rows[0]['last_name'];
    				$user['token'] = $rows[0]['token'];
    				$user['status'] = $rows[0]['status'];
  					$this->sendResponse(200,"success","",$user);
        }
				public function getUserDetails(){
						$token = $this->_request['token'];
						$sql = "SELECT * FROM ".self::usersTable;
						$sql .= " where token='$token'";
						$rows = $this->executeGenericDQLQuery($sql);
						$user = array();
						$user['id'] = $rows[0]['id'];
						$user['user_type'] = $rows[0]['user_type'];
						$user['user_name'] = $rows[0]['user_name'];
						$user['mobile'] = $rows[0]['mobile'];
						$user['email'] = $rows[0]['email'];
						$user['first_name'] = $rows[0]['first_name'];
						$user['last_name'] = $rows[0]['last_name'];
						$user['token'] = $rows[0]['token'];
						$user['status'] = $rows[0]['status'];
						$this->sendResponse(200,"success","",$user);
				}

				public function register(){
					$user_data = $this->_request['user_data'];
					$user_name = $user_data['user_name'];
					$password = md5($user_data['password']);
					$email = $user_data['email'];
					$first_name = $user_data['first_name'];
					$last_name = $user_data['last_name'];
					$mobile = $user_data['mobile'];
					$user_type = $user_data['user_type'];
					$status = 0; //0 for inactive and 1 for active
					$sql = "INSERT INTO ".self::usersTable."(user_type, user_name, mobile, password, email, first_name, last_name,status) VALUES ('$user_type','$user_name','$mobile','$password','$email','$first_name','$last_name','$status')";
					$rows = $this->executeGenericDMLQuery($sql);
					$this->sendResponse(200,"success","Successfully added");
        }
				public function login() {
					if(!isset($this->_request['user_name']) || !isset($this->_request['password']))
						$this->sendResponse(202,"failed","validation Error","Invalid user name or password");
					$user_name = $this->_request['user_name'];
					$password = md5($this->_request['password']);
					$token = $this->generateRandomString();
					$sql = "update ".self::usersTable." set token='$token' where user_name='$user_name'";
					$result = $this->executeGenericDMLQuery($sql);
					if($result){
						$sql = "select * from ".self::usersTable." where user_name = '$user_name' and password = '$password' limit 1";
						$rows = $this->executeGenericDQLQuery($sql);
						if(sizeof($rows)){
								$users = array();
								if($rows[0]['status'] == '1'){
									$users[0]['id'] = $rows[0]['id'];
									$users[0]['user_type'] = $rows[0]['user_type'];
									$users[0]['user_name'] = $rows[0]['user_name'];
									$users[0]['mobile'] = $rows[0]['mobile'];
									$users[0]['email'] = $rows[0]['email'];
									$users[0]['first_name'] = $rows[0]['first_name'];
									$users[0]['last_name'] = $rows[0]['last_name'];
									$users[0]['token'] = $rows[0]['token'];
									$users[0]['status'] = $rows[0]['status'];

									$this->sendResponse(200,'success',$this->messages['loginSuccess'],$users);
								}
								else {
									$this->sendResponse(202,"failed","You are not a active user contact to admin");
								}
						}
						else {
							$this->sendResponse(201,"failure","fail");
						}
					}
					else{
						$this->sendResponse(202,"validation Error","Invalid user name or password");
					}
        }
				public function logout(){
					if(isset($this->_request['token'])){
						$token = $this->_request['token'];
						$sql = "update ".self::usersTable." set token='' where token='$token'";
						$result = $this->executeGenericDMLQuery($sql);
						if($result){
							$this->sendResponse2(200,$this->messages['userLogout']);
						}
					}
				}
				public function changePassword(){
					if(isset($this->_request['token'])){
						$token = $this->_request['token'];
						$password = md5($this->_request['password']);
						$sql = "update ".self::usersTable." set password='$password' where token='$token'";
						$result = $this->executeGenericDMLQuery($sql);
						if($result){
							$this->sendResponse2(200,$this->messages['changedPassword']);
						}
					}
				}
				public function updateProfile(){
					$user_data = isset($this->_request['user_data']) ? $this->_request['user_data'] : $this->_request;
					$email = isset($user_data['email']) ? $user_data['email'] : '';
					$first_name = isset($user_data['first_name']) ? $user_data['first_name'] : '';
					$last_name = isset($user_data['last_name']) ? $user_data['last_name'] : '';
					$mobile = isset($user_data['mobile']) ? $user_data['mobile'] : '';
					$token = $user_data['token'];
							$previous = false;
							$sql = "update ".self::usersTable." set ";
							if(isset($user_data['email'])){
								$sql .="email ='$email'";
								$previous = true;
							}
							if(isset($user_data['first_name'])){
								$comma = ($previous) ? ',' : '';
								$sql .="$comma first_name ='$first_name'";
								$previous = true;
							}
							if(isset($user_data['last_name'])){
								$comma = ($previous) ? ',' : '';
								$sql .="$comma last_name ='$last_name'";
								$previous = true;
							}
							if(isset($user_data['mobile'])){
								$comma = ($previous) ? ',' : '';
								$sql .="$comma mobile ='$mobile'";
								$previous = true;
							}
							$sql .= " where token='$token'";
							$result = $this->executeGenericDMLQuery($sql);
							if($result){
								$this->sendResponse2(200,$this->messages['userUpdated']);
							}
				}
				public function checkPassword(){
					if(isset($this->_request['password']) && isset($this->_request['token'])){
						$cpass = $this->_request['password'];
						$token = $this->_request['token'];
						$sql = "SELECT password FROM ".self::usersTable." where token='$token'";
						$rows = $this->executeGenericDQLQuery($sql);
						$users = array();
						if($rows[0]['password'] == md5($cpass)){
							$this->sendResponse(200,"success","ok");
						}
						else{
							$this->sendResponse(201,"failure","fail");
						}
					}
				}
				public function upload() {
					$headers = apache_request_headers(); // to get all the headers
					$accessToken = $headers['accessToken'];
					// fetching the details of the plan
					$name = $this->_request['name'];
					$date = $this->_request['date'];
					// $date = date("m/d/Y", strto	time($this->_request['date']));
				  // $date = date("Y-m-d H:i:s",);//getdate($this->_request['date']);//date("Y-m-d",$this->_request['date']);//$this->_request['date']; //date('d-m-Y', $this->_request['date']);
					$regdNo = $this->_request['regdNo'];

					// getting the file information
					$file_name = $_FILES['file']['name'];
			    $file_size =$_FILES['file']['size'];
			    $file_tmp =$_FILES['file']['tmp_name'];
			    $file_type=$_FILES['file']['type'];
			    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
			    $extensions = array("pdf");

			    if(in_array($file_ext,$extensions )=== false){
			     $this->sendResponse(201,"Error","Its allow only pdf file");
			    }
			    if($file_size > 2097152){
			    	// $errors[]='File size cannot exceed 2 MB';
						$this->sendResponse(201,"Error","File size cannot exceed 2 MB");
					}
			    if(empty($errors)==true){
			        move_uploaded_file($file_tmp,self::planUploadPath.$file_name);
							$filePath = self::planUploadPath.$file_name;

							// getting the user id details from token
							$sql = "select id from ".self::usersTable." where token = '$accessToken'";
							$rows = $this->executeGenericDQLQuery($sql);
							$userId = $rows[0]['id'];
							// saving file and the building plan details in the database
							$sql = "insert into ".self::planTable."(user,name,file_path,regdNo,date) values('$userId','$name','$filePath','$regdNo','$date')";
							// echo $sql;
							$rows = $this->executeGenericDMLQuery($sql);
							$this->sendResponse(200,"success",$this->messages['dataSaved']);
			    }else{
			        print_r($errors);
			    }
				}
				public function updatePlan() {
					$headers = apache_request_headers(); // to get all the headers
					$accessToken = $headers['accessToken'];
					$plan_id = $this->_request['id'];
					$name = $this->_request['name'];
					$date = $this->_request['date'];
					$regdNo = $this->_request['regdNo'];
					if($accessToken){
						$sql = "update ".self::planTable." set name = '$name', regdNo = '$regdNo', date= '$date',status = 'pending'";
						if(isset($_FILES['file'])){
							$file_name = $_FILES['file']['name'];
							$file_size = $_FILES['file']['size'];
							$file_tmp = $_FILES['file']['tmp_name'];
							$file_type= $_FILES['file']['type'];
							$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
							$extensions = array("pdf");
							if(in_array($file_ext,$extensions )=== false){
							 $this->sendResponse(201,"Error","Its allow only pdf file");
							}
							if($file_size > 2097152){
								$this->sendResponse(201,"Error","File size cannot exceed 2 MB");
							}
							move_uploaded_file($file_tmp,self::planUploadPath.$file_name);
							$filePath = self::planUploadPath.$file_name;
							$sql .= ", file_path='$filePath'";
						}
						$sql .= " where id=".$plan_id;
						$result = $this->executeGenericDMLQuery($sql);
						if($result){
							$this->sendResponse2(200,$this->messages['userUpdated']);
						}
					}
				}
				public function allBuildingPlan(){
					$headers = apache_request_headers(); // to get all the headers
					$accessToken = $headers['accessToken'];
					if($accessToken){
						$sql = "select id,user_type from ".self::usersTable." where token = '$accessToken'";
						$rows = $this->executeGenericDQLQuery($sql);
						$userId = $rows[0]['id'];
						$usertype = $rows[0]['user_type'];
					}
					// SELECT  FROM buiding_plan INNER JOIN users ON buiding_plan.user = users.id WHERE buiding_plan.user = 29
					$sql = "SELECT buiding_plan.id, buiding_plan.user, buiding_plan.name, buiding_plan.regdNo, buiding_plan.file_path, buiding_plan.status, buiding_plan.remark, buiding_plan.date, buiding_plan.asset_name, users.first_name, users.last_name FROM ".self::planTable." INNER JOIN users ON buiding_plan.user = users.id";
					if($usertype && $usertype == 3)
						$sql .= " where buiding_plan.user=".$userId;
					if($usertype && $usertype == 2)
						$sql .= " where buiding_plan.status='pending'";
					$rows = $this->executeGenericDQLQuery($sql);
					$plan = array();
					for($i = 0; $i < sizeof($rows); $i++) {
						$plan[$i]['id'] = $rows[$i]['id'];
						$plan[$i]['user_id'] = $rows[$i]['user'];
						$plan[$i]['name'] = $rows[$i]['name'];
						$plan[$i]['regdNo'] = $rows[$i]['regdNo'];
						$plan[$i]['filepath'] = $rows[$i]['file_path'];
						$plan[$i]['status'] = $rows[$i]['status'];
						$plan[$i]['remark'] = $rows[$i]['remark'];
						$plan[$i]['date'] = $rows[$i]['date'];
						$plan[$i]['asset_name'] = $rows[$i]['asset_name'];
						$user['first_name'] = $rows[$i]['first_name'];
						$user['last_name'] = $rows[$i]['last_name'];
						$plan[$i]['users'] = $user;
					}
					$this->sendResponse2(200,$this->messages['dataFetched'],$plan);
				}
				public function buildingPlanByID(){
					$headers = apache_request_headers(); // to get all the headers
					$accessToken = $headers['accessToken'];
					$id = $this->_request['id'];
					if($accessToken){
						$sql = "select id,user_type from ".self::usersTable." where token = '$accessToken'";
						$rows = $this->executeGenericDQLQuery($sql);
						$usertype = $rows[0]['user_type'];
						$sql = "SELECT * FROM ".self::planTable." where id=".$id;
						$rows = $this->executeGenericDQLQuery($sql);
						$plan['id'] = $rows[0]['id'];
						$plan['user_id'] = $rows[0]['user'];
						$plan['name'] = $rows[0]['name'];
						$plan['regdNo'] = $rows[0]['regdNo'];
						$plan['filepath'] = $rows[0]['file_path'];
						$plan['status'] = $rows[0]['status'];
						$plan['remark'] = $rows[0]['remark'];
						$plan['date'] = $rows[0]['date'];
						$plan['asset_name'] = $rows[0]['asset_name'];
						$this->sendResponse2(200,$this->messages['dataFetched'],$plan);
					}
				}
				public function updateAcceptance() {
					$plan_data = isset($this->_request['data']) ? $this->_request['data'] : $this->_request;
					$plan_id = $plan_data['id'];
					$plan_status = $plan_data['status'];
					$plan_remark = $plan_data['remark'];
					$verifier_id = $plan_data['verifier_id'];
					$sql = "update ".self::planTable." set status = '$plan_status', remark = '$plan_remark', verifier_id=".$verifier_id." where id=".$plan_id;
					$result = $this->executeGenericDMLQuery($sql);
					if($result){
						$this->sendResponse2(200,$this->messages['userUpdated']);
					}
				}
				public function planCount() {
					$headers = apache_request_headers(); // to get all the headers
					$accessToken = $headers['accessToken'];
					if($accessToken){
						$sql = "select id,user_type from ".self::usersTable." where token = '$accessToken'";
						$rows = $this->executeGenericDQLQuery($sql);
						$userId = $rows[0]['id'];
						$usertype = $rows[0]['user_type'];
					}
					$planCount = array();
					$sql = "SELECT * FROM ".self::planTable." where status = 'rejected'";
					if($usertype && $usertype == 2)
						$sql .=" AND verifier_id=".$userId;
					else if($usertype && $usertype == 3)
						$sql .=" AND user=".$userId;
					$result = $this->executeGenericDQLQuery($sql);
					$planCount['rejected'] = sizeof($result);

					$sql = "SELECT * FROM ".self::planTable." where status = 'approved'";
					if($usertype && $usertype == 2)
						$sql .=" AND verifier_id=".$userId;
					else if($usertype && $usertype == 3)
						$sql .=" AND user=".$userId;
					$result = $this->executeGenericDQLQuery($sql);
					$planCount['approved'] = sizeof($result);

					$sql = "SELECT * FROM ".self::planTable." where status = 'pending'";
					if($usertype && $usertype == 3)
						$sql .=" AND user=".$userId;
					$result = $this->executeGenericDQLQuery($sql);
					$planCount['pending'] = sizeof($result);
					$this->sendResponse(200,'success','No.of count on all plan List',$planCount);
				}
				/*
				* Function : getPlans
				This function can be used to give various results based on the parameters passed to it

				#  plans based on the status - if 'status' send as the parameter
				#  (YET TO implemented) All the plans if status is not mentioned - This will be the case for the verifier and the super admin
				#  (YET TO implemented) Plans based on the user Id - If the user id is mentioned

				*/
				public function getPlans() {
				$headers = apache_request_headers(); // to get all the headers
						$accessToken = $headers['accessToken'];
						$status = $this->_request['status'];
						if($accessToken){
							$sql = "select id,user_type from ".self::usersTable." where token = '$accessToken'";
							$rows = $this->executeGenericDQLQuery($sql);
							$userId = $rows[0]['id'];
							$usertype = $rows[0]['user_type'];
						}
					$sql = "SELECT * FROM ".self::planTable;
					if(isset($this->_request['status'])){
						$sql.=" where status='$status'";
					}
					if($usertype && $usertype == 2 && $status != 'pending')
 						$sql .=" AND verifier_id=".$userId;
 					else if($usertype && $usertype == 3)
 						$sql .=" AND user=".$userId;
					$rows = $this->executeGenericDQLQuery($sql);
					$data = array();
					for($i=0;$i<sizeof($rows);$i++){
						$data[$i]['id'] = $rows[$i]['id'];
						$data[$i]['user'] = $rows[$i]['user'];
						$data[$i]['name'] = $rows[$i]['name'];
						$data[$i]['regdNo'] = $rows[$i]['regdNo'];
						$data[$i]['file_path'] = $rows[$i]['file_path'];
						$data[$i]['status'] = $rows[$i]['status'];
						$data[$i]['verifier_id'] = $rows[$i]['verifier_id'];
						$data[$i]['remark'] = $rows[$i]['remark'];
						$data[$i]['date'] = $rows[$i]['date'];
						$data[$i]['asset_name'] = $rows[$i]['asset_name'];
					}
					$this->sendResponse2(200,$this->messages['dataFetched'],$data);
				}
				public function user() {
						if(!isset($this->_request['operation']))
							$this->sendResponse2(400,$this->messages['operationNotDefined']);
						$sql = null;
						switch ($this->_request['operation']) {
							case 'create':
								$user_data = $this->_request['user_data'];
								$user_name = $user_data['user_name'];
								$password = md5($user_data['password']);
								$email = $user_data['email'];
								$first_name = $user_data['first_name'];
								$last_name = $user_data['last_name'];
								$mobile = $user_data['mobile'];
								$user_type = $user_data['user_type'];
								$status = 0; //0 for inactive and 1 for active
								$sql = "INSERT INTO ".self::usersTable."(user_type, user_name, mobile, password, email, first_name, last_name,status) VALUES ('$user_type','$user_name','$mobile','$password','$email','$first_name','$last_name','$status')";
								// echo $sql;
								$rows = $this->executeGenericDMLQuery($sql);
								$this->sendResponse2(200,$this->messages['userCreated']);
								break;
							case 'update':
							$user_data = isset($this->_request['user_data']) ? $this->_request['user_data'] : $this->_request;
							$user_name = isset($user_data['user_name']) ? $user_data['user_name'] : '';
							$password = isset($user_data['password']) ? md5($user_data['password']) : '';
							$email = isset($user_data['email']) ? $user_data['email'] : '';
							$first_name = isset($user_data['first_name']) ? $user_data['first_name'] : '';
							$last_name = isset($user_data['last_name']) ? $user_data['last_name'] : '';
							$mobile = isset($user_data['mobile']) ? $user_data['mobile'] : '';
							$status = isset($user_data['status']) ? $user_data['status'] : '';
									$previous = false;
									$sql = "update ".self::usersTable." set ";
									if(isset($user_data['user_name'])){
										$previous = true;
										$sql .="user_name ='$user_name'";
									}
									if(isset($user_data['password'])){
										$comma = ($previous) ? ',' : '';
										$sql .="$comma password ='$password' ";
										$previous = true;
									}
									if(isset($user_data['email'])){
										$comma = ($previous) ? ',' : '';
										$sql .="$comma email ='$email'";
										$previous = true;
									}
									if(isset($user_data['first_name'])){
										$comma = ($previous) ? ',' : '';
										$sql .="$comma first_name ='$first_name'";
										$previous = true;
									}
									if(isset($user_data['last_name'])){
										$comma = ($previous) ? ',' : '';
										$sql .="$comma last_name ='$last_name'";
										$previous = true;
									}
									if(isset($user_data['mobile'])){
										$comma = ($previous) ? ',' : '';
										$sql .="$comma mobile ='$mobile'";
										$previous = true;
									}
									if(isset($user_data['status'])){
										$comma = ($previous) ? ',' : '';
										$sql .="$comma status = $status";
									}
									$sql .= " where id=".$user_data['id'];
									// $user_type = $user_data['user_type'];
									// $status = 0; //0 for inactive and 1 for active
									// echo $sql;
									$result = $this->executeGenericDMLQuery($sql);
									if($result){
										$this->sendResponse2(200,$this->messages['userUpdated']);
									}
								break;
							case 'delete':
							$user_data = isset($this->_request['user_data']) ? $this->_request['user_data'] : $this->_request;
							  $sql = "delete from ".self::usersTable. " where id=".$user_data['id'];
								$result = $this->executeGenericDMLQuery($sql);
								if($result){
									$this->sendResponse2(200,$this->messages['deleted']);
								}
								break;
							// this will fetche the all the users and the single user data if the id is mention for the user
							case 'get':
								$user_data = isset($this->_request['user_data']) ? $this->_request['user_data'] : $this->_request;
								$sql = "SELECT * FROM ".self::usersTable;
								if(isset($user_data['id']))
									$sql .= " where id=".$user_data['id'];
								$rows = $this->executeGenericDQLQuery($sql);
								$users = array();
								for($i=0;$i<sizeof($rows);$i++)
								{
									$users[$i]['id'] = $rows[$i]['id'];
									$users[$i]['user_type'] = $rows[$i]['user_type'];
									$users[$i]['user_name'] = $rows[$i]['user_name'];
									$users[$i]['mobile'] = $rows[$i]['mobile'];
									$users[$i]['email'] = $rows[$i]['email'];
									$users[$i]['first_name'] = $rows[$i]['first_name'];
									$users[$i]['last_name'] = $rows[$i]['last_name'];
									$users[$i]['token'] = $rows[$i]['token'];
									$users[$i]['status'] = $rows[$i]['status'];
								}
								$this->sendResponse2(200,$this->messages['dataFetched'],$users);
								break;
							default:
									$this->sendResponse2(400,$this->messages['operationNotDefined']);
						}
				}
			}

	$api = new API;
	$api->processApi();
?>
