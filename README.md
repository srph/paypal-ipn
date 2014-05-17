PayPalIPN
======

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
### 1.0: Github release. Has has not yet been tested if it actually works.
Expect syntactical and logical bugs.

Todos:
- VERIFIED RESPONSE
- Test
- Composer implementation
