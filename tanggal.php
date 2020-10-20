<?php
    
    
$tanggal = $_POST['event-start-time'];


$pisah = explode('/',$tanggal);
$larik = array($pisah[0],$pisah[1],$pisah[2]);
$satukan = implode('-',$larik);

$eventStartTime = str_replace(" ", "T", $satukan).":00+07:00";


echo $eventStartTime;

?>