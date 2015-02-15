<?php  

/**
 *
 * @package  core php  
 * @author   Michael Cheng <michael@orientationsys.com>
 * @description 
 *  Use it as a githhub webhook script to send issue activities notification email
 *  to specific email addresses
  */

require 'PHPMailer/PHPMailerAutoload.php';


 /* PHPMailer installation sample here:

require './PHPMailerAutoload.php';
$mail = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp1.example.com;smtp2.example.com';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'user@example.com';                 // SMTP username
$mail->Password = 'secret';                           // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 587;                                    // TCP port to connect to

$mail->From = 'from@example.com';
$mail->FromName = 'Mailer';
$mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
$mail->addAddress('ellen@example.com');               // Name is optional
$mail->addReplyTo('info@example.com', 'Information');
$mail->addCC('cc@example.com');
$mail->addBCC('bcc@example.com');

$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = 'Here is the subject';
$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}
 */
 
 
 
 
 
 $emailAddresses = array(
	'tttccc@gmail.com',
    //'michael@orientationsys.com'
);




try {

	$objPayload = json_decode(file_get_contents('php://input'));

}catch(Exception $e) {

	//Log the error
	file_put_contents('github_error.txt', $e . ' ' . $objPayload, FILE_APPEND);

	exit(0);
}

/*EMAIL TEMPLATE BEGINS*/

$imgSrc   = 'http://www.orientationsys.com/wp-content/themes/orientationsys/images/logo.png'; // Change image src to your site specific settings
$imgDesc  = 'Oientationsys logo'; // Change Alt tag/image Description to your site specific settings
$imgTitle = 'Orientationsys'; // Change Alt Title tag/image title to your site specific settings

/*
Change your message body in the given $subjectPara variables. 
$subjectPara1 means 'first paragraph in message body', $subjectPara2 means'first paragraph in message body',
if you don't want more than 1 para, just put NULL in unused $subjectPara variables.
*/

//If that is a comment created for an issue
if($objPayload->action === 'created'){
	
	$subjectPara1 = 'git repository: <a href="'.$objPayload->repository->html_url.'">'.$objPayload->repository->name.'</a> issue has been commented as following:';
	$subjectPara2 = NULL;
	$subjectPara3 = 'Issue comment: <a href="'. $objPayload->issue->html_url.'">'.$objPayload->issue->title.'('.$objPayload->issue->id.')</a> has just been '.$objPayload->action.' by '. $objPayload->sender->login .'.';
	$subjectPara4 = '<strong>"'.$objPayload->comment->body.'"</strong>';
	$subjectPara5 = $objPayload->comment->updated_at;
	
}
//If that is an issue activity
//Not notify labeled action as it's seems useless
elseif($objPayload->action !='labeled'){
	
	$subjectPara1 = 'git repository: <a href="'.$objPayload->repository->html_url.'">'.$objPayload->repository->name.'</a> issue has activity as following:';
	$subjectPara2 = 'Issue: <a href="'. $objPayload->issue->html_url.'">'.$objPayload->issue->title.'('.$objPayload->issue->id.')</a> has just been <strong>'.$objPayload->action.'</strong> by '. $objPayload->sender->login .'.';
	//$subjectPara3 = '<span style="color:'.$objPayload->label->color.'">'.$objPayload->label->name.'</span>';
	foreach($objPayload->issue->labels as $label){
		$subjectPara3 .='<span style="background-color:'.$label->color.'; padding: 2px 4px; font-size: 12px; font-weight:bold; border-radius:2px; box-shadow: 0px -1px 0px rgba(0, 0, 0, 0.12) inset;">'.$label->name.'</span> ';
	}	
	$subjectPara4 = '<strong>"'.$objPayload->issue->body.'"</strong>';
	$subjectPara5 = $objPayload->issue->updated_at;
}else{
	exit(0);
}
$message = '<html>'.
'<head>'.
'<meta http-equiv="content-type" content="text/html; charset=utf-8">'.
'<title>Email notification</title>'.
'</head>'.
'<body>'.
'<div id="header" style="width: 80%;height: 60px;margin: 0 auto;padding: 10px;color: #fff;text-align: center;background-color: #E0E0E0;font-family: Open Sans,Arial,sans-serif;">'.
   '<img height="50" width="220" style="border-width:0" src="'.$imgSrc.'" alt="'.$imgDesc.'" title="'.$imgTitle.'">'.
'</div>'.

'<div id="outer" style="width: 80%;margin: 0 auto;margin-top: 10px;">'. 
   '<div id="inner" style="width: 78%;margin: 0 auto;background-color: #fff;font-family: Open Sans,Arial,sans-serif;font-size: 13px;font-weight: normal;line-height: 1.4em;color: #444;margin-top: 10px;">'.
       '<p>'.$subjectPara1.'</p>'.
       '<p>'.$subjectPara2.'</p>'.
       '<p>'.$subjectPara3.'</p>'.
       '<p>'.$subjectPara4.'</p>'.
       '<p>'.$subjectPara5.'</p>'.
   '</div>'.  
'</div>'.


'</body></html>';

/*EMAIL TEMPLATE ENDS*/




$mail = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp.googlemail.com';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'github_notification@orientationsys.com';                 // SMTP username
$mail->Password = 'orientationsys7788';                           // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 587;                                    // TCP port to connect to

$mail->From = 'github_notification@orientationsys.com';
$mail->FromName = 'github notification';
// Send issues content to each email address
foreach($emailAddresses as $email){
	$mail->addAddress($email, 'Michael'); 	
}
//$mail->addAddress('tttccc@gmail.com', 'Michael');     // Add a recipient
//$mail->addAddress('ellen@example.com');               // Name is optional
$mail->addReplyTo('do-not-rely@orientationsys.com', 'auto-send email, do not reply');
//$mail->addCC('cc@example.com');
//$mail->addBCC('bcc@example.com');

//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);  
$mail->CharSet="UTF-8";                                // Set email format to HTML

if($objPayload->action === 'created'){
	$mail->Subject = 'github issue comment notification';
}else{
	$mail->Subject = 'github issue notification';
}
$mail->Body    = $message;
$mail->AltBody = 'github issue notification';



if(!$mail->send()) {
	$file = 'github_notification_error.txt';
	$current = file_get_contents($file);
	$current .= date('Y-m-d H:i:s ') . 'Message could not be sent. Mailer Error:'.$mail->ErrorInfo . "\n";
	file_put_contents($file, $current);
    
} else {
    echo 'Message has been sent';
};




/*

$subject = 'github issue notification';  //change subject of email
$from    = 'github_notification@orientationsys.com';    // give from email address

// mandatory headers for email message, change if you need something different in your setting.
$headers  = "From: " . $from . "\r\n";
$headers .= "Reply-To: ". $from . "\r\n";
//$headers .= "CC: test@example.com\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n"; 




/* Write into a file to testing pupose 
$file = 'github_update.txt';
// Open the file to get existing content
$current = file_get_contents($file);
// Append a new person to the file
$current .= date('Y-m-d H:i:s ') . $messages . "\n";
// Write the contents back to the file
file_put_contents($file, $current);
*/



?>
