 <?php
 /**
  * File: $Id$
  *
  * Initialization of SOAP server
  *
  * @package modules
  * @copyright (C) 2003 by the Xaraya Development Team.
  * @link http://www.xaraya.com
  * 
  * @subpackage module name
  * @author Marcel van der Boom <marcel@xaraya.com>
 */
 
 /**
  * Initialise the installed SOAP server APIs
  * 
  * Carries out a number of initialisation tasks to get SOAP up and
  * running.
  * @param none
  * @returns void
  */
 function soapserver_userapi_initsoapserver()
 {
     // include SOAP library
     require_once('modules/soapserver/lib/class.nusoap_base.php');
 
 //    $server = new soap_server();
 // use some not-quite-functional WSDL for now...
     $server =& new soap_server('modules/soapserver/xaraya.wsdl');
	 //$server = & new soap_server('soap_val');
     if (!$server) {
         //die("webservices_userapi_initSOAPServer: can't create server");
         echo new soap_fault( 
             'Server', '', 
             'Unable to create server', '' 
         ); 
         return;
     }
 // register some dummy function for now...
 //    $server->register('wsModAPIFunc');
 
     return $server;
 }
 
 ?>
