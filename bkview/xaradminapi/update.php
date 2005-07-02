<?php

/**
 * File: $Id$
 *
 * Short description of purpose of file
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

/**
 * update a bkview item
 *
 * @author the Bkview module development team
 * @param $args['exid'] the ID of the item
 * @param $args['name'] the new name of the item
 * @param $args['number'] the new number of the item
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function bkview_adminapi_update($args)
{
    if (!xarSecurityCheck('AdminAllRepositories')) return;

    extract($args);

    $invalid = array();
    if (!isset($repoid) || !is_numeric($repoid)) {
        $invalid[] = 'item ID';
    }
    if (!isset($reponame) || !is_string($reponame)) {
        $invalid[] = 'repo name';
    }
    if (!isset($repopath) || !is_string($repopath)) {
        $invalid[] = 'repo path';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'update', 'Bkview');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    $item = xarModAPIFunc('bkview','user','get',array('repoid' => $repoid));

    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $bkviewtable = $xartable['bkview'];

    $sql = "UPDATE $bkviewtable
            SET xar_name = '" . xarVarPrepForStore($reponame) . "',
                xar_path = '" . xarVarPrepForStore($repopath) . "'
            WHERE xar_repoid = " . xarVarPrepForStore($repoid);
    if(!$dbconn->Execute($sql)) return;

    // Let any hooks know that we have updated an item.  As this is an
    // update hook we're passing the updated $item array as the extra info
    $item['module'] = 'bkview';
    $item['itemid'] = $repoid;
    $item['name'] = $reponame;
    $item['number'] = $repopath;
    xarModCallHooks('item', 'update', $repoid, $item);

    // Let the calling process know that we have finished successfully
    return true;
}
?>