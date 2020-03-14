<?php

// example on using PHPMailer with GMAIL

include("class.phpmailer.php");
include("class.smtp.php"); // note, this is optional - gets called from main class if not already loaded

class EmailHelper {

public function send($toArray, $subject, $body,$attachments=false) {
    $mail             = new PHPMailer();

    $mail->IsSMTP();
    $mail->SMTPAuth   = true;                  // enable SMTP authentication
    $mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
    $mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
    $mail->Port       = 465;                   // set the SMTP port

    $mail->Username   = "SIBS@sarotam.com";  // GMAIL username
    $mail->Password   = "InTouch@250818";            // GMAIL password wrong one for testing so email should not go
    
    $mail->From       = "SIBS@sarotam.com";
    $mail->FromName   = "Sarotam Industrial Business System";
    $mail->Subject    = $subject;
    $mail->WordWrap   = 50; // set word wrap

    $mail->MsgHTML($body);

    $mail->AddReplyTo("SIBS@sarotam.com","Sarotam Industrial Business System");
    
    $mail->IsHTML(true); // send as HTML
    if($attachments){
          $mail->AddAttachment($attachments);
    }

    foreach($toArray as $to) {
      $mail->AddAddress($to);
    }
   
    //optional
//    if($ccArray){
//        foreach($ccArray as $cc){
//         $mail->AddCC($cc);
//        }        
//    }
    
   // $mail->IsHTML(true); // send as HTML

    if(!$mail->Send()) {
      return $mail->ErrorInfo;
    } else {
      return 0;
    }
}

}
