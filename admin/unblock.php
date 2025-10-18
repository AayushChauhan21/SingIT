<?php
include("connection.php");
$stid = $_GET['stid'];
$sid = $_GET['sid'];
$pid = $_GET['pid'];

if ($stid) {
    $qry = mysqli_query($con, "update skill_type set status=1 where stid=$stid");
    header("location:blocked_skill_type.php");
}

if ($sid) {
    $qry = mysqli_query($con, "update skill_master set status=1 where sid=$sid");
    header("location:blocked_skill_master.php");
}

if ($pid) {
    $qry = mysqli_query($con, "update package set status=1 where pid=$pid");
    header("location:blocked_package.php");
}
?>