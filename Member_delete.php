<?php
include "koneksi.php";

    $mid = $_POST['id'];
    
    


    
    
    $addmember = mysqli_query($konek_db,"DELETE FROM members WHERE member_id ='$mid'");
    $addmember = mysqli_query($konek_db,"DELETE FROM group_members WHERE member_id ='$mid'");
  