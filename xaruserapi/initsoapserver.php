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

    // Make sure any warnings are suppressed, since the soap server
    // needs to control all output.
    //error_reporting(0);
    ini_set('display_errors', 0);
 
    // Create a new soap server
    $server =& new soap_server();

    // Set the encoding type to the default for the site
    $locale = explode('.', xarMLSGetSiteLocale());
    if (!empty($locale[1])) {
       $server->soap_defencoding = strtoupper($locale[1]);
    }

    // Declare the entry point for use in the WSDL file
    $server->configureWSDL('xaraya', 'urn:xar', xarServerGetBaseURL() . '/ws.php?type=soap');

    // Two functions to register.
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
        return new soap_fault('Server', '', 'Unable to create server', ''); 
    }
 
    return $server;
}
 
?>
