<?php
/**
 * Moveable type module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage moveabletype
 * @author Marcel van der Boom <marcel@xaraya.com>
 */
function moveabletype_userapi_publishPost($args)
{
    // We dont need this in Xaraya, so just return success
    return xarModAPIFunc('xmlrpcserver','user','successresponse');
}
?>
