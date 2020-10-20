<!DOCTYPE html>
<html>

<head>
	<title>Manajemen Meeting</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="stylesheet" type="text/css" href="asset/plugins/datepick/jquery.datetimepicker.css"/ >
<script src="asset/plugins/datepick//jquery.js"></script>
<script src="asset/plugins/datepick//build/jquery.datetimepicker.full.min.js"></script>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
</head>

<body>
<?php
session_start();
require_once ('meeting.php');
require_once ('member.php');
date_default_timezone_set('Asia/Jakarta');
require __DIR__ . '/vendor/autoload.php';

class Event
{
	public $groupID, $event_title, $event_place, $event_start_time,
	$event_end_time, $event_description;

function koneksi(){
   $this->database = new Database();
   $this->database->connectToDatabase();
   $this->meeting = new Meetings();
   $this->member = new Member();

  }
function getMeeting($mettingId){
	$this->koneksi();
	$query ="SELECT *
	FROM meetings
	WHERE meeting_id = '$mettingId'";
	 $this->database->execute($query);
	 return $this->database->result;
}

}

?>


<div class="container">
<h2>View Detail Meeting</h2>
<?php

$meetingId = $_GET['id'];
$tes = new Event();

foreach($tes->getMeeting($meetingId) as $q){


?>
<div class="form-group col-sm-12">
		<div class="row"><label class="alert alert-primary col-sm-2">Tittle</label>
		<label class="alert alert-secondary col-sm-8"><strong><?php echo $q['meeting_name']; ?></strong></label></div>
</div>
<div class="form-group col-sm-12">
		<div class="row"><label class="alert alert-success col-sm-2">Detail</label>
		<label class="col-sm-8 alert alert-secondary"><strong><?php echo $q['meeting_name']; ?></strong></label></div>
</div>
<div class="form-group col-sm-12">
	<div class="row"><label class="alert alert-success col-sm-2 ">Location</label>
		<label class="col-sm-8 alert alert-secondary"><strong><?php echo $q['meeting_place']; ?></strong></label></div>
</div>
<div class="form-group col-sm-12">
	<div class="row"><label class="alert alert-success col-sm-2 ">Meeting Date</label>
		<label class="col-sm-8 alert alert-secondary"><strong><?php echo $q['meeting_date']; ?></strong></label></div>
</div>
<div class="form-group col-sm-12">
	<div class="row"><label class="alert alert-success col-sm-2 ">Start Time</label>
		<label class="col-sm-8 alert alert-secondary"><strong><?php echo $q['meeting_start']; ?></strong></label></div>
</div>
<div class="form-group col-sm-12">
	<div class="row"><label class="alert alert-success col-sm-2 ">End Time</label>
		<label class="col-sm-8 alert alert-secondary"><strong><?php echo $q['meeting_end']; ?></strong></label></div>
</div>
<div class="form-group col-sm-12">
	<div class="row"><label class="alert alert-success col-sm-2 ">Group Meeting</label>
		<label class="col-sm-8 alert alert-secondary"><strong><?php echo $q['meeting_name']; ?></strong></label></div>
</div>
<div class="form-group" style="margin-top:10px">
<div class="row"> <button class="btn btn-primary" value="<?php echo $q['meeting_id']; ?>" onclick="cek(this.value)"> View Attendees </button> <button class="btn btn-warning" style="margin-left:10px"><a href="test_editevent.php?id=<?php echo $q['meeting_id']; ?>" >Edit</a></button><span></span><button class="btn btn-danger" style="margin-left:10px"><a href="delete_event.php?id=<?php echo $q['meeting_id']; ?>" >Delete</a></button></div>
</div>
<div class="form-group col-sm-12" id="vmember" >


	


<?php } ?>
</div>
</div>
<script>
function cek(str) {
  var xhttp;

  xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
    document.getElementById("vmember").innerHTML = this.responseText;
    }
  };
  xhttp.open("GET", "getatt.php?val="+str, true);
  xhttp.send();
}



</script>



<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
</body>
</html>