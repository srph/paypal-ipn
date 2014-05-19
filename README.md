PayPalIPN
======

Inspired by [PHP-PayPal-IPN](https://github.com/Quixotix/PHP-PayPal-IPN) by [Quixotix](https://github.com/Quixotix). Although the library was working,, I thought it needed some rewrite. I also wanted to rewrite it in my style. The library also seemed to have been unupdated for years. Additionally, it has no composer implementation. If am wrong about anything, I stand corrected.

### By default, SSL and sandbox mode is switched off.
      $notifier = new PaypalIPN;
      $notifier->run();

### To run the notifier with sandbox mode and SSL
      $notifier->sandbox()->ssl()->run();

### You may pass booleans to the sandbox() and ssl() method to simply disable it
_( Take note that this is entirely the same as $notifier->run() )_

      $notifier->sandbox(false)->ssl(false)->run();

### Finally, make sure to catch exception thrown if a notification is invalid
	try {
		$notifier->run();
	} catch(SRPH\PaypalIPN\Exceptions\InvalidResponseException) {
		die();
    	// Log as you wish
	}

## Changelogs

Changelogs are available in the wiki.
