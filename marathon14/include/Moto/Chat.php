<?php

include_once 'Moto/Regru.php';
class Moto_Chat extends Moto_Regru 
{
	protected $_adminEmail = 'krio.rogue@gmail.com';
	protected $_notifyEmails = array('krio.rogue@gmail.com', 'ru@templatemonster.com','n.prosto@templatemonster.com');
	protected $_chatRefferer = 'templatemonster.ru';
	protected $_chatOrderId = 'Customer+ID+3583';
	protected $_chatSekret = 'lak_asdg345';
	protected $_messages = array(
		'error_sorry_try_later' => '',
		'error_no_free_promocode' => '',
		'error_validation_code_used' => '',
		'error_confirmation_fail' => '',
		'error_email_validation_fail' => '',
		'success_confirmation_send' => '',
		'success_promocode_send' => '',
	);
	
	protected $_defaultOptions = array(
		'refName' => 'chat',
	);
	function initRequestAction($request)
	{
	}
	
	function saveAction($request)
	{
		try {
			$email = $request['email'];
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
				return;
			}

			$sid = $confirmator->createConfirmationCode($form, $email, $request);
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

	function getlinkAction($data){

		$name = isset($data['name']) ? $data['name'] : 'Guest';
		$room = isset($data['room']) ? $data['room'] : '';
		$mail = isset($data['mail']) ? $data['mail'] : '';
		$message = isset($data['message']) ? $data['message'] : '';
		$key = md5($mail . $name . $message . $this->_chatRefferer . $room . $this->_chatSekret);
		$chatDomain =  isset($data['domain']) ? $data['domain'] : 'www.templatemonster.ru';
		$href = 'http://' . $chatDomain . '/chat/connect/?email=' . $mail . '&nick=' . $name . '&referer=' . $this->_chatRefferer . '&room=' . $room . '&key=' . $key  . '&question=' . $message;
		$this->_response['status'] = true;
		$this->_response['href'] = $href;
		return;
	}

	
	function confirmAction($request)
	{
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
			$emails['count'] = $confirmator->getCountEmailsByForm($form->id);
			$emails['list'] = $confirmator->getEmailsByForm($form->id);
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
	AND form_id = 4
	AND confirmed != '0000-00-00 00:00:00'
)";
		Database::instance()->query($sql);
	}
}