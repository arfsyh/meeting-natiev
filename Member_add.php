<?php
include "koneksi.php";

    $n = $_POST['name'];
    $e = $_POST['email'];
    $gid = $_POST['gid'];
    
    


    
    
    $addmember = mysqli_query($konek_db,"INSERT INTO members(member_name,member_email) VALUES ('$n','$e')");
  
    $pembimbing = mysqli_fetch_array(mysqli_query($konek_db,"SELECT member_id FROM members WHERE member_name='$n' AND member_email='$e' "));
    $mid=$pembimbing['member_id'];

    $group_member = mysqli_query($konek_db,"INSERT INTO group_members(member_id,group_id) VALUES ('$mid','$gid')");


  
               


?>