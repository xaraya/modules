<?php
/**
 * File: $Id$
 * 
 * Delete a forum
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
 * delete a forum
 * @param $args['fid'] ID of the forum
 * @returns bool
 * @return true on success, false on failure
 */
function xarbb_adminapi_delete($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($fid)) {
        $msg = xarML('Invalid Parameter Count',
                    '', 'admin', 'delete', 'xarbb');
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
    if(!xarSecurityCheck('DeletexarBB',1,'Forum',$data['catid'].':'.$data['fid'])) return;

	// topics and comments are deleted in delete gui func so do not care
    // shouldn't this call be here?
    // <jojodee> - probably need to delete topics and comments here too 
    // else they may be left hanging in db

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $xbbforumstable = $xartable['xbbforums'];

    // Delete the item
    $query = "DELETE FROM $xbbforumstable
              WHERE xar_fid = " . xarVarPrepForStore($fid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let any hooks know that we have deleted a forum
    $args['module'] = 'xarbb';
    $args['itemtype'] = 1; // forum
    xarModCallHooks('item', 'delete', $fid, $args);

    // Let the calling process know that we have finished successfully
    return true;
}

?>
