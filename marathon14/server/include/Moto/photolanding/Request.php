<?php
/**
 * Created by JetBrains PhpStorm.
 * User: terion
 * Date: 18.01.13
 * Time: 15:42
 * To change this template use File | Settings | File Templates.
 */
class Moto_Request
{
    static protected $proxy;
    protected $action;
    protected $method;
    protected $data;

    function __construct($action, $method = 'get', $data = '')
    {
        $this->action = $action;
        $this->method = $method;
        $this->data = $data;
    }

    static function setProxy($server, $port)
    {
        self::$proxy = array(
            'server' => $server,
            'port' => $port
        );
    }

    static function unsetProxy()
    {
        self::$proxy = null;
    }

    function run()
    {
        if (!function_exists('curl_init'))
        {
            throw new Exception('cURL is not installed!');
        }

        $response = new Moto_ApiResponse();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->action);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if ($this->method == 'post')
        {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->data);
        }

        if (!is_null(self::$proxy))
        {
	        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
            curl_setopt($ch, CURLOPT_PROXY, self::$proxy['server']);
            curl_setopt($ch, CURLOPT_PROXYPORT, self::$proxy['port']);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        $content = curl_exec($ch);

        if (curl_errno($ch))
        {
            $response->setError(array('type' => 'curl', 'code' => curl_errno($ch), 'message' => curl_error($ch)));
        }
        else
        {
            $json = json_decode($content, true);
            if (!is_null($json))
            {
                $content = $json;
            }
            $response->setResponse($content);
        }

        curl_close($ch);
        return $response;
    }
}
