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
 * Get all tags
 *
 * @public
 * @author John Cox 
 * @author Richard Cave 
 * @param $args['type'] (optional) type of the tag to get
 * @returns array
 * @return link array, or false on failure
 * @raise BAD_PARAM
 */
function html_userapi_getalltags($args)
{
    // Extract arguments
    extract($args);

    // Argument check
    if (isset($type)) {
        $type = '';
    }

    // Security Check
	if(!xarSecurityCheck('ReadHTML')) return;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Set table names
    $htmltable = $xartable['html'];
    $htmltypestable = $xartable['htmltypes'];

    // Check if type was passed to function
    if (!empty($type)) {
        // Set WHERE clause to retrieve by tag type
        // Get ID of type 
        $query = "SELECT xar_id
                  FROM $htmltypestable
                  WHERE xar_type = '" . xarVarPrepForStore($type) . "'";

        $result =& $dbconn->Execute($query);

        // Check for errors
        if (!$result) {
            $msg = xarML('Invalid type #(1) for #(2) function #(3)() in module #(4)',
                         $type, 'adminapi', 'create', 'html');
            xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
            return;
        }

        // Get type
        list($typeid) = $result->fields;
        $result->Close();

        $where = " WHERE $htmltable.xar_tid = " . xarVarPrepForStore($typeid);
    } else {
        $where = " WHERE $htmltable.xar_tid = $htmltypestable.xar_id";
    }

    // Create query
    $query = "SELECT $htmltable.xar_cid,
                     $htmltable.xar_tid,
                     $htmltypestable.xar_type,
                     $htmltable.xar_tag,
                     $htmltable.xar_allowed
              FROM $htmltable, $htmltypestable";
    $query .= $where;
    $query .= " ORDER BY $htmltypestable.xar_type, $htmltable.xar_tag";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Set empty array
    $tags = array();

    // Put tags into an array
    for (; !$result->EOF; $result->MoveNext()) {
        list($cid,
             $tid,
             $type,
             $tag,
             $allowed) = $result->fields;

         $tags[] = array('cid'       => $cid,
                         'tid'       => $tid,
                         'type'      => $type,
                         'tag'       => $tag,
                         'allowed'   => $allowed);
    }

    // Close result set
    $result->Close();

    return $tags;
}

?>
