<?php

 /**
  * File: $Id$
  *
  * Return the SOAP client object
  *
  * @package modules
  * @copyright (C) 2003 by the Xaraya Development Team.
  * @link http://www.xaraya.com
  *
  * @subpackage module name
  * @author Jason Judge
 */

 /**
  * Return the SOAP object
  *
  * @returns soapclient object
  * @access private
  */
function soapserver_userapi_soapclient($args)
{
    // $methodname, $params, $endpoint, $namespace
    extract($args);

    include_once('modules/soapserver/lib/nusoap.php');

    if (!isset($endpoint)) {
        $soapclient =& new soapclient();
    } elseif (!isset($wsdl)) {
        $soapclient =& new soapclient($endpoint);
    } elseif (!isset($proxyhost)) {
        $soapclient =& new soapclient($endpoint, $wsdl);
    } elseif (!isset($proxyport)) {
        $soapclient =& new soapclient($endpoint, $wsdl, $proxyhost);
    } elseif (!isset($proxyusername)) {
        $soapclient =& new soapclient($endpoint, $wsdl, $proxyhost, $proxyport);
    } elseif (!isset($proxypassword)) {
        $soapclient =& new soapclient($endpoint, $wsdl, $proxyhost, $proxyport, $proxyusername);
    } elseif (!isset($timeout)) {
        $soapclient =& new soapclient($endpoint, $wsdl, $proxyhost, $proxyport, $proxyusername, $proxypassword);
    } elseif (!isset($response_timeout)) {
        $soapclient =& new soapclient($endpoint, $wsdl, $proxyhost, $proxyport, $proxyusername, $proxypassword, $timeout);
    } else {
        $soapclient =& new soapclient($wsdl, $wsdl, $proxyhost, $proxyport, $proxyusername, $proxypassword, $timeout, $response_timeout);
    }

    //$soapclient-> = 'UTF-8';

    return $soapclient;
}

?>
