<?php
/**
 * File: $Id$
 * 
 * Update a forum
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
/**
 * update a forum
 * @param $args['fid'] the ID of the link
 * @param $args['fname'] the new keyword of the link
 * @param $args['fdesc'] the new title of the link
 */
function xarbb_adminapi_update($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($fid)) ||
        (!isset($fname)) ||
        (!isset($fdesc))) {
        $msg = xarML('Invalid Parameter Count',
                    '', 'admin', 'update', 'xarbb');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // The user API function is called.
    $data = xarModAPIFunc('xarbb',
                          'user',
                          'getforum',
                          array('fid' => $fid));
    if (empty($data)) return;
    // Security Check
    if(!xarSecurityCheck('EditxarBB',1,'Forum',$data['catid'].':'.$data['fid'])) return;
    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $xbbforumstable = $xartable['xbbforums'];
    // Update the forum
    $query = "UPDATE $xbbforumstable
            SET xar_fname   = '" . xarVarPrepForStore($fname) . "',
                xar_fdesc   = '" . xarVarPrepForStore($fdesc) . "',
                xar_fstatus = '" . xarVarPrepForStore($fstatus) . "'
            WHERE xar_fid = " . xarVarPrepForStore($fid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    // Let any hooks know that we have updated a forum
    $args['module'] = 'xarbb';
    $args['itemtype'] = 1; // forum
    xarModCallHooks('item', 'update', $fid, $args);
    // Let the calling process know that we have finished successfully
    return true;
}
?>