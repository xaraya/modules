<?php
/**
 * Update a term in the encyclopedia
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 */

function encyclopedia_adminapi_updatevol($args)
{
    extract($args);
    if ((!isset($vid)) ||
        (!isset($volume)) ||
        (!isset($description))) {
        $msg = "No encyclopedia volume was found";
        xarErrorSet(XAR_USER_EXCEPTION, 'NOT_FOUND', new DefaultUserException($msg));
        return;
    }
    if (!xarSecurityCheck('EditEncyclopedia',0,'Volume',$volume . "::" . $vid)) {return;}

    $result = xarModAPIFunc('categories', 'admin', 'updatecat',
                            array('cid' => $vid,
                                  'name' => $volume,
                                  'description' => $description,
                                  'moving' => 0));
    return $result;
}

?>