<?php

class Moto_Catalog_Template
{
    protected $_data = array();

    function __construct($data = array())
    {
        $this->_data = array(
            'id' => null,
            'price' => null,
            'discounted_price' => null,
            'buyUrl' => null,
            'templatecategory' => null,
            'templatecategory_id' => null
        );

        if (is_array($data) && !empty($data))
        {
            $this->_data = array_merge($this->_data, $data);
        }
    }

    function __toString()
    {
        return "<p>ID - {$this->_data['id']}; Price - {$this->_data['price']}; BuyURL - {$this->_data['buyUrl']}; Category - {$this->_data['templatecategory']}</p> ";
    }
	
}