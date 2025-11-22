<?php
include("connection.php");

if (isset($_POST["email"]) && isset($_POST["pass"])) {
    $email = $_POST["email"];
    $pass  = $_POST["pass"];

    $qry = "SELECT * FROM user WHERE email='$email' AND password='$pass'";
    $res = mysqli_query($con, $qry);

    if (mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        
        $response = [
            "success" => "true",
            "id" => $row['id']
        ];
        
        echo json_encode($response);
        
    } else {
        echo json_encode(["success" => "false", "error" => "Invalid email or password"]);
    }
} else {
    echo json_encode(["success" => "false", "error" => "Email and password are required"]);
}
?>