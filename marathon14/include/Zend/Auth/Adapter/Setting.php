<?php defined ('SYSPATH') OR die ('No direct access allowed.');



/**
 * Адаптер аутентификации пользователя на базе таблицы настроек setting
 * @author akond
 *
 */
class Zend_Auth_Adapter_Setting implements Zend_Auth_Adapter_Interface
{
	private $login;

	private $password;



	function authenticate ()
	{
		$code = Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
		$role = '';

		if ($this->isAdmin ())
		{
			$code = Zend_Auth_Result::SUCCESS;
			$role = Acl::ROLE_ADMIN;
		}
		elseif ($this->isEditor ())
		{
			$code = Zend_Auth_Result::SUCCESS;
			$role = Acl::ROLE_AUTHOR;
		}

		return new Zend_Auth_Result ($code, $role);
	}


	/**
	 * Возвращает значение настройки из таблицы настроек
	 * @param $name
	 * @return mixed
	 */
	function getSetting ($name)
	{
		$setting = ORM::factory ('setting', $name);
		if ($setting->isExist ())
		{
			return $setting->value;
		}
	}


	/**
	 * Устанавливает логин с которым пользователь пытается зайти
	 * @return void
	 */
	function setLogin ($login)
	{
		$this->login = $login;
	}


	/**
	 * Возвращает логин с которым пользователь пытается зайти
	 * @return
	 */
	function getLogin ()
	{
		return $this->login;
	}


	/**
	 * Устанавливает пароль с которым пользователь пытается зайт
	 * @return void
	 */
	function setPassword ($password)
	{
		$this->password = $password;
	}


	/**
	 * Возвращает пароль с которым пользователь пытается зайти
	 * @return
	 */
	function getPassword ()
	{
		return $this->password;
	}


	/**
	 * Проверяет не с админским ли логином/паролем заходит пользователь
	 * @return boolean
	 */
	private function isAdmin ()
	{
		$login = $this->getSetting (Setting_Model::S_ADMIN_LOGIN);
		$password = $this->getSetting (Setting_Model::S_ADMIN_PASSWORD);

		if ($login === $this->getLogin ())
		{
			if ($password === $this->getPassword ())
			{
				return true;
			}
		}
	}


	/**
	 * Проверяет не с авторским ли логином/паролем заходит пользователь
	 * @return boolean
	 */
	private function isEditor ()
	{
		$author = ORM::factory ('editor')->where ('login', $this->getLogin ())->where ('password', $this->getPassword ())->find ();
		if ($author->isExist ())
		{
			if ($author->is_active)
			{
				return true;
			}
		}
	}
}
