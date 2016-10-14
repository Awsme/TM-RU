	<?php
/*
SELECT substring_index( u.email, '@', -1 ) AS email_domain, COUNT( * ) AS ccc
FROM `pc_user` u, pc_promocode c
WHERE c.user_id = u.id
AND c.owner_id =3
GROUP BY substring_index( u.email, '@', -1 )
ORDER BY `ccc` DESC
*/
class Moto_Regru 
{
	protected $_adminEmail = 'krio.rogue@gmail.com';
	protected $_notifyEmails = array('krio.rogue@gmail.com', 'ru@templatemonster.com','n.prosto@templatemonster.com');
	protected $_options = array();
	
	protected $_messages = array(
		'error_sorry_try_later' => 'Упс. Что-то пошло не так. Попробуйте повторить попытку позже.',
		'error_no_free_promocode' => 'Промокоды разобрали быстрей, чем горячие пирожки. Нам необходимо некоторое время, чтоб обновить базу. Повторите попытку позже.',
		'error_validation_code_used' => 'Упс. Что-то пошло не так. Робот уверен, что промокод вам пока не положен.',
		'error_confirmation_fail' => 'Наш почтовый робот заботливо сообщает, что указанный вами email уже принимал участие в акции невиданной щедрости от TemplateMonster Russia и REG.ru. Хорошего понемногу :).',
		'error_email_validation_fail' => 'К сожалению проверка емейла не прошла. Попробуйте еще разок.',
		'success_confirmation_send' => 'Сию секунду запрос на получение промокода был отправлен на указанную вами электронную почту. Незамедлительно проверяйте ее, чтоб как можно быстрей насладиться качественными услугами от REG.ru.',
		'success_promocode_send' => 'Промокод был отправлен на указанную вами электронную почту. Пользуйтесь качественными услугами от REG.ru на здоровье.',
	);

	protected $_response = array(
		'status' => true,
		'code' => 200,
		'messages' => '',
		'data' => array()
	);
	protected $_request = array();

	protected $_defaultOptions = array(
		'promocodeOptions' => array(
			'apikey' => 'tm.ru-regru',
			'apipass' => 'kybsdaigei328gbisuadgbia76dt332r',
			'apiurl' => 'http://www.motocms.com/_promocode/api.php',
		),
		'refName' => 'landing-regru',
		'landingUrl' => 'regru',
		'mailSubjectConfirmation' => 'Два месяца бесплатного хостинга почти у вас в кармане',
		'mailSubjectPromocode' => 'Промокод на 2 месяца бесплатного хостинга от REG.ru',
		'mailTemplateDir' => 'includes/letters/landing-regru',
	);
	
	protected $_confirmator = null;

	protected $_promocoder = null;
	
	public function __construct($options = array())
	{
		$this->_init($options);
	}
	
	protected function _init($options = array())
	{
		$this->_options = $this->_defaultOptions;
		if (is_array($options))
			$this->_options = array_merge($this->_options, $options);
	}

	function setOption($name, $value)
	{
		$this->_options[$name] = $value;
		return $this;
	}
	
	function getOption($name, $default = null)
	{
		return (isset($this->_options[$name]) ? $this->_options[$name] : $default);
	}
	
	function getResponse()
	{
		return $this->_response;
	}

	function run($action, $request)
	{
		$method = $action . 'Action';
		if (method_exists($this, $method))
		{
			$this->$method($request);
		}
		return $this->_response;
	}
	
	protected function _getFrom($obj, $name, $default = null)
	{
		if (is_array($obj) && isset($obj[$name]))
			return $obj[$name];
		if (is_object($obj) && isset($obj->$name))
			return $obj->$name;
		return $default;
	}
	
	function getConfirmator()
	{
		if ($this->_confirmator == null)
		{
			include_once 'Moto/Form/Confirmation.php';
			$this->_confirmator = new Moto_Form_Confirmation();
		}
	
		return $this->_confirmator;
	}
	
	protected function _getPromocode($email, $request = null)
	{
		if (!preg_match('/^([a-z0-9_\.\-])+\@(([a-z0-9а-я\-])+\.)+([a-z0-9а-я]{2,6})$/i', $email, $match))
		{
			throw new Exception('Bad request');
		}
	
		include_once 'Moto/Promocode.php';
		$params = $this->getOption('promocodeOptions');
		if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], '.fmt') > 0 )
		{
			$params['apiurl'] = 'http://www.motocms.com.fmt/_promocode/api.php';
		}

		$this->_promocoder = $getterPromocode = new Moto_Promocode($params);
		$result = $getterPromocode->getPromocode(array('email' => $email));
		
		return $result;
	}
	
	protected function _checkFreePromocode($request = null)
	{
		include_once 'Moto/Promocode.php';
		$params = $this->getOption('promocodeOptions');
		if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], '.fmt') > 0 )
		{
			$params['apiurl'] = 'http://www.motocms.com.fmt/_promocode/api.php';
		}

		$this->_promocoder = $getterPromocode = new Moto_Promocode($params);
		$result = $getterPromocode->checkFree();
		
		return $result;
	}

	function confirmAction($request)
	{
		try
		{
			$sid = $this->_getFrom($request, 'sid');
			$email = $this->_getFrom($request, 'email');
			
			$confirmator = $this->getConfirmator();
			$refName = $this->getOption('refName');
			$form = $confirmator->getFormByRefName($refName);
			if (!$form)
			{
				$form = array(
					'ref_name' => $refName,
				);
				$form = $confirmator->addForm($form, 5);
			}
			if (!$form)
			{
				throw new Exception('bad_form');
			}
			
			$confirmation = $confirmator->getConfirmationByEmail($form->id, $email);
			$this->_response['confirmation'] = $confirmation;
			
			if ($confirmation && $confirmation->enabled && $confirmation->sid === $sid)
			{
				//not confirmed yet

				$promocodeResponse = $this->_getPromocode($email);
				$this->_response['promocode'] = $promocodeResponse;
				if ($promocodeResponse['status'])
				{
					$template = $this->getMailTemplate('promocode');
					$subject = $this->getOption('mailSubjectPromocode', 'Промокод на 2 месяца бесплатного хостинга от REG.ru');
					$body = $this->renderTemplate($template, array('promocode' => $promocodeResponse['promocode']));
					$result = $this->sendEmail($email, $subject, $body);
					$this->_response['message'] = $this->_messages['success_promocode_send'];
					
					if ($confirmation->max_try > 0)
						$confirmation->max_try --;
					
					$confirmation->enabled = ($confirmation->max_try > 0) * 1;
					$confirmation->confirmed = date('Y-m-d H:i:s');
					$confirmator->saveConfirmation($confirmation);
				}
				else
				{
					$subject = 'TM.RU : REG.RU : MAYBE NO FREE PROMOCODE : ' . date('Y-m-d H:i:s');
					$body = 'Date: ' . date('Y-m-d H:i:s') . "\n";
					$body .= 'User email: ' . $confirmation->email . "\n";
					$body .= 'Request created: ' . $confirmation->created . "\n";
					$body .= 'Form content: ' . print_r(json_decode(base64_decode($confirmation->data), true), true) . "\n";
					$body .= 'Promocoder response: ' . print_r($promocodeResponse, true) . "\n";
					
					$body = "<html><body><pre>$body</pre></body></html>";
					
					$this->sendEmail($this->_adminEmail, $subject, $body);
					throw new Exception('error_no_free_promocode');
				}
			}
			else
			{
				throw new Exception('error_validation_code_used');
			}
		}
		catch (Exception $e)
		{
			$this->_response['status'] = false;
			$message = $e->getMessage();
			if (strpos($_SERVER['HTTP_HOST'], '.fmt'))
			{
				$this->_response['exception'] = array(
					'code' => $e->getCode(),
					'message' => $e->getMessage(),
					'trace' => $e->getTraceAsString(),
				);
			}
			if (isset($this->_messages[$message]))
			{
				$message = $this->_messages[$message];
			}
			$this->_response['message'] = $message;
		}
	}
	
	function getMailTemplate($name)
	{
//		$file = CURRENT_THEME_DIR . '/includes/letters/landing-regru/' . $name . '.html';
		$file = CURRENT_THEME_DIR . '/' . $this->getOption('mailTemplateDir') . '/' . $name . '.html';
		if (!file_exists($file))
			throw new Exception('mail_template_not_exists');
		return file_get_contents($file);
	}
	
	function renderTemplate($template, $data)
	{
		$vars = explode(',', '${' . implode('},${', array_keys($data)) . '}');
		$values = array_values($data);
		return str_replace($vars, $values, $template);
	}
	
	function getPromocoderLog()
	{
		if ($this->_promocoder != null)
			return $this->_promocoder->getLog();
		return 'null';
	}
	
	function initPromocodeAction($request)
	{
		try {
			$email = $request['email'];
//			throw new Exception('error_sorry_try_later');
			if (preg_match('/@([0-9]*minutemail\.com|[0-9a-z\.]*mintemail\.com|zoaxe\.com|mailinator\.[a-z]+|mailcatch\.com|artemworld\.net|zoaxe\.com|guerrillamailblock\.com|guerrillamail\.[a-z]+|yopmail\.com|softgrr\.ru|vposade\.com|no-spam\.ws|aeroflot\.ee|claws\.ru|mailforspam\.com|rmqkr\.net|asdasd\.ru|tritlex\.org\.ua|sharklasers\.com|isyzran\.ru|reply\.li|caterpillac\.com)$/i', $email))
			{
				throw new Exception('error_sorry_try_later');
			}
			if (preg_match('/\.\./i', $email))
			{
				throw new Exception('error_sorry_try_later');
			}
			
//			$email = '`' . $email; // set bad mail
//			$email = uniqid() . $email; // for debug
			$confirmator = $this->getConfirmator();
			$refName = $this->getOption('refName');
			$form = $confirmator->getFormByRefName($refName);
			if (!$form)
			{
				$form = array(
					'ref_name' => $refName,
				);
				$form = $confirmator->addForm($form, 5);
			}
			if (!$form)
			{
				//error
				return false;
			}
			
			$isExists = $confirmator->isExistsEmail($form->id, $email);
			if ($isExists)
			{
				throw new Exception('error_confirmation_fail');
			}

			$check = $this->_checkFreePromocode();
			if (!$check || !$check['status'] || !isset($check['count']))
			{
				$subject = 'TM.RU : ' . $this->getOption('landingUrl') . ' : ERROR : CHECK FREE : ' . date('Y-m-d H:i:s');
				$body = 'Date: ' . date('Y-m-d H:i:s') . "\n";
				$body .= 'User email: ' . $email . "\n";
				$body .= 'Promocoder response: ' . print_r($check, true) . "\n";
				$body .= 'Promocoder log: ' . $this->getPromocoderLog() . "\n";				
				$body .= 'REQUEST: ' . print_r($_REQUEST, true) . "\n";
				$body = "<html><body><pre>$body</pre></body></html>";
				$this->sendEmail($this->_adminEmail, $subject, $body);
				throw new Exception('error_sorry_try_later');
			}
			if (isset($check['count']) && $check['count'] && $check['count'] < 50)
			{
				$subject = 'TM.RU : ' . $this->getOption('landingUrl') . ' : LOW COUNT PROMOCODE : ' . $check['count'];
				$body = 'Date: ' . date('Y-m-d H:i:s') . "\n";
				$body .= 'Promocoder response: ' . print_r($check, true) . "\n";
				$body .= 'Promocoder log: ' . $this->getPromocoderLog() . "\n";				
				$body .= 'REQUEST: ' . print_r($_REQUEST, true) . "\n";
				$body = "<html><body><pre>$body</pre></body></html>";
				if (strpos($_SERVER['HTTP_HOST'], '.fmt'))
					$this->sendEmail($this->_adminEmail, $subject, $body);
				else
					$this->sendEmail($this->_notifyEmails, $subject, $body);
			}
			/*
			if (isset($check['count']) && $check['count'] < 1)
			{
				$subject = 'TM.RU : ' . $this->getOption('landingUrl') . ' : MAYBE NO FREE PROMOCODE : ' . date('Y-m-d H:i:s');
				$body = 'Date: ' . date('Y-m-d H:i:s') . "\n";
				$body .= 'User email: ' . $email . "\n";
				$body .= 'Promocoder response: ' . print_r($check, true) . "\n";
				$body .= 'Promocoder log: ' . $this->getPromocoderLog() . "\n";
				$body .= 'REQUEST: ' . print_r($_REQUEST, true) . "\n";
				$body = "<html><body><pre>$body</pre></body></html>";
				if (strpos($_SERVER['HTTP_HOST'], '.fmt'))
					$this->sendEmail($this->_adminEmail, $subject, $body);
				else
					$this->sendEmail($this->_notifyEmails, $subject, $body);
				throw new Exception('error_no_free_promocode');
			}
*/
			$sid = $confirmator->createConfirmationCode($form, $email, $request);
			
			$template = $this->getMailTemplate('confirmation');
			
			$subject = $this->getOption('mailSubjectConfirmation');
			$url = 'http://www.templatemonster.com/ru/' . $this->getOption('landingUrl') . '/?sid=' . $sid . '&action=confirm&email=' . $email;
			if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], '.fmt') > 0 )
			{
				$url = 'http://www.templatemonster.com.fmt/ru/' . $this->getOption('landingUrl') . '/?sid=' . $sid . '&action=confirm&email=' . $email;
			}
			$body = $this->renderTemplate($template, array('confirmation_url' => $url));
		
			$result = $this->sendEmail($email, $subject, $body);
			$this->_response['message'] = $this->_messages['success_confirmation_send'];
			$this->_response['step'] = 1;
		}
		catch (Exception $e)
		{
			$this->_response['status'] = false;
			$message = $e->getMessage();
			if (isset($this->_messages[$message]))
			{
				$message = $this->_messages[$message];
			}
			$this->_response['message'] = $message;
		}
	}
	
	/**
	 * Send actual email
	 * @param array|string $email
	 * @param string $subject
	 * @param string $text
	 * @return array
	 */
	protected function sendEmail($emails, $subject, $text, $attachments = array())
	{
		$mail = new Zend_Mail('UTF-8');
		$fromEmail = $this->getOption('fromEmail', 'ru@templatemonster.com');
		$fromName = $this->getOption('fromName', 'Почтовый робот ТМ Russia');
		$mail->setFrom($fromEmail, $fromName);
		if (!is_array($emails))
			$emails = array($emails);
		foreach($emails as $email)
		{
			$mail->addTo($email);
		}
		$mail->setSubject($subject);
		$mail->setBodyHtml($text);
		
		if (is_array($attachments) && count($attachments) > 0)
		{
			foreach($attachments as $attachment)
			if (isset($attachment['path']) && file_exists($attachment['path']))
			{
				$content = file_get_contents($attachment['path']);
				$at = $mail->createAttachment($content);
				if (!empty($attachment['type']))
					$at->type = $attachment['type'];
				$at->disposition = (isset($attachment['disposition']) ? $attachment['disposition'] : Zend_Mime::DISPOSITION_INLINE);
				$at->encoding    = (isset($attachment['encoding']) ? $attachment['encoding'] : Zend_Mime::ENCODING_BASE64);
				$at->filename    = (isset($attachment['filename']) ? $attachment['filename'] : basename($attachment['path']));
			}
		}
		
		$transport = null;
		
		if (strpos($_SERVER['HTTP_HOST'], '.fmt')) 
		{
			$transport = new Zend_Mail_Transport_Smtp('192.168.4.185');
		}
		
		try
		{
			$mail->send($transport);
			return true;
		}
		catch (Exception $e)
		{
			return false;
		}
	}	
	
	function getPromocodeAction($request)
	{
		if (!preg_match('/^([a-z0-9_\.\-])+\@(([a-z0-9а-я\-])+\.)+([a-z0-9а-я]{2,6})$/i', $request['email'], $match))
		{
			throw new Exception('Bad request');
		}
	
		include_once 'Moto/Promocode.php';

		$params = $this->getOption('promocodeOptions');
		if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], '.fmt') > 0 )
		{
			$params['apiurl'] = 'http://www.motocms.com.fmt/_promocode/api.php';
		}

		$this->_promocoder = $getterPromocode = new Moto_Promocode($params);
		$result = $getterPromocode->getPromocode(array('email' => $request['email']));

		$this->_response['result'] = $result;
		$this->_response['request'] = $request;
	}
}