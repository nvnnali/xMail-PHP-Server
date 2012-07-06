<?php 
class configHandler
{
	/**
	 * The array holding all xMail settings
	 * @access private
	 */
	private static $settings;
	
	/**
	 * The instance of this class
	 * @access private
	 */
	private static $instance;
	
	/**
	 * Private constructor to prevent it being created directly
	 * @access private
	 */
	private function __construct()
	{
		require_once '..'.XMAIL_CONF_PATH;
		
		foreach($config as $key => $value)
		{
			$settings[$key] = $value;
		}
	}
	
	/**
	 * Triggers an error to prevent anyone from cloning the configHandler
	 * @access public
	 */
	public function __clone()
	{
		trigger_error("one does not simply clone the configHandler", E_USER_ERROR);
	}
	
	/**
	 * Singleton method used to access the settings
	 * @access public
	 * @return configHandler
	 */
	public static function singleton()
	{
		if(!isset(self::$instance))
		{
			$obj = __CLASS__;
			self::$instance = new $obj;
		}
		return self::$instance;
	}
	
	/**
	 * Gets a configuration node
	 * @access public
	 * @param $key String The key of the node wanted to get
	 * @return Object
	 */
	public static function get($key)
	{
		if(isset($key))
		{
			return $settings[$key];
		}
		else
		{
			trigger_error("Could not find config node \'".$key."\'", E_USER_ERROR);
		}
	}
}
?>