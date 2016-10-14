<?php
/**
 * Created by JetBrains PhpStorm.
 * User: terion
 * Date: 11.01.13
 * Time: 15:58
 * To change this template use File | Settings | File Templates.
 */
class Moto_ApiResponse
{
    protected $status;
    protected $error;
    protected $response;
    protected $errorTemplate = array('type' => 'default', 'code' => 0, 'message' => 'Uknown error.');

    protected $_data = array(
        'status' => 'success',
        'response' => array()
    );

    function setStatus($status = '')
    {
        if (isset($status))
        {
	        $this->_data['status'] = $status;
        }            
    }

    function setError($error)
    {
        $this->setStatus('error');

        $this->_data['error'] = is_array($error) ? array_merge($this->errorTemplate, $error) : $this->errorTemplate;

        if (is_string($error))
        {
            $this->_data['error']['message'] = $error;
        }
    }

    function setResponse($response = '')
    {
        if (isset($response))
        {
	        $this->_data['response'] = $response;
        }
    }

    function get()
    {
        return $this->_prepare($this->_data);
    }

    function getJSON()
    {
        return json_encode($this->_data);
    }

    function getStatus()
    {
        return $this->_data['status'];
    }

    function getResponse()
    {
        return $this->_data['response'];
    }

    function hasErrors()
    {
        return $this->_data['status'] == 'error' && isset($this->_data['error']);
    }

    function getError()
    {
        if ($this->hasErrors())
        {
            return $this->_data['error'];
        }
    }

    function getErrorCode()
    {
        if ($this->hasErrors())
        {
            return $this->_data['error']['code'];
        }

    }

    function getErrorType()
    {
        if ($this->hasErrors())
        {
            return $this->_data['error']['type'];
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
}
