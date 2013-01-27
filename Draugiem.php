<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Draugiem.lv Authorization Class
 *
 * Simple CodeIgniter library that helps to implement authorization system via Draugiem.lv
 */
class Draugiem
{


	private $app_id; // Application ID
	private $app_key; // Application key
	private $app_url; // Application URL for redirect
	
	const API_URL = "http://api.draugiem.lv/php/"; // Draugiem.lv API URL
	const LOGIN_URL = "http://api.draugiem.lv/authorize/"; // Draugiem.lv login URL
	
	
	/**
	 * Constructs Draugiem.lv authorization object
	 *
	 * @param array $config Configuration values
	 */
	public function __construct($config)
	{
	
		$this->CI =& get_instance();
		$this->CI->load->library("session"); // Loads CodeIgniter session library

		$this->app_id = (int)$config["app_id"];
		$this->app_key = $config["app_key"];
		$this->app_url = $config["app_url"];

	}
	

	/**
	 * Authorizes Draugiem.lv user
	 *
	 * @return bool Returns TRUE if user has authorized successfully or FALSE on failure
	 */
	public function authorize()
	{

		$authorized = FALSE;
		
		if(isset($_GET["dr_auth_status"]) && $_GET["dr_auth_status"] == "ok" && 
			isset($_GET["dr_auth_code"]) && ! empty($_GET["dr_auth_code"]))
		{
		
			$this->logout(); // Clears previous session
			$data = $this->api_call($_GET["dr_auth_code"]); // Makes API call
			
			if($data !== FALSE)
			{
				
				$user_data = reset($data["users"]);
				$this->CI->session->set_userdata(array("USER" => $user_data)); // Sets user's data in session
				
				$authorized = TRUE; // Authorized successfully
				
			}
			
		}
		
		return $authorized;

	}


	/**
	 * Makes API call to Draugiem.lv API server (API authorize request)
	 * 
	 * @param string $code Draugiem.lv authorization code
	 * @return array Returns API response data as array or FALSE on failure
	 */
	private function api_call($code)
	{
		
		$response = FALSE;
		$url = self::API_URL ."?app=". $this->app_key ."&code=". $code ."&action=authorize";
		
		if(function_exists("curl_init"))
		{
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			
			if($data = curl_exec($ch))
			{
				
				$data = unserialize($data);
				
				if( ! array_key_exists("error", $data))
				{
					$response = $data;
				}
			
			}
			
			curl_close($ch);
			
		}
		
		return $response;
		
	}


	/**
	 * Checks if user is logged in
	 *
	 * @return bool Returns TRUE if user is logged in or FALSE if isn't
	 */
	public function is_logged_in()
	{
		
		return ($this->CI->session->userdata("USER") !== FALSE);

	}			


	/**
	 * Returns user's data, stored in session
	 *
	 * @param string $key Key of user's data array, to get specific value (optional)
	 * @return mixed Returns user's data array or specific value or FALSE on failure
	 */
	public function user_data($key = FALSE)
	{
		
		$user_data = $this->CI->session->userdata("USER");
		return ($key === FALSE || ! $user_data) ? $user_data : $user_data[$key];

	}			


	/**
	 * Clears user's data in session
	 */
	public function logout()
	{

		$this->CI->session->unset_userdata(array("USER" => ""));

	}


	/**
	 * Creates Draugiem.lv login URL
	 */
	public function login_url()
	{
		
		$hash = md5($this->app_key . $this->app_url);
		return self::LOGIN_URL ."?app=". $this->app_id ."&hash=". $hash ."&redirect=". urlencode($this->app_url);
		
	}


}
	

/* End of file Draugiem.php */
/* Location: ./application/libraries/Draugiem.php */
