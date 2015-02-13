<?php  

/**
 *
 * @package  core php  
 * @author   Michael Cheng <michael@orientationsys.com>
 * @description 
 *  Use it as a githhub webhook script to send issue activities notification email
 *  to specific email addresses
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

$imgSrc   = 'https://www.google.com/images/srpr/logo11w.png'; // Change image src to your site specific settings
$imgDesc  = 'google search logo'; // Change Alt tag/image Description to your site specific settings
$imgTitle = 'Google Logo'; // Change Alt Title tag/image title to your site specific settings

/*
Change your message body in the given $subjectPara variables. 
$subjectPara1 means 'first paragraph in message body', $subjectPara2 means'first paragraph in message body',
if you don't want more than 1 para, just put NULL in unused $subjectPara variables.
*/
$subjectPara1 = 'issue('.$objPayload->issue->id.'): '.$objPayload->issue->title.' has just been '.$objPayload->action.'.';
$subjectPara3 = NULL;
$subjectPara4 = NULL;
$subjectPara5 = NULL;

$message = '<!DOCTYPE HTML>'.
'<head>'.
'<meta http-equiv="content-type" content="text/html">'.
'<title>Email notification</title>'.
'</head>'.
'<body>'.
//'<div id="header" style="width: 80%;height: 60px;margin: 0 auto;padding: 10px;color: #fff;text-align: center;background-color: #E0E0E0;font-family: Open Sans,Arial,sans-serif;">'.
//   '<img height="50" width="220" style="border-width:0" src="'.$imgSrc.'" alt="'.$imgDesc.'" title="'.$imgTitle.'">'.
//'</div>'.

'<div id="outer" style="width: 80%;margin: 0 auto;margin-top: 10px;">'. 
   '<div id="inner" style="width: 78%;margin: 0 auto;background-color: #fff;font-family: Open Sans,Arial,sans-serif;font-size: 13px;font-weight: normal;line-height: 1.4em;color: #444;margin-top: 10px;">'.
       '<p>'.$subjectPara1.'</p>'.
       '<p>'.$subjectPara2.'</p>'.
       '<p>'.$subjectPara3.'</p>'.
       '<p>'.$subjectPara4.'</p>'.
       '<p>'.$subjectPara5.'</p>'.
   '</div>'.  
'</div>'.


'</body>';

/*EMAIL TEMPLATE ENDS*/

$subject = 'github issue notification';  //change subject of email
$from    = 'notification@github.com';                           // give from email address

// mandatory headers for email message, change if you need something different in your setting.
$headers  = "From: " . $from . "\r\n";
$headers .= "Reply-To: ". $from . "\r\n";
//$headers .= "CC: test@example.com\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n"; 

// Send issues content to each email address
foreach($emailAddresses as $email){
	mail($email, $subject, $message, $headers);
}


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
