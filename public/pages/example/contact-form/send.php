<?php
namespace cms {

	require_once __DIR__ . '/../../../../vendor/sphido/json/src/json.php';

	// is request made by Sphido
	isset($this) && $this instanceof Sphido or die('Sorry can be executed only from Sphido');

	// captcha check
	$captcha = isset($_POST['captcha']) ? $_POST['captcha'] : null;
	$captcha === 'Prague' or die(\json\error('Captcha failed! Please fix captcha first'));

	// send email
	$from = isset($_POST['from']) ? $_POST['from'] : 'nobody@nobody';
	$subject = isset($_POST['subject']) ? $_POST['subject'] : null;
	$message = isset($_POST['message']) ? $_POST['message'] : null;

	if (
		$from && $subject && $message && // check inputs values
		mail('ozana@omdesign.cz', $subject, $message, "From: $from\n") // send message
	) {
		die(\json\ok('Well done! Your message was send!'));
	} else {
		die(\json\error('Something went wront :-('));
	}
}