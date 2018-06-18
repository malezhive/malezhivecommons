<?php
/**
* Mail class definition. This class manage the mail with their template used.
*
* @author Didelot Guillaume <gdidelot@live.fr>
* @version 1.0.0
* @package MalezHive
* @subpackage Commons
*/
namespace MalezHive\Commons;

/**
* Mail class definition. This class manage the mail with their template used.
*
* @method boolean Send($to = null)
*
* @exception Type_Parameter_Mandatory
* @exception Type_Unknown
* @exception Mail_Provider_Unknown
* @exception Mail_Provider_Mandatory
* @exception Boundary_Mandatory
* @exception Headers_Mandatory
* @exception Message_Mandatory
*/
class Mail
{
    /**
    * The receiver address
    * @var string
    */
    public $To;
    
    /**
    * The subject
    * @var string
    */
    public $Subject;
    
    /**
    * The html message
    * @var string
    */
    public $HtmlMessage;
    
    /**
    * The text message
    * @var string
    */
    public $TextMessage;
    
    /**
    * The global message
    * @var string
    */
    public $Message;
    
    /**
    * The headers
    * @var string
    */
    private $Headers;
    
    /**
    * The mail type
    * @var string
    */
    private $Type;

    /**
    * The creation date of the mail
    * @var datetime
    */
    private $CreationDate;
    
    /**
    * The mail boundary
    * @var string
    */
    private $Boundary;
    
    /**
    * The new line separator
    * @var string
    */
    private $NewLine;
    
    /**
    * The file path to the xml template
    * @var string
    */
    private $FilePath;
    
    /**
    * The values to include in the mail
    * @var string[]
    */
    private $Values;
	
	/**
    * The mail is a newsletter
    * @var boolean
    */
    private $IsNewsletter;
    
    /**
    * Default constructor
    *
    * @param string $type Set the mail type
    * @param string[] $values The values to switch from the template
    */
    public function __construct($type, $values, $isNewsletter = false)
    {
        Logger::Info(sprintf("Mail.__construct : Construct mail %s, with %s values", $type, count($values)));
        
        if ($type == null || $type == '') 
		{
            throw new Exception("Type_Parameter_Mandatory");
        }
        
        $this->IsNewsletter = $isNewsletter;
        $this->Type = $type;
        $this->CreationDate = date("Y-m-d H:i:s");
        $this->Boundary = "-----=" . md5(uniqid(rand()));
        
        $this->generateHeader();
		
		$this->FilePath = ($this->IsNewsletter == true) ? sprintf("%s/mails/newsletters/%s.xml", COMMONS_DIR, $this->Type) : sprintf("%s/mails/%s.xml", COMMONS_DIR, $this->Type);

        if (file_exists($this->FilePath) == false) 
		{
            throw new \Exception("Type_Unknown");
        }

        $this->Values = $values;
    }
    
    /**
    * This method send the built mail
    *
    * @param mixed $to Set the receiver
    *
    * @return boolean true if sent
    */
    public function Send($to = null)
    {
        Logger::Info("Mail.Send : Start to send mail for " . count($to) . " persons.");
        
        $result = false;
        
        try {
            $sent = false;
            if (isset($to)) {
                if (is_array($to) == false) {
                    $this->parseTemplate();
                    $this->generateNewLineTag($to);
                    Logger::Info("Mail.Send : Send mail to : $to / subject : $this->Subject / header : $this->Headers / message : $this->Message");
                    $sent = @mail($to, $this->Subject, $this->Message, $this->Headers);
                } elseif (is_array($to)) {
                    foreach ($to as $mail) {
                        $this->parseTemplate();
                        $this->generateNewLineTag($mail);
                        Logger::Info("Mail.Send : Send mail to : $mail / subject : $this->Subject");
                        $sent = @mail($mail, $this->Subject, $this->Message, $this->Headers);
                    }
                } else {
                    throw new \Exception("Mail_Provider_Unknown");
                }
                
                Logger::Info("Mail.Send : The text message : " . $this->TextMessage);
                Logger::Info("Mail.Send : The html message : " . $this->HtmlMessage);
            } else {
                throw new \Exception("Mail_Provider_Mandatory");
            }
            
            if ($sent == false) {
                Logger::Error("Mail.Send : Send mail subject : $this->Subject failed");
            } else {
                Logger::Info("Mail.Send : Send mail subject : $this->Subject");
            }
            
            $result = $sent;
        } catch (\Exception $ex) {
            Logger::Error($ex->getMessage());
            $result = false;
        }
        
        return $result;
    }
    
    /**
    * Generate the mail's header
    */
    private function generateHeader()
    {
        Logger::Info("Mail.generateHeader : Start to generate header");
        
        if ($this->Boundary == null || $this->Boundary == '') {
            throw new \Exception("Boundary_Mandatory");
        }
        
        $this->Headers .= "MIME-Version: 1.0\n";
        $this->Headers .= "Content-Type: multipart/alternative; boundary=\"$this->Boundary\"\n";
        $this->Headers .= "From: \"Urbanium\" <webmaster@urbanium.fr>\n";
        $this->Headers .= "Bcc: webmaster@urbanium.fr\n";
        
        Logger::Info("Mail.generateHeader : Header generated");
    }
    
    /**
    * Compute the new line separator depends on mailer
    *
    * @param string $mail The targeted mail address
    */
    private function generateNewLineTag($mail)
    {
        Logger::Info("Mail.generateNewLineTag : Start to generate the new line tag for $mail");
        
        if ($this->Headers == null || $this->Headers == '') {
            throw new \Exception("Headers_Mandatory");
        }
        
        if ($this->Message == null || $this->Message == '') {
            throw new \Exception("Message_Mandatory");
        }
        
         // add a tab on microsoft smtp server
        if (!preg_match("#^[a-z0-9._-]+@(hotmail|live|msn).[a-z]{2,4}$#", $mail)) {
            $this->NewLine = "\r\n";
        } else {
            $this->NewLine = "\n";
        }
        
        $this->Headers = str_replace("#NewLine", $this->NewLine, $this->Headers);
        $this->Message = str_replace("#NewLine", $this->NewLine, $this->Message);
        
        Logger::Info("Mail.generateNewLineTag : New line tag generated");
    }
    
    /**
    * This method parse xml template mail and build a Mail object
    *
    * @return void Set the path of the mail template
    */
    private function parseTemplate()
    {
        try {
            Logger::Info("Mail.parseTemplate : Start to parse $this->FilePath");
            
            $mail = simplexml_load_file($this->FilePath);
            $this->Subject = $mail->subject;
            $this->TextMessage = $mail->messagetext->asXML();
            $this->HtmlMessage = $mail->messagehtml->asXML();

            if (isset($this->Values)) {
                for ($i = 0; $i < count($this->Values); $i++) {
                    $key = array_keys($this->Values)[$i];
                    $value = (string)$this->Values[$key];
                    Logger::Info("Mail.parseTemplate : Replace $key with $value");
                    
                    $this->Message = " #NewLine--" . $this->Boundary . "#NewLine";
                    
                    Logger::Info("Mail.parseTemplate : Create text part");
                    
                    $this->TextMessage = str_replace($key, $value, $this->TextMessage);
                    $this->TextMessage = str_replace('<messagetext>', '', $this->TextMessage);
                    $this->TextMessage = str_replace('</messagetext>', '', $this->TextMessage);
                    $this->Message .= "Content-Type: text/plain; charset=\"ISO-8859-1\" #NewLine";
                    $this->Message .= "Content-Transfer-Encoding: 8bit #NewLine";
                    $this->Message .= "#NewLine" . $this->TextMessage . "#NewLine";
                    $this->Message .= "#NewLine--" . $this->Boundary . "#NewLine";
                    
                    Logger::Info("Mail.parseTemplate : Create html part");
                    
                    $this->HtmlMessage = str_replace($key, $value, $this->HtmlMessage);
                    $this->HtmlMessage = str_replace('<messagehtml>', '', $this->HtmlMessage);
                    $this->HtmlMessage = str_replace('</messagehtml>', '', $this->HtmlMessage);
                    $this->Message .= "Content-Type: text/html; charset=\"ISO-8859-1\" #NewLine";
                    $this->Message .= "Content-Transfer-Encoding: 8bit #NewLine";
                    $this->Message .= "#NewLine" . utf8_decode($this->HtmlMessage) . "#NewLine";
                    $this->Message .= "#NewLine--" . $this->Boundary . "-- #NewLine";
                    $this->Message .= "#NewLine--" . $this->Boundary . "-- #NewLine";
                }
            }
            $this->HtmlMessage = utf8_decode($this->HtmlMessage);
            Logger::Info("Mail.parseTemplate : Parse template finished");
        } catch (\Exception $ex) {
            Logger::Error($ex->getMessage());
        }
    }
}
?>