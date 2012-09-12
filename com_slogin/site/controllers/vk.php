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
require_once JPATH_SITE.'/FirePHPCore/fb.php';

class SMLoginControllerVk extends SMLoginController
{
	protected $client_id;
	protected $client_secret;
	
	public function __construct()
	{
	
		$cofig = array();
		parent::__construct($cofig);
		$this->client_id =  $this->config->get('vk_client_id');
		$this->client_secret = $this->config->get('vk_client_secret');
	
	}
	
	/**
	 * Аутентификация пользователя
	 */
	public function auth()
	{
		parent::auth();
		$redirect = urlencode(JURI::base().'?option=com_smlogin&task=vk.check');

		$params = array(
				'client_id=' . $this->client_id,
			    'response_type=code',
			    'redirect_uri=' . $redirect,
				'scope=offline'
		);
		$params = implode('&', $params);

		$url = 'http://oauth.vkontakte.ru/oauth/authorize?' . $params;

		header('Location:' . $url);
	}

	/**
	* Проверка аутентификации на сайте донора
	* Создание новой учетной записи на сайте или утентификация, если такой пользователь уже есть
	*/	
	public function check()
	{
		$input = JFactory::getApplication()->input;

		if ($code = $input->get('code')) {
			//подключение к API
			$params = array(
							'client_id=' . $this->client_id,
						    'client_secret=' . $this->client_secret,
						    'code=' . $code
			);
			$params = implode('&', $params);			
			
			$url = 'https://api.vkontakte.ru/oauth/access_token?' . $params;
			$data = json_decode($this->open_http($url));
			if ($data->error) {
				die($data->error_description);
			}
			
// 			Получение данных о пользователе поле fields
// 			Нужное можно указать!
// 			uid, first_name, last_name, nickname, screen_name, sex, bdate (birthdate), city, country, 
// 			timezone, photo, photo_medium, photo_big, has_mobile, rate, contacts, education, online, counters.
// 			По умолчанию возвращает uid, first_name и last_name 
			
// 			name_case - дополнительный параметр
// 			падеж для склонения имени и фамилии пользователя. 
// 			Возможные значения: 
// 			именительный – nom, 
// 			родительный – gen, 
// 			дательный – dat, 
// 			винительный – acc, 
// 			творительный – ins, 
// 			предложный – abl. 
// 			По умолчанию nom.
			
			$ResponseUrl = 'https://api.vkontakte.ru/method/getProfiles?uid='.$data->user_id.'&access_token='.$data->access_token.'&fields=nickname,contacts';
			$request = json_decode($this->open_http($ResponseUrl))->response[0];

			$type = 'vk';
			$id = $request->uid;

			$username = $this->getUserName($type, $id);
			//проверяем существует ли пользователь с таким именем
			$user_id = JUserHelper::getUserId($username);

			FB::log($data->expires_in); 
			
			if (!$user_id) {
				//вконтакте не дают email пользователя!
				//присваиваем случаййное
				$email = $id . '@' . $type. '.com';
				$name = $this->setUserName($request->first_name, $request->last_name);
				$this->storeUser($username, $name, $email);
			} else {
				$this->loginUser($user_id);
			}

		}

	}

}
