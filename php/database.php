<?php
// This is the database-config-file. If the database changes, the entries have to be updated.
if(!function_exists('getDatabase'))
{
	function getDatabase() {
		// Enables exception throwing for error handling.
		mysqli_report(MYSQLI_REPORT_STRICT);
		$username = 'root';
		$password = '';
		$host = 'localhost';
		$database = 'joelklinglereventdatabase';	
		if(!isset($conn))
		{
			try
			{
				$conn = new mysqli($host, $username, $password, $database);
			}
			catch(Exception $ex)
			{
				// The Database doesn't exist.
				// Create Database and all its tables.
				echo '<h1>Die Datenbank wird installiert</h1>';
				echo '<h3>Warten Sie bis alle Schritte abgelaufen sind und klicken Sie <a href="../ticketshop/index.php">Hier</a></h3>';
				include 'install.php';
				$conn = new mysqli($host, $username, $password, $database);
			}
			return $conn;
		}
	}
}
?>