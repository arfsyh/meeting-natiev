<?php
require_once ('database.php');
require_once ('member.php');

/**
 * Halaman dashboard atau landing page setelah login
 */
class Fingerprint
{
	//akan di dalam database

	function koneksi(){
		$this->database = new Database();
		$this->database->connectToDatabase();
		$this->member = new Member();
	}

	function maxNumberIDs(){
		return $maxNumberFingerPrintID = 127; 
	}

	function printAllMembersNameByGroupID($groupID){
		$this->koneksi();
		foreach($this->member->getAllNameByGroupMembers($groupID) as $c) {
			echo $c['member_name']."<br>";
		}
	}

	function printMemberNameByID($memberID){
		$this->koneksi();
		foreach($this->member->getMemberNameByID($memberID) as $c){
			return $c['member_name'];
		}
	}

	function printMemberID($memberID){
		$this->koneksi();
		foreach($this->member->getMemberNameByID($memberID) as $c){
			return $c['member_id'];
		}
	}

	function getGroupIDByMemberID($memberID){
		$this->koneksi();
		foreach($this->member->getGroupIDByMemberID($memberID) as $c){
			return $c['group_id'];
		}
	}

	function getGroupNameByMemberID($memberID){
		$this->koneksi();
		foreach($this->member->getGroupIDByMemberID($memberID) as $c){
			return $c['group_name'];
		}
	}

	function countIDsUsageByMachineID($machineID){
		$this->koneksi();
		$query = "SELECT COUNT(fingerprint_code) AS jumID, fingerprint_code
				  FROM member_fingerprint
				  WHERE fingerprint_machine_id='$machineID'";
      	$this->database->execute($query);
      	return $this->database->result;
	}
	function printCountIDsUsageByMachineID($machineID){
		foreach ($this->countIDsUsageByMachineID($machineID) as $key) {
			return $key['jumID'];
		}
	}

	function countRemainingFingerPrintID($machineID){
		return $remainingID = $this->maxNumberIDs() - $this->printCountIDsUsageByMachineID($machineID);
	}

	function getAllMachineIDs(){
		$this->koneksi();
		$query = 'SELECT machine_id, machine_code, max_id_numbers
				  FROM fingerprint_machine';
		$this->database->execute($query);
		return $this->database->result;
	}

	function getAllMachineBelongsToByGroupID($groupID){
		$this->koneksi();
		$query = "SELECT machine_id, max_id_numbers
				  FROM fingerprint_machine
				  WHERE group_id='$groupID'";
		$this->database->execute($query);
		return $this->database->result;
	}
	function printMaxID($groupID){
		foreach ($this->getAllMachineBelongsToByGroupID($groupID) as $key) {
			return $key['max_id_numbers'];
		}
	}
	function printMachineID($groupID){
		foreach ($this->getAllMachineBelongsToByGroupID($groupID) as $key) {
			return $key['machine_id'];
		}
	}


		function getAllMachineBelongsToByGroupIDs($groupID){
		$this->koneksi();
		$query = "SELECT fm.machine_id, fm.max_id_numbers, mf.fingerprint_code
				  FROM fingerprint_machine fm
				  JOIN member_fingerprint mf
				  ON fm.machine_id = mf.fingerprint_machine_id
				  WHERE fm.group_id='$groupID'";
		$this->database->execute($query);
		return $this->database->result;
	}

	function countUsageAllFingerPrintID($machineID){
		$this->koneksi();
			$query = "SELECT COUNT(fingerprint_code) AS jumCode
					  FROM member_fingerprint
					  WHERE fingerprint_machine_id = '$machineID'";
			$this->database->execute($query);
			return $this->database->result;
	}

	function getAllDataFingerprintByMachineID($machineID){
		$this->koneksi();
		$query = "SELECT max_id_numbers
				  FROM fingerprint_machine
				  WHERE machine_id='$machineID'";
		$this->database->execute($query);
		return $this->database->result;		
	}

	function getNumberRowsAllMemberFingerprintIDByMemberID($memberID){
		$this->koneksi();
		$query = "SELECT fingerprint_code
				  FROM member_fingerprint
				  WHERE member_id='$memberID'
				  ORDER BY fingerprint_code ASC";
		$this->database->execute($query);
		return $this->database->result->num_rows;
	}
	function getAllMemberFingerprintIDByMemberID($memberID){
		$this->koneksi();
		$query = "SELECT fingerprint_code
				  FROM member_fingerprint
				  WHERE member_id='$memberID'
				  ORDER BY fingerprint_code ASC";
		$this->database->execute($query);
		return $this->database->result;
	}

	function getAllMemberFingerprintIDForCheckBoxByMachineID($machineID){
		$this->koneksi();
		$query = "SELECT fingerprint_code
				  FROM member_fingerprint
				  WHERE fingerprint_machine_id='$machineID'";
		$this->database->execute($query);
		return $this->database->result;
	}
	function cetakAllMemberFingerPrintID($machineID){
		$allIDTerpakai = array();
		foreach ($this->getAllMemberFingerprintIDForCheckBoxByMachineID($machineID) as $key) {
			 $allIDTerpakai [] = $key['fingerprint_code'];
		}
		return $allIDTerpakai;
	}	

	function cetakAllFingerPrintID($maxNumberFingerPrintID){
		$daftarID = array();
		for($i=1; $i<=$maxNumberFingerPrintID; $i++) {
			$daftarID [] = $i;
		}
		return $daftarID;
	}

}//Class

$member = new Fingerprint();
$q = $_REQUEST["q"];
$jumlahKolom = 0;
echo $member->printMemberNameByID($q);
$memberID = $member->printMemberID($q);
echo "<br>";
echo "Group: ".$member->getGroupNameByMemberID($q)." [".$member->getGroupIDByMemberID($q)."]<br>";
echo "Fingerprint ID: ";
 	$numberRows = $member->getNumberRowsAllMemberFingerprintIDByMemberID($q);
	if($numberRows==0){
		echo "<font color=red>Belum punya Fingerprint ID</font><br>";
	}
 	foreach ($member->getAllMemberFingerprintIDByMemberID($q) as $key){
 		echo $key['fingerprint_code'].", ";	 			
 	}
	
	$groupID = $member->getGroupIDByMemberID($q);
	$maxNumberFingerPrintID = $member->printMaxID($groupID);
	$machineID = $member->printMachineID($groupID);

	echo "<br> Kode Mesin: ".$machineID;
 	foreach ($member->countUsageAllFingerPrintID($machineID) as $key) {
 		$elapsedID = $maxNumberFingerPrintID - $key['jumCode'];
 		echo " Terpakai: ".$key['jumCode']." Sisa : ".$elapsedID." ID<br>";
 	}

	//$member->insertNewMemberFingerPrintIDs($machineID,$memberID,$fingerprintCode)

	$arrIdTerpakai = $member->cetakAllMemberFingerPrintID($machineID);
 	$arrAllID = $member->cetakAllFingerPrintID($maxNumberFingerPrintID);
	$idTakTerpakai = array_diff($arrAllID, $arrIdTerpakai);
	echo "<form id='myForm' action='simpan_fingerprintcode.php' method='post'>";
	echo "<input type='text' name='machineID' value='$machineID' hidden>";
	echo "<input type='text' name='memberID' value='$memberID' hidden>";
	foreach ($idTakTerpakai as $c) {
		echo "<input type='checkbox' name='fingerprintCode[]' value='".$c."'>".$c." ";
	}
	echo "<br>";
	echo "<button id='enroll'>Enroll</button>";
	echo "</form>";

	echo "<span id='result'></span>"; ?>

	<!DOCTYPE html>
	<html>
	<head>
		<title></title>
	</head>
	<body>
	<script type="text/javascript" src="scripts/jquery-3.4.1.js"></script>
	<script type="text/javascript" src="scripts/insert.js"></script>	
	</body>
	</html>
<?php

?>