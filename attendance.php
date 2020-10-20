<?php
require_once("database.php");
date_default_timezone_set('Asia/Jakarta');

/**
 * Class untuk menangani presensi peserta lewat sidik jari
 * 1. Peserta memindai sidik jari ke presensi
 * 2. Pemindai membaca sidik jari dan mengirimkan ID pemilik ke server
 * 3. Server menerima ID peserta
 * 4. Server mencocokkan ID dengan agenda meeting waktu saat itu
 * 5. Server mengubah status "Tidak hadir" menjadi "Hadir", lalu mencatat waktu saat itu
 * 6. Server mengirim pesan ke LCD berupa "Nama" dan "Jam Presensi" saat itu
 */
class Attendance
{
	function koneksi(){
		$this->database = new Database();
		$this->database->connectToDatabase();
	}
	
	function getAllMeetingNowForAttendanceCheck($memberID, $timenow, $dateToday){
		$this->koneksi();
		$query = "SELECT ma.status, mt.meeting_start, mt.meeting_end, ma.member_name,
						 mt.meeting_id, ma.member_id, mt.meeting_date,
						 ma.attendance_time, mt.meeting_name, ma.id, ma.status
					FROM meeting_attendance ma 
					JOIN meetings mt
					ON ma.meeting_id=mt.meeting_id
					WHERE ma.member_id='$memberID' 
					AND mt.meeting_date='$dateToday'
					AND mt.meeting_start <= '$timenow'
					AND mt.meeting_end >= '$timenow'
					AND ma.status='Tidak hadir'";
		$this->database->execute($query);
		return $this->database->result;
	}

	/* Mendapatkan field NAMA seorang peserta
	*/
	function getAttendanceName($memberID){
		$this->koneksi();
		$query = "SELECT member_name
					FROM members
					WHERE member_id='$memberID'"; 
		$this->database->execute($query);
		return $this->database->result;
	}

	/* Menampilkan NAMA seorang peserta
	*/
	function displayAttendanceName($memberID){
		foreach($this->getAttendanceName($memberID) as $c){
			return $c['member_name'];
		}
	}

	function countAllMeetingNowForAttendanceCheck($memberID, $timenow, $dateToday){
		$this->koneksi();
		$query = "SELECT COUNT(ma.status) AS jumStatus	
					FROM meeting_attendance ma 
					JOIN meetings mt
					ON ma.meeting_id=mt.meeting_id
					WHERE ma.member_id='$memberID' 
					AND mt.meeting_date='$dateToday'
					AND mt.meeting_start <= '$timenow'
					AND mt.meeting_end >= '$timenow'
					AND ma.status='Tidak hadir'";
		$this->database->execute($query);
		return $this->database->result;
	}

	function displayCountAllMeetingNowForAttendanceCheck($memberID, $timenow, $dateToday){
		foreach($this->countAllMeetingNowForAttendanceCheck($memberID, $timenow, $dateToday) as $c){
			return $c['jumStatus'];
		}
	}	

   function fingerPrintAttendance($memberID, $attendanceTime, $meetingID){
   		$this->koneksi();
    	//TODO: tambahkan validasi jika member sudah presensi
    	//jika presensi sebelum waktu meeting
        $query = "UPDATE meeting_attendance 
        		  SET status 			= 'Hadir',
        		  	  attendance_time	= '$attendanceTime'
                  WHERE meeting_id = '$meetingID' AND member_id = '$memberID'";
        $this->database->execute($query);
        $this->database->result;
    }

    function getMemberIDByFingerprintCodeMachineID($fingerprintCode,$machineID){
		$this->koneksi();
		$query = "SELECT 	member_id
				  FROM 		member_fingerprint
				  WHERE 	fingerprint_code='$fingerprintCode'
				  AND 		fingerprint_machine_id='$machineID'";
		$this->database->execute($query);
		return $this->database->result;
	}
	function cetakMemberID($fingerprintCode,$machineID){
		foreach($this->getMemberIDByFingerprintCodeMachineID($fingerprintCode,$machineID) as $c){
			return $c['member_id'];
		}
	}	
	function getNumrowsMemberIDByFingerprintCode($fingerprintCode){
		$this->koneksi();
		$query = "SELECT 	member_id
				  FROM 		member_fingerprint
				  WHERE 	fingerprint_code='$fingerprintCode'";
		$this->database->execute($query);
		return $this->database->result->num_rows;
	}
}

$attendance = new Attendance();

$timeAttendanceinNumbers = time();
$timeAttendanceinString = Date("H:i:s");
$dateAttendanceinString = Date("Y-m-d");
$dateAttendanceinNumbers = strtotime($dateAttendanceinString);

/*
* TODO: jika ada yg presensi tapi belum masuk waktunya/tidak ada meeting.
*/


$fingerprintCode = $_REQUEST["q"];
$numRows = $attendance->getNumrowsMemberIDByFingerprintCode($fingerprintCode);
$memberID = $attendance->cetakMemberID($fingerprintCode);
if($numRows==0){
	echo "Tidak ada";
} else {
	if($attendance->displayCountAllMeetingNowForAttendanceCheck($memberID, $timeAttendanceinString, $dateAttendanceinString)==0){
		echo $attendance->displayAttendanceName($memberID);
		echo "\n";
		echo "Sudah presensi!/Rapat belum dimulai";
	} else {
		foreach ($attendance->getAllMeetingNowForAttendanceCheck($memberID, $timeAttendanceinString, $dateAttendanceinString) as $c){
			$attendance->fingerPrintAttendance($memberID, $timeAttendanceinString, $c['meeting_id']);
			echo $c['member_name'] ." ".$c['meeting_name']."\n Pukul: ".$timeAttendanceinString."(".$timeAttendanceinNumbers.")\n";	
		} 	
	}
}

