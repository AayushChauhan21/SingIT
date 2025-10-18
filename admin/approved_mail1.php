<?php
include('con.php');

$cid = $_GET['coid'];

include('smtp/PHPMailerAutoload.php');
// $q = mysqli_query($con, "select * from applicant where apid=$apid");
// $r = mysqli_fetch_array($q);
// $caid = $r['caid'];
// $jid = $r['jid'];

// $nq = mysqli_query($con, "select * from candidate_master where caid=$caid");
// $nr = mysqli_fetch_array($nq);
// $caname = $nr['caname'];
// $email = $nr['email'];

// $qq = mysqli_query($con, "select * from job_master where jid=$jid");
// $rr = mysqli_fetch_array($qq);
// $jname = $rr['jname'];
// $timing = $rr['timing'];
// $min_salary = $rr['min_salary'];
// $max_salary = $rr['max_salary'];

$nqq = mysqli_query($con, "select * from company_master where coid=$cid");
$nrr = mysqli_fetch_array($nqq);
$coname = $nrr['coname'];
$hrname = $nrr['hrname'];
$email = $nrr['email'];

// your interview has been arrenged . you are ordered to come with your resiume

$message = "
<center>
<html>
<body>
<h2>
  <b>Hello $hrname 👋 </b>,</h2> <br>
  <img src='https://cdni.iconscout.com/illustration/premium/thumb/mobile-biometric-5650378-4707997.png' alt='Embedded Image'>
   <br>
   <h3><p>Your Company $coname's Registration Has Been Approved By Admin ,<br> Now You Are Login With Your Details Successfully.</p></h3>
  </body>
</html>
</center>
";

echo smtp_mailer($email, 'Login Now', $message, $con, $cid);
function smtp_mailer($to, $subject, $msg, $con, $cid)
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
        header("location:view_company_details.php?coid=" . $cid);
    }
}
?>