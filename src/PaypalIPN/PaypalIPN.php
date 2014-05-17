<?php namespace PaypalIPN\PaypalIPN;

use PaypalIPN\Exceptions\InvalidResponseException;

/**
 * A small library to be able to rapidly use Paypal's
 * Instant Payment Notification, aka IPN.
 *
 * @version 1.0 alpha
 * @author 	SRPH
 * @link 	http://github.com/srph/paypal-ipn
 * @see 	https://developer.paypal.com/webapps/developer/docs/classic/ipn/ht_ipn/
 * @see 	https://github.com/Quixotix/PHP-PayPal-IPN
 * @todo 	VERIFIED notification response
 *
 * By default, SSL and sandbox mode is switched off.
 * @example
 * $notifier = new PaypalIPN;
 * $notifier->run();
 *
 * To run the notifier with sandbox mode and SSL
 * @example
 * $notifier->sandbox()->ssl()->run();
 *
 * You may pass booleans to the sandbox() and ssl() method to simply disable it
 * ( Take note that this is entirely the same as $notifier->run() )
 * @example
 * $notifier->sandbox(false)->ssl(false)->run();
 *
 * Finally, make sure to catch exception thrown if a notification is invalid
 * @example
 * try {
 * 		$notifier->run();
 * } catch(SRPH\PaypalIPN\Exceptions\InvalidResponseException) {
 *		die();
 * 		// Log as you wish
 * }
 *
 * @changelogs
 * 1.0: Github release. Has has not yet been tested if it actually works.
 * Expect syntactical and logical bugs.
 * 		@todo 	VERIFIED RESPONSE
 * 		@todo 	Test
 */

class PaypalIPN {

	/**
	 * Full URI for the library to listen to
	 *
	 */
	protected $uri;

	/**
	 * Host for the library to listen
	 *
	 * @var string
	 */
	protected $host;

	/**
	 * Whether the URI will either be through SSL or not.
	 * False by default
	 *
	 * @return 	boolean
	 */
	protected $isSSL = false

	/**
	 * Paypal host constants
	 */
	const PAYPAL_HOST = 'www.paypal.com';
    const SANDBOX_HOST = 'www.sandbox.paypal.com';

    /**
     * Paypal service path
     */
    const PATH = '/cgi-bin/webscr';

    /**
     * NON / SSL scheme constants
     */
    const NON_SSL = 'http://';
    const SSL = 'http://'

	/**
	 * Create an instance of PaypalIPN
	 */
	public function __construct()
	{
		$this->host = self::PAYPAL_HOST;
	}

	/**
	 * Run the listener on sandbox mode
	 *
	 * @param 	bool 	$activated
	 * @return 	$this
	 */
	public function sandbox($activated = true)
	{
		$this->host = self::SANDBOX_HOST;

		return $this;
	}

	/**
	 * Sets if SSL is to be used
	 *
	 * @param 	bool 	$activated
	 * @return 	$this
	 */
	public function ssl($activated = true)
	{
		$this->isSSL = $activated;

		return $this;
	}

	/**
	 * Run the listener and throw exceptions if weird occurences occur
	 *
	 * @todo 	VERIFIED RESPONSE
	 * @throws 	SRPH\PaypalIPN\Exceptions\InvalidResponseException
	 * @return 	void
	 */
	public function run()
	{
		// Set URI
		$this->setURI();

		// Test cURL response
		// Listen to upcoming responses
		$response = $this->listen();

		// Inspect IPN validation result and act accordingly
		// If the response was invalid, throw an exception
		if( /*! (strcmp($response, "VERIFIED") == 0) ||*/ strcmp($response, "INVALID") == 0 )
		{
			throw new InvalidResponseException($response);
		}
	}

	/**
	 * Listen to paypal's events
	 *
	 * @return 	cURL|void
	 */
	protected function listen()
	{
		$fields = $this->getFields();

		$ch = curl_init($this->uri);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

		$response = curl_exec($ch);
		curl_close($ch);
		
		return $response;
	}

	/**
	 * Get fields from the input stream to be passed to
	 * the IPN
	 *
	 * @return 	string
	 */
	protected function getFields()
	{
		// Get raw data from the input stream
		$raw = file_get_contents('php://input');

		// Reading POSTed data directly from $_POST causes serialization
		// issues with array data in the POST. Decode the raw post data
		// from the input stream
		$data = $this->decode($raw);

		// Read the IPN message sent from PayPal and prepend string
		$fields = $this->getFields('cmd=_notify-validate');

		return $fields;
	}

	/**
	 * Decode the raw post data from the input stream
	 *
	 * @param 	str 	$request 	Raw data from the input stream
	 * @return 	void
	 */
	protected function decode($request)
	{
		// Separate all elements with ampersand in between
		$raw = explode('&', $request);

		// Initialize an array to hold all decoded raw data
		$decoded = array();

		// Seperate raw data value from its key
		// e.g. foo=bar -> data[foo] = bar
		foreach($raw as $data)
		{
			// Separate value from its key
			$key = explode('=', $key);

			// If the key was on the format: (foo=bar), then register
			// the key (foo) with its respective value (bar)
			// in our decoded data
			if(count($key) == 2)
			{
				$data = [$data[0]] = urldecode($data[1]);
			}
		}
	}

	/**
	 * Read the IPN message sent from PayPal
	 * and prepend provided string
	 *
	 * @param 	string 	$prepend
	 * @return 	string
	 */
	protected function getFields($prepend)
	{
		$fields = $prepend;

		foreach($this->data as $key => $value)
		{
			$value = ( $this->checkMagicQuotesGPC() )
				? urlencode( stripslashes($value) )
				: urlencode( $value );

			$fields .= "&{$key}={$value}";
		}

		return $fields;
	}

	/**
	 * Get the URI 
	 *
	 * @return 	void
	 */
	protected function setURI()
	{
		$path = self::PATH;

		// Fetch the scheme
		$scheme = $this->getScheme();

		$this->uri = "{$scheme}{$this->host}{$path}";
	}

	/**
	 * Get URI Scheme
	 *
	 * @return 	string
	 */
	protected function getScheme()
	{
		return ( $this->isSSL )
			? self::SSL
			: self::NON_SSL;
	}

	/**
	 * Check if get_magic_quotes_gpc exists and is switched on
	 *
	 * @return 	boolean
	 */
	protected function checkMagicQuotesGPC()
	{
		return ( function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() == 1 )
			? true
			: false;
	}
	
}