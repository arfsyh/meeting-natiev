<?php
$dbhostname	= "localhost";
$dbusername = "root";
$dbuserpassword = "";
$dbname = "meeting";

$conn = mysqli_connect($dbhostname, $dbusername, $dbuserpassword, $dbname);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());}

    $groupID = $_GET['val'];

    $query = "SELECT members.member_name,members.member_email
				  FROM meeting_attendance JOIN members JOIN meetings
				  ON meeting_attendance.member_name=members.member_name 
				  AND meeting_attendance.meeting_id=meetings.meeting_id
                  WHERE meeting_attendance.meeting_id=$groupID";
                  
    $result = mysqli_query($conn,$query);                       

    
        echo " <table class='table'> <thead > <tr> <th scope='col'>Members Name</th> <th scope='col'>Members Email</th> <th scope='col'>Status</th> </tr> </thead> <tbody>";   
        if(mysqli_num_rows($result)>0){
        
            
            while ($q = mysqli_fetch_assoc($result)){
                 echo " <tr> <td>".$q['member_name']."</td> <td>".$q['member_email']."</td><td></td></tr>";}
        echo "</tbody></table>";
       }