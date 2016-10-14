<?php
/**
 * Created by JetBrains PhpStorm.
 * @author vandamm
 * Date: 15.05.12
 * Time: 19:19
 */
class Moto_Application
{
	protected $routePrefix;
	protected $prefix;

	protected $fromEmail = 'ru@templatemonster.com';
	protected $fromName = 'Почтовый робот ТМ Russia';


	public function __construct()
	{
		//$this->prefix = $prefix = Database::instance()->table_prefix();;
		$this->prefix = "";
	}


	/**
	 * Route the request
	 * @return bool
	 */
	public function route()
	{
		try
		{
			if (preg_match('#/' . $this->routePrefix . '/(\w+)/#', $_SERVER['REQUEST_URI'], $matches)
				&& method_exists($this, $matches[1]))
			{
				$result = call_user_func(array($this, $matches[1]));
				if (!empty($result))
				{
					return json_encode($result);
				}
			}
		}
		catch (Exception $e)
		{
			$this->redirect();
		}
		return false;
	}


	/**
	 * Redirect in case of errors
	 */
	protected function redirect()
	{
		header('Location: /ru/' . $this->routePrefix . '/');
		exit;
	}

	public function setSenderInfo($info = array())
	{
		$this->fromEmail = isset($info['fromEmail']) ? $info['fromEmail'] : $this->fromEmail;
		$this->fromName = isset($info['fromName']) ? $info['fromName'] : $this->fromName;
	}

	/**
	 * Send actual email
	 * @param $email
	 * @param $subject
	 * @param $text
	 * @return array
	 */
	protected function sendEmail($email, $subject, $text, $tr = null)
	{
		$mail = new Zend_Mail('UTF-8');
		$mail->setFrom($this->fromEmail, $this->fromName);
		$mail->addTo($email);
		$mail->setSubject($subject);
		$mail->setBodyHtml($text);

        if (strpos($_SERVER['HTTP_HOST'], '.fmt'))
        {
            $tr = new Zend_Mail_Transport_Smtp('192.168.5.23');
        }
//		$tr = new Zend_Mail_Transport_Sendmail();
		//$tr = new Zend_Mail_Transport_Smtp();

		try
		{
			$mail->send($tr);
			return array('success' => true);
		}
		catch (Exception $e)
		{
			return array('error' => $e->getMessage());
		}

	}

	protected function isValidMail( $mail = '' )
	{
		return $mail && filter_var($mail, FILTER_VALIDATE_EMAIL);
	}

	protected function motoLog(Exception $e)
	{
		$path = CURRENT_THEME_DIR . '/motolog.txt';
		file_put_contents($path, date("Y-m-d H:i:s") . ' ' . $e->getMessage() . "\n\n", FILE_APPEND);
	}
}
