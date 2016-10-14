<?php

class Moto_Valentine
{

	/**
	 * Route the request
	 * @return bool
	 */
	public function route()
	{
		try
		{
			if (preg_match('#/love/(\w+)/#', $_SERVER['REQUEST_URI'], $matches) && method_exists($this, $matches[1]))
			{
				$result = call_user_func(array($this, $matches[1]));
				if (!empty($result))
				{
					echo json_encode($result);
				}
				return true;
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
		header('Location: /ru/love/');
		exit;
	}


	/**
	 * @param string $email
	 * @return array
	 */
	public function send($email=null)
	{
		if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
		{
			return array('error' => 'Введите корректный email', 'status' => false);
		}
		$email = $_POST['email'];
		$name = (isset($_POST['name'])) ? $_POST['name'] : '';
		$url = 'http://www.templatemonster.com/ru/love/?n=' . base64_encode(base64_encode($name)) ;
		return $this->sendEmail($email, 'Открытка-признание',
"<html>
<head></head>
<body>
Привет!
<br/><br/>
Кто-то, кому Вы глубоко симпатичны, попросил нас прислать Вам вот эту ссылку на открытку-признание:
<br/><br/>
<a href='{$url}'>{$url}</a>
<br/><br/>
Делаем это с огромным удовольствием, любовью и самыми чистыми чувствами. Хорошего дня.
<br/><br/>
Всегда Ваши,<br/>
Купидоны компании<br/>
<a href='http://www.templatemonster.com/ru/'>TemplateMonster Russia</a>
</body>
</html>"
		);


	}

	/**
	 * Send actual email
	 * @param $email
	 * @param $subject
	 * @param $text
	 * @return array
	 */
	protected function sendEmail($email, $subject, $text)
	{
		$mail = new Zend_Mail('UTF-8');
		$mail->setFrom('love@templatemonster.ru', 'Купидоны TM Russia');
		$mail->addTo($email);
		$mail->setSubject($subject);
		$mail->setBodyHtml($text);

		$transport = null;
		
		if (strpos($_SERVER['HTTP_HOST'], '.fmt')) 
		{
			$transport = new Zend_Mail_Transport_Smtp('192.168.4.185');
		}
		else {
			$config = array(
				//'ssl' => 'tls',
				//'port' => 587,
				'auth' => 'login',
				'username' => 'skrypka@templatemonster.com',
				'password' => 'Zh2Il8OKXrgQ');
			$transport = new Zend_Mail_Transport_Smtp('mail.templatemonster.com', $config);
		}
		
		try
		{
			$mail->send($transport);
			return array('success' => true);
		}
		catch (Exception $e)
		{
			return array('error' => 'Проблема при отправке email', 'status' => false);
		}

	}
	
	function dataTrack($request)
	{
		try {
			if (!isset($request['email']))
				return;
			$confirmator = $this->getConfirmator();
			$refName = 'love';
			$form = $confirmator->getFormByRefName($refName);
			if (!$form)
			{
				$form = array('ref_name' => $refName);
				$form = $confirmator->addForm($form, 1);
			}
			if (!$confirmator->isExistsEmail($form->id, $request['email']))
			{
				$sid = $confirmator->createConfirmationCode($form, $request['email'], $request);
			}
			else
			{
				$confirmation = $confirmator->getConfirmationByEmail($form->id, $request['email']);
				$confirmation->max_try ++;
				$confirmator->saveConfirmation($confirmation);
			}
		}
		catch (Exception $e)
		{
		}
	}

	protected $_confirmator = null;
	function getConfirmator()
	{
		if ($this->_confirmator == null)
		{
			include_once 'Moto/Form/Confirmation.php';
			$this->_confirmator = new Moto_Form_Confirmation();
		}
	
		return $this->_confirmator;
	}
	
	function showmedataAction()
	{
		$db = Database::instance();
		$prefix = $db->table_prefix();
		$tableName = 'custom_form_confirmation';
		
		$rows = $db
			->query("SELECT * FROM {$prefix}{$tableName} WHERE form_id = 5 ORDER BY `id` DESC LIMIT 500")
			->as_array()
			;
		foreach($rows as $i => $row)
		{
			$rows[$i]->data = json_decode(base64_decode($row->data));
		}
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']))
		{
			echo json_encode($rows);
			exit;
		}
		?>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>
</head>
<body>
		<?php
		echo '<pre>';
		echo "Count: " . count($rows) . "\n\n";
		if (!isset($_SERVER['REMOTE_ADDR']) || $_SERVER['REMOTE_ADDR'] != '69.65.22.194')
			exit;
		reset($rows);
		foreach($rows as $i => $row)
		{
			echo $row->email . "	" . $row->data->name . "\n";
		}
		exit;
		
	}

}