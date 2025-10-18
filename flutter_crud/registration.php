<?php
include("connection.php");

if (isset($_POST["name"]) && isset($_POST["email"]) && isset($_POST["pass"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $pass = $_POST["pass"];

    // Check if email already exists
    $check = "SELECT * FROM `user` WHERE `email` = '$email'";
    $res = mysqli_query($con, $check);

    if (mysqli_num_rows($res) > 0) {
        echo json_encode(["success" => "false", "error" => "Email already exists"]);
        exit;
    }

    // Insert new user
    $qry = "INSERT INTO `user`(`name`, `email`, `password`) VALUES ('$name','$email','$pass')";
    if (mysqli_query($con, $qry)) {
        echo json_encode(["success" => "true"]);
    } else {
        echo json_encode(["success" => "false", "error" => "Login Fail!!!"]);
    }
}
?>
