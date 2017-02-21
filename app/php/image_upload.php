<?php
// Uploads a image to the img/ directory.
if(isset($_FILES['file']))
{
	$source_path = $_FILES['file']['tmp_name'];
	$target_path = "../img/".$_FILES['file']['name'];
	move_uploaded_file($source_path, $target_path);
}
?>