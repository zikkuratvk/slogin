<?php
/**
* SMLogin
*
* @version 	1.0
* @author		SmokerMan
* @copyright	© 2012. All rights reserved.
* @license 	GNU/GPL v.3 or later.
*/

// No direct access.
defined('_JEXEC') or die('(@)|(@)');

require_once JPATH_COMPONENT_SITE.DS.'controller.php';

class SMLoginControllerOdnoklassniki extends SMLoginController
{
	protected $client_id;
	protected $client_secret;
	protected $application_key;
	
	public function __construct() 
	{
		$cofig = array();
		parent::__construct($cofig);
		$this->client_id =  $this->config->get('odnoklassniki_client_id');
		$this->client_secret = $this->config->get('odnoklassniki_client_secret');
		$this->application_key =  $this->config->get('odnoklassniki_application_key');		

		

	}
	
	
	/**
	* Аутентификация пользователя
	*/
	public function auth()
	{
		parent::auth();
		
		$redirect = urlencode(JURI::base().'?option=com_smlogin&task=odnoklassniki.check');
		$params = array(
				'client_id=' . $this->client_id,
			    'response_type=code',
			    'redirect_uri=' . $redirect,
				'scope=VALUABLE ACCESS'
		);
		$params = implode('&', $params);
	
		$url = 'http://www.odnoklassniki.ru/oauth/authorize?'.$params;
	
		header('Location:' . $url);
	}

	/**
	* Проверка аутентификации на сайте донора
	* Создание новой учетной записи на сайте или утентификация, если такой пользователь уже есть
	*/	
	public function check()
	{
		
		$input = JFactory::getApplication()->input;
		if ($code = $input->get('code', null, 'STRING')) {
			// get access_token from mail  API
			$redirect = urlencode(JURI::base().'?option=com_smlogin&task=odnoklassniki.check');
			$params = array(
					'client_id=' . $this->client_id,
					'client_secret=' . $this->client_secret,
					'grant_type=authorization_code',
			        'code=' . $code,
	            	'redirect_uri=' . $redirect
			 
	
			);
			$params = implode('&', $params);
			
			$url = 'http://api.odnoklassniki.ru/oauth/token.do';
			$request = json_decode($this->open_http($url, true, $params));
			
			/*
			Объект $request содержит следующие поля:
			uid - уникальный номер пользователя
			first_name - имя пользователя
			last_name - фамилия пользователя
			birthday - дата рождения пользователя
			gender - пол пользователя
			pic_1 - маленькое фото
			pic_2 - большое фото
			*/
			
			$request_params = array(
					'application_key=' . $this->application_key,
					'access_token=' .  $request->access_token,
					'method=users.getCurrentUser',
					'sig=' . md5('application_key=' . $this->application_key . 'method=users.getCurrentUser' . md5($request->access_token . $this->client_secret))
			);
			
			$params = implode('&', $request_params);

			$url = 'http://api.odnoklassniki.ru/fb.do?'.$params;
			$request = json_decode($this->open_http($url));

			//username prefix for Joomla
			$type = 'odnoklassniki';
			$id = $request->uid;
			
			$username = $this->getUserName($type, $id);
			//проверяем существует ли пользователь с таким именем
			$user_id = JUserHelper::getUserId($username);
			if (!$user_id) {
				$email = $id . '@' . $type;
				$name = $this->setUserName($request->first_name, $request->last_name);
				$this->storeUser($username, $name, $email);
			} else {
				$this->loginUser($user_id);
			}
	
		} elseif ($err = $input->get('error')) {
			die($err);
		}
	
	}	
	
	/**
	 * Получение прпаметров для запроса. Формирование сигнатуры
	 * @param array $request_params		Обязательные параметры для запроса
	 * @return string	Параметры для запроса
	 */
	protected function get_server_params(array $request_params) {
		ksort($request_params);
		foreach ($request_params as $key => $value) {
			$params .= "$key=$value&";
		}
		
		$sig_params = str_replace('&', '', $params);
		$signature = md5($sig_params . $this->client_secret);
		$params = $params .'sig=' . $signature;
		
		return  $params;
	}
}