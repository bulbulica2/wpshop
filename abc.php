<?php
$to      = 'necrocris2000@yahoo.com';
$subject = 'the subject';
$message = 'hello';
$headers = 'From: flavia@dreamlandbyflavia.com ' . "\r\n" .
    'Reply-To: flavia@dreamlandbyflavia.com ' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);