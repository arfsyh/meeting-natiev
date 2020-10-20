<?php
require_once ('database.php');
date_default_timezone_set('Asia/Jakarta');

class Meetings
{
	private $result;

	function koneksi(){
		$this->database = new Database();
		$this->database->connectToDatabase();
	}

	/*
	* Menghitung jumlah meeting hari ini
	* 
	*/
	function getJumAllMeetingToday($dateToday){
		$this->koneksi();
		$query = "SELECT COUNT(mt.meeting_id) AS jumMeeting
				FROM meeting_groups mg 
				JOIN meetings mt
				ON mg.group_id=mt.group_id
				WHERE mt.meeting_date='$dateToday'";
		$this->database->execute($query);
		return $this->database->result;
	}

	/*
	* Mengambil seluruh data meeting hari ini, urut DESC berdasarkan meeting_start
	* 
	*/
	function getAllMeetingToday($dateToday){
		$this->koneksi();
		$query = "SELECT mg.group_name, mt.meeting_name, mt.meeting_date, mt.meeting_id,
						 mt.meeting_start, mt.meeting_end, mt.meeting_place, mg.group_id 
				FROM meeting_groups mg 
				JOIN meetings mt
				ON mg.group_id=mt.group_id
				WHERE mt.meeting_date='$dateToday'
				ORDER BY meeting_start DESC";
		$this->database->execute($query);
		return $this->database->result;
	}

	function displayJumAllMeetingToday($dateToday){
		foreach($this->getJumAllMeetingToday($dateToday) as $c){
			return $c['jumMeeting'];
		}
	}

	/*
	* Menghitung seluruh meeting hari ini
	* 
	*/
	function countAllMeetingToday($dateToday){
		$this->koneksi();
		$query = "SELECT COUNT(mt.meeting_id) AS jumMeetingToday
				FROM meeting_groups mg 
				JOIN meetings mt
				ON mg.group_id=mt.group_id
				WHERE mt.meeting_date='$dateToday'";
		$this->database->execute($query);
		return $this->database->result;
	}

	function displayCountAllMeetingToday($dateToday){
		foreach($this->countAllMeetingToday($dateToday) as $c){
			return $c['jumMeetingToday'];
		}
	}

	function getJumOverlapMeetingByDate($groupID, $meetingDate){
		$this->koneksi();
		$query = "SELECT COUNT(meeting_id) AS overlapDate
				  FROM meetings 
				  WHERE group_id='$groupID' AND meeting_date='$meetingDate'";
		$this->database->execute($query);
		return $this->database->result;
	}

	function displayJumOverlapMeetingByDate($groupID, $meetingDate){
		foreach($this->getJumOverlapMeetingByDate($groupID, $meetingDate) as $c){
			return $c['overlapDate'];
		}
	}

	function getOverlapMeetingByTime($groupID, $meetingDate){
		$this->koneksi();
		$query = "SELECT meeting_start, meeting_end
				  FROM meetings 
				  WHERE group_id='$groupID' AND meeting_date='$meetingDate'";
		$this->database->execute($query);
		return $this->database->result;		
	}

	function displayOverlapMeetingByTime($groupID, $meetingDate, $meetingStart){
		foreach($this->getOverlapMeetingByTime($groupID, $meetingDate) as $c){
			$meetingstartFromDB = strtotime($c['meeting_start']);
			$meetingendFromDB = strtotime($c['meeting_end']);
			$meetingStartFromUser = strtotime($meetingStart);
			if($meetingStartFromUser >= $meetingstartFromDB AND $meetingStartFromUser <= $meetingendFromDB){
					//echo "Sudah ada meeting. Cari waktu yang lain."; 
					return 1;
				 } else {
					//echo "Silakan buat meeting.";
					return 0;
			}			
		}
	}

	function getZeroMeeting($groupID, $meetingDate){
		$this->koneksi();
		$query = "SELECT COUNT(*) as zero
			   	  FROM meetings 
				  WHERE group_id='$groupID' AND meeting_date='$meetingDate'";
		$this->database->execute($query);
		return $this->database->result;		
	}

	function displayZeroMeeting($groupID, $meetingDate){
		foreach($this->getZeroMeeting($groupID, $meetingDate) as $xy){
			return $xy['zero'];
		}		
	}

	/*
	*	Menghitung jumlah meeting yang sama Group ID, Tanggal, Start dan End
	*	Berfungsi untuk mendeteksi meeting yang sama
	*/
	function countSameMeetingSchedule($groupID, $meetingDate, $meetingEnd, $meetingStart){
		$this->koneksi();
		$query = "SELECT COUNT(meeting_id) AS jumSameMeeting
				  FROM meetings 
				  WHERE group_id='$groupID' 
				  AND meeting_date='$meetingDate'
				  AND meeting_start='$meetingStart'
				  AND meeting_end='$meetingEnd'";
		$this->database->execute($query);
		return $this->database->result;		
	}

	/*
	*	Menampilkan jumlah meeting yang sama Group ID, Tanggal, Start dan End
	*	Berfungsi untuk mendeteksi meeting yang sama
	*/
	function displayCountSameMeetingSchedule($groupID, $meetingDate, $meetingEnd, $meetingStart){
		foreach($this->countSameMeetingSchedule($groupID, $meetingDate, $meetingEnd, $meetingStart) as $xy){
			return $xy['jumSameMeeting'];
		}		
	}	

	/*
	* Membuat agenda meeting baru serta membuat daftar hadir
	*/
	function createNewMeeting($groupID, $meetingName, $meetingDate, $meetingStart, $meetingEnd, $meetingPlace){
		$query = "INSERT INTO meetings (group_id, meeting_name, meeting_date, meeting_start, meeting_end, meeting_place) 
           		  VALUES ($groupID, '$meetingName', '$meetingDate', '$meetingStart','$meetingEnd', '$meetingPlace')";
		$this->database->execute($query);
		sleep(3);
		//$this->createAttendanceLists($groupID);
	}

	/**
	* Note: Belum mempertimbangkan multiple pengundang rapat
	* bisa jadi ID max Meeting bukan dari grup yang sama. Karena ada grup lain yang barusan
	* membuat acara.
	* PROBLEM CONCURRENCY
	* Solusi sementara harus difilter berdasarkan Group ID
	*/

	/* Mengambil ID Meeting/Event terbaru */
	function getMaxRowsAndGroupIDOFMeetings($groupID){
		$this->koneksi();
		$query = "SELECT meeting_id
				  FROM meetings 
				  WHERE group_id = '$groupID'
				  ORDER BY meeting_id DESC LIMIT 1";
		$this->database->execute($query);
		return $this->database->result;
	}

	/* Mengambil data member berdasarkan grup */
	function getAllGroupMembers($groupID){
		$this->koneksi();
		$query = "SELECT mb.member_name, mb.member_id
					  FROM meeting_groups mg JOIN members mb JOIN group_members gm
					  ON mg.group_id=gm.group_id 
					  AND mb.member_id=gm.member_id
					  WHERE mg.group_id='$groupID'";
	      	$this->database->execute($query);
	      	return $this->database->result;
	}

	function getAllEmailByGroupMembers($groupID){
		$this->koneksi();
		$query = "SELECT mb.member_email
				  FROM meeting_groups mg JOIN members mb JOIN group_members gm
				  ON mg.group_id=gm.group_id 
				  AND mb.member_id=gm.member_id
				  WHERE mg.group_id='$groupID'";
	    $this->database->execute($query);
	    return $this->database->result;	
	}

	/* 
	*	Membuat daftar hadir berdasarkan anggota grup
	*/
	function createAttendanceLists($groupID){
		$this->koneksi();
		foreach($this->getAllGroupMembers($groupID) as $x){
			foreach($this->getMaxRowsAndGroupIDOFMeetings($groupID) as $c)
				$query = "INSERT INTO meeting_attendance (meeting_id, member_id, group_id, member_name)
						  VALUES ('$c[meeting_id]','$x[member_id]','$groupID','$x[member_name]')";
				$this->database->execute($query);
		}		
	}

	/*
	* Menghitung jumlah anggota grup
	*/
	function countGroupMember($groupID){
		$this->koneksi();
		$query = "SELECT COUNT(id) AS jumMember
				  FROM group_members
				  WHERE group_id='$groupID'";
		$this->database->execute($query);
		return $this->database->result;
	}

	function displayCountGroupMember($meetingID){
		foreach ($this->countGroupMember($meetingID) as $key) {
			return $key['jumMember'];
		}
	}

	/*
	* Menghitung jumlah kehadiran setiap meeting
	*/
	function countMeetingAttendance($meetingID){
		$this->koneksi();
		$query = "SELECT COUNT(ma.status) AS jumHadir
				  FROM meeting_attendance ma
				  JOIN meetings mt 
				  ON ma.meeting_id=mt.meeting_id 
				  WHERE ma.meeting_id='$meetingID' AND ma.status='Hadir'";
		$this->database->execute($query);
		return $this->database->result;
	}

	function displayCountMeetingAttendance($meetingID){
		foreach ($this->countMeetingAttendance($meetingID) as $key) {
			return $key['jumHadir'];
		}
	}

	/*
	* Menghitung jumlah meeting tiap hari dalam satu bulan kalendar
	* 
	*/
	function countEverydayMeetingInMonth($month){
		$this->koneksi();
		$query = "SELECT COUNT(mt.meeting_id) AS jumMeetingToday
				FROM meeting_groups mg 
				JOIN meetings mt
				ON mg.group_id=mt.group_id
				WHERE mt.meeting_date='$month'";
		$this->database->execute($query);
		return $this->database->result;
	}	

}

$timeAttendanceinNumbers = time();
$timeAttendanceinString = Date("H:i:s");
$dateAttendanceinString = Date("Y-m-d");
$dateAttendanceinNumbers = strtotime($dateAttendanceinString);
	//$meeting_start = strtotime($meetingStart);
	//$meeting_end = strtotime($meetingEnd);
	//$meeting_date = strtotime($meetingDate);


$meeting = new Meetings();


//**********************************//
//Menampilkan Seluruh Rapat Hari Ini//
//**********************************//
/*
echo "Meeting hari ini (".$meeting->displayCountAllMeetingToday($dateAttendanceinString).") : \n";
echo "------------------------\n";
if($meeting->displayJumAllMeetingToday($dateAttendanceinString)==0){
	echo "<<Tidak ada meeting>> \n";
} else {
	foreach ($meeting->getAllMeetingToday($dateAttendanceinString) as $c){
		echo "\n";
		echo $c['group_name']."\n";
		echo "Agenda: ".$c['meeting_name']."\n";
		echo "Tanggal: ".$c['meeting_date']."\n";
		echo "Waktu: ".$c['meeting_start']." - ".$c['meeting_end']."\n";
		echo "Tempat: ".$c['meeting_place']."\n";
		echo "\n";
	}	
}


//********************	//
//Membuat agenda rapat 	//
//*********************						
$groupID = 1;
$meetingName = "Rapat KK Relata";
$meetingDate = "2019-06-18";
$meetingStart = "16:10:00";
$meetingEnd = "17:00:00";
$meetingPlace = "Lab Riset";

echo "<<Membuat meeting baru>> \n";
if($meeting->displayCountSameMeetingSchedule($groupID, $meetingDate, $meetingEnd, $meetingStart)==1){
	echo "Sudah ada meeting yang sama. \n";
} else if ($meeting->displayJumOverlapMeetingByDate($groupID, $meetingDate)==0 OR $meeting->displayOverlapMeetingByTime($groupID, $meetingDate, $meetingStart)==0){
	//echo "Nol" dan "overlap = 0";
	echo "\n";
	echo "Sedang membuat meeting... \n";
	$meeting->createNewMeeting($groupID, $meetingName, $meetingDate, $meetingStart, $meetingEnd, $meetingPlace);
} else {
	echo "Sori, ada tabrakan jadwal. \n";
}

*/
