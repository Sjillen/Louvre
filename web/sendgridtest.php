<?php
	// using SendGrid's PHP Library
// https://github.com/sendgrid/sendgrid-php
// If you are using Composer (recommended)
require __DIR__.'/../vendor/autoload.php';
// If you are not using Composer
// require("path/to/sendgrid-php/sendgrid-php.php");
$from = new SendGrid\Email("Example User", "test@example.com");
$subject = "Sending with SendGrid is Fun";
$to = new SendGrid\Email("Example User", "test@example.com");
$content = new SendGrid\Content("text/plain", "and easy to do anywhere, even with PHP");
$mail = new SendGrid\Mail($from, $subject, $to, $content);
$apiKey = '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';
$sg = new \SendGrid($apiKey);
$response = $sg->client->mail()->send()->post($mail);
echo $response->statusCode();
print_r($response->headers());
echo $response->body();