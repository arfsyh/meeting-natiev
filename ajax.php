<?php

$event_title = $_POST['event-title'];
$event_place = $_POST['event-place'];
$event_start_time = $_POST['event-start-time'];
$event_end_time = $_POST['event-end-time'];
$event_description = $_POST['event-description'];
$eventStartTime = str_replace(" ", "T", $event_start_time).":00+07:00 <br>";
$eventEndTime = str_replace(" ", "T", $event_end_time).":00+07:00 <br>";
echo $event_group = $_POST['event-group'];

//echo "$event_title <br>";
//echo "$event_place <br>";
//echo "$event_start_time <br>";
//echo "$event_end_time <br>";
//echo "$event_description <br>";
?>