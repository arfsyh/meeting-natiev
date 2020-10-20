<?php
require_once('database.php');
date_default_timezone_set('Asia/Jakarta');

	//$meetingStart;
	//$meetingEnd;
	//$meetingDate;
	//$statusHadir;

	$mysql = new Database();
	$mysql->connectToDatabase();

	foreach($mysql->getMeetingDateStartEnd(4) as $x){
		//echo $x['meeting_start']." - ".$x['meeting_end'];
		$meetingDate = $x['meeting_date'];
		$meetingStart = $x['meeting_start'];
		$meetingEnd = $x['meeting_end'];
	}

	//$mysql->createMeetingAttendanceLists(1,2);
	$timeAttendanceinNumbers = time();
	$timeAttendanceinString = Date("H:i:s");
	$dateAttendanceinString = Date("Y-m-d");
	$dateAttendanceinNumbers = strtotime($dateAttendanceinString);
	$meeting_start = strtotime($meetingStart);
	$meeting_end = strtotime($meetingEnd);
	$meeting_date = strtotime($meetingDate);
	echo "\n";
	echo "Jadwalnya tanggal: $meetingDate ($meeting_date) || Waktu: $meetingStart ($meeting_start) - $meetingEnd ($meeting_end) \n";
	echo "Attendance date: $dateAttendanceinString ($dateAttendanceinNumbers) \n";
	echo "Attendance time: $timeAttendanceinString ($timeAttendanceinNumbers) \n";
	//fingerPrintAttendance($meetingID, $memberID)
	//$mysql->checkAttendance(meetingID, memberID, $timestamp);
	//$meetingID = 2;
	$memberID = 7;

	//foreach($mysql->checkAttendance($memberID, $timeAttendanceinNumbers) as $c){
	//	if ($c['status'] == "Hadir" AND $dateAttendanceinString==$meetingDate){
	//		echo $c['member_name']." Anda sudah presensi \n";
	//	} else if ($timeAttendanceinNumbers < $meeting_start  AND $dateAttendanceinString==$meetingDate){
	//		echo $c['member_name']." Rapat belum dimulai \n"; 
	//	} else if ($timeAttendanceinNumbers > $meeting_end AND $dateAttendanceinString==$meetingDate){
	//		echo $c['member_name']." Rapat telah selesai \n";
	//	} else {
	//		$mysql->fingerPrintAttendance($memberID, $timeAttendanceinString);
	//		echo $c['member_name']." Pukul: ".$timeAttendanceinString." || ".$timeAttendanceinNumbers."\n";
	//	}
	//}

	echo "\n";
	echo "Meeting hari ini: \n";
	echo "---------------\n";
	foreach ($mysql->getAllMeetingToday($dateAttendanceinString) as $c){
		echo $c['group_name']."\n";
		echo "Agenda: ".$c['meeting_name']."\n";
		echo "Tanggal: ".$c['meeting_date']."\n";
		echo "Waktu: ".$c['meeting_start']." - ".$c['meeting_end']."\n";
		echo "Tempat: ".$c['meeting_place']."\n";
	}

	echo "\n";
	echo "Meeting saat ini: \n";
	echo "---------------\n";
	foreach ($mysql->getAllMeetingNow($dateAttendanceinString) as $c){
		$meetingstart = strtotime($c['meeting_start']);
		$meetingend = strtotime($c['meeting_end']);
		if ($timeAttendanceinNumbers > $meetingstart AND $timeAttendanceinNumbers < $meetingend){
				echo "Grup: ".$c['group_name']. "\n";
				echo "Agenda: ".$c['meeting_name']."\n";
				echo "Tanggal: ".$c['meeting_date']."\n";
				echo "Waktu: ".$c['meeting_start']." - ".$c['meeting_end']."\n";
				echo "Tempat: ".$c['meeting_place']."\n";
				echo "Peserta: \n";
				foreach ($mysql->getGroupMembers($c['group_id']) as $k){	
					if($k['member_id'] == $memberID){
						echo $k['member_name']." \n";
					} else {
						echo $k['member_name']."\n";
					}
				}
		}
		echo "\n";
	}

	$listMeetingAttendanceID = [];
	foreach($mysql->getAllIDMeetingAttendance($dateAttendanceinString, $memberID, $timeAttendanceinString) as $c){
		$listMeetingAttendanceID[] = $c['id'];
	}
	$minListMeetingAttendanceID = min($listMeetingAttendanceID);
	echo "\n";
	echo "Presensi: \n";
	echo "---------------\n";
	
	if($minListMeetingAttendanceID==""){
		echo "Sudah presensi.";
	} else {
		foreach ($mysql->getAllMeetingNowForAttendanceCheck($dateAttendanceinString, $memberID, $timeAttendanceinString, $minListMeetingAttendanceID) as $c){
			//echo $c['member_id']." ";
			//echo $c['meeting_id']." ";
			//echo $c['status']." "; 
			//echo $c['meeting_name']." "; 		
			//echo $c['meeting_start']." "; 
			//echo $c['meeting_end']." ";
			//echo $c['attendance_time']." ";
			//echo "\n";
			$meetingDate = strtotime($c['meeting_date']);
			$meetingStart = strtotime($c['meeting_start']);
			$meetingEnd = strtotime($c['meeting_end']);
			
			//jika tidak hadir semua
			$mysql->fingerPrintAttendance($memberID, $timeAttendanceinString, $c['meeting_id']);
			echo $c['member_id'] ." ".$c['meeting_name']." Pukul: ".$timeAttendanceinString."(".$timeAttendanceinNumbers.")\n";
		}
	}

//echo "Peserta: \n";
//foreach ($mysql->getGroupMembers(1) as $c){
//	echo $c['member_name']."\n";
//}

