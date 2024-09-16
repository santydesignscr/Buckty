<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH.'/third_party/hybridauth/Hybrid/Auth.php';

class HybridAuthLib extends Hybrid_Auth
{

	function __construct($config = array())
	{
		$this->ci =& get_instance();
		$this->ci->load->helper('url_helper');
        $this->ci->load->model('BucktySettings');
        $api = $this->ci->BucktySettings->getApi();
        $config = array(
		'base_url' => 'index.php/hauth/endpoint',
		"providers" => array (),
		// if you want to enable logging, set 'debug_mode' to true  then provide a writable file by the web server on "debug_file"
		"debug_mode" => false,

		"debug_file" => APPPATH.'/logs/hybridauth.log',
	);
		if(!empty($api['facebook'])) {
			$config['providers']['facebook'] = array(
				"enabled" => true,
				"keys" => array("id" => $api['facebook']['id'],
					"secret" => $api['facebook']['secret'])
		);
		}
		if(!empty($api['twitter'])) {
			$config['providers']['twitter'] = array (
				"enabled" => true,
				"keys"    => array ( "key" => $api['twitter']['id'],
					"secret" => $api['twitter']['secret'] )
			);
		}
		if(!empty($api['google'])) {
			$config['providers']['google'] = array (
				"enabled" => true,
				"keys"    => array ( "id" => $api['google']['id'],
					"secret" => $api['google']['secret']
				),
			);
		}
		$config['base_url'] = base_url().$config['base_url'];
		parent::__construct($config);

		log_message('debug', 'HybridAuthLib Class Initalized');
	}

	/**
	 * @deprecated
	 */
	public static function serviceEnabled($service)
	{
		return self::providerEnabled($service);
	}

	public static function providerEnabled($provider)
	{
		return isset(parent::$config['providers'][$provider]) && parent::$config['providers'][$provider]['enabled'];
	}
}