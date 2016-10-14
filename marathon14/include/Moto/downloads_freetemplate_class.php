<?php
class Downloads_FreeTemplate
{
	protected $_apikey = '';
	protected $_apipass = '';
	protected $_apiurl = 'http://free.templatemonster.ru/downloads/api.php';
	protected $_log = '';

	/**
	 *
	 * @param array $params
	 */
	function __construct($params)
	{
		$this->_apikey = $this->safeGet($params, 'apikey');
		$this->_apipass = $this->safeGet($params, 'apipass');
		$this->_apiurl = $this->safeGet($params, 'apiurl', $this->_apiurl);
	}

	/**
	 *
	 * @param array || object $var
	 * @param string $name
	 * @param any $default
	 * @return any
	 */
	function safeGet($var, $name, $default = '')
	{
		if (is_array($var) && isset($var[$name]))
			return $var[$name];
		if (is_object($var) && isset($var->$name))
			return $var->$name;
		return $default;
	}

	protected function _sendRequest($request, $debug = false)
	{
		$url = $this->_apiurl . '?apikey=' . $this->_apikey;
		if (!isset($request['rnd']))
			$request['rnd'] = time();

		if ($debug)
			$this->_log .= 'request vars : ' . print_r($request, true) . "\n";

		$request = json_encode($request);
		$request = base64_encode($request);

		if ($debug)
			$this->_log .= "request strings : $request\n";

		$sign = md5($this->_apipass . '@' . $request);
		$url .= '&sign=' . $sign;

		if ($debug)
			$this->_log .= "sign : $sign\n";
		if ($debug)
			$this->_log .= "Url : $url\n";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'request=' . $request);

		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);

		$response = trim(curl_exec($ch));
		if ($debug)
		{
			$this->_log .= "response string : $response\n";
		}
		$response = json_decode($response, true);
		if ($debug)
		{
			$this->_log .= "response vars : " . print_r($response, true) . "\n";
		}
		return $response;
	}
	/**
	 *
	 * @param array $data
	 * @param bollean $debug
	 * @return array
	 */
	function getLink($data, $debug = false)
	{
		$this->_log = '';
		$request = array(
			'action' => 'requestLink',
			'data' => $data,
		);
		
		$response = $this->_sendRequest($request, $debug);
		
		return $response;
	}

	/**
	 *	Return working log, if used getPromocode($login, $email, true)
	 * @return string
	 */
	function getLog()
	{
		return $this->_log;
	}
}

