<?php

/**
 * Just echo the struct back
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage module name
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function xmlrpcvalidatorapi_userapi_echostruct($args)
{
    extract($args);
    $sno=$msg->getParam(0);
    return new xmlrpcresp($sno);
}
?>