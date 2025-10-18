<?php
include("con.php");


$mid = $_GET['mid'];

if ($mid) {
    // $qry = mysqli_query($con, "update mail set status=1 where mid=$mid");
    // header("location:approved_job.php?mid=$mid");
    header("location:mail_send.php?mid=$mid");
}


?>