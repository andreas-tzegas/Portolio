<?php
// send_mail.php
header('Content-Type: text/html; charset=UTF-8');

$TO_EMAIL       = 'AndreasXega@proton.me';
$SUBJECT_PREFIX = '📬 Contact Portfolio — ';
$FROM_EMAIL     = 'no-reply@votre-domaine.tld'; // adapte au domaine
$FROM_NAME      = 'Portfolio Contact';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit('Méthode non autorisée.');
}

// Honeypot
if (!empty($_POST['website'])) {
  http_response_code(200);
  exit('OK');
}

function clean($v) {
  return trim(filter_var($v, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES));
}

$name    = clean($_POST['name']    ?? '');
$email   = trim($_POST['email']    ?? '');
$subject = clean($_POST['subject'] ?? '');
$message = trim($_POST['message']  ?? '');

$errors = [];
if ($name === '' || mb_strlen($name) < 2) $errors[] = 'Nom invalide.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide.';
if ($message === '' || mb_strlen($message) < 5) $errors[] = 'Message trop court.';

if ($errors) {
  http_response_code(422);
  echo '<!doctype html><meta charset="utf-8"><title>Erreur</title>';
  echo '<p>Merci de corriger :</p><ul>';
  foreach ($errors as $e) echo '<li>'.htmlspecialchars($e, ENT_QUOTES, 'UTF-8').'</li>';
  echo '</ul><p><a href="contact.html">Retour au formulaire</a></p>';
  exit;
}

$finalSubject = $SUBJECT_PREFIX . ($subject !== '' ? $subject : 'Nouveau message');
$finalSubject = '=?UTF-8?B?'.base64_encode($finalSubject).'?=';

$ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

$bodyText =
"Vous avez reçu un nouveau message depuis le portfolio.\n\n".
"Nom: {$name}\n".
"Email: {$email}\n".
"Sujet: ".($subject !== '' ? $subject : '(non précisé)')."\n".
"IP: {$ip}\n\n".
"Message:\n{$message}\n";

$bodyHtml = '<html><body style="font-family:Arial,Helvetica,sans-serif;line-height:1.6;color:#222">'.
  '<h2>Nouveau message — Portfolio</h2>'.
  '<p><strong>Nom:</strong> '.htmlspecialchars($name, ENT_QUOTES, 'UTF-8').'</p>'.
  '<p><strong>Email:</strong> '.htmlspecialchars($email, ENT_QUOTES, 'UTF-8').'</p>'.
  '<p><strong>Sujet:</strong> '.htmlspecialchars(($subject !== '' ? $subject : '(non précisé)'), ENT_QUOTES, 'UTF-8').'</p>'.
  '<p><strong>IP:</strong> '.htmlspecialchars($ip, ENT_QUOTES, 'UTF-8').'</p>'.
  '<hr>'.
  '<p>'.nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8')).'</p>'.
  '</body></html>';

$boundary = '=_Part_'.md5(uniqid('', true));

$headers  = 'From: '.mb_encode_mimeheader($FROM_NAME, 'UTF-8').' <'.$FROM_EMAIL.'>'."\r\n";
$headers .= 'Reply-To: '.mb_encode_mimeheader($name, 'UTF-8').' <'.$email.'>'."\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= 'Content-Type: multipart/alternative; boundary="'.$boundary."\"\r\n";

$body  = "--$boundary\r\n";
$body .= "Content-Type: text/plain; charset=UTF-8\r\n";
$body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
$body .= $bodyText . "\r\n";
$body .= "--$boundary\r\n";
$body .= "Content-Type: text/html; charset=UTF-8\r\n";
$body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
$body .= $bodyHtml . "\r\n";
$body .= "--$boundary--\r\n";

$ok = @mail($TO_EMAIL, $finalSubject, $body, $headers);

if ($ok) {
  echo '<!doctype html><meta charset="utf-8"><title>Merci</title>';
  echo '<p>✅ Message envoyé. Je vous réponds au plus vite.</p>';
  echo '<p><a href="index.html">Retour à l’accueil</a> — <a href="contact.html">Envoyer un autre message</a></p>';
} else {
  http_response_code(500);
  echo '<!doctype html><meta charset="utf-8"><title>Erreur</title>';
  echo '<p>❌ Envoi impossible (fonction mail() indisponible sur le serveur).</p>';
  echo '<p>Contactez-moi directement à <a href="mailto:'.$TO_EMAIL.'">'.$TO_EMAIL.'</a>.</p>';
}