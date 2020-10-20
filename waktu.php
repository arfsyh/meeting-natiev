<?php
$datetime1 = new DateTime();
$datetime2 = new DateTime('2019-05-13 10:12:00');
$interval = $datetime1->diff($datetime2);
echo $interval->format('%Y-%m-%d %H:%i:%s');

echo "\n"; 
echo 'Default Timezone: ' . date('d-m-Y H:i:s') . "\n";
date_default_timezone_set('Asia/Jakarta');
echo 'Indonesian Timezone: ' . date('d-m-Y H:i:s');

$waktu1 = "09:00:00";
$waktu2 = "11:00:00";
$presensi = "11:58:00";

$timestamp1 = strtotime($waktu1);
$timestamp2 = strtotime($waktu2);
$timestamp3 = strtotime($presensi);

echo "\n";
echo "Mulai ".$timestamp1. " - Selesai ".$timestamp2;
echo "\n";
if ($timestamp3 < $timestamp1){
	echo "Rapat belum mulai";
} else if ($timestamp3 > $timestamp2){
	echo "Rapat sudah selesai";
} else {
	echo "Oke";
}