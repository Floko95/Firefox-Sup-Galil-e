<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function envoyerMail($destinataire, $id, $type, $code) {
	require $_SERVER['DOCUMENT_ROOT'] . 'bde6/mail/Exception.php';
	require $_SERVER['DOCUMENT_ROOT'] . 'bde6/mail/PHPMailer.php';
	require $_SERVER['DOCUMENT_ROOT'] . 'bde6/mail/SMTP.php';

	$mail = new PHPMailer;
	$mail->isSMTP(); 
	$mail->SMTPDebug = 2; // 0 = off (for production use) - 1 = client messages - 2 = client and server messages
	$mail->Host = "smtp.univ-paris13.fr"; // use $mail->Host = gethostbyname('smtp.gmail.com'); // if your network does not support SMTP over IPv6
	$mail->Port = 465; // TLS only
	$mail->SMTPSecure = 'ssl'; // ssl is deprecated
	$mail->SMTPAuth = true;
	$mail->Username = '11709182'; // email
	$mail->Password = '1008020279S'; // password
	$mail->setFrom('yannis.jacoud@edu.univ-paris13.fr', 'BDE'); // From email and name
	$mail->addAddress($destinataire, 'Mr. Brown'); // to email and name
	if ($type == 1) {
		$mail->Subject = 'BDE : Validation de votre compte';
	} else if ($type == 2) {
		$mail->Subject = 'BDE :Réinitialisation du mot de passe';
	}
	
	$mail->msgHTML("Afin de valider votre compte, veuillez cliquer sur ce lien\n\nhttp://localhost/bde6/php/verification.php?id=".$id."&code=".$code); //$mail->msgHTML(file_get_contents('contents.html'), __DIR__); //Read an HTML message body from an external file, convert referenced images to embedded,
	$mail->AltBody = 'HTML messaging not supported'; // If html emails is not supported by the receiver, show this body
	// $mail->addAttachment('images/phpmailer_mini.png'); //Attach an image file

	if(!$mail->send()){
		echo "Mailer Error: " . $mail->ErrorInfo;
	}else{
		echo "Message sent!";
	}
}

function chaineAleatoire($taille) {
  do {
    $code = bin2hex(openssl_random_pseudo_bytes($taille, $cstrong));
  }
  while ($code == false);
  return $code;
}

function text($chaine) {
	return htmlspecialchars($chaine, ENT_QUOTES, 'UTF-8', false);
}
