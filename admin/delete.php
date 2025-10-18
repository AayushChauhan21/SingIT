<?php
include("connection.php");
$gid = $_GET['gid'];
if ($gid) {
    $qry = mysqli_query($con, "delete from genre where gid=$gid");
    header("location:view_genres.php");
}

$arid = $_GET['arid'];
if ($arid) {
    $qry = mysqli_query($con, "delete from artist where arid=$arid");
    header("location:view_artist.php");
}

$sid = $_GET['sid'];
if ($sid) {
    $qry = mysqli_query($con, "delete from song where sid=$sid");
    header("location:view_songs.php");
}

$lid = $_GET['lid'];
if ($lid) {
    $qry = mysqli_query($con, "delete from language where lid=$lid");
    header("location:view_language.php");
}

?>