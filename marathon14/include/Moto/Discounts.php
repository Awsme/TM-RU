<?php
include 'Moto/Application.php';

/**
 * Created by JetBrains PhpStorm.
 * @author vandamm
 * Date: 15.05.12
 * Time: 19:18
 */
class Moto_Discounts extends Moto_Application
{
	protected $routePrefix = 'discounts';
	protected $table = 'customdiscounts';

	public function subscribe()
	{
		if (!isset($_POST['email']))
		{
			echo json_encode(array('error' => 'Введите корректный email'));
		}
		$email = $_POST['email'];

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
				));
			}
			else
			{
				$sid = $existing[0]->sid;
			}
			$url = 'http://www.templatemonster.com/ru/discounts/confirm/?sid=' . $sid;

			return $this->sendEmail($email, 'Вы почти стали избранным. Остался последний шаг', "<html>
<head></head>
<body>
Несколько секунд назад вы решили, что ни в коем случае нельзя упускать шанс купить превосходные шаблоны TemplateMonster,
когда на них будет объявлена краткосрочная сногсшибательная скидка.
<br/><br/>
Если все еще не передумали, то жмите на ссылку и подтвердите ваш email:
<br/><br/>
<a href='{$url}'>{$url}</a>
<br/><br/>
Обязуемся сообщить вам самому первому, когда будем готовы к этому грандиозному событию.
<br/><br/>
Искренне,<br/>
команда друзей из<br/>
<a href='http://www.templatemonster.com/ru/'>Template Monster Russia</a>
</body>
</html>");
		}

		return array('error' => 'Ищите ссылку на скачивание в вашем почтовом ящике');
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


		$this->redirect('discounts-confirmed');
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

		/*echo '<table>';
		$i = 0;
		foreach ($emails as $email)
		{
			echo '<tr><td>' . ++$i . '</td><td>' . $email->email . '</td></tr>';
		}
		echo '</table>';*/
		return count($emails);
	}

	public function redirect($url = '')
	{
		header('Location: /ru/' . $url);
		exit;
	}
	
	/**
	 * Get list emails
	 * http://www.templatemonster.com/ru/discounts/getemails/?p=aComrseRPaNKHeuqbzVx
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
			//print_r($emails);
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
