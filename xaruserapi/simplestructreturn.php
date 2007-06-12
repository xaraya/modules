<?php

/**
 * Validate a simple struct return
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage xmlrpcvalidatorapi
 * @author Marcel van der Boom <marcel@xaraya.com>
*/


function xmlrpcvalidatorapi_userapi_simplestructreturn($args)
{
    extract($args);
    $sno=$msg->getParam(0);
    $v=$sno->scalarval();
    return new xmlrpcresp(new xmlrpcval(array(
                                              "times10" =>
                                              new xmlrpcval($v*10, "int"),
                                              "times100" =>
                                              new xmlrpcval($v*100, "int"),
                                              "times1000" =>
                                              new xmlrpcval($v*1000, "int")),
                                        "struct"));
}
?>