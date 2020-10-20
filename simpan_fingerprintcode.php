<?php
require_once ('database.php');

class Simpan
{
	//akan di dalam database
	function koneksi(){
		$this->database = new Database();
		$this->database->connectToDatabase();
	}

	function insertNewMemberFingerPrintIDs($machineID,$memberID,$fingerprintCode){
		$this->koneksi();
		$query = "INSERT INTO member_fingerprint (fingerprint_machine_id, member_id, fingerprint_code)
			  	  VALUES ('$machineID', '$memberID', '$fingerprintCode')";
		$this->database->execute($query);
	}
}

$simpan = new Simpan();

$machineID 	= $_POST['machineID'];
$memberID 	= $_POST['memberID'];

if(isset($_POST['fingerprintCode'])){
	$fingerprintCode = $_POST['fingerprintCode'];
	foreach ($fingerprintCode as $enrollCode) {
		$simpan->insertNewMemberFingerPrintIDs($machineID,$memberID,$enrollCode);
	}
	echo "Berhasil Enroll ID: ";
	foreach ($fingerprintCode as $enrollCode) {
		echo "<b>".$enrollCode."</b> ";
	}

} else {
	echo "Fingerprint ID belum dipilih.";
}
?>