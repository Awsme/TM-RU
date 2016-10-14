<?php
/**
 * Created by JetBrains PhpStorm.
 * User: terion
 * Date: 18.01.13
 * Time: 12:39
 * To change this template use File | Settings | File Templates.
 */
class Moto_PhotolandingConfig
{
    static $prices = array(
        'domainPrice' => 19,
        'templatePrice' => 230,
        'hostingPlans' => array(
            'plan6' => array(
	            'name' => 'на пол года',
	            'price' => 60
            ),
            'plan12' => array(
	            'name' => 'на год',
	            'price' =>100
            ),
            'plan36' => array(
	            'name' => 'на три года',
	            'price' =>250
            ),
        ),
        'discount' => 0.05
    );

	static private $_rootPath;

    static $vk = array(
        'appId' => 3362063,
        'secretKey' => 'vrvD2FYeRdK67W9EbuRg'
    );

	static $regru = array(
		'user' => 'templatemonster',
		'pwd' => 'oiuas6732igsqiah'
	);

	static function getPath()
	{
		return "http://".$_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	}

	static function setRootPath($root)
	{
		if ($root[0] != '/')
		{
			$root = '/' . $root;
		}
		self::$_rootPath = "http://".$_SERVER['SERVER_NAME'] . $root;
	}

	static function getRootPath()
	{
		return self::$_rootPath;
	}
    /*
    static $app = array(
        'basePath' => 3360674,
    );
    */
}
