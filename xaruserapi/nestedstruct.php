<?php

/**
 * Validate a nested struct
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage xmlrpcvalidatorapi
 * @author Marcel van der Boom <marcel@xaraya.com>
*/


function xmlrpcvalidatorapi_userapi_nestedstruct($args)
{
    extract($args);
    $sno=$msg->getParam(0);

    $twoK=$sno->structmem("2000");
    $april=$twoK->structmem("04");
    $fools=$april->structmem("01");
    $curly=$fools->structmem("curly");
    $larry=$fools->structmem("larry");
    $moe=$fools->structmem("moe");
    return new xmlrpcresp(new xmlrpcval($curly->scalarval()+
                                        $larry->scalarval()+
                                        $moe->scalarval(), "int"));

}
?>