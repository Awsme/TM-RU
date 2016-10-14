<?php
/**
 * Created by JetBrains PhpStorm.
 * User: terion
 * Date: 18.01.13
 * Time: 12:31
 * To change this template use File | Settings | File Templates.
 */
class Moto_UserDetails
{
    protected $_data = array(
        'vkuid' => ''
    );

    function __construct($userData = array())
    {
        if (is_array($userData))
        {
            $this->_data = array_merge($this->_data, $userData);
        }
    }

    public function __get($property) {
        if (in_array($property, $this->_data)) {
            return $this->_data[$property];
        }
    }

    /*
    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }

        return $this;
    }
    */
}
