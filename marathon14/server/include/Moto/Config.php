<?php
/**
 * Small realtime storage for config vars to pass through the theme
 */
class Moto_Config
{
	static protected $__data = array();


	/**
	 * Get data from config
	 * @param string $name Config key
	 * @param mixed $default Value to return if key does not exist
	 * @return mixed
	 */
	static function get($name, $default = null)
	{
		if (isset(self::$__data[$name]))
			return self::$__data[$name];
		return $default;
	}

	/**
	 * Get data from subkey
	 * @param string $name Config key
	 * @param string $key Config subkey
	 * @param mixed $default Value to return if key does not exist
	 * @return mixed
	 */
	static function getSub($name, $key = '', $default = null)
	{
		if (isset(self::$__data[$name][$key]))
			return self::$__data[$name][$key];
		return $default;
	}

	/**
	 * Store a value to config
	 * @param string $name Config key
	 * @param mixed $value Value to store
	 * @return mixed
	 */
	static function set($name, $value)
	{
		self::$__data[$name] = $value;
		return $value;
	}
	
	/**
	 * Store a value to sub subkey
	 * @param string $name Config key
	 * @param string $key Config subkey
	 * @param mixed $value Value to store
	 * @return mixed
	 */
	static function setSub($name, $key, $value)
	{
		self::$__data[$name][$key] = $value;
	}
}


