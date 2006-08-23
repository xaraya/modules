<?php
/**
 * Prototype management interface using the phpbeans client
 *
 * @package modules
 * @subpackage modulename
 * @copyright HS-Development BV, 2006-08-23
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://hsdev.com
 * @author Marcel van der Boom <mrb@hsdev.com>
**/

function xorba_admin_discover($args)
{
    $data = array();
    xarVarFetch('xorba_server','str:',$data['xorba_server'], null, XARVAR_NOT_REQUIRED);
    xarVarFetch('xorba_port','str:'  ,$data['xorba_port']  , null, XARVAR_NOT_REQUIRED);
    xarVarFetch('xorba_user','str:'  ,$data['xorba_user']  , null, XARVAR_NOT_REQUIRED);
    xarVarFetch('xorba_pass','str:'  ,$data['xorba_pass']  , null, XARVAR_NOT_REQUIRED);
    xarVarFetch('xorba_object','str:',$xorba_object   , 'server', XARVAR_NOT_REQUIRED);
    xarVarFetch('xorba_method','str:',$xorba_method   , null, XARVAR_NOT_REQUIRED);
    
    if(isset($data['xorba_server']) and isset($data['xorba_port']))
    {
        try 
        {
            // Ready to connect and do our thing
            $client = xarModApiFunc('xorba','admin','getclient',$data);
        
            // We need a list of objects
            $server = $client->getObject('server');
            foreach($server->listObjects() as $id => $name)
            {
                if(!isset($xorba_object) and $name == 'server')
                    $xorba_object = $name;
                $data['objects'][] = array('id' => $name, 'name' => $name);
            }
            $data['xorba_object'] = $xorba_object;
        
            // Get the methods of the object
            $activeObject = $client->getObject($xorba_object);
            foreach($activeObject->listMethods() as $id => $name)
            {
                $data['methods'][] = array('id' => $name, 'name' => $name);
            }
            $data['xorba_method'] = $xorba_method;
        
            // Retrieve method info
            $methodInfo = array();
            if($activeObject->hasMethod($xorba_method))
            {
                $methodInfo = $activeObject->methodInfo($xorba_method);
            }
            $data['xorba_methodinfo'] = $methodInfo;
            $client->disconnect();
        } catch (Exception $e) {
            $data['exception'] = $e->getMessage();
        }
    }
    return $data;
}
?>