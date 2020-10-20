<?php
include "koneksi.php";
$ini = new tes();

$mid = $_POST['id'];
$n = $_POST['name'];
$e = $_POST['email'];

$group_member = mysqli_query($konek_db,"UPDATE FROM members SET member_name='$n',member_email='$e' WHERE member_id='$mid'");

