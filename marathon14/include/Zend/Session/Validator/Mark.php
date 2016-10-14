<?php defined('SYSPATH') OR die('No direct access allowed.');



class Zend_Session_Validator_Mark extends Zend_Session_Validator_Abstract
{
	const MARK = 'mark';



	function setup ()
	{
		$this->setValidData(self::MARK);
	}


	function validate ()
	{
		return $this->getValidData() === self::MARK;
	}
}
