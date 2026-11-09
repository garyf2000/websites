<?php
// Maxy quote form handler: emails the request to the site owner.
// Change $to when the real inbox is ready.
$to = 'info@maxywebdesign.com';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: quote.html'); exit; }

// Honeypot: real visitors leave this empty.
if (!empty($_POST['website'])) { header('Location: quote.html?sent=1'); exit; }

function clean($v, $max = 2000) {
    $v = trim((string)$v);
    $v = str_replace(["\r", "\n"], ' ', $v);
    return mb_substr($v, 0, $max);
}
$name     = clean($_POST['name'] ?? '', 120);
$email    = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$business = clean($_POST['business'] ?? '', 160);
$type     = clean($_POST['type'] ?? '', 80);
$budget   = clean($_POST['budget'] ?? '', 80);
$details  = mb_substr(trim((string)($_POST['details'] ?? '')), 0, 4000);

if ($name === '' || !$email) { header('Location: quote.html?error=1'); exit; }

$host = preg_replace('/^www\./', '', $_SERVER['HTTP_HOST'] ?? 'localhost');
$from = 'quote@' . $host;   // must be an address on your own domain for Hostinger mail

$subject = 'New quote request from ' . $name;
$body  = "Name: $name\nEmail: $email\nBusiness: $business\nNeed: $type\nBudget: $budget\n\nAbout the project:\n$details\n";
$headers = "From: Maxy Website <$from>\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8\r\n";

$ok = mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $headers);
header('Location: quote.html?' . ($ok ? 'sent=1' : 'error=1'));
exit;
