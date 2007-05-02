<?php
/**
 * File: $Id$
 *
 * Create a xmlrpc response based on a module template
 *
 * This function uses xarTplModule to construct an xmlrpc 
 * response. To enable XMLRPC API developers to have a choice in 
 * how to create their API, this function supports two methods
 * for creating a response:
 * 1. supply a template in the API module called: apimodname-rpccommand.xd
 * 2. use a generic protocol template in this module
 *
 * The first method has the advantage that the command of the API can be
 * spelled out in the template, thus making it easy to construct a command
 * and have complete control over how the datastructures are passed on to it.
 * Disadavantage is however that the complete xmlrpc response must be spelled out,
 * which sometimes can be very lengthy (although with some clever BL tricks you can
 * easily shorten the templates, as method 2 will show.
 *
 * The second method uses the generic templates:
 * xmlrpc-createresponse.xd and includes/xmlrpc-types.xd
 * to model the complete response format of xmlrpc.
 * Advantage is that no specific template has to be coded and the datastructure
 * is always the same. For xmlrpc diehards this will be easier 
 * Disadvantage is that you'll have to stare at the specific datastructure which
 * is need by the generic template, see xardocs/notes.txt in this module
 *
 * So, the two methods are tradeoff between consistency and easy of use, take your pick!
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
    //xarLogMessage(print_r($params,true), XARLOG_LEVEL_WARNING);
    $data = array();
    if(isset($module) && isset($command)) {
        // Using a specific template supplied by the module
        $type = $module; // Type used in the module template is the originating module
        $data = $params; // Just pass on the $params as tplData to the xarTplModule call
    } else {
        $module = 'xmlrpcserver';   // *we* are providing the response
        $type   = 'xmlrpc';         // type is an xmlrpc response (doh)
        $command= 'createresponse'; // command in this module to use
        // Using the generic protocol template
        foreach ($params as $parameter) {
            $data[] = __scan_param($parameter);
        }
        $data = array('params' => $data);
    }
    
    // Fault response?
    if(isset($fault)) {
        $data['fault'] = true;
    }
    
    //xarLogMessage(print_r($data,true), XARLOG_LEVEL_WARNING);
    // We need to disable the template comments, if they are enabled, otherwise
    // the response will be invalid
    $themecomments = xarModGetVar('themes','ShowTemplates');
    xarModSetVar('themes','ShowTemplates',0);
    $output = xarTplModule($module,$type,$command, $data);
    xarModSetVar('themes','ShowTemplates',$themecomments);
    return $output;
}

/**
 * Private function which recursively scans the supplied params and returns the
 * proper data array to submit to the generic template. Basically what we're
 * doing is replacing the numeric indices of the passed in nested array with
 
 *
 * @todo We're translating one type of array into another, messy
 */
function __scan_param($root)
{
    //xarLogMessage(print_r($root,true),XARLOG_LEVEL_WARNING);
    $data = array();
    switch($root[0]) {
        case 'struct':
            $members = array();
            foreach($root[1] as $member) {
                $members[] = array('name'  => $member[0],
                                   'type'  => $member[1],
                                   'value' => is_array($member[2]) ? __scan_param($member[2]) : $member[2]);
            }
            $data = array('type' => $root[0], 'members' => $members);
            break;
        case 'array' :
            $members = array();
            foreach($root[1] as $member) {
                $members[] = array('type' => $member[0],
                                   'value' => is_array($member[1]) ? __scan_param($member[1]) : $member[1]);
            }
            $data = array('type' => $root[0], 'members' => $members);
            break;
        case 'string':
        case 'int':
        case 'i4':
        case 'double':
        case 'boolean':
        case 'base64':
        case 'dateTime.iso8601':
            // Scalar type, next elem in array is value
            $data = array('type' => $root[0], 'value' => $root[1]);
            break;
        default:
            // An invalid xml rpc type was specified, ignore it
            //echo ('invalid xmlrpc type specified ' . $root[0]);
    }
    //xarLogMessage(print_r($data,true), XARLOG_LEVEL_WARNING);
    return $data;
}
?>
