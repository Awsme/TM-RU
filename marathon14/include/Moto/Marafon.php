<?php

class Moto_Marafon
{
	protected $prefix;
	protected $table = 'marafon';


	public function __construct()
	{
		$this->prefix = $prefix = Database::instance()->table_prefix();;
	}


	/**
	 * Route the request
	 * @return bool
	 */
	public function route()
	{
		try
		{
			if (preg_match('#/marafon/(\w+)/#', $_SERVER['REQUEST_URI'], $matches) && method_exists($this, $matches[1]))
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
		header('Location: /ru/marafon/');
		exit;
	}


	protected function uniqid()
	{
		return str_replace('.', '', uniqid('', true));
	}

	/**
	 * @param string $email
	 * @return array
	 */
	public function signup($email=null)
	{
		if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
		{
			echo json_encode(array('error' => 'Введите корректный email'));
			exit;
		}
		$email = $_POST['email'];


		$existing = Database::instance()
			->query("SELECT * FROM {$this->prefix}marafon where email='" . mysql_real_escape_string($email) . "'")
			->as_array();

		if (!$existing || !$existing[0]->confirmed)
		{
			if (!$existing)
			{
				Database::instance()->insert('marafon', array(
					'email' => $email,
					'created_at' => time(),
				));

				$id = Database::instance()
					->query("SELECT LAST_INSERT_ID() as last_insert_id FROM {$this->prefix}marafon")
					->as_array();
				if (!$id)
				{
					return array('error' => 'Странная ошибка');
				}
				$id = $id[0]->last_insert_id;

				$sid = $this->uniqid();
				Database::instance()->update('marafon', array(
					'sid' => $sid
				), array(
					'id' => $id
				));
			}
			else
			{
				$sid = $existing[0]->sid;
			}
			$url = 'http://www.templatemonster.com/ru/marafon/confirm/?sid=' . $sid . '&email=' . $email;

			return $this->sendEmail($email, 'Вы в шаге от доступа к секретным знаниям', "<html>
<head></head>
<body>
Вы, или кто-то очень близкий и родной Вам отправил запрос на получение популярных вопросов,
заданных в рамках марафона <a href='http://www.templatemonster.com/ru/marafon/'>'Интернет - это так просто'</a>.
Если не передумали, то жмите на ссылку ниже:
<br/><br/>
<a href='{$url}'>{$url}</a>
<br/><br/>
После этого нужно будет просто снова проверить почту.
<br/><br/>
С уважением,<br/>
команда экспертов<br/>
<a href='http://www.templatemonster.com/ru/marafon/'>Template Monster Russia</a>
</body>
</html>");

			/*$r = mail($email, 'Вы в шаге от доступа к секретным знаниям', '
  Вы, или кто-то очень близкий и родной Вам отправил запрос на получение популярных вопросов, заданных в рамках марафона "Интернет - это так просто" (http://www.templatemonster.com/ru/marafon/). Если не передумали, то жмите на ссылку ниже:

http://www.templatemonster.com/ru/marafon/confirm/?sid=' . $sid . '

После этого нужно будет просто снова проверить почту.

С уважением,
команда экспертов
Template Monster Russia
http://www.templatemonster.com/ru/marafon/', 'From: ru@templatemonster.com');*/

		}

		return array('error' => 'Ищите ссылку на скачивание в вашем почтовом ящике');
	}


	/**
	 * Confirm an email
	 * @param string $email
	 * @return array
	 */
	public function confirm($email = null, $sendMail = true)
	{
		if ($email === null)
		{
			// Create table
			if (isset($_GET['sid']) && $_GET['sid'] == 'qyLJleIWekZ4eHBNFN7QXmMI39YYsq')
			{
				$this->init();
				$this->redirect();
			}

			if (!isset($_GET['sid']) || !isset($_GET['email']))
			{
				$this->redirect();
			}
			$sid = $_GET['sid'];
			$email = $_GET['email'];

			$result = Database::instance()
				->query("SELECT * FROM {$this->prefix}{$this->table} WHERE sid = '" . $sid . "'")
				->as_array();
			if (!$result)
			{
				return array('error' => 'Неизвестная ошибка!');
			}

			Database::instance()->update($this->table, array(
				'confirmed' => 1,
			), array(
				'id' => $result[0]->id
			));
		}
		else
		{
			$result = Database::instance()
				->query("SELECT * FROM {$this->prefix}{$this->table} WHERE email = '" . $email . "'")
				->as_array();
			if (!$result || !$result[0]->sid)
			{
				$sid = $this->uniqid();
				if ($result)
				{
					Database::instance()->update($this->table, array(
						'confirmed' => 1,
						'sid' => $sid
					), array(
						'id' => $result[0]->id
					));
				}
				else
				{
					Database::instance()->insert($this->table, array(
						'email' => $email,
						'sid' => $sid,
						'confirmed' => 1,
						'created_at' => time(),
					));
				}
			}
			else
			{
				$sid = $result[0]->sid;
			}
		}

		if (1 == 2 && $sendMail)
		{
			$url = 'http://www.templatemonster.com/ru/marafon/download/?sid=' . $sid;
			$result = $this->sendEmail($email, '"Интернет - это так просто". Самые популярные вопросы марафона.', '<html>
<head></head>
<body>
Поздравляем!
<br/><br/>
Теперь вам доступен самый полный список популярных вопросов, заданных в рамках марафона "Интернет - это так просто",
и ответов на них наших экспертов. Верим, что желание учиться и познавать новое обязательно приведут вас к успеху.
Скачать его вы можете по следующей ссылке:
<br/><br/>
<a href="' . $url . '">' . $url . '</a>
<br/><br/>
Кстати, если соберетесь делать <a href="http://www.templatemonster.com/ru/website-templates-type/">сайт</a>,
<a href="http://www.templatemonster.com/ru/cms-blog-templates.html">блог</a> или
<a href="http://www.templatemonster.com/ru/ecommerce-templates.html">интернет-магазин</a>, то помните, что готовые шаблоны от
TemplateMonster Russia - это ваши лучшие друзья. Они отлично экономят нервы, время и деньги.
<br/><br/>
С уважением,<br/>
команда экспертов<br/>
<a href="http://www.templatemonster.com/ru/marafon/">Template Monster Russia</a>
</body>
</html>');

			echo 'Пожалуйста, проверьте свой почтовый ящик';
		}
		return array();
	}


	/**
	 * Download the file
	 * @return array
	 */
	public function download()
	{
		if (!isset($_GET['sid']) || $_GET['sid'] == '')
		{
			$this->sendFile();
			return array();
		}
		$sid = $_GET['sid'];

		$result = Database::instance()
			->query("SELECT * FROM {$this->prefix}{$this->table} WHERE sid = '" . mysql_real_escape_string($sid) . "'")
			->as_array();

		if ($result)
		{
			Database::instance()->query("
				UPDATE {$this->prefix}{$this->table}
				SET `downloaded` = `downloaded` + 1
				WHERE id = '" . $result[0]->id . "'
			");

			$this->sendFile();
			return array();
		}
		return array();
	}


	protected function sendFile()
	{
		$file = CURRENT_THEME_DIR . 'data/top100.pdf';
		if (file_exists($file))
		{
			header('Content-type: text/plain');
			header('Content-Disposition: attachment; filename="top100.pdf"');
			readfile($file);
		}
	}


	/**
	 * Initialize the engine
	 */
	public function init()
	{
		Database::instance()->query("
			CREATE TABLE IF NOT EXISTS {$this->prefix}{$this->table} (
				id int(11) unsigned NOT NULL AUTO_INCREMENT,
				sid varchar(50) NOT NULL,
				email varchar(255) NOT NULL,
				created_at int(11) NOT NULL,
				confirmed tinyint(1) NOT NULL DEFAULT '0',
				downloaded int(11) NOT NULL DEFAULT '0',
				PRIMARY KEY (id),
				UNIQUE KEY sid (sid)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8
		");

		/*$file = CURRENT_THEME_DIR . 'data/emails.txt';
		if (file_exists($file))
		{
			$emails = array_unique(explode("\n", file_get_contents($file)));
			foreach ($emails as $email)
			{
				if (!empty($email))
				{
					$this->confirm($email);
				}
			}
		}*/
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
		$mail->setFrom('ru@templatemonster.com');
		$mail->addTo($email);
		$mail->setSubject($subject);
		$mail->setBodyHtml($text);

		$tr = new Zend_Mail_Transport_Sendmail();
		//$tr = new Zend_Mail_Transport_Smtp();
		try
		{
			$mail->send($tr);
			return array('success' => true);
		}
		catch (Exception $e)
		{
			return array('error' => 'Проблема при отправке email');
		}

	}


	public function emails()
	{
		if (!isset($_GET['p']) || $_GET['p'] != 'aComrseRPaNKHeuqbzVx')
			return false;

		$emails = Database::instance()
			->query("SELECT email FROM {$this->prefix}{$this->table} WHERE confirmed = 1")
			->as_array();

		$downloaded = Database::instance()
			->query("SELECT email FROM {$this->prefix}{$this->table} WHERE downloaded > 0")
			->as_array();

		/*echo '<table>';
		$i = 0;
		foreach ($emails as $email)
		{
			echo '<tr><td>' . ++$i . '</td><td>' . $email->email . '</td></tr>';
		}
		echo '</table>';*/
		return array('confirmed' => count($emails), 'downloaded' => count($downloaded));
	}
	
	/**
	 * Get list emails
	 * http://www.templatemonster.com/ru/marafon/getemails/?p=aComrseRPaNKHeuqbzVx
	 */
	public function getemails()
	{
		if (!isset($_GET['p']) || $_GET['p'] != 'aComrseRPaNKHeuqbzVx')
			return false;

		$emails = Database::instance()
			->query("SELECT * FROM {$this->prefix}{$this->table} WHERE `confirmed` = 1")
			->as_array();
		echo '<pre>';
		$i = 0;
		foreach ($emails as $email)
		{
			$email->created_at = date('Y-m-d H:i:s', $email->created_at);
			echo ++$i . "\t{$email->email}\t{$email->downloaded}\t{$email->created_at}\n";
		}
		echo "\n\n";
		return;
	}

	
	public function importtonewsletter()
	{
		if (!isset($_GET['p']) || $_GET['p'] != 'aComrseRPaNKHeuqbzVx')
			return false;
		echo '<pre>';
		
		$file = CURRENT_THEME_DIR . 'data/emails.txt';
		if (file_exists($file))
		{
			$emails = array_unique(explode("\n", file_get_contents($file)));
			foreach ($emails as $icount => $email)
			{
				if (!empty($email))
				{
					$this->confirm($email, false);
				}
			}
			echo "Add from $file $icount email(s)\n";
		}
		else
		{
			echo "oops $file not exists\n";
		}
		
		$sql1 = "	SELECT email, concat('marafon-', sid) AS sid, created_at, 1 AS confirmed
	FROM `{$this->prefix}{$this->table}`
	WHERE 1
	AND `confirmed` = 1
";

		
//		echo "$sql1\n";
//		$emails = Database::instance()->query($sql1)->as_array();
//		print_r($emails);

		$sql = "
INSERT IGNORE INTO rutm_v4customnewsletters (email, sid, created_at, confirmed) ( $sql1 )";
		
		
		echo "$sql\n";
		$r = Database::instance()->query($sql);
		print_r($r);
	}
}