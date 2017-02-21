<?php
// This skript handles all read-operations.
include 'database.php';
// Read all Event types by an optional id $id.
if(!function_exists('getEventTypes'))
{
	function getEventTypes($id = 'a')
	{
		$conn = getDatabase();
		if($id == 'a')
		{
			$sql = "SELECT * FROM eventtype;";
		}
		else
		{
			$sql = "SELECT * FROM eventtype WHERE Id = ".$id.";";
		}
		$result = $conn->query($sql);
		$results = array();
		while($row = mysqli_fetch_assoc($result))
		{
			$results[] = $row;
		}
		return $results;
	}
}

if(!function_exists('getEventType'))
{
	// Gets the eventtype for an optional event id $id.
	function getEventType($id = 'a')
	{
		$conn = getDatabase();
		if($id == 'a')
		{
			$sql = "SELECT * FROM eventtypes;";
		}
		else
		{
			$sql = "SELECT * FROM eventtypes WHERE IdEvent = ".$id.";";
		}
		$result = $conn->query($sql);
		$results = array();
		while($row = mysqli_fetch_assoc($result))
		{
			$results[] = $row;
		}
		$types = [];
		foreach ($results as $event) {
			$res = getEventTypes($event["IdEventType"]);	
			array_push($types, $res);
		}
		return $types;
	}
}

if(!function_exists('getEvents'))
{
	// Read Events by an optional event-id $id.
	function getEvents($id = 'a')
	{
		$conn = getDatabase();
		if($id == 'a')
		{
			$sql = "SELECT * FROM events;";
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
		 return $results;	
	}
}

if(!function_exists('getEventsByUser'))
{
	// Read Events by an user $id
	function getEventsByUser($id = '-1')
	{
		$conn = getDatabase();
		$sql = "SELECT * FROM events WHERE IdUser = ".$id.";";
		$result = $conn->query($sql);
		$results = array();
		while($row = mysqli_fetch_assoc($result))
		{
			$results[] = $row;
		}
		return $results;	
	}
}

// Gets an user by an id $id.
if(!function_exists('getUser'))
{
	// Gets the user by Id
	function getUser($id = -1)
	{
		$conn = getDatabase();
		if($id != -1)
		{
			$sql = "SELECT * FROM users WHERE Id = ".$id;
			$results = $conn->query($sql);
			return $results->fetch_assoc();
		}
		return false;
	}
}
?>