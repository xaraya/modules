<?php
/**
 * File: $Id$
 * 
 * Create a new forum
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
 * create a new forum
 * @param $args['fname'] name of forum
 * @param $args['fdesc'] description of forum
 * @returns int
 * @return autolink ID on success, false on failure
 */
function xarbb_adminapi_create($args)
{

    // Get arguments from argument array
    extract($args);

    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return

    $invalid = array();
    if (!isset($fname) || !is_string($fname)) {
        $invalid[] = 'fname';
    } 
    if (!isset($fposter) || !is_numeric($fposter)) {
        $invalid[] = 'fposter';
    } 
    if (!isset($fdesc) || !is_string($fdesc)) {
        $invalid[] = 'fdesc';
    } 
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)', join(', ', $invalid), 'admin', 'create', 'xarbb');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    } 

    //Let's set defaults so we can pass in values if necessary
    if ((!isset($ftopics)) || (empty($ftopics))){
        $ftopics=0;
    }
    if ((!isset($fposts))|| (empty($fposts))){
        $fposts=0;
    }


    // Security Check
    if(!xarSecurityCheck('AddxarBB',1,'Forum')) return;

    // Default categories is none
    if (empty($cids) || !is_array($cids) ||
        // catch common mistake of using array('') instead of array()
        (count($cids) > 0 && empty($cids[0])) ) {
        $cids = array();
        // for security check below
        $args['cids'] = $cids;
    } else {
        $args['cids'] = array_values(preg_grep('/\d+/',$cids));
    }

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $xbbforumstable = $xartable['xbbforums'];

    // Get next ID in table
    $nextId = $dbconn->GenId($xbbforumstable);

    // Get Time
    if ((!isset($fpostid)) || (empty($fpostid))){
        $fpostid= time();
    }
    // Add item
    $query = "INSERT INTO $xbbforumstable (
              xar_fid,
              xar_fname,
              xar_fdesc,
              xar_ftopics,
              xar_fposts,
              xar_fposter,
              xar_fpostid,
              xar_fstatus   )
            VALUES (
              $nextId,
              '" . xarVarPrepForStore($fname) . "',
              '" . xarVarPrepForStore($fdesc) . "',
              '" . xarVarPrepForStore($ftopics) . "',
              '" . xarVarPrepForStore($fposts) . "',
              '" . xarVarPrepForStore($fposter) . "',
              '$fpostid',
              '" . xarVarPrepForStore($fstatus) . "')";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Get the ID of the item that we inserted
    $fid = $dbconn->PO_Insert_ID($xbbforumstable, 'xar_fid');

    if (empty($cids)) {
        $cids[] = xarModGetVar('xarbb', 'mastercids.1');
    }

    // Let any hooks know that we have created a new forum
    $args['module'] = 'xarbb';
    $args['itemtype'] = 1; // forum
    $args['itemid'] = $fid;
    $args['cids'] = $cids;
    xarModCallHooks('item', 'create', $fid, $args);

    // Return the id of the newly created link to the calling process
    return $fid;
}

?>
