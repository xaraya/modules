<?php

/**
 * File: $Id$
 *
 * Return statistics on a repository
 *
 * @package modules
 * @copyright (C) 2004 by the Xaraya Development Team.
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

function bkview_userapi_getstats($args)
{
    extract($args);
    if(!isset($repo) || !is_object($repo)) {
        // repo should have been passed in
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'repository object', 'userapi', 'getstats', 'Bkview');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                        new SystemException($msg));
        return;
    }
    return $repo->bkgetStats('');
}

?>
