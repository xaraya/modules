<?php
 /**
  * File: $Id$
  *
  * Test function for SOAP Server
  *
  * @package modules
  * @copyright (C) 2009 by the Xaraya Development Team.
  * @link http://www.xaraya.com
  * 
  * @subpackage module name
  * @author Jason Judge <judgej@xaraya.com>
 */
 
 /**
  * Provide a simple test function for the SOAP Server.
  * By default, this will be the only function available.
  * 
  * @param string name Optional name (any test string).
  * @returns string A formatted message.
  */

function soapserver_userapi_hello($args)
{
    // If a name has been passed in, then use it in the return message.
    // If not, then return a generic message.
    if (isset($args['name']) && is_string($args['name'])) {
        $message = xarML('Hello. Your name is "#(1)"', $args['name']);
    } else {
        $message = xarML('Hello. You did not give your name.');
    }

    return $message;
}
 
?>