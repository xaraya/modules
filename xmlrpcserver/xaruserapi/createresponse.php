<?php
/**
 * File: $Id$
 *
 * Create a xmlrpc response based on a module template
 *
 * This function uses xarTplModule to read in a template from
 * the module specified in the $module parameter. and construct
 * a xmlrpc response from that template. The $params parameter is
 * like the $tplData array normally used for 'normal' templates.
 * The templates for the responses are in the calling module and
 * should be called 'modulename_command.xd'
 * Doing it this way is a nice way to construct APIs for xmlrpc
 * because you can use BL to construct the messages, which makes it
 * rather easy to debug them and update to new versions of the API
 *
 * @package modules
 * @copyright (C) 2004 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @param string $module  The module providing the template for the response
 * @param string $command Which command are we generating a response for
 * @param array  $params  Array holding the variables to replace in the template
 * @subpackage xmlrpcserver
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function xmlrpcserver_userapi_createresponse($args)
{
    // Get the parameters
    extract($args);
    
    // We need to disable the template comments, if they are enabled, otherwise
    // the response will be invalid
    $themecomments = xarModGetVar('themes','ShowTemplates');
    xarModSetVar('themes','ShowTemplates',0);
    $output = xarTplModule($module, $module, $command, $params);
    xarModSetVar('themes','ShowTemplates',$themecomments);
    return $output;
}
?>
