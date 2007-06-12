<?php

/**
 * Validate an easy struct
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage xmlrpcvalidatorapi
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function xmlrpcvalidatorapi_userapi_easystruct($args)
{
    extract($args);
    $sno=$msg->getParam(0);
    $moe=$sno->structmem("moe");
    $larry=$sno->structmem("larry");
    $curly=$sno->structmem("curly");
    $num=$moe->scalarval()+ $larry->scalarval()+ $curly->scalarval();
    return new xmlrpcresp(new xmlrpcval($num,"int"));
}
?>