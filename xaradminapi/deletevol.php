<?php
/**
 * Delete a volume from the encyclopedia
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

function encyclopedia_adminapi_deletevol($args)
{
    extract($args);
    if (!isset($vid)) {
        $msg = "No encyclopedia volume was found";
        xarErrorSet(XAR_USER_EXCEPTION, 'NOT_FOUND', new DefaultUserException($msg));
        return;
    }

    xarModAPIFunc('categories', 'admin', 'deletecat',
                            array('cid' => $vid));

    xarModCallHooks('volume', 'deletevol', $vid, '');
    return true;
}

?>