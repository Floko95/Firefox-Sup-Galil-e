<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function envoyerMail($destinataire, $id, $type, $code) {
	require $_SERVER['DOCUMENT_ROOT'] . 'Firefox-Sup-Galil-e/mail/Exception.php';
	require $_SERVER['DOCUMENT_ROOT'] . 'Firefox-Sup-Galil-e/mail/PHPMailer.php';
	require $_SERVER['DOCUMENT_ROOT'] . 'Firefox-Sup-Galil-e/mail/SMTP.php';

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
		$message = 'Afin de valider votre inscription, veuillez cliquer sur ce lien\n\nhttp://localhost/Firefox-Sup-Galil-e/php/verification.php?id='.$id.'&code='.$code;
	} else if ($type == 2) {
		$mail->Subject = 'BDE :Réinitialisation du mot de passe';
		$message = 'Afin de choisir un nouveau mot de passe, veuillez cliquer sur ce lien\n\nhttp://localhost/Firefox-Sup-Galil-e/php/modification.php?id='.$id.'&code='.$code;
	}
	
	$mail->msgHTML($message);
	$mail->AltBody = 'HTML messaging not supported';

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
