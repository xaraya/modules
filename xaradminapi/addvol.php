<?php
/**
 * Add a volume to the encyclopedia
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

function encyclopedia_adminapi_addvol($args)
{
    if (!xarSecurityCheck('AddEncyclopedia')) {return;}
    extract($args);
    if ((!isset($volume)) || (!isset($description))) {
        $msg = "No encyclopedia volume was found";
        xarErrorSet(XAR_USER_EXCEPTION, 'NOT_FOUND', new DefaultUserException($msg));
        return;
    }

    $cid = xarModGetVar('encyclopedia', 'volumes');
    $vid = xarModAPIFunc('categories', 'admin', 'create',
                            array('name' => $volume,
                                'description' => $description,
                                'parent_id' => $cid));

    xarModCallHooks('volume', 'addvol', $vid, 'vid');

    return $vid;
}

?>