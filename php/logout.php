<?php
// Destroys the session.
session_start();
if(isset($_POST['action']) && !empty($_POST['action']))
{
	$action = $_POST['action'];
	if($action == "logout")
	{
		session_destroy();
		$returnValues['success'] = true;
		$returnValues['message'] = "Sie wurden erfolgreich ausgeloggt.";
		echo json_encode($returnValues);
		return true;
	}
}
?>