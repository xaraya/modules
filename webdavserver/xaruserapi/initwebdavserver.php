<?php

/**
 * File: $Id$
 *
 * Initialisation of the webdav server, instantiates a server object
 *
 * @package modules
 * @copyright (C) 2004 by Marcel van der Boom
 * 
 * @subpackage webdavserver
 * @author Marcel van der Boom <marcel@hsdev.com>
*/

function webdavserver_userapi_initwebdavserver()
{
    include 'modules/webdavserver/xarincludes/xarwebdav.php';
    return new xarwebdav();
}

?>