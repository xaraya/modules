 <?php
 
 /**
  * File: $Id$
  *
  * Call a SOAP method
  *
  * @package modules
  * @copyright (C) 2003 by the Xaraya Development Team.
  * @link http://www.xaraya.com
  * 
  * @subpackage module name
  * @author Marcel van der Boom <marcel@xaraya.com>
 */
 
 /**
  * Call a remote SOAP method
  * 
  * Opens a SOAP connection
  * with the specified parameters.
  * @returns resultrarray
  * @access private
  */
 function webservices_userapi__callSOAP($methodname, $params, $endpoint, $namespace)
 {
     
     include_once('modules/webservices/lib/nusoap.php');
     
     $soapclient =& new soapclient($endpoint['site'].$endpoint['path']);
     
     if($err = $soapclient->getError()){
         echo 'Request: <xmp>'.$soapclient->request.'</xmp>';
         echo 'Response: <xmp>'.$soapclient->response.'</xmp>';
         echo 'Debug log: <pre>'.$soapclient->debug_str.'</pre>';
         // throw exception
     } else {
         $return_val = $soapclient->call($methodname, $params, $namespace);
         if($err = $soapclient->getError()){
             // handle error however
             echo 'Request: <xmp>'.$soapclient->request.'</xmp>';
             echo 'Response: <xmp>'.$soapclient->response.'</xmp>';
             //echo 'Debug log: <pre>'.$soapclient->debug_str.'</pre>';
         } else {
             return $return_val;
         }
     }
     unset($soapclient); 
     
 }
?>
