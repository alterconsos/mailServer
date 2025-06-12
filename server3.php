<?php
/**
 * This example shows making an SMTP connection with authentication.
 */
//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
//date_default_timezone_set('Etc/UTC');

use PHPMailer\PHPMailer\Exception;

require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';
 
// 'secure' => 'tls' or 'ssl' ou absent
 
$mailerA = array(
 	'name' => '[Alterconsos A]',
 	'host'=> 'compote.o2switch.net',
 	'port' => '465',
 	'username' => 'appli.hebdo@alterconsos.fr',
 	'secure' => 'ssl',
 	'auth' => true,
 	'smtp' => true
);
 
$mailerB = array(
 	'name' => '[Alterconsos B]',
 	'host'=> 'auth.smtp.1and1.fr',
 	'port' => '587',
 	'username' => '...',
 	'secure' => 'tls',
 	// 'secure' => 'tls' or 'ssl' ; // (fac)
 	'auth' => true,
 	'smtp' => false
);
 
$mailers = array(
	'A' => $mailerA,
 	'B' => $mailerB
);

$blacklist = ['vhouillon@sfr.fr',
'leboeuf.christine@neuf.fr',
'patriceregimbart@gmail.com',
'jeanlouis.chace@sfr.fr',
'nathalie.chace@sfr.fr',
'lize.alice@neuf.fr',
'tony2016@netc.fr',
'peyrataud.laetitia@neuf.fr',
'pasgwe@numericable.com',
'martine.morera@numericable.fr',
'martinferte@orange.fr',
'ingrid.cros@numericable.com',
'valy1903@sfr.fr',
'haroun.hannifa@gmail.com',
'f.clavagnier@gmail.com',
'madalen.touret@gmail.com',
'seb_theurel@yahoo.fr',
'anne.verot@beauxartsparis.fr',
'patricia.dasneves@sfr.fr',
'jguerillot@sfr.fr',
'mathieu.jourdan@netcourrier.com',
'samarsy@inbox-group.com',
'fpillot@noos.fr',
'didier.pasquiet@sfr.fr',
'ouly@free.fr',
'gjuil@sfr.fr',
'pascale10.rey@sfr.fr',
'ram.lepeltier@sfr.fr',
'daddiecool@aliceadsl.fr',
'patrick.boumard2@wanadoo.fr',
'denis.delrieu@legumesdesjours.fr',
'annouv@free.fr',
'mathieu.jourdan@netcourrier.com'
// 'daniele.bordessoule@neuf.fr',
// 'eetter@sfr.fr',
// 'delphinerobaglia@sfr.fr',
// 'lm0308@numericable.fr',
];

$arg = $_POST;

$ok = isset($arg['mdp']);

if ($ok && isset($arg['mailer'])) {
 	$mailer = $mailers[$arg['mailer']];
 	if (isset($mailer)) {
 		$mdp = $arg['mdp'];
 		$from = $mailer['username'];
 	} else
 		$ok = false;
} else 
 	$ok = false;

// $smtp = isset($arg['smtp']);
$smtp = $mailer['smtp'];
 
if ($ok && isset($arg['to'])) {
 	$to = explode(",", $arg['to']);
 	if (!isset($to) || count($to) <= 0)
 		$ok = false;
} else 
 	$ok = false;
 
if ($ok && isset($arg['subject']))
 	$subject = $arg['subject'];
else
 	$ok = false;
 
if ($ok && isset($arg['text']))
 	$text = $arg['text'];
else
 	$ok = false;
 
date_default_timezone_set('Europe/Paris');
$date = date('Y-m-d H:i:s', time());

$bl = false;

if ($ok) {
 	try {
		$mail = new PHPMailer;
 		$mail->CharSet = "UTF-8";
		
		//Tell PHPMailer to use SMTP : ne marche pas chez 1and1 ;
		if ($smtp) 
			$mail->isSMTP(); 
		else
			$mail->isMail(); // SMTP marche pas chez 1and1 ;	
		
		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$mail->SMTPDebug = 0;

 		$mail->Host = $mailer['host']; // Specify main and backup server
		$mail->Port = $mailer['port'];
 		$mail->SMTPAuth = true; // Enable SMTP authentication
 		$mail->Username = $mailer['username']; // SMTP username
 		$mail->Password = $mdp;
  		if (isset($mailer['secure']))
 			$mail->SMTPSecure = $mailer['secure']; // 'tls' Enable encryption, 'ssl' also accepted
  		
 		$mail->From = $mailer['username'];
 		$mail->FromName = $mailer['name'];
 		for($x = 0; $x < count($to); $x++){
			if (in_array($to[$x], $blacklist)) {
				$ok = false;
				$bl = true;
				$err = "KO : blacklist ".$to[$x];
			} else {
 				$mail->addAddress($to[$x]); // Add a recipient
			}
 		}

		if ($ok) {
			$mail->Subject = $subject;
			$mail->isHTML(true); // Set email format to HTML
			
			$mail->Body = $text;
			
			$msg1 = $date . " " . $subject . " " . strlen($text) . "c " . $arg['to'];
			error_log($msg1);
			if(!$mail->send()) {
				$err = "KO : ".$mail->ErrorInfo;
			} else
				$err = "OK : ".$date;
		}

 	} catch (Exception $e) {
 		$err = "KO : ".$e->getMessage();
 	}
}

if ($ok) {
	echo $err;
} else {
	if ($bl) 
		echo "OK : ".$date." blacklist";
	else
		echo $date." ".$err;
	error_log($err);
}

?>