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
  * Carries out a number of initialisation tasks to get SOAP up and running.
  * @param none
  * @returns void
  */

function soapserver_userapi_initsoapserver()
{
    // include SOAP library
    require_once('modules/soapserver/lib/class.nusoap_base.php');
 
    // Create a new soap server
    $server =& new soap_server();

    // Declare the entry point for use in the WSDL file
    $server->configureWSDL('xaraya', 'urn:xar', xarServerGetBaseURL() . '/ws.php?type=soap');

    $server->register('wsModAPISimpleFunc',
        // input parameters - args is a polymorphic struct
        array(
            'module' => 'xsd:string',
            'func' => 'xsd:string',
            'type' => 'xsd:string',
            'username' => 'xsd:string',
            'password' => 'xsd:string',
            'args' => 'xsd:struct'
        ),
        // output parameters - no restrictions on type
        array(
            'output' => 'xsd:any'
        ),
        'urn:xar',
        false, // soapaction
        'rpc',
        'encoded',
        'Simple API function'
    );

    $server->register('wsModAPIFunc',
        // input parameters - args is a polymorphic struct
        array(
            'module' => 'xsd:string',
            'func' => 'xsd:string',
            'type' => 'xsd:string',
            'username' => 'xsd:string',
            'password' => 'xsd:string',
            'args' => 'xsd:struct'
        ),
        // output parameters - no restrictions on type
        array(
            'output' => 'xsd:any'
        ),
        'urn:xar',
        false, // soapaction
        'rpc',
        'encoded',
        'API function'
    );

    if (!$server) {
        //die("webservices_userapi_initSOAPServer: can't create server");
        echo new soap_fault( 
            'Server', '', 
            'Unable to create server', '' 
        ); 
        return;
    }
 
    return $server;
}
 
?>
