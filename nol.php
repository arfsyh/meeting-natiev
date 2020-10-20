<?php
include "database.php";
date_default_timezone_set('Asia/Jakarta');

class XYZ
{
		function koneksi(){
		$this->database = new Database();
		$this->database->connectToDatabase();
	}

	function getOverlapMeeting($groupID, $meetingDate){
		$this->koneksi();
		$queryCekOverlap = "SELECT count(*) as tes
				  		 	FROM meetings 
				  			WHERE group_id='$groupID' AND meeting_date='$meetingDate'";
		$this->database->execute($queryCekOverlap);
		return $this->database->result;
	}
}

$groupID = 1;
$meetingName = "Rapat Lab Pabrik";
$meetingDate = "2019-06-16";
$meetingStart = "16:20:00";
$meetingEnd = "17:30:00";
$meetingPlace = "Ruang Sidang FTI";

$meeting = new XYZ();
foreach($meeting->getOverlapMeeting($groupID, $meetingDate) as $c){
	echo $c['tes'];
}


	function createNewMeeting($groupID, $meetingName, $meetingDate, $meetingStart, $meetingEnd, $meetingPlace){
		$this->koneksi();
		foreach($this->getOverlapMeeting($groupID, $meetingDate) as $c){
			$meetingstartFromDB = strtotime($c['meeting_start']);
			$meetingendFromDB = strtotime($c['meeting_end']);
			$meetingStartFromUser = strtotime($meetingStart);
			if($meetingStartFromUser >= $meetingstartFromDB AND $meetingStartFromUser <= $meetingendFromDB){
				echo "Sudah ada meeting. Cari waktu yang lain.";
			} else {
				foreach($this->getZeroMeeting($groupID, $meetingDate) as $xy){
					if($xy['zero']==0 OR $xy['zero']>=0){
						$query = "INSERT INTO meetings (group_id, meeting_name, meeting_date, meeting_start, meeting_end, meeting_place) 
		                  		  VALUES ($groupID, '$meetingName', '$meetingDate', '$meetingStart','$meetingEnd', '$meetingPlace')";
						$this->database->execute($query);
						sleep(5);
						$this->createAttendanceLists();
					}
				}							
			}
		}
	}