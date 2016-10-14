<?php

require_once 'config.php';
require_once 'include/db.php';
require_once 'include/Moto/Application.php';

set_include_path(implode(PATH_SEPARATOR, array(
    realpath('./include'),
    get_include_path(),
)));
require "Zend/Loader/Autoloader.php";
$autoloader = Zend_Loader_Autoloader::getInstance();


class WebStudioApplication extends Moto_Application
{
	private $_defaults = array(
		'table' => 'webstudio_subcribers',
		'subject' => 'Вопрос по веб студиям',
		'subject1' => 'До отправки заявки на марафон остался один шаг',
		'subject2' => 'Ответы на вопросы от подписчиков web-studio',
		'adminEmail' => 'sertificat@templatemonster.ru',
		'adminName' => 'Гриша Пиксель (TemplateMonster Russia)',
		'text' => 'Дорогой друг!
<br /><br />
Мы получили твою заявку на участие в марафоне «Своя веб-студия за 14 дней» (если впервые слышишь это название, то просто проигнорируй письмо, сотри, не читай, живи себе как жил).
<br /><br />
Хорошая новость заключается в том, что тебе осталось только перейти по ссылке и ответить на три простых вопроса:
<br /><br />
{{link}}
<br /><br />
		О старте твоего персонального марафона мы обязательно напомним. Будь готов!
<br /><br />
Гриша Пиксель,<br />
твой возможный подельник<br />
из TemplateMonster Russia',
	);

	private $_options = array();

	private $smtpdata = array(
		'auth' => 'login',
		'username' => 'skrypka@templatemonster.com',
		'password' => 'Zh2Il8OKXrgQ'
	);

	public function __construct($config = array())
	{
		parent::__construct();
		$this->_options = array_merge($this->_defaults, $config);
	}

	public function sendQuestion($name, $question, $email)
	{
		if (!$this->isValidMail($email))
			throw new Exception('Invalid parametrs');

		$message = 'Имя: ' . $name . '<br/>'
			. 'Email: ' . $email . '<br/>'
			. 'Вопрос: ' . $question . '<br/>';

		return $this->sendEmail($this->_options['adminEmail'], $this->_options['subject'], $message, $this->getTransport());
	}

	public function subscribe($email)
	{
		if (!$this->isValidMail($email))
			throw new Exception('Invalid parametrs');

		$this->setSenderInfo(array(
			'fromEmail' => $this->_options['adminEmail'],
			'fromName' => $this->_options['adminName']
		));

		$subscriber = $this->addSubscriber($email);
		$lastId = DBManger::lastInsertId();

		if(!$subscriber){
			return array('exist' => true);
		}
		$this->maichimp_subscribe($email);
		$landing_url = explode('?',$_SERVER['HTTP_REFERER']);
		$link = '<a href="' . $landing_url[0]. '?app=site14days&id='
			. $lastId . '&code=' . md5($email) . '">Ссылка</a>';
		$text = str_replace('{{link}}', $link, $this->_options['text']);
		//echo $text;

		return $this->sendEmail($email, $this->_options['subject1'], $text, $this->getTransport());
	}

	public function approve($id, $code, $better_than_other ="", $your_dream = "", $first_million ="", $sendAndmin = false)
	{
		$id = (int)$id;
		$query = "SELECT * FROM webstudio_subcribers WHERE id={$id}";
		$subscriber = DBManger::query($query);

		$this->maichimp_subscribe($subscriber[0]["email"]);
		/*if ($subscriber[0]["status"] == 'approved') {
			return array('status' => 'enough');
		} else*/ if (md5($subscriber[0]["email"]) == $code) {
			$this->updateStatus($id);
			if($sendAndmin){
				$this->sendAnswers($subscriber[0]["email"], $better_than_other, $your_dream, $first_million);
			}
			
			return array('status' => 'approved');
		}
		return array('status' => 'failed');
	}

	private function sendAnswers($email, $better_than_other, $your_dream, $first_million)
	{


		$answers = 'Email: ' . $email.' <br><br>Чем ты лучше остальных, тех кому мы отказали в участии?: ' . $better_than_other . '<br/>'
		. 'Какая твоя самая сокровенная мечта в жизни?: ' . $your_dream . '<br/>'
		. 'Как будет выглядеть распорядок твоего дня когда ты узнаешь, что заработал первый миллион?: ' . $first_million . '<br/>';

		$this->setSenderInfo(array(
			'fromEmail' => $this->_options['adminEmail']
		));

		return $this->sendEmail($this->_options['adminEmail'], $this->_options['subject2'], '<pre>' . $answers . '<pre>', $this->getTransport());
	}

	private function updateStatus($id)
	{
		$query = "UPDATE `webstudio_subcribers` SET `status`='approved' WHERE id=" . $id;
		DBManger::execute($query);		
	}

	private function addSubscriber($email)
	{
		$this->checkTable();
		$query = "SELECT * FROM webstudio_subcribers WHERE email='{$email}'";
		$existing = DBManger::query($query);	

		if(count($existing) > 0){	
			return false;
		}

		$query = "INSERT INTO `webstudio_subcribers`(`email`) VALUES ('{$email}')";
		DBManger::execute($query);
		return true;
	}

	private function checkTable()
	{
		$query = "CREATE TABLE IF NOT EXISTS webstudio_subcribers (`id` INT( 32 ) UNSIGNED NOT NULL AUTO_INCREMENT,	`status` ENUM('open', 'approved') NULL DEFAULT 'open',	`email` VARCHAR( 200 ) NULL,primary key (`id`)) TYPE = MyISAM;";
		DBManger::execute($query);
		return true;
	}

	private function getTransport()
	{
		return strpos($_SERVER['HTTP_HOST'], '.fmt') //? new Zend_Mail_Transport_Smtp('192.168.5.36')
			 ? new Zend_Mail_Transport_Smtp('192.168.5.47')
			: new Zend_Mail_Transport_Smtp('mail.templatemonster.com', $this->smtpdata);
	}

	private function maichimp_subscribe($email) {
	   	$locale = "ru";
	   	$listId = array(	    
	    	// "ru" => "b4b990e9a6"
	    	"ru" => "8aa420eaa6"
	   	);
	   	$list = $listId[$locale];   

	   	$apiKey = "e782ec2f25335c106987d2aea53e8d27";
	   	$apiUrl = "https://us11.api.mailchimp.com/2.0/";
	   	$url = $apiUrl . "lists/subscribe";
	    $maildata = array("email[email]" => $email);
	    $request = array_merge($maildata,
		    array(
		     	"double_optin" => false,
		        "update_existing" => true,
		        "replace_interests" => true,
		        "send_welcome" => false,
		        'apiFormat' => 'json',
		        "apikey" => $apiKey,
		        "id" => $list
	   	));

	   	$curl = curl_init();
	   	curl_setopt($curl, CURLOPT_URL, $url);
     	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
     	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
     	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
     	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
     	curl_setopt($curl, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
     	curl_setopt($curl, CURLOPT_POST, true);
     	curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
     	curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
     	curl_setopt($curl, CURLOPT_HEADER, true);
     	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 100);
     	curl_setopt($curl, CURLOPT_TIMEOUT, 20);

	    $UrlSite = $_SERVER["HTTP_HOST"];
	   	$position = strpos($UrlSite, ".fmt");
	   	if ($position > 0) {
	    	curl_setopt($curl, CURLOPT_HTTPPROXYTUNNEL, true);
	    	curl_setopt($curl, CURLOPT_PROXY, "192.168.5.111:3128");
	   	}
	    curl_exec($curl);
	    curl_close($curl);	         
	}
}

$app = new WebStudioApplication();

if (isset($_REQUEST['question']) && isset($_REQUEST['email'])) {
	$response['data'] = $app->sendQuestion($_REQUEST['name'], $_REQUEST['question'], $_REQUEST['email']);
}

if (isset($_REQUEST['email_subs'])) {
	$response['data'] = $app->subscribe($_REQUEST['email_subs']);
}

if (isset($_REQUEST['code']) && isset($_REQUEST['id'])) {
	$response['data'] = $app->approve($_REQUEST['id'], $_REQUEST['code']);
}

if (isset($_REQUEST['better_than_other']) && isset($_REQUEST['code']) && isset($_REQUEST['id'])) {
	$response['data'] = $app->approve($_REQUEST['id'], $_REQUEST['code'], $_REQUEST['better_than_other'], $_REQUEST['your_dream'], $_REQUEST['first_million'], true);
}


$response = json_encode($response);
echo $response;
die();