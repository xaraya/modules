<?php
/**
 * File: $Id$
 *
 * Xaraya HTML Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage HTML Module
 * @author John Cox
*/

/**
 * Get a specific tag
 *
 * @public
 * @author John Cox 
 * @author Richard Cave 
 * @param $args['cid'] id of tag to get
 * @returns array
 * @return link array, or false on failure
 * @raise BAD_PARAM
 */
function html_userapi_gettag($args)
{
    // Extract arguments
    extract($args);

    // Argument check
    if (!isset($cid) || !is_numeric($cid)) {
        $msg = xarML('Invalid Parameter #(1) for #(2) function #(3)() in module #(4)',
                     'cid', 'userapi', 'get', 'html');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // Security Check
	if(!xarSecurityCheck('ReadHTML')) return;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Set table names
    $htmltable = $xartable['html'];
    $htmltypestable = $xartable['htmltypes'];

    // Get tag
    $query = "SELECT $htmltable.xar_cid,
                     $htmltable.xar_tid,
                     $htmltypestable.xar_type,
                     $htmltable.xar_tag,
                     $htmltable.xar_allowed
              FROM $htmltable, $htmltypestable
              WHERE $htmltable.xar_cid = " . xarVarPrepForStore($cid) . "
              AND $htmltable.xar_tid = $htmltypestable.xar_id";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($cid, 
         $tid,
         $type, 
         $tag, 
         $allowed) = $result->fields;

    $result->Close();

    $tag = array('cid'      => $cid,
                 'tid'      => $tid,
                 'type'     => $type,
                 'tag'      => $tag,
                 'allowed'  => $allowed);

    return $tag;
}

?>
