<?php
require_once ("database.php");

class Member
{
	function koneksi(){
		$this->database = new Database();
		$this->database->connectToDatabase();
	}

	//Mendapatkan seluruh data members
	//untuk keperluan secara umum
	function getAllEmailByGroupMembers($groupID){
		$this->koneksi();
		foreach($this->getMaxRowsAndGroupIDOFMeetings() as $c){
			$query = "SELECT mb.member_email
					  FROM meeting_groups mg JOIN members mb JOIN group_members gm
					  ON mg.group_id=gm.group_id 
					  AND mb.member_id=gm.member_id
					  WHERE mg.group_id='$c[group_id]'";
	      	$this->database->execute($query);
	      	return $this->database->result;	
		}
	}

	function getAllMachineByGroupMemberID($groupID){
		$this->koneksi();
			$query = "SELECT fm.machine_id, 
						     fm.group_id, 
					         fm.max_id_numbers
					  FROM fingerprint_machine fm 
					  WHERE fm.group_id='$groupID'";
	      	$this->database->execute($query);
	      	return $this->database->result;	
	}	

	function getAllNameByGroupMembers($groupID){
		$this->koneksi();
		$query = "SELECT mb.member_name
				  FROM meeting_groups mg JOIN members mb JOIN group_members gm
				  ON mg.group_id=gm.group_id 
				  AND mb.member_id=gm.member_id
				  WHERE mg.group_id='$groupID'";
      	$this->database->execute($query);
      	return $this->database->result;	
	}

	function getMemberNameByID($memberID){
		$this->koneksi();
		$query = "SELECT member_name, member_id
				  FROM members
				  WHERE member_id='$memberID'";
		$this->database->execute($query);
		return $this->database->result;
	}

	function getGroupIDByMemberID($memberID){
		$this->koneksi();
		$query = "SELECT mg.group_id, mg.group_name
				  FROM meeting_groups mg JOIN members mb JOIN group_members gm
				  ON mg.group_id=gm.group_id 
				  AND mb.member_id=gm.member_id
				  WHERE mb.member_id='$memberID'";
      	$this->database->execute($query);
      	return $this->database->result;	
	}

} //Class