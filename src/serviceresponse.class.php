<?php
/**
* The common service response
*
* @author Didelot Guillaume <gdidelot@live.fr>
* @version 1.0
* @package 
* @subpackage 
*/
namespace MalezHive\Commons;

/**
* The common service response
*
* @author Didelot Guillaume <gdidelot@live.fr>
* @version 1.0.0
* @package Core
* @subpackage CoreCommons
*
* @method Core\CoreCommons\ServiceResponse CreateError(\Exception $ex)
*/
class ServiceResponse
{
    /**
    * The object response to return at the end of the service call
    * @var mixed
    */
    public $response;
    
    /**
    * The service detect an error
    * @var boolean
    */
    public $isFailed;
    
    /**
    * The message exception
    * @var string $exception
    */
    public $exception;
    
    /**
    *  The default constructor
    *
    * @param mixed $response The response to provide
    */
    public function __construct($response)
    {
        $this->response = $response;
        $this->isFailed = false;
        $this->exception = null;
    }
    
    /**
    * Create an error response
    *
    * @param \Exception $ex The exception to throw
    *
    * @return Core\CoreCommons\ServiceResponse The failed message response
    */
    public static function CreateError(\Exception $ex)
    {
        Logger::Error($ex->getMessage());
        $serviceResponse = new ServiceResponse(null);
        $serviceResponse->isFailed = true;
        $serviceResponse->exception = $ex->getMessage();
        return $serviceResponse;
    }
}

?>