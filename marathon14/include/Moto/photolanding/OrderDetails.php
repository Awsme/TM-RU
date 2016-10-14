<?php
class Moto_OrderDetails
{
    protected $_userData;
    protected $_orderData = array(
        'domain' => null,
        'template' => null,
        'hostingPackageId' => null,
        'name' => null,
        'email' => null,
        'phone' => null,
        'discount' => false,
        'fullPrice' => null,
        'finalPrice' => null
    );

    function __construct($order, $userData = array())
    {
        //TODO:: check for final price

       // $order['discount'] = $order['discount'] == 'true';
       $this->_orderData = array_merge($this->_orderData, $order);


        $cookieName = 'vk_app_' . Moto_PhotolandingConfig::$vk['appId'];
        $vkuid = 0;
        if (isset($_COOKIE[$cookieName]))
        {
            parse_str($_COOKIE[$cookieName]);
            $vkuid = isset($mid) ? $mid : 0;
        }

        $userData = is_array($userData) ? $userData : array();

        $this->_userData = new Moto_UserDetails(array_merge($userData, array('vkuid' => $vkuid)));
    }

    function __get($property) {

        if ($property == 'userData')
        {
            return $this->_userData;
        }

        if (in_array($property, array_keys($this->_orderData))) {
            return $this->_orderData[$property];
        }
    }

    function __set($property, $value) {
        if (in_array($property, array_keys($this->_orderData))) {
            $this->_orderData[$property] = $value;
        }
    }

}
