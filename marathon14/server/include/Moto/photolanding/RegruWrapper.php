<?php
/**
 * Created by JetBrains PhpStorm.
 * User: pirog
 * Date: 10.01.13
 * Time: 17:04
 * To change this template use File | Settings | File Templates.
 */

class Moto_RegruWrapper
{
	protected $user = 'test';
	protected $pwd = 'test';
	protected $apiUrl = 'https://api.reg.ru/api/regru2/';

	function __construct($user = null, $pwd = null)
	{
		if (!is_null($user))
		{
			$this->user = $user;
		}

		if (!is_null($pwd))
		{
			$this->pwd = $pwd;
		}
	}

	function checkDomain($domain)
	{
		$action = $this->apiUrl . 'domain/check';
		$data = array('domain_name' => $domain);
		$postdata = 'username=' . $this->user . '&password=' . $this->pwd . '&input_format=json&input_data=' . json_encode($data);

        $curl = new Moto_Request($action, 'post', $postdata);
		$res = $curl->run();

		return ($res instanceof Moto_ApiResponse) ? $res->get() : '';

	}
}