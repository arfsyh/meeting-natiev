<?php

class Database
{
	private $dbusername, $dbhostname, $dbuserpassword, $dbConnection, $dbname;
	public $result;

	function __construct(){
		$this->dbhostname		= "localhost";
		$this->dbusername 		= "root";
		$this->dbuserpassword 	= "";
		$this->dbname 			= "meeting";
	}

  	public function connectToDatabase(){
        $this->dbConnection = mysqli_connect($this->dbhostname,
                                             $this->dbusername,
                                             $this->dbuserpassword); 
        mysqli_select_db($this->dbConnection, $this->dbname);
        if(!$this->dbConnection){
            die('Maaf, koneksi belum tersambung: '. mysqli_connect_error());
        }
    }

	public function execute($query){
		$this->result = mysqli_query($this->dbConnection, $query);
	}

	function getAllGroup(){
			$query = "SELECT group_id, group_name 
					FROM meeting_groups";
      	$this->execute($query);
      	return $this->result;
	}

	function createNewGroup($groupName){
		$query = "INSERT INTO meeting_groups (group_name) 
                  VALUES ('$groupName')";
		$this->execute($query);
	}

	function addMembertoGroup($memberID, $groupID){
		$query = "INSERT INTO group_members (member_id, group_id) 
                  VALUES ('$memberID','$groupID')";
		$this->execute($query);
	}

	function getAllGroupMembers(){
		$query = "SELECT meeting_groups.group_name, members.member_name
				  FROM meeting_groups JOIN members JOIN group_members
				  ON meeting_groups.group_id=group_members.group_id 
				  AND members.member_id=group_members.member_id";
      	$this->execute($query);
      	return $this->result;
	}

	function createMeeting($groupID, $meetingName, $meetingDate, $meetingStart, $meetingEnd, $meetingPlace){
		$query = "INSERT INTO meetings (group_id, meeting_name, meeting_date, meeting_start, meeting_end, meeting_place) 
                  VALUES ($groupID, '$meetingName', '$meetingDate', '$meetingStart','$meetingEnd', '$meetingPlace')";
		$this->execute($query);
		//var_dump($this->result);
	}

	function getMeetingbyGroupID($groupID){
		$query = "SELECT mg.group_name, mt.meeting_name, mt.meeting_date, mt.meeting_time, mt.meeting_place 	
				FROM meeting_groups mg JOIN meetings mt
				ON mg.group_id=mt.group_id
				WHERE mg.group_id=$groupID";
		$this->execute($query);
		return $this->result;
	}

	function getAllMeetingToday($dateToday){
		$query = "SELECT mg.group_name, mt.meeting_name, mt.meeting_date, 
						 mt.meeting_start, mt.meeting_end, mt.meeting_place 
				FROM meeting_groups mg JOIN meetings mt
				ON mg.group_id=mt.group_id
				WHERE mt.meeting_date='$dateToday'";
		$this->execute($query);
		return $this->result;
	}

	function getAllMeetingNow($dateToday){
		$query = "SELECT mg.group_name, mt.meeting_name, mt.meeting_date, 
						 mt.meeting_start, mt.meeting_end, mt.meeting_place,
						 mt.meeting_id, mg.group_id
				FROM meeting_groups mg 
				JOIN meetings mt
				ON mg.group_id=mt.group_id
				WHERE mt.meeting_date='$dateToday'";
		$this->execute($query);
		return $this->result;
	}

	function getAllMeetingNowForAttendanceCheck($dateToday, $memberID, $timenow, $meetingAttendanceID){
		$query = "SELECT ma.status, mt.meeting_start, mt.meeting_end, 
						 mt.meeting_id, ma.member_id, mt.meeting_date,
						 ma.attendance_time, mt.meeting_name, ma.id, ma.status
					FROM meeting_attendance ma 
					JOIN meetings mt
					ON ma.meeting_id=mt.meeting_id
					WHERE ma.member_id='$memberID' 
					AND mt.meeting_date='$dateToday'
					AND mt.meeting_start <= '$timenow'
					AND mt.meeting_end >= '$timenow'
					AND ma.status='Tidak hadir'
					AND ma.id='$meetingAttendanceID'";
		$this->execute($query);
		return $this->result;
	}

	function getAllIDMeetingAttendance($dateToday, $memberID, $timenow){
		$query = "SELECT ma.status, mt.meeting_start, mt.meeting_end, 
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
		$this->execute($query);
		return $this->result;
	}


	function getAllMeetingIDNow($dateToday){
		$query = "SELECT meeting_id
				FROM meetings
				WHERE meeting_date='$dateToday'";
		$this->execute($query);
		return $this->result;
	}	

	function getGroupMembers($groupID){
		$query = "SELECT members.member_name, members.member_id, members.member_email
				  FROM meeting_groups JOIN members JOIN group_members
				  ON meeting_groups.group_id=group_members.group_id 
				  AND members.member_id=group_members.member_id
				  WHERE meeting_groups.group_id=$groupID";
      	$this->execute($query);
      	return $this->result;
	}

	function createMeetingAttendanceLists($groupID, $meetingID){
		foreach($this->getGroupMembers($groupID) as $c){
			$query = "INSERT INTO meeting_attendance (meeting_id, member_name, member_id)
					 VALUES ('$meetingID','$c[member_name]','$c[member_id]')";
			$this->execute($query);
		}
	}

	function getMeetingDateStartEnd($meetingID){
		$query = "SELECT meeting_date, meeting_start, meeting_end
				  FROM meetings
				  WHERE meeting_id = $meetingID";
		$this->execute($query);
		return $this->result;
	}

	function checkAttendance($meetingID, $memberID){
		$query = "SELECT status
				  FROM meeting_attendance
				  WHERE meeting_id = '$meetingID' AND member_id = '$memberID'";
		$this->execute($query);
		return $this->result;
	}

	function checkMemberExistorNOT($memberID){
		$query = "SELECT member_id
				  FROM members
				  WHERE member_id=$memberID";
		$this->execute($query);
		return $this->result;
	}

    function fingerPrintAttendance($memberID, $attendanceTime, $meetingID){
    	//TODO: tambahkan validasi jika member sudah presensi
    	//jika presensi sebelum waktu meeting
        $query = "UPDATE meeting_attendance 
        		  SET status 			= 'Hadir',
        		  	  attendance_time	= '$attendanceTime'
                  WHERE meeting_id = '$meetingID' AND member_id = '$memberID'";
        $this->execute($query);
        $this->result;
    }
}

$mysql = new Database();
$mysql->connectToDatabase();

?>