<?php

class Moto_Response
{
    protected $errorTemplate = array('type' => 'default', 'code' => 0, 'message' => 'Uknown error.');
    protected $_data = array(
        'status' => 'success',
        'response' => array(),
        'error' => array()
    );

    public function __set($property, $value)
    {
        if (array_key_exists($property, $this->_data)) {

            if ($property == 'error')
            {
                $this->_data['status'] = 'error';
                $this->_data['error'] = is_array($value) ? array_merge($this->errorTemplate, $value) : $this->errorTemplate;
                if (is_string($value))
                {
                    $this->_data['error']['message'] = $value;
                }
            }

            $this->_data[$property] = $value;
        }
    }

    public function __get($property)
    {
        if (array_key_exists($property, $this->_data)) {

            if ($property == 'error' && !$this->hasErrors())
                return;
            return $this->_data[$property];
        } else if ($property == 'errorCode' && $this->hasErrors()) {
            return  $this->_data['error']['code'];
        } else if ($property == 'errorType' && $this->hasErrors()) {
            return  $this->_data['error']['type'];
        }
    }

    protected function _prepare($data)
    {
        if ($data['status'] == 'success' && empty($data['error']))
        {
            unset($data['error']);
        }

        if (empty($data['response']))
        {
            unset($data['response']);
        }
        return $data;
    }

    function get()
    {
        return $this->_prepare($this->_data);
    }

    function getJSON()
    {
        return json_encode($this->_prepare($this->_data));
    }

    function hasErrors()
    {
        return $this->_data['status'] == 'error' && isset($this->_data['error']);
    }
}
