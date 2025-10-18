<?php
include ('con.php');

$jid = $_GET['jid'];

include ('smtp/PHPMailerAutoload.php');
// $q = mysqli_query($con, "select * from applicant where apid=$apid");
// $r = mysqli_fetch_array($q);
// $caid = $r['caid'];
// $jid = $r['jid'];


$qq = mysqli_query($con, "select * from job_master where jid=$jid");
$rr = mysqli_fetch_array($qq);
$jname = $rr['jname'];
$timing = $rr['timing'];
$min_salary = $rr['min_salary'];
$max_salary = $rr['max_salary'];
$coid = $rr['coid'];

$co = mysqli_query($con, "select * from company_master where coid=$coid");
$cr = mysqli_fetch_array($co);
$coname = $cr['coname'];
// $hrname = $cr['hrname'];
// $email = $cr['email'];


// $nqq = mysqli_query($con, "select email from candidate_master");
// $nrr = mysqli_fetch_array($nqq);
// while ($nrr = mysqli_fetch_array($nqq))
//     $email = $nrr['email'];


// $nq = mysqli_query($con, "select caname from candidate_master");
// if ($nq) {
//     while ($nr = mysqli_fetch_array($nq)) {
//         $caname = $nr['caname'];

//     }

// }
// $email = $nr['email'];


$ml = mysqli_query($con, "select * from candidate_master");
if ($ml) {
    while ($nrr = mysqli_fetch_array($ml)) {
        $caname = $nrr['caname'];

        $message = "
        <center>
        <html>
        <body>
        <h2> Hello 
        <b> $caname 👋 </b>,</h2> <br>
         <img src='https://cdni.iconscout.com/illustration/premium/thumb/man-use-contact-phone-book-apps-9573558-7826330.png' alt='Embedded Image'>
        <br>
        <h3> <p> New Job <b> $jname </b> Has Been Uploaded..<br>Company Name Is <b> $coname </b>...<br>
        Salary Is <b> $min_salary - $max_salary </b>..<br> Visit And Watch The Job Details If You Want That Job. </p></h3>
        </body>
        </html>
        </center>
        ";

        echo smtp_mailer($nrr['email'], 'New Job Uploaded', $message, $con, $jid);

    }
}

// your interview has been arrenged . you are ordered to come with your resiume
// echo smtp_mailer($email, 'New Job Upploaded', $message, $con, $jid);
function smtp_mailer($to, $subject, $msg, $con, $jid)
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
        header("location:view_job_master.php");
    }
}