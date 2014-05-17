PayPalIPN
======

By default, SSL and sandbox mode is switched off.
@example
$notifier = new PaypalIPN;
$notifier->run();

To run the notifier with sandbox mode and SSL
@example
$notifier->sandbox()->ssl()->run();

You may pass booleans to the sandbox() and ssl() method to simply disable it
( Take note that this is entirely the same as $notifier->run() )
@example
$notifier->sandbox(false)->ssl(false)->run();

Finally, make sure to catch exception thrown if a notification is invalid
@example
try {
  $notifier->run();
} catch(SRPH\PaypalIPN\Exceptions\InvalidResponseException) {
 	die();
	// Log as you wish
}

Changelogs
1.0: Github release. Has has not yet been tested if it actually works.
Expect syntactical and logical bugs.
		@todo 	VERIFIED RESPONSE
		@todo 	Test
		@todo 	Composer implementation
