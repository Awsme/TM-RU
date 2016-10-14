<?php
include 'Moto/Application.php';

class Moto_Newsletters extends Moto_Application
{
	protected $routePrefix = 'newsletters';
	protected $table = 'customnewsletters';

	public function subscribe($toConfirm = true)
	{
		if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
		{
			echo json_encode(array('error' => 'Введите корректный email'));
			exit;
		}
		$email = $_POST['email'];

		if (isset($_POST['noconfirm']))
		{
			$toConfirm = false;
		}

		$existing = Database::instance()
			->query("SELECT * FROM {$this->prefix}{$this->table} where email='" . mysql_real_escape_string($email) . "'")
			->as_array();


		if (!$existing || !$existing[0]->confirmed)
		{
			if (!$existing)
			{
				$sid = sha1($email . time());
				Database::instance()->insert($this->table, array(
					'email' => $email,
					'sid' => $sid,
					'created_at' => time(),
					'confirmed' => !$toConfirm
				));

			}
			else
			{
				$sid = $existing[0]->sid;
			}
			if ($toConfirm){
				$url = 'http://www.templatemonster.com/ru/' . $this->routePrefix . '/confirm/?sid=' . $sid;


				return $this->sendEmail($email, 'Вы избранный. Остался последний шаг', "<html>
<head></head>
<body>
Приветствуем!
<br/><br/>
Несколько секунд назад вы решили, что хотите быть ближе к интернациональной семье TemplateMonster Russia и первым узнавать о всевозможных акциях, получать рассылку новых шаблонов, интересные статьи и мнения экспертов.
<br/><br/>
Если вы все еще не передумали, то жмите на ссылку ниже и подтвердите ваш email:
<br/><br/>
<a href='{$url}'>{$url}</a>
<br/><br/>
Уже очень скоро вы будете регулярно получать самую свежую, интересную и эксклюзивную информацию из мира сайтостроения и развития собственных проектов в сети.
<br/><br/>
Искренне,<br/>
команда друзей из<br/>
<a href='http://www.templatemonster.com/ru/'>Template Monster Russia</a>
</body>
</html>");
			}

		}
		return array(
			'error' => 'Ищите ссылку на подтверждение в вашем почтовом ящике',
			'existing' => isset($existing[0]),
			'confirmed' => (isset($existing[0]) && isset($existing[0]->confirmed) ? $existing[0]->confirmed : null)
		);
	}


	/**
	 * Confirm an email
	 * @return array
	 */
	public function confirm()
	{
		if (!isset($_GET['sid']))
		{
			$this->redirect();
		}
		$sid = $_GET['sid'];

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


		$this->redirect($this->routePrefix . '-confirmed');
	}


	/**
	 * Initialize the engine
	 */
	public function init()
	{
		Database::instance()->query("
			CREATE TABLE IF NOT EXISTS {$this->prefix}{$this->table} (
				id int(11) unsigned NOT NULL AUTO_INCREMENT,
				email varchar(255) NOT NULL,
				sid varchar(255) NOT NULL,
				created_at int(11) NOT NULL,
				confirmed tinyint(1) NOT NULL DEFAULT '0',
				PRIMARY KEY (id),
				UNIQUE KEY email (email),
				UNIQUE KEY sid (sid)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8
		");

		echo 'Done';
	}


	public function emails()
	{
		if (!isset($_GET['p']) || $_GET['p'] != 'aComrseRPaNKHeuqbzVx')
			return false;

		$emails = Database::instance()
			->query("SELECT email FROM {$this->prefix}{$this->table} WHERE confirmed = 1")
			->as_array();

		return count($emails);
	}

	public function redirect($url = '')
	{
		header('Location: /ru/' . $url);
		exit;
	}
	
	/**
	 * Get list emails
	 * http://www.templatemonster.com/ru/newsletters/getemails/?p=aComrseRPaNKHeuqbzVx
	 */
	public function getemails()
	{
		try
		{
			if (!isset($_GET['p']) || $_GET['p'] != 'aComrseRPaNKHeuqbzVx')
				return false;

			echo '<pre>';
			$emails = Database::instance()
				->query("SELECT * FROM {$this->prefix}{$this->table} WHERE `confirmed` = 1")
				->as_array();
			$i = 0;
			echo "<b>Confirmed list</b>\n";
			foreach ($emails as $email)
			{
				$email->created_at = date('Y-m-d H:i:s', $email->created_at);
				echo ++$i . "\t{$email->email}\t{$email->created_at}\n";
			}
			echo "\n\n";
			
			$emails = Database::instance()
				->query("SELECT * FROM {$this->prefix}{$this->table} WHERE `confirmed` = 0")
				->as_array();
			$i = 0;
			echo "<b>NOT Confirmed list</b>\n";
			foreach ($emails as $email)
			{
				$email->created_at = date('Y-m-d H:i:s', $email->created_at);
				echo ++$i . "\t{$email->email}\t{$email->created_at}\n";
			}
			echo "\n\n";
		}
		catch(Exception $e)
		{
			echo '<pre>';
			print_r($e);
		}
		exit;
		return;
	}

}
