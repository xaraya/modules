<?php

/**
 * Validate an array of structs
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage xmlrpcvalidatorapi
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function xmlrpcvalidatorapi_userapi_arrayofstructs($args)
{
    extract($args);
    $sno=$msg->getParam(0);
    $numcurly=0;
    for($i=0; $i<$sno->arraysize(); $i++) {
        $str=$sno->arraymem($i);
        $str->structreset();
        while(list($key,$val)=$str->structeach())
            if ($key=="curly")
                $numcurly+=$val->scalarval();
    }
    return new xmlrpcresp(new xmlrpcval($numcurly,"int"));
}
?>