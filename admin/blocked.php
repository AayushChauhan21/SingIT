<?php
include("connection.php");
error_reporting(1);
$stid = $_GET['stid'];
$sid = $_GET['sid'];
$pid = $_GET['pid'];


if ($stid) {
    $qry = mysqli_query($con, "update skill_type set status=0 where stid=$stid");
    $sname = "";
    $jname = "";
    $qry1 = mysqli_query($con, "select * from skill_master where status=1 and stid=$stid");
    while ($row = mysqli_fetch_array($qry1)) {
        $sname = $sname . " , " . $row['sname'];
        $qry3 = mysqli_query($con, "select * from job_master where status=1 and sid=$row[sid]"); {
            while ($row1 = mysqli_fetch_array($qry3)) {
                $jname = $jname . " , " . $row1['jname'];
            }
            $qry4 = mysqli_query($con, "update job_master set status=2 where sid=$row[sid]");
        }
    }
    $qry2 = mysqli_query($con, "update skill_master set status=0 where stid=$stid");
    echo "<script>alert('skill $sname will also be blocked.');</script>";
    echo "<script>alert('job $jname will also be disapproved.');</script>";
    echo "<script>window.location.assign('view_skill-type.php');</script>";
}

if ($sid) {
    $qry = mysqli_query($con, "update skill_master set status=0 where sid=$sid");
    $jname = "";
    $qry1 = mysqli_query($con, "select * from job_master where status=1 and sid=$sid");
    while ($row = mysqli_fetch_array($qry1)) {
        $jname = $jname . " , " . $row['jname'];
    }
    $qry2 = mysqli_query($con, "update job_master set status=2 where sid=$sid");
    echo "<script>alert('job $jname will also be disapproved.');</script>";
    echo "<script>window.location.assign('view_skill-master.php');</script>";
}

if ($pid) {
    $qry = mysqli_query($con, "update package set status=0 where pid=$pid");
    header("location:blocked_package.php");
}
?>