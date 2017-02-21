<?php
// This skript handles data modifying. This includes 'CREATE', 'UPDATE' and 'DELETE';
include 'read.php';
include 'database.php';
$returnValues = array (
	'success' => false,
	'message' => ""
);
if(isset($_POST['action']) && !empty($_POST['action']))
{
	$action = $_POST['action'];
	$parameter = array();
	parse_str($_POST['data'], $parameter);
	switch($action)
	{
		// Delegates the methods by their names.
		case 'saveEvent': saveEvent($parameter); break;
		case 'deleteEvent': deleteEvent($parameter); break;
		case 'saveProfile' : saveProfile($parameter); break;
	}
}
// Saves an event (Create, Update).
function saveEvent($params)
{
	$conn = getDatabase();
	if(isset($params["id"]))
	{
		$id = $params["id"];
	}
	$types = explode(',', $params["type"]);
	if(!isset($id))
	{
		// Creates the event
		$sql = "INSERT INTO events (ShortName, Description, Image, TicketCost, TicketQuantity, TicketsLeft, Location, EventStartDate, EventEndDate, Artist, isHot, IdUser) 
		VALUES ('".htmlspecialchars($params["short-name"])."','".htmlspecialchars($params["description"])."','".htmlspecialchars($params['image-path'])."','".htmlspecialchars($params["ticket-cost"])."','".htmlspecialchars($params["ticket-quantity"])."','".htmlspecialchars($params["ticket-quantity"])."',
			'".htmlspecialchars($params["location"])."','".htmlspecialchars($params["start-date"])."','".htmlspecialchars($params["end-date"])."','".htmlspecialchars($params["artist"])."',0,".htmlspecialchars($params['user-id']).");";
	}
	else
	{
		// Updates the event
		$sql = "UPDATE events SET ShortName='".htmlspecialchars($params["short-name"])."', Description='".htmlspecialchars($params["description"])."', Image='".htmlspecialchars($params['image-path'])."', TicketCost='".htmlspecialchars($params["ticket-cost"])."', 
		TicketQuantity='".htmlspecialchars($params["ticket-quantity"])."', Location='".htmlspecialchars($params["location"])."', EventStartDate='".htmlspecialchars($params["start-date"])."',
		EventEndDate='".htmlspecialchars($params["end-date"])."', Artist='".htmlspecialchars($params["artist"])."', isHot=0 WHERE Id = ".$id;
	}
	if($conn->query($sql) === TRUE)
	{
		// Get the Event
		if(!isset($id))
		{
			$sql = "SELECT * FROM events ORDER BY Id DESC LIMIT 1;";
		}
		else
		{
			$sql = "SELECT * FROM events WHERE Id = ".$id.";";
		}
		$result = $conn->query($sql);
		$results = array();
		while($row = mysqli_fetch_assoc($result))
		{
			$results[] = $row;
		}
		if(isset($results))
		{
			foreach ($types as $type) 
			{
				if($type != "")
				{
					// Check if type is already existing
					$existingType = $conn->query("SELECT * FROM eventtypes WHERE IdEvent=".$results[0]["Id"]." AND IdEventType=".$type)->fetch_assoc();
					if(!isset($existingType))
					{
						// If not, add the type
						$conn->query("INSERT INTO eventtypes (IdEvent, IdEventType) VALUES (".$results[0]["Id"].",".$type.");");
					}					
				}
			}
		}
		else
		{
			$returnValues['success'] = false;
			$returnValues['message'] = "Veranstaltung wurde ohne Typen gespeichert.";
			echo json_encode($returnValues);
			return false;
		}
		$returnValues['success'] = true;
		$returnValues['message'] = "Veranstaltung wurde gespeichert.";
		echo json_encode($returnValues);
		return true;
	}
	else
	{
		$returnValues['success'] = false;
		$returnValues['message'] = "Die Veranstaltung konnte nicht gespeichert werden.";
		echo json_encode($returnValues);
		return false;
	}
}

// Deletes an event.
function deleteEvent($params)
{
	if(!isset($params["id"]))
	{
		// Check params.
		$returnValues['success'] = false;
		$returnValues['message'] = "Veranstaltung's ID existiert nicht.";
		echo json_encode($returnValues);
		return false;
	}
	$conn = getDatabase();
	$id = $params["id"];
	// Gets the event.
	$event = $conn->query("SELECT * FROM events WHERE Id = ".$id.";");
	if(isset($event))
	{
		// Deletes the eventtypes associated with this event.
		if($conn->query("DELETE FROM eventtypes WHERE IdEvent = ".$id.";") === TRUE)
		{
			// Deletes the tickets associated with this event.
			if($conn->query("DELETE FROM ticket WHERE IdEvent = ".$id.";") === TRUE)
			{
				// Deletes the event itself.
				if($conn->query("DELETE FROM events WHERE Id = ".$id.";") === TRUE)
				{
					$returnValues['success'] = true;
					$returnValues['message'] = "Veranstaltung wurde aufgelöst.";
					echo json_encode($returnValues);
					return true;
				}
				else
				{
					$returnValues['success'] = false;
					$returnValues['message'] = "Veranstaltung konnte ncht gelöscht werden.";
					echo json_encode($returnValues);
					return false;
				}
			}
			else
			{
				$returnValues['success'] = false;
				$returnValues['message'] = "Die Veranstaltung konnte nicht gelöscht werden, da beim entfernen der Tickets ein Problem auftratt.";
				echo json_encode($returnValues);
				return false;
			}
		}
		else
		{
			$returnValues['success'] = false;
			$returnValues['message'] = $conn->getMessage();
			echo json_encode($returnValues);
			return false;
		}
	}
	else
	{
		$returnValues['success'] = false;
		$returnValues['message'] = "Veranstaltung wurde nicht gefunden.";
		echo json_encode($returnValues);
		return false;
	}
}

// Saves the user-profile. (Create, Update)
function saveProfile($params)
{
	$conn = getDatabase();
	// Check if the user already exists or is new registered
	if(isset($params['user-id']))
	{
		if($params['user-id']>=0 && $params['user-id'] != "undefined")
		{
			// Modify existing User
			// Check if the email already exists.
			$user = $conn->query("SELECT * FROM users WHERE email = '".$params['email']."' && Id <> ".$params['user-id']."  LIMIT 1;")->fetch_assoc();
			if(isset($user))
			{
				// This email already exists.
				$returnValues['success'] = false;
				$returnValues['message'] = "Diese E-Mail Adresse existiert bereits.";
				echo json_encode($returnValues);
				return false;
			}
			else
			{
				$user = $conn->query("SELECT * FROM users WHERE Id=".$params['user-id']);
				if(isset($user))
				{
					// Checks the password.
					if($params['password-one'] == $params['password-two'])
					{
						// User in DB found
						if($params['password-one'] != "")
						{
							// Update with new password
							$sql = "UPDATE users SET Name='".$params['name']."', FirstName='".$params['first-name']."', Email='".$params['email']."', Telephone='".$params['telephone']."',Password='".sha1(trim(strtolower($params['password-one'])))."' WHERE Id=".$params['user-id'].";";
						}
						else
						{
							// Update without new password.
							$sql = "UPDATE users SET Name='".$params['name']."', FirstName='".$params['first-name']."', Email='".$params['email']."', Telephone='".$params['telephone']."' WHERE Id=".$params['user-id'].";";
						}
						if($conn->query($sql) === TRUE)
						{
							// User successfuly modified
							$returnValues['success'] = true;
							$returnValues['message'] = "Profil erfolgreich aktualisiert.";
							echo json_encode($returnValues);
							return true;
						}
						else
						{
							// An error occured.
							$returnValues['success'] = false;
							$returnValues['message'] = $conn->error;
							echo json_encode($returnValues);
							return false;	
						}
					}
					else
					{
						// Passwords don't match
						$returnValues['success'] = false;
						$returnValues['message'] = "Passwörter sind nicht identisch.";
						echo json_encode($returnValues);
						return false;
					}
				}
				else
				{
					// Didn't find the user in the db.
					$returnValues['success'] = false;
					$returnValues['message'] = "Benutzer wurde nicht gefunden.";
					echo json_encode($returnValues);
					return false;
				}
			}
		}
	}
	// The user is new
	// Check if the email already exists.
	$user = $conn->query("SELECT * FROM users WHERE email = '".$params['email']."' LIMIT 1;")->fetch_assoc();
	if(isset($user))
	{
		// User already exists.
		$returnValues['success'] = false;
		$returnValues['message'] = "Diese E-Mail Adresse existiert bereits.";
		echo json_encode($returnValues);
		return false;
	}
	else
	{
		// Create a new user.
		if($conn->query("INSERT INTO users (Name, FirstName, Email, Telephone, Password) VALUES ('".$params['name']."','".$params['first-name']."','".$params['email']."','".$params['telephone']."','".sha1(trim(strtolower($params['passwordOne'])))."');") === TRUE)
		{
			// Check if the create was successfull.
			$user = $conn->query("SELECT * FROM users WHERE email = '".$params['email']."' LIMIT 1;")->fetch_assoc();
			if(isset($user))
			{
				$userId = $user["Id"];
				switch(isset($params['role']))
				{
					case true:
					$roleId = 1;
					break;
					case false:
					$roleId = 2;
					break;
				}
				// Sets the user-roles.
				if($conn->query("INSERT INTO userroles (IdUser, IdRole) VALUES (".$userId.",".$roleId.");") === TRUE)
				{
					$returnValues['success'] = true;
					$returnValues['message'] = "Registrierung erfolgreich. Sie können sich jetzt einloggen.";
					echo json_encode($returnValues);
					return true;
				}
			}
			else
			{
				$returnValues['success'] = false;
				$returnValues['message'] = "Registrierung fehlgeschlagen. Bitte versuche Sie es erneut.";
				echo json_encode($returnValues);
				return false;
			}
		}
		else
		{
			$returnValues['success'] = false;
			$returnValues['message'] = $conn->getMessage();
			echo json_encode($returnValues);
			return false;
		}
	}
}
?>