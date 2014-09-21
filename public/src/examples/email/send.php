<?php
namespace vestibulum;

isset($this) && $this instanceof Vestibulum or die('Sorry can be executed only from Vestibulum');

// check AJAX request
isAjax() or json(['message' => 'Not AJAX request, but nice try :-)']);

// captcha check
$captcha = isset($_POST['captcha']) ? $_POST['captcha'] : null;
$captcha === 'Prague' or json(['error' => true, 'message' => 'Captcha failed! Please write Prague']);

// send email
$from = isset($_POST['from']) ? $_POST['from'] : 'nobody@nobody';
$subject = isset($_POST['subject']) ? $_POST['subject'] : null;
$message = isset($_POST['subject']) ? $_POST['subject'] : null;

if (mail('ozana@omdesign.cz', $subject, $message, "From: $from\n")) {
	json(['message' => 'Well done! Your message was send!', 'error' => false]);
} else {
	json(['message' => 'Something went wront :-(', 'error' => true]);
}

