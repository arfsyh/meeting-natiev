<html>
<head><title>Manajemen Meeting</title><link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head><body class="bg-light">

<?php
require_once ('database.php');
require_once ('meeting.php');
date_default_timezone_set('Asia/Jakarta');

/**
 * Halaman dashboard atau landing page setelah login
 */
class Dashboard
{
	
	function koneksi(){
		//$this->database = new Database();
		//$this->database->connectToDatabase();
		$this->meeting = new Meetings();
	}

	function displayAllMeetingToday($dateAttendanceinString)
	{
		$this->koneksi();
		echo "<b>Agenda hari ini (".$this->meeting->displayCountAllMeetingToday($dateAttendanceinString).") </b>: </br>";
		if($this->meeting->displayJumAllMeetingToday($dateAttendanceinString)==0){
			echo "<p>";
			echo "--Tidak ada agenda-- <br>";
		} else {
			foreach ($this->meeting->getAllMeetingToday($dateAttendanceinString) as $c){
				echo "<br>";
				echo $c['group_name']." (".$this->meeting->displayCountMeetingAttendance($c['meeting_id']).'/'. $this->meeting->displayCountGroupMember($c['group_id']).") "."<br>";
				echo "Agenda: ".$c['meeting_name']."<br>";
				echo "Tanggal: ".$c['meeting_date']."<br>";
				echo "Waktu: ".$c['meeting_start']." - ".$c['meeting_end']."<br>";
				echo "Tempat: ".$c['meeting_place']."<br>";
			}	
		}
	}
}
$timeAttendanceinNumbers = time();
$timeAttendanceinString = Date("H:i:s");
$dateAttendanceinString = Date("Y-m-d");
$dateAttendanceinNumbers = strtotime($dateAttendanceinString);


$display = new Dashboard();
//**********************************//
//Menampilkan Seluruh Rapat Hari Ini//
//**********************************//

?>
<div class="container p-3">
<h2>AGENDA FTI UAD</h2>
<a class='btn btn-success btn-float m-1' href='new_event.php' target='isi'>New Meeting</a> <a class='btn btn-primary btn-float m-1' href='groups-lawas.php' target='isi'>Groups</a> <a class='btn btn-secondary btn-float m-1' href='enroll_fingerprint.php' target='isi'>Add Fingerprint</a> <a class='btn btn-secondary btn-float m-1' href='fingerprint_attendance.php' target='isi'>Attendance</a></p>
<?php $display->displayAllMeetingToday($dateAttendanceinString);?>
</div>
</body>
</html>