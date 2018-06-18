<?php
/**
* this class is the definition of a ComListener
*
* @author Didelot Guillaume <gdidelot@live.fr>
* @version 1.0
* @package Core
* @subpackage CoreContracts
*/
namespace MalezHive\Commons;

use Core;

/**
* Communication listener about provided parameters from javascript and send result to javascript
*
* @method JSON getData()
* @method void sendData($response)
*
* @exception Data_Mandatory
*/
class ComListener
{
    /**
    * Get all datas contain in the buffer
    *
    * @return JSON This response contains the JSON response provided by client side
    */
    public static function getData()
    {
        $data = json_decode(@file_get_contents("php://input"));
        
        if ($data == null) {
            throw new \Exception("Data_Mandatory");
        }
        
        return $data;
    }

    /**
    * This is an async method
    *
    * @param object $response The data to send
    *
    */
    public static function sendData($response)
    {
        try {
            $output = json_encode($response);
            echo $output;
            flush();
        } catch (\Exception $ex) {
            Core\CoreCommons\Logger::Error("ComListener.SendData : failed " . $ex->getMessage());
        }
    }
}
?>