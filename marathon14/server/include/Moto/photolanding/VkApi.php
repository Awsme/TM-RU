<?php
/**
 * Created by JetBrains PhpStorm.
 * User: terion
 * Date: 18.01.13
 * Time: 11:47
 * To change this template use File | Settings | File Templates.
 */
class Moto_VkApi
{
    var $api_secret;
    var $app_id;
    var $api_url;

    function __construct($app_id, $api_secret, $api_url = 'api.vk.com/api.php')
    {
        $this->app_id = $app_id;
        $this->api_secret = $api_secret;
        if (!strstr($api_url, 'http://')) $api_url = 'http://'.$api_url;
        $this->api_url = $api_url;
    }

    function api($method, $params = false)
    {
        if (!$params) $params = array();
        $params['api_id'] = $this->app_id;
        $params['v'] = '3.0';
        $params['method'] = $method;
        $params['timestamp'] = time();
        $params['format'] = 'json';
        $params['random'] = rand(0,10000);
        ksort($params);
        $sig = '';
        foreach($params as $k=>$v) {
            $sig .= $k.'='.$v;
        }
        $sig .= $this->api_secret;
        $params['sig'] = md5($sig);
        $query = $this->api_url.'?'.$this->params($params);

        $curl = new Moto_Request($query);
        $res = $curl->run();

        if (!$res->hasErrors())
        {

            $apiResponse = $res->getResponse();


            if (is_array($apiResponse['error']))
            {
                $res->setError(array('type' => 'vk', 'code' => $apiResponse['error']['error_code'], 'message' => $apiResponse['error']['error_msg']));
            }

            if (!is_array($apiResponse))
            {
                $res->setError(array('type' => 'vk', 'message' => 'Wrong Vk Api response'));
            }
        }

        return $res;
    }

    function params($params)
    {
        $pice = array();
        foreach($params as $k=>$v) {
            $pice[] = $k.'='.urlencode($v);
        }
        return implode('&',$pice);
    }
}
