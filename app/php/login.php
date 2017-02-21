<?php
// Checks if the user exists in database and handles the login.
include 'database.php';
session_start();
$returnValues = array (
	'success' => false,
	'message' => ""
);
if(isset($_POST['action']) && !empty($_POST['action'])) 
{
	$action = $_POST['action'];
	if($action == "login") {
		$parameter = array();
		parse_str($_POST['data'], $parameter);
		$formEmail = trim(strtolower($parameter['email']));
		$formPw = sha1(trim(strtolower($parameter['password'])));

		try 
		{
			$conn = getDatabase();
		}
		catch(Exception $e)
		{
			$returnValues['success'] = false;
			$returnValues['message'] = $e->getMessage();
			echo json_encode($returnValues);
			return false;
		}
		if(!$conn->connect_error)
		{
			// Select the user from databse by email and by password.
			$user = $conn->query("SELECT Id FROM users WHERE email = '".$formEmail."' && password = '".$formPw."' LIMIT 1;")->fetch_assoc();
			if(isset($user["Id"]))
			{
				$_SESSION['User'] = $user["Id"];
				$userRole = $conn->query("SELECT IdRole FROM userroles WHERE IdUser = ".$user["Id"].";")->fetch_assoc();
				$_SESSION['Role'] = $userRole["IdRole"];
				$returnValues['success'] = true;
				$returnValues['message'] = "Sie wurden erfolgreich eingeloggt";
				echo json_encode($returnValues);
				return true;
			} 
			else 
			{
				$returnValues['success'] = false;
				$returnValues['message'] = "Benutzername / Passwort ist falsch.";
				echo json_encode($returnValues);
				return false;
			}
		}
	}
}
?>