<pre>
<?php
$api = new Moto_SOME_CLASS_NAME();
$api->setOption('key', 'tmru-photolanding');
$api->setOption('pass', 'c4ca4238a0b923820dcc509a6f75849b');
$api->setOption('url', 'http://accounts.cms-guide.com.rogue.fmt/api.shop.photolanding.php');
$api->setOption('debug', true);

$data = array(
	'email' => 'RoGue@devoffice.com',
	'name' => ' Rogue Test',
	'phone' => '38054545454',
	'domain' => 'asdasdsa.com',
	'template' => 38058,
/*
1	Domain, Hosting 6m, Template
2	Domain, Hosting 12m, Template
3	Domain, Hosting 36m, Template
*/
	'product_id' => 2,

/*
1	PayPal [TM]
2	MoneyBookers [TM]
3	WebMoney [TM]
*/	
	'merchant_id' => 3, //PayPal: 1; WM : 3
	'locale' => 'ru',

	'remote_addr' => '127.0.0.1', // IP кастомера
	'language' => 'en-US, English[en]; en-US,en;q=0.5', // language браузер
	'user_agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0',
	'referer' => 'http://accounts.cms-guide.com.rogue.fmt/',


	'hosting' => 'plan12', //*
	'vk_id' => 11111111, //*
	'discount' => 1, //*
	'price' => 349, //*
	'real_price' => 420, //*
	'success' => 'http://www.templatemonster.com.fmt/ru/photolanding/success/',
	'fail' => 'http://www.templatemonster.com.fmt/ru/photolanding/fail/',
	//'promocode' => '',
	//'local_time' => 'Wed Jan 02 2013 17:33:18 GMT+0200 (EET) (GMT+0200)',
	
);

$result = $api->send('getLinkByOrder', $data);
echo "<hr>";
echo htmlspecialchars(print_r($result, true));

class Moto_SOME_CLASS_NAME
{
	/**
	 *
	 * @var array
	 */
	protected $_options = array();
	/**
	 *
	 * @var array
	 */
	protected $_defaultOptions = array(
		'url' => '',
		'key' => '',
		'pass' => '',
		'CURLOPT_CONNECTTIMEOUT' => 10,
		'CURLOPT_TIMEOUT' => 20,
		'cookies' => array(
			'aff', 'referal',
		),
		'server' => array(
			'HTTP_USER_AGENT', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR', 'HTTP_ACCEPT_LANGUAGE', 'HTTP_REFERER'
		),
	);

	/**
	 *
	 * @param null|array $options
	 */
	function __construct($options = null)
	{
		$this->setOptions($this->_defaultOptions);
		if (is_array($options))
			$this->setOptions($options);
	}

	/**
	 *
	 * @param array $options
	 * @param boolean $rewrite
	 * @return Moto_SOME_CLASS_NAME
	 */
	function setOptions(array $options, $rewrite = false)
	{
		if ($rewrite)
			$this->_options = $options;
		else
			$this->_options = array_merge($this->_options, $options);
		return $this;

	}

	/**
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return Moto_SOME_CLASS_NAME
	 */
	function setOption($name, $value = null)
	{
		$this->_options[$name] = $value;
		return $this;
	}

	/**
	 *
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 */
	function getOption($name, $default = null)
	{
		return (isset($this->_options[$name]) ? $this->_options[$name] : $default);
	}

	/**
	 * Concat values of array|object to string
	 *
	 * @param array|object $data
	 * @param array $skipKeys List of keys for ignore merging
	 * @return string
	 */
	function valuesToString($data, $skipKeys = array('md5', 'MD5SUM'))
	{
		$str = '';
		foreach($data as $key => $value )
		{
			if( !in_array((string)$key, $skipKeys) )
			{
				if (is_array($value) || is_object($value))
				{
					$str .= $this->valuesToString($value, $skipKeys);
				}
				else
				{
					$str .= $value;
				}
			}
		}
		return $str;
	}

	/**
	 * Get signature of array or string with secretWord with md5 method
	 *
	 * @param array|object|string $data
	 * @param string $secretWord
	 * @return string
	 */
	function getSignature($data, $secretWord)
	{
		if (!is_string($data))
			$data = $this->valuesToString($data);
		if (!is_string($data))
			return null;
		$md5 = md5($data . $secretWord);
		return $md5;
	}

	/**
	 *
	 * @param array|object $param
	 * @return array
	 */
	function encodeArray($param)
	{
		if (is_array($param) || is_object($param))
		{
			foreach ($param as $key => $value)
			{
				$param[$key] = $this->encodeArray($value);
			}
		}
		else
		{
			return base64_encode($param);
		}
		return $param;
	}

	/**
	 *
	 * @param array|object $param
	 * @return array
	 */
	function decodeArray($param)
	{
		if (is_array($param) || is_object($param))
		{
			foreach ($param as $key => $value)
			{
				$param[$key] = $this->decodeArray($value);
			}
		}
		else
		{
			return base64_decode($param);
		}
		return $param;
	}

	/**
	 *
	 * @param string $method
	 * @return string
	 */
	protected function _getAction($method)
	{
		return $method;
	}

	/**
	 *
	 * @return array
	 */
	protected function _getCookies()
	{
		$keys = $this->getOption('cookies', array());
		$result = $this->_getValuesByKeys($_COOKIE, $keys);
		return $result;
	}

	/**
	 *
	 * @return array
	 */
	protected function _getServer()
	{
		$keys = $this->getOption('server', array());
		$result = $this->_getValuesByKeys($_SERVER, $keys);
		return $result;
	}

	/**
	 *
	 * @param array $source
	 * @param array $keys
	 * @return array
	 */
	protected function _getValuesByKeys($source, $keys)
	{
		$result = array();
		if (count($keys))
		{
			for($i = 0, $icount = count($keys); $i < $icount; $i ++)
			{
				if (isset($source[$keys[$i]]))
					$result[$keys[$i]] = $source[$keys[$i]];
			}
		}
		return $result;
	}

	protected function _debug($str)
	{
		echo $str . "\n";
	}


	protected function _checkRequest($request)
	{
		if (empty($request['action']))
			throw new Exception('Bad Request');
		if (empty($request['params']))
			throw new Exception('Bad Request');
		if (empty($request['server']) || !count($request['server']))
			throw new Exception('Bad Request');
	}

	/**
	 *
	 * @param string $method
	 * @return string
	 */
	protected function _getApiUrl($method)
	{
		$url = $this->getOption('url');
		if (empty($url))
			throw new Exception('Bad Api Url ' . $url);
		$info = parse_url($url);
		if (isset($info['query']))
			$url .= '&';
		else
			$url = rtrim($url, '?') . '?';
		$url .= 'apikey=' . $this->getOption('key');
		return $url;
	}

	/**
	 *
	 * @param string $method
	 * @param array $data
	 * @return array
	 */
	function send($method, $data)
	{
		$debug = $this->getOption('debug', false);
		$url = $this->_getApiUrl($method);
		$request = array(
			'action' => $this->_getAction($method),
			'params' => $data,
			'cookie' => $this->_getCookies(),
			'server' => $this->_getServer(),
			'time' => time()
		);
		if ($debug)
			$this->_debug("<b>Request pre check</b>\n" . print_r($request, true));
		$this->_checkRequest($request);
		
		$request['md5'] = $this->getSignature($request, $this->getOption('pass', ''));
		if ($debug)
			$this->_debug("<b>Request</b>\n" . print_r($request, true));

		$request = $this->encodeArray($request);
		if ($debug)
			$this->_debug("<b>Encoded Request</b>\n" . print_r($request, true));

		$ch = curl_init();

		if ($debug)
			$this->_debug("<b>Set api url</b> $url");

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		if (is_array($request))
			$request = http_build_query($request);
		if ($debug)
			$this->_debug("<b>Request String</b> $request");

		curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->getOption('CURLOPT_CONNECTTIMEOUT', 10));
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->getOption('CURLOPT_TIMEOUT', 20));

		try
		{
			$response = trim(curl_exec($ch));
			if ($debug)
				$this->_debug("<b>response string</b> : $response");

			$response = json_decode($response, true);
			if ($debug)
				$this->_debug("<b>response coded</b> : " . print_r($response, true));
			$response = $this->decodeArray($response);
		}
		catch (Exception $e)
		{
			$response = null;
		}
		return $response;
	}
}