<?php
$dbhostname	= "localhost";
$dbusername = "root";
$dbuserpassword = "";
$dbname = "meeting";

$conn = mysqli_connect($dbhostname, $dbusername, $dbuserpassword, $dbname);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());}

    $groupID = $_GET['val'];

    $query = "SELECT members.member_name,members.member_email, members.member_id 
				  FROM meeting_groups JOIN members JOIN group_members
				  ON meeting_groups.group_id=group_members.group_id 
				  AND members.member_id=group_members.member_id
                  WHERE meeting_groups.group_id=$groupID";
                  
    $result = mysqli_query($conn,$query);                       

    
        echo " <table style='border: 1px solid black'> <thead style='border: 1px solid black'> <tr> <th scope='col'>Members Name</th> <th scope='col'>Members Email</th> <th scope='col'><input type='checkbox' name='member_selected' id='member_selected' onClick='member_toggle(this)'><font size=1>Select All</font></th> </tr> </thead> <tbody style='border: 1px solid black'>";   
        if(mysqli_num_rows($result)>0){
        
            
            while ($q = mysqli_fetch_assoc($result)){
                 echo " <tr> <td>".$q['member_name']."</td> <td>".$q['member_email']."</td><td><input type='checkbox' name='member[]' id='member[]' value='".$q['member_email']."' > </td></tr>";}
        echo "</tbody></table>";
    }


