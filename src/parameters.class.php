<?php
/**
 * Parameters class definition. This class manage the configuration of the application.
 *
* @author Didelot Guillaume <gdidelot@live.fr>
* @version 1.0
* @package Core
* @subpackage CoreCommons
 */
namespace MalezHive\Commons;

/**
* Parameters class definition. This class manage the configuration of the application.
*
* @method Parameters Singleton()
* @method array GetParametersFiles()
* @method string Get($key)
* @method boolean SetParameterFile($filename)
* @method boolean StartWith($haystack, $needle)
*/
class Parameters 
{
	/**
	* The parameters keys
	* @var mixed
	*/
	public static $Keys;
	
	/**
	* The session instance
	* @var Core\CoreCommons\Parameters 
	*/
	private static $instance;

	/**
	* This method return an instance of Parameters
	*
	* @return Core\CoreCommons\Parameters The instance of Parameters
	*/
	public static function Singleton() 
	{
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}	
	
	/**
	* The default constructor
	*/
	private function __construct() 
	{
		self::$Keys = array();
		$file = CORE_DIR . "/../parameters.json";
		$str = file_get_contents($file);
		$json = json_decode($str); 
		self::$Keys = $json;
	}

	/**
	* Get parameter files
	*/
	public static function GetParametersFiles()
	{
		$files = array();
		$directory = CORE_DIR . "/../";
		
		$dh  = opendir($directory);
		while (false !== ($filename = readdir($dh))) 
		{
			if(self::StartWith($filename, "parameters"))
			{
				$path_parts = pathinfo($filename);
				if($path_parts['extension'] == 'json')
				{
					$files[] = $filename;
				}
			}
		}
		return $files;
	}
	
	/**
	* Get a value from the provided key
	*
	* @param string $key The key to parse
	*
	* @result string $result The found result or error message
	*/
	public static function Get($key)
	{
		$result = '';

		try
		{
			if(isset(self::$Keys->$key))
			{
				$result = self::$Keys->$key;
			}
			else
			{
				throw new \Exception("The parameter $key was not found");
			}
		}
		catch(\Exception $ex)
		{
			$result = sprintf("Key was not found : %s", $ex->getMessage());
		}
		
		return $result;
	}
	
	/**
	* Set parameter files
	*
	* @param string $filename The file name to set
	*
	* @return boolean true if it's set
	*/
	public static function SetParameterFile($filename)
	{
		$directory = CORE_DIR . "/../app/";
		$file = $directory . $filename;
		$newFile =  $directory . "parameters.json";
		return copy($file, $newFile);
	}

	/**
	* Find a key in string which start with the parameter
	*
	* @param string $haystack The string to parse
	* @param string $needle The filter to apply on the search pattern
	*
	* @return boolean true if it's found
	*/
	private static function StartWith($haystack, $needle)
	{
		return $needle === "" || strpos($haystack, $needle) === 0;
	}
}
?> 