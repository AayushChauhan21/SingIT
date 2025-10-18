<?php
include("connection.php");
$jid = $_GET['jid'];
$j_id = $_GET['jid'];
$coid = $_GET['coid'];
$cid = $_GET['coid'];

if ($jid) {
    $qry = mysqli_query($con, "update job_master set status=1 where jid=$jid");
    // header("location:approved_job.php?jid=$jid");
    header("location:approved_job.php?jid=$jid");
}

if ($j_id) {
    $qry = mysqli_query($con, "update job_master set status=1 where jid=$j_id");
    // header("location:approved_job.php?jid=$j_id");
    header("location:approved_job1.php?jid=$j_id");
}

if ($coid) {
    $qry = mysqli_query($con, "update company_master set status=1 where coid=$coid");
    // header("location:view_company_master.php");
    header("location:approved_mail.php?coid=$coid");
}

if ($cid) {
    $qry = mysqli_query($con, "update company_master set status=1 where coid=$cid");
    // header("location:view_company_master.php");
    header("location:approved_mail1.php?coid=$cid");
}

?>