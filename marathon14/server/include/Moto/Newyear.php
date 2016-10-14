<?php

include_once 'Moto/Regru.php';
class Moto_Newyear extends Moto_Regru 
{
	protected $_adminEmail = 'krio.rogue@gmail.com';
	protected $_notifyEmails = array('krio.rogue@gmail.com', 'ru@templatemonster.com','n.prosto@templatemonster.com');
	
	protected $_messages = array(
		'error_sorry_try_later' => 'Упс. Что-то пошло не так. Попробуйте повторить попытку позже.',
		'error_no_free_promocode' => 'Подарки разбирают как горячие пирожки.<br/><b>Попробуйте повторить попытку через несколько часов.</b>',
		'error_validation_code_used' => 'Упс. Олень Деда Мороза уверен, что промокод вам пока не положен.',
		'error_confirmation_fail' => 'Кажется Вы уже получали подарок от Деда Мороза. Хорошего понемногу :)',
		'error_email_validation_fail' => 'К сожалению проверка емейла не прошла. Попробуйте еще разок.',
//		'success_confirmation_send' => 'Сию секунду Олень Деда Мороза послал запрос получение промокода был отправлен на указанную вами электронную почту.',
		'success_confirmation_send' => '',
		'success_promocode_send' => 'Подарок от Деда мороза отправлен на указанную вами электронную почту.',
	);
	
	protected $_defaultOptions = array(
		'promocodeOptions' => array(
			'apikey' => 'tm.ru-ny2013',
			'apipass' => '347e8bc1bfdcf9e600cd2a68949d08f6',
			'apiurl' => 'http://www.motocms.com/_promocode/api.php',
		),
		'refName' => 'landing-ny2013',
		'landingUrl' => 'newyear',
		'mailSubjectConfirmation' => 'Подарок близко',
		'mailSubjectPromocode' => 'Долгожданный подарок',
		'mailSubjectPromocode_5' => 'Долгожданный подарок (-5%)',
		'mailSubjectPromocode_10' => 'Долгожданный подарок (-10%)',
		'mailSubjectPromocode_20' => 'Долгожданный подарок (-20%)',
		'mailSubjectPromocode_25' => 'Долгожданный подарок (-25%)',
		'mailSubjectPromocode_50' => 'Долгожданный подарок (-50%)',
		'mailSubjectPromocode_75' => 'Долгожданный подарок (-75%)',
		'mailSubjectPromocode_100' => 'Долгожданный подарок (-100%)',
		'mailTemplateDir' => 'includes/letters/landing-ny2013',
		'fromName' => 'Почта Деда Мороза',
	);
	
	function update01Action()
	{
		return;
		$db = Database::instance();
		$query = "ALTER TABLE `rutm_v4custom_form_confirmation` ADD `step` TINYINT UNSIGNED NOT NULL DEFAULT '0' AFTER `enabled`";
		$db->query($query);
		$query = "SHOW COLUMNS FROM `rutm_v4custom_form_confirmation` ";
		$c = $db->query($query)->as_array();
		echo '<pre>';
		print_r($c);
		exit;
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
				//check on free
				
				//check on step
				if ($confirmation->step < 2)
				{
					$confirmation->step = 2;
					$confirmation->confirmed = date('Y-m-d H:i:s');
					$confirmator->saveConfirmation($confirmation);
				}
				if ($confirmation->step == 3)
				{
					$promocodeResponse = $this->_getPromocode($email);
					if ($promocodeResponse['status'])
					{
						$this->_response['data']['promocode'] = $promocodeResponse['promocode'];
						$this->_response['data']['discount'] = $promocodeResponse['discount'];

						$template = $this->getMailTemplate('promocode_' . $promocodeResponse['discount']);
						$subject = $this->getOption('mailSubjectPromocode', 'Подарок от Деда');
						$subject = $this->getOption('mailSubjectPromocode_' . $promocodeResponse['discount'], $subject);
						
						$body = $this->renderTemplate($template, array('promocode' => $promocodeResponse['promocode'], 'discount' => $promocodeResponse['discount']));
						$result = $this->sendEmail($email, $subject, $body);
						if ($confirmation->max_try > 0)
							$confirmation->max_try --;
						$confirmation->enabled = ($confirmation->max_try > 0) * 1;
						$confirmator->saveConfirmation($confirmation);
					}
				
				}
			}
			else
			{
				throw new Exception('error_validation_code_used');
			}
			if ($confirmation->step == 2)
				Moto_Config::set('landingNewYearStep', 'choice-gift');
			if ($confirmation->step == 3)
			{
				Moto_Config::set('landingNewYearStep', 'choice-gift');
				Moto_Config::set('landingNewYearBCGift', 'gift-box-chosen');
			}
			$this->_response['email'] = $confirmation->email;
			$this->_response['sid'] = $confirmation->sid;
			$this->_response['step'] = $confirmation->step;
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
	
	
	function choicegiftAction($request)
	{
		try
		{
			$data = $request['data'];
			$sid = $this->_getFrom($data, 'sid');
			$email = $this->_getFrom($data, 'email');			
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
			if ($confirmation && $confirmation->enabled && $confirmation->sid === $sid)
			{
				//check on free
				
				//check on step
				if ($confirmation->step == 2 || $confirmation->step == 3)
				{
					$promocodeResponse = $this->_getPromocode($email);
//					$this->_response['promocode'] = $promocodeResponse;
					if ($promocodeResponse['status'])
					{
						$this->_response['data']['promocode'] = $promocodeResponse['promocode'];
						$this->_response['data']['discount'] = $promocodeResponse['discount'];
						
						$template = $this->getMailTemplate('promocode_' . $promocodeResponse['discount']);
						$subject = $this->getOption('mailSubjectPromocode', 'Подарок от Деда');
						$subject = $this->getOption('mailSubjectPromocode_' . $promocodeResponse['discount'], $subject);
						
						$body = $this->renderTemplate($template, array('promocode' => $promocodeResponse['promocode'], 'discount' => $promocodeResponse['discount']));
						$result = $this->sendEmail($email, $subject, $body);
						$this->_response['message'] = $this->_messages['success_promocode_send'];
						
						$confirmation->step = 3;
						if ($confirmation->max_try > 0)
							$confirmation->max_try --;
						$confirmation->enabled = ($confirmation->max_try > 0) * 1;
						$confirmator->saveConfirmation($confirmation);
					}
					else
					{
						$subject = 'TM.RU : NY2013 : MAYBE NO FREE PROMOCODE : ' . date('Y-m-d H:i:s');
						$body = 'Date: ' . date('Y-m-d H:i:s') . "\n";
						$body .= 'User email: ' . $confirmation->email . "\n";
						$body .= 'Request created: ' . $confirmation->created . "\n";
						$body .= 'Form content: ' . print_r(json_decode(base64_decode($confirmation->data), true), true) . "\n";
						$body .= 'Promocoder response: ' . print_r($promocodeResponse, true) . "\n";
						$body .= 'REQUEST: ' . print_r($_REQUEST, true) . "\n";
						$body = "<html><body><pre>$body</pre></body></html>";
						$this->sendEmail($this->_adminEmail, $subject, $body);
						throw new Exception('error_no_free_promocode');
					}
				}
				else
				{
					throw new Exception('error_confirmation_fail');
				}
			}
			else
			{
				throw new Exception('error_validation_code_used');
			}
			$this->_response['email'] = $confirmation->email;
			$this->_response['sid'] = $confirmation->sid;
			$this->_response['step'] = $confirmation->step;
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
	
	function showmedataAction()
	{
		$db = Database::instance();
		$prefix = $db->table_prefix();
		$tableName = 'custom_form_confirmation';
		
		$rows = $db
			->query("SELECT * FROM {$prefix}{$tableName} WHERE form_id = 2 ORDER BY `id` DESC LIMIT 50")
			->as_array()
			;
		foreach($rows as $i => $row)
		{
			$rows[$i]->data = json_decode(base64_decode($row->data));
		}
		echo json_encode($rows);
		exit;
	}

}