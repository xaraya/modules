<?php
/**
 * Google Rel=NoFollow Transform
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 - 2009 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage NoFollow
 * @author John Cox
*/
function nofollow_init() 
{
    // Set up module hooks
    if (!xarModRegisterHook('item',
                           'transform',
                           'API',
                           'nofollow',
                           'user',
                           'transform')) {
        $msg = xarML('Could not register hook');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    return true;
}

function nofollow_delete() 
{
    // Remove module hooks
    if (!xarModUnregisterHook('item',
                             'transform',
                             'API',
                             'nofollow',
                             'user',
                             'transform')) {
        $msg = xarML('Could not un-register hook');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    return true;
}
?>