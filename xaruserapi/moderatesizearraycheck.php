<?php

/**
 * Validate a moderate size array
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage module name
 * @author Marcel van der Boom <marcel@xaraya.com>
*/


function xmlrpcvalidatorapi_userapi_moderatesizearraycheck($args)
{
    extract($args);
    $ar=$msg->getParam(0);
    $sz=$ar->arraysize();
    $first=$ar->arraymem(0);
    $last=$ar->arraymem($sz-1);
    return new xmlrpcresp(new xmlrpcval($first->scalarval() .
                                        $last->scalarval(), "string"));
}
?>