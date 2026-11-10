<?php
// Maxy quote form handler.
// 1) Emails the request to the site owner ($to).
// 2) Emails a confirmation to the customer, built from what they typed in the form.
$to = 'info@maxywebdesign.com';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: quote.html'); exit; }

// Honeypot: real visitors leave this empty.
if (!empty($_POST['website'])) { header('Location: quote.html?sent=1'); exit; }

// Single-line fields: strip line breaks (blocks header injection) and cap length.
function clean($v, $max = 2000) {
    $v = trim((string)$v);
    $v = str_replace(["\r", "\n"], ' ', $v);
    return mb_substr($v, 0, $max);
}
function h($v) { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
function subj($s) { return '=?UTF-8?B?' . base64_encode($s) . '?='; }

$name     = clean($_POST['name'] ?? '', 120);
$email    = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$business = clean($_POST['business'] ?? '', 160);
$type     = clean($_POST['type'] ?? '', 80);
$budget   = clean($_POST['budget'] ?? '', 80);
$details  = mb_substr(trim((string)($_POST['details'] ?? '')), 0, 4000);

if ($name === '' || !$email) { header('Location: quote.html?error=1'); exit; }

$from = 'info@maxywebdesign.com';   // must be a real mailbox on your Hostinger domain

// The form fields, in one place. Both emails are generated from this list.
$fields = [
    'Name'     => $name,
    'Email'    => (string)$email,
    'Business' => $business,
    'Need'     => $type,
    'Budget'   => $budget,
];

/* ---------- 1) Email to the site owner (plain text) ---------- */
$ownerBody = '';
foreach ($fields as $label => $value) { $ownerBody .= "$label: $value\n"; }
$ownerBody .= "\nAbout the project:\n$details\n";
$ownerHeaders = "From: Maxy Website <$from>\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8\r\n";
$ok = mail($to, subj('New quote request from ' . $name), $ownerBody, $ownerHeaders);

/* ---------- 2) Confirmation email to the customer (HTML + plain text) ---------- */
$first = trim(explode(' ', $name)[0]);

// Plain-text version
$txt  = "Hi $first,\n\n";
$txt .= "Thank you for asking Maxy Web Design for a quote. We have your request and will reply within one business day with a price range and a plan.\n\n";
$txt .= "What you sent us:\n";
foreach ($fields as $label => $value) {
    if ($label === 'Name' || $label === 'Email' || $value === '') continue;
    $txt .= "  $label: $value\n";
}
if ($details !== '') { $txt .= "\nAbout the project:\n$details\n"; }
$txt .= "\nIf you need to add something, just reply to this email.\n\nMaxy Web Design\ninfo@maxywebdesign.com\n";

// HTML version: one table row per form field
$rows = '';
foreach ($fields as $label => $value) {
    if ($label === 'Name' || $label === 'Email' || $value === '') continue;
    $rows .= '<tr><td style="padding:8px 16px 8px 0;color:#71717A;font-size:13px;white-space:nowrap">' . h($label)
          .  '</td><td style="padding:8px 0;color:#18181B;font-size:15px">' . h($value) . '</td></tr>';
}
$detailsHtml = $details !== ''
    ? '<p style="margin:20px 0 6px;color:#71717A;font-size:13px">About the project</p><p style="margin:0;color:#18181B;font-size:15px;line-height:1.6">' . nl2br(h($details)) . '</p>'
    : '';

$html = '<!doctype html><html><body style="margin:0;background:#F4F4F5;font-family:Arial,Helvetica,sans-serif">'
  . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr><td align="center" style="padding:24px 12px">'
  . '<table role="presentation" width="560" cellpadding="0" cellspacing="0" style="max-width:560px;width:100%;background:#FFFFFF;border-radius:12px;overflow:hidden">'
  . '<tr><td style="background:#09090B;padding:24px 28px"><span style="font-size:22px;font-weight:700;color:#FBBF24">Maxy</span> <span style="font-size:12px;color:#A1A1AA;letter-spacing:.1em">WEB DESIGN</span></td></tr>'
  . '<tr><td style="padding:28px">'
  . '<h1 style="margin:0 0 12px;font-size:22px;color:#18181B">Thanks, ' . h($first) . '. We have your request.</h1>'
  . '<p style="margin:0 0 20px;color:#3F3F46;font-size:15px;line-height:1.6">We will reply within one business day with a price range and a plan.</p>'
  . '<p style="margin:0 0 6px;color:#18181B;font-size:14px;font-weight:700">What you sent us</p>'
  . '<table role="presentation" cellpadding="0" cellspacing="0" style="border-top:1px solid #E4E4E7;width:100%">' . $rows . '</table>'
  . $detailsHtml
  . '<p style="margin:28px 0 0;color:#3F3F46;font-size:14px;line-height:1.6">Need to add something? Just reply to this email.</p>'
  . '</td></tr>'
  . '<tr><td style="padding:16px 28px;background:#FAFAFA;color:#71717A;font-size:12px">Maxy Web Design · <a href="mailto:info@maxywebdesign.com" style="color:#71717A">info@maxywebdesign.com</a></td></tr>'
  . '</table></td></tr></table></body></html>';

// multipart/alternative: mail apps show the HTML, or fall back to the text
$boundary = 'maxy_' . bin2hex(random_bytes(8));
$custHeaders  = "From: Maxy Web Design <$from>\r\nReply-To: $to\r\nMIME-Version: 1.0\r\n";
$custHeaders .= "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n";
$custBody  = "--$boundary\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($txt));
$custBody .= "--$boundary\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($html));
$custBody .= "--$boundary--";
@mail($email, subj('We got your quote request, Maxy Web Design'), $custBody, $custHeaders);

// The visitor sees success if the owner copy went out; the customer copy is a courtesy.
header('Location: quote.html?' . ($ok ? 'sent=1' : 'error=1'));
exit;
