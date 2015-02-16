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
    'lz@orientationsys.com'
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
	
	//Use regular expression to check if issue body contains uploaded images
	$pattern = '/(!)(\[)((?:[a-z][a-z0-9_]*))(\])(\()((?:[a-z][a-z]+))(:)(\/)(\/)(cloud\.githubusercontent\.com)(\/)(assets)(\/).*?(\))/is';
	preg_match_all($pattern,$objPayload->comment->body,$match);
	
	if(count($match[0]) != 0){
		$pattern_url='/(\()(https)(:)(\/)(\/)(cloud\.githubusercontent\.com)(\/)(assets)(\/).*?(\))/is(() (https) (:) (/) (/) (cloud.githubusercontent.com) (/) (assets) (/) ())';
		foreach($match[0] as $image){
			$pattern_url='/((?:[a-z][a-z]+))(:)(\/)((?:\/[\w\.\-]+)+)/is';
			preg_match_all($pattern_url,$image,$image_url);
			$subjectPara4 = '<span style="color:#244F79">"'.str_replace($image, '<br><img src="'.$image_url[0][0].'"/><br>',$objPayload->comment->body).'"</strong>';	
		}
	}else{
		$subjectPara4 = '<span style="color:#244F79">"'.$objPayload->comment->body.'"</strong>';		
	}
	
	$subjectPara5 = date('Y-m-d H:i:s',strtotime($objPayload->comment->updated_at));
	
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
	//$subjectPara4 = '<strong>"'.$objPayload->issue->body.'"</strong>';
	//Use regular expression to check if issue body contains uploaded images
	$pattern = '/(!)(\[)((?:[a-z][a-z0-9_]*))(\])(\()((?:[a-z][a-z]+))(:)(\/)(\/)(cloud\.githubusercontent\.com)(\/)(assets)(\/).*?(\))/is';
	preg_match_all($pattern,$objPayload->issue->body,$match);
	
	if(count($match[0]) != 0){
		$pattern_url='/(\()(https)(:)(\/)(\/)(cloud\.githubusercontent\.com)(\/)(assets)(\/).*?(\))/is(() (https) (:) (/) (/) (cloud.githubusercontent.com) (/) (assets) (/) ())';
		foreach($match[0] as $image){
			$pattern_url='/((?:[a-z][a-z]+))(:)(\/)((?:\/[\w\.\-]+)+)/is';
			preg_match_all($pattern_url,$image,$image_url);
			$subjectPara4 = '<span style="color:#244F79">"'.str_replace($image, '<br><img src="'.$image_url[0][0].'"/><br>',$objPayload->issue->body).'"</strong>';	
		}
	}else{
		$subjectPara4 = '<span style="color:#244F79">"'.$objPayload->issue->body.'"</strong>';		
	}
	$subjectPara5 = date('Y-m-d H:i:s',strtotime($objPayload->issue->updated_at));

}else{
	exit(0);
}
/*$message = '<html>'.
'<head>'.
'<meta http-equiv="content-type" content="text/html; charset=utf-8">'.
'<title>Email notification</title>'.
'</head>'.
'<body>'.
'<div id="header" style="width: 80%;height: 20px;margin: 0 auto;padding: 10px; text-align: center; background-color: #E0E0E0;font-family: Open Sans,Arial,sans-serif;">'.
   //'<img height="50" width="220" style="border-width:0" src="'.$imgSrc.'" alt="'.$imgDesc.'" title="'.$imgTitle.'">'.
   ''.$subjectPara1.''.
'</div>'.

'<div id="outer" style="width: 80%;margin: 0 auto;margin-top: 10px;">'. 
   '<div id="inner" style="width: 78%;margin: 0 auto;background-color: #fff;font-family: Open Sans,Arial,sans-serif;font-size: 13px;font-weight: normal;line-height: 1.4em;color: #444;margin-top: 10px;">'.
       
       '<p>'.$subjectPara2.'</p>'.
       '<p>'.$subjectPara3.'</p>'.
       '<p>'.$subjectPara4.'</p>'.
       '<p>'.$subjectPara5.'</p>'.
   '</div>'.  
'</div>'.


'</body></html>';
*/

$message = <<< EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
      <title>Simples-Minimalistic Responsive Template</title>
      
      <style type="text/css">
         /* Client-specific Styles */
         #outlook a {padding:0;} /* Force Outlook to provide a "view in browser" menu link. */
         body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0;}
         /* Prevent Webkit and Windows Mobile platforms from changing default font sizes, while not breaking desktop design. */
         .ExternalClass {width:100%;} /* Force Hotmail to display emails at full width */
         .ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} /* Force Hotmail to display normal line spacing.*/
         #backgroundTable {margin:0; padding:0; width:100% !important; line-height: 100% !important;}
         img {outline:none; text-decoration:none;border:none; -ms-interpolation-mode: bicubic;}
         a img {border:none;}
         .image_fix {display:block;}
         p {margin: 0px 0px !important;}
         table td {border-collapse: collapse;}
         table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
         a {color: #0a8cce;text-decoration: none;text-decoration:none!important;}
         /*STYLES*/
         table[class=full] { width: 100%; clear: both; }
         /*IPAD STYLES*/
         @media only screen and (max-width: 640px) {
         a[href^="tel"], a[href^="sms"] {
         text-decoration: none;
         color: #0a8cce; /* or whatever your want */
         pointer-events: none;
         cursor: default;
         }
         .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
         text-decoration: default;
         color: #0a8cce !important;
         pointer-events: auto;
         cursor: default;
         }
         table[class=devicewidth] {width: 440px!important;text-align:center!important;}
         table[class=devicewidthinner] {width: 420px!important;text-align:center!important;}
         img[class=banner] {width: 440px!important;height:220px!important;}
         img[class=colimg2] {width: 440px!important;height:220px!important;}
         
         
         }
         /*IPHONE STYLES*/
         @media only screen and (max-width: 480px) {
         a[href^="tel"], a[href^="sms"] {
         text-decoration: none;
         color: #0a8cce; /* or whatever your want */
         pointer-events: none;
         cursor: default;
         }
         .mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
         text-decoration: default;
         color: #0a8cce !important; 
         pointer-events: auto;
         cursor: default;
         }
         table[class=devicewidth] {width: 280px!important;text-align:center!important;}
         table[class=devicewidthinner] {width: 260px!important;text-align:center!important;}
         img[class=banner] {width: 280px!important;height:140px!important;}
         img[class=colimg2] {width: 280px!important;height:140px!important;}
         td[class=mobile-hide]{display:none!important;}
         td[class="padding-bottom25"]{padding-bottom:25px!important;}
        
         }
      </style>
   </head>
   <body>

<!-- Start of seperator -->
<table width="100%" bgcolor="#ffffff" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="seperator">
   <tbody>
      <tr>
         <td>
            <table width="800" align="center" cellspacing="0" cellpadding="0" border="0" class="devicewidth">
               <tbody>
                  <tr>
                     <td align="center" height="30" style="font-size:1px; line-height:1px;">&nbsp;</td>
                  </tr>
                  <tr>
                     <td width="550" align="center" height="1" bgcolor="#d1d1d1" style="font-size:1px; line-height:1px;">&nbsp;</td>
                  </tr>
                  <tr>
                     <td align="center" height="30" style="font-size:1px; line-height:1px;">&nbsp;</td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- End of seperator -->   
<!-- Start Full Text -->
<table width="100%" bgcolor="#ffffff" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="full-text">
   <tbody>
      <tr>
         <td>
            <table width="800" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
               <tbody>
                  <tr>
                     <td width="100%">
                        <table width="800" cellpadding="0" cellspacing="0" border="0" align="center" class="devicewidth">
                           <tbody>
                              <!-- Spacing -->
                              <tr>
                                 <td height="5" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td>
                                    <table width="800" align="center" cellpadding="0" cellspacing="0" border="0" class="devicewidthinner">
                                       <tbody>
                                          <!-- Title -->
                                          <tr>
                                             <td style="font-family: Helvetica, arial, sans-serif; font-size: 24px; color: #333333; text-align:center; line-height: 30px;" st-title="fulltext-title">
											 {$subjectPara1}
                                             </td>
                                          </tr>
                                          <!-- End of Title -->
                                          <!-- spacing -->
                                          <tr>
                                             <td width="100%" height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                                          </tr>
                                          <!-- End of spacing -->
                                          <!-- content -->
										  <tr>
											<td style="font-family: Helvetica, arial, sans-serif; font-size: 16px; color: #666666; text-align:center; line-height: 30px;" st-content="fulltext-content">
												<p>{$subjectPara2}</p>
												<p>{$subjectPara3}</p>
											</td>
										  </tr>
                                          <tr>
                                             <td style="font-family: Helvetica, arial, sans-serif; font-size: 16px; color: #666666; text-align:left; line-height: 30px;" st-content="fulltext-content">
                                                
												<p>{$subjectPara4}</p>
												
                                             </td>
                                          </tr>
										  <tr>
											<td style="font-family: Helvetica, arial, sans-serif; font-size: 14px; color: #666666; text-align:right; line-height: 30px;" st-content="fulltext-content">
												<p>{$subjectPara5}</p>
											</td>
										  </tr>
                                          <!-- End of content -->
                                       </tbody>
                                    </table>
                                 </td>
                              </tr>
                              <!-- Spacing -->
                              <tr>
                                 <td height="20" style="font-size:1px; line-height:1px; mso-line-height-rule: exactly;">&nbsp;</td>
                              </tr>
                              <!-- Spacing -->
                           </tbody>
                        </table>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- end of full text -->
<!-- Start of seperator -->
<table width="100%" bgcolor="#ffffff" cellpadding="0" cellspacing="0" border="0" id="backgroundTable" st-sortable="seperator">
   <tbody>
      <tr>
         <td>
            <table width="800" align="center" cellspacing="0" cellpadding="0" border="0" class="devicewidth">
               <tbody>
                  <tr>
                     <td align="center" height="30" style="font-size:1px; line-height:1px;">&nbsp;</td>
                  </tr>
                  <tr>
                     <td width="800" align="center" height="1" bgcolor="#d1d1d1" style="font-size:1px; line-height:1px;">&nbsp;</td>
                  </tr>
                  <tr>
                     <td align="center" height="30" style="font-size:1px; line-height:1px;">&nbsp;</td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<!-- End of seperator -->  

   
   </body>
   </html>
EOF;


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
