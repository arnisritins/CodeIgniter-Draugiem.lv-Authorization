CodeIgniter Draugiem.lv Authorization
=====================================

Simple CodeIgniter library that helps to implement authorization system via Draugiem.lv


Installation
------------

### 1. Copy Draugiem.php file:
Move Draugiem.php file to your CodeIgniter application folder **.application/libraries/Draugiem.php**



### 2. Set config values:
Then open **.application/config/config.php** and update these options:

Set your own encryption key (required for CodeIgniter session library):
 ```php
$config['encryption_key'] = '1234abcd';
 ```

Set this option TRUE, in order to logout, when user closes browser:
 ```php
$config['sess_expire_on_close'] = TRUE;
 ```

Also, if you are using cookie sessions, it is better to encrypt them:
 ```php
$config['sess_encrypt_cookie'] = TRUE;
 ```



### 3. Create config file for Draugiem class:
Now you have to create a config file **.application/config/draugiem.php** where to store your application details:

 ```php
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['app_id'] = 00000001;
$config['app_key'] = '3c0acdc9c8hg79b43d8azc81c7171drr';
$config['app_url'] = 'http://example.com/users/login/';

/* End of file draugiem.php */
/* Location: ./application/config/draugiem.php */
 ```
 
Note, that config file must contain 3 obligatory options. Values of *app_id* and *app_key* you will get in Draugiem.lv developers page, where you created application, but *app_url* must contain an URL of your application, where it processes authorization.



### 4. Autoload Draugiem library:
Open **.application/config/autoload.php** and add Draugiem library to autoload array:

 ```php
$autoload['libraries'] = array('draugiem');
 ```
Now Draugiem class methods will be available anywhere in your application.




Using Draugiem class methods
----------------------------

Draugiem authorization class has some methods, so check out how to use them.



### Getting Draugiem.lv login URL
To get Draugiem.lv login URL, use `Draugiem::login_url()` method:
 ```php
<a href="<?php echo $this->draugiem->login_url(); ?>">Click to login via Draugiem.lv</a>
 ```


### Authorizing via Draugiem.lv
To authorize via Draugiem.lv, use `Draugiem::authorize()` method:

 ```php
if($this->draugiem->authorize())
{
  echo "Authorization succeed!";
}
else
{
	echo "Draugiem.lv authorization failed!";
}
 ```

**Important:**

You have to call this method only in you login page, which has parameters `$_GET["dr_auth_status"]` and `$_GET["dr_auth_code"]`, received from Draugiem.lv authorization page. In case of failure method returns FALSE.


### Checking for logged user
In order to check, if user is logged in, use `Draugiem::is_logged_in()` method:

 ```php
if($this->draugiem->is_logged_in())
{
	echo "You are logged in!";
}
 ```



### Getting user data
To get logged user's data array, use `Draugiem::user_data()` method:

 ```php
$user = $this->draugiem->user_data();
 ```
This method will return an associative array with such structure:

 ```
Array
(
    [uid] => 1403145
    [name] => Arnis
    [surname] => Ritins
    [nick] => 
    [place] => Rezekne
    [img] => http://i5.ifrype.com/profile/403/145/v1304169363/sm_1403145.jpg
    [imgi] => http://i5.ifrype.com/profile/403/145/v1304169363/i_1403145.jpg
    [imgm] => http://i5.ifrype.com/profile/403/145/v1304169363/nm_1403145.jpg
    [imgl] => http://i5.ifrype.com/profile/403/145/v1304169363/l_1403145.jpg
    [sex] => M
    [birthday] => 1992-12-16
    [age] => 20
    [adult] => 1
    [type] => User_Default
    [created] => 14.03.2007 17:49:01
    [deleted] => 
)
 ```

You can also pass an argument to this method, to get specific value, for example:
 ```php
$name = $this->draugiem->user_data("name");
 ```



### Logging out
You can log out simply by `Draugiem::logout()` method: 
 ```php
$this->draugiem->logout();
 ```

This method clears user data in CodeIgniter session.




Process authorization actions with controller
---------------------------------------------

To process login/logout actions, you can create new CodeIgniter controller, for example **./applcation/controllers/users.php**:

 ```php
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller
{
  
	public function __construct()
	{
		parent::__construct();
		$this->load->helper("url");
	}

	public function login()
	{
	
		if($this->draugiem->authorize())
		{
      		// Ahorization completed, so here you can store user data into database etc.
			redirect(base_url());
		}
		else
		{
			die("Draugiem.lv authorization failed!");
		}
		
	}

	public function logout()
	{
		$this->draugiem->logout();
		redirect(base_url());
	}

}

/* End of file users.php */
/* Location: ./application/controllers/users.php */
 ```
Now you have links both for login and logout:

`http://example.com/users/login/` - This will be authorization URL, so specify it in Draugiem config file as *app_url*.

`http://example.com/users/logout/` - And this link use to logout.


### Testing:
Now create sample controller to test authorization system:
 ```php
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->helper("url");
	}
	
	public function index()
	{
		
		if($this->draugiem->is_logged_in()) // User is logged - print some data about him
		{
		
			$user = $this->draugiem->user_data();
		
			echo '<p>Hello '. $user["name"] .' '. $user["surname"] .'!</p>';
			echo '<p><a href="'. base_url('users/logout') .'">Logout</a></p>';
		
		}
		else // User is not logged in - let him click on link to login via Draugiem.lv
		{
		
			echo '<a href="'. $this->draugiem->login_url() .'">Click to login via Draugiem.lv</a>';
		
		}
		
	}

}
	
/* End of file test.php */
/* Location: ./application/controllers/test.php */
 ```
Dont't forget to set this controller as default in **./application/config/routes.php** file.
