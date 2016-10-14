<?php defined('SYSPATH') OR die('No direct access allowed.');



/**
 * Валидатор сессии по IP адресу клиента
 */
class Zend_Session_Validator_UserIP extends Zend_Session_Validator_Abstract
{
	/**
	 * Возвращает IP адрес клиента
	 * @return string
	 */
	private function getUserIP()
	{
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		elseif (isset($_SERVER['REMOTE_ADDR']))
		{
			return $_SERVER['REMOTE_ADDR'];
		}
	}


	/**
	 * Setup() - this method will store user IP in the session as 'valid data'
	 *
	 * use Zend_Session::registerValidator(new Zend_Session_Validator_UserIP())
	 * for register this validator
	 *
	 * @return void
	 */
	public function setup()
	{
		$this->setValidData($this->getUserIP());
	}
	/**
	 * Validate() - this method will determine if the current user IP matches the
	 * user IP we stored when we initialized this variable.
	 *
	 * @return bool
	 */


	public function validate()
	{
		$currentIP = ($this->getUserIP());
		return $currentIP === $this->getValidData();
	}
}
