<?php

include_once 'Moto/Regru.php';
class Moto_Templatemonsterblog extends Moto_Regru 
{
	protected $_adminEmail = 'krio.rogue@gmail.com';
	protected $_notifyEmails = array('krio.rogue@gmail.com', 'ru@templatemonster.com','n.prosto@templatemonster.com');
	
	protected $_messages = array(
		'error_sorry_try_later' => 'Что-то пошло не так. До начала конца света у вас еще есть время заполучить инструкцию. Попробуйте повторить попытку позже.',
		'error_no_free_promocode' => '',
		'error_validation_code_used' => 'Что-то пошло не так. До начала конца света у вас еще есть время заполучить инструкцию. Попробуйте повторить попытку позже.',
		'error_confirmation_fail' => 'Кажется Вы уже получали инструкцию по спасению. Хорошего понемногу :)',
		'error_email_validation_fail' => 'К сожалению проверка емейла не прошла. Попробуйте еще разок.',
		'success_confirmation_send' => 'Почтовый голубь мира унес инструкцию в ваш ящик',
		'success_promocode_send' => 'Поздравляем. Вы доказали свое право быть избранным. Детальная инструкция с картинками уже у вас на почте.',
	);
	
	protected $_defaultOptions = array(
		'promocodeOptions' => array(
		),
		'refName' => 'tm-blog-theend',
		'landingUrl' => '',
		'mailSubjectConfirmation' => 'Спасение близко',
		'mailSubjectFaq' => 'Спецвыпуск с инструкцией по спасению',
		'mailTemplateDir' => 'includes/letters/blog/2012/theend',
		'fromName' => 'Монстровская Правда',
		'fromEmail' => 'pravda@templatemonster.ru',
	);
	
	function initRequestAction($request)
	{
		try {
			$email = $request['email'];
//			throw new Exception('error_sorry_try_later');
			if (preg_match('/@([0-9]*minutemail\.com|[0-9a-z\.]*mintemail\.com|zoaxe\.com|mailinator\.[a-z]+|artemworld\.net|zoaxe\.com|guerrillamailblock\.com|guerrillamail\.[a-z]+|yopmail\.com|softgrr\.ru|vposade\.com|no-spam\.ws|aeroflot\.ee|claws\.ru|mailforspam\.com|rmqkr\.net|asdasd\.ru|tritlex\.org\.ua|sharklasers\.com|isyzran\.ru|reply\.li|caterpillac\.com)$/i', $email))
			{
				throw new Exception('error_sorry_try_later');
			}
			if (preg_match('/\.\./i', $email))
			{
				throw new Exception('error_sorry_try_later');
			}
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

			$sid = $confirmator->createConfirmationCode($form, $email, $request);
			
			$template = $this->getMailTemplate('confirmation');
			
			$subject = $this->getOption('mailSubjectConfirmation');
			$url = 'http://www.templatemonsterblog.ru/#sid=' . $sid . '&action=confirm&ref_name=theend&email=' . $email;
			if (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], '.fmt') > 0 )
			{
				$url = 'http://www.templatemonsterblog.ru.fmt/#sid=' . $sid . '&action=confirm&ref_name=theend&email=' . $email;
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
				$template = $this->getMailTemplate('faq');
				$subject = $this->getOption('mailSubjectFaq', 'Спецвыпуск с инструкцией по спасению');
				$body = $this->renderTemplate($template, array());
				$attachments = array(
					array(
						'path' => CURRENT_THEME_DIR . '/' . $this->getOption('mailTemplateDir') . '/MP_specvypusk.pdf',
						'type' => 'application/pdf',
						'filename' => 'MP_specvypusk.pdf'
					)
				);
				$result = $this->sendEmail($email, $subject, $body, $attachments);
				$this->_response['message'] = $this->_messages['success_promocode_send'];
				if ($confirmation->max_try > 0)
					$confirmation->max_try --;
				$confirmation->enabled = ($confirmation->max_try > 0) * 1;
				$confirmation->confirmed = date('Y-m-d H:i:s');
				$confirmator->saveConfirmation($confirmation);
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
	
	function showemailsAction()
	{
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
			$emails = array();
			try {
				$emails[0] = $confirmator->getCountEmailsByForm($form->id, array('confirmed' => false));
				$emails[1] = $confirmator->getCountEmailsByForm($form->id, array('confirmed' => true));
			}
			catch(Exception $e)
			{
				print_r($e);
			}
			print_r($emails);
			exit;
	}
	
	function importtonewsletterAction()
	{
		$sql = "
INSERT IGNORE INTO rutm_v4customnewsletters (email, sid, created_at, confirmed)
(
	SELECT email, sid, unix_timestamp(created) AS created_at, 1 AS confirmed
	FROM `rutm_v4custom_form_confirmation`
	WHERE 1
	AND form_id = 3
	AND confirmed != '0000-00-00 00:00:00'
)";
		Database::instance()->query($sql);
	}
}