<?php
include "database.php";

class Grup
{
	function koneksi(){
		$this->database = new Database();
		$this->database->connectToDatabase();
	}

	function createNewGroup($groupName){
		$this->koneksi();
		$query = "INSERT INTO meeting_groups (group_name) 
                  VALUES ('$groupName')";
		$this->database->execute($query);
	}

	function getAllGrup(){
		$this->koneksi();
		$query = "SELECT group_id, group_name 
				  FROM meeting_groups";
	   	$this->database->execute($query);
		return $this->database->result;	
	}

	function displayAllGrup(){
		foreach($this->getAllGrup() as $c){
			echo $c['group_name']."\n";
		}
	}
}

$grup = new Grup();
$grup->displayAllGrup();

$namaGrup = "Prodi Teknologi Pangan";
$grup->createNewGroup($namaGrup);
