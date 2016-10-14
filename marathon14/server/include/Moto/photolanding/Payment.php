<?php
class Moto_Payment
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
     * @return Moto_ApiResponse
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

        if ($debug)
            $this->_debug("<b>Set api url</b> $url");

        if (is_array($request))
            $request = http_build_query($request);
        if ($debug)
            $this->_debug("<b>Request String</b> $request");

        Moto_Request::unsetProxy();
        $req = new Moto_Request($url, 'post', $request);
        $res = $req->run();
        if (!$res->hasErrors())
        {
            $result = $res->getResponse();
            if (isset($result['error']))
            {
                $err = $this->decodeArray($result['error']);
                $res->setError(array(
                    'type' => 'payment_api_error',
                    'code' => $err['code'],
                    'message' => $err['message']
                ));
            }
            elseif (isset($result['response']))
            {
                $res->setResponse($this->decodeArray($result['response']));
            }
        }


        return $res->get();

        /*
        echo '<pre>';
        var_dump($res);
        echo '</pre>';
        */
        /*
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);


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
        */
    }
}
