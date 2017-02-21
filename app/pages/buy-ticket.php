<?php
// Checks the user-login-status
$role = -1;
if(!isset($_SESSION['Role']))
{
	// If not logged-in - show the login-form.
	?>
	<script>
		$('.login').fadeIn('300');
		$('.login-form-processing').fadeOut('300');
		$('.login-form-content').fadeIn('300');
	</script>
	<?php
}
else
{
	// go
	$role = $_SESSION['Role'];
	if($role == 1 || $role == 2)
	{
		include 'php/read.php';
		$id = $_POST["event-id"];
		$event = getEvents($id)[0];
	?>
	<h3>Tickets für '<?php echo $event['ShortName']; ?>'</h3>
	
	<?php
	}
}
?>