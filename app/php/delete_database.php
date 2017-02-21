<?php
include 'database.php';
echo "Datenbank wird gelöscht...";
$conn = getDatabase();
if($conn->query("DROP DATABASE joelklinglereventdatabase") === TRUE)
{
	echo "<br />Datenbank gelöscht.";
}
else
{
	echo $conn->error;
}
?>