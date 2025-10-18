<?php
include('con.php');

$mid = $_GET['mid'];

include('smtp/PHPMailerAutoload.php');

$nqq = mysqli_query($con, "select * from mail where mid=$mid");
$nrr = mysqli_fetch_array($nqq);
$sender = $nrr['sender'];
$sub = $nrr['sub'];
$receiver = $nrr['receiver'];
$msg = $nrr['msg'];

// your interview has been arrenged . you are ordered to come with your resiume

$message = "
<center>
<html>
<body>
<h2>
  <b>Hello $receiver 👋 </b>,</h2> <br>
   <br>
   <h3><p>$msg</p></h3>
  </body>
</html>
</center>
";

echo smtp_mailer($receiver, $sub, $message, $con, $mid);
function smtp_mailer($to, $subject, $msg, $con, $mid)
{
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 587;
    $mail->IsHTML(true);
    $mail->CharSet = 'UTF-8';
    //$mail->SMTPDebug = 2; 
    $mail->Username = "jobportal564@gmail.com";
    $mail->Password = "dhqv gusy rzca zllj";
    $mail->SetFrom("email");
    $mail->Subject = $subject;
    $mail->Body = $msg;
    $mail->AddAddress($to);
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => false
        )
    );
    if (!$mail->Send()) {
        echo $mail->ErrorInfo;
    } else {
        header("location:mail.php");
    }
}
?>