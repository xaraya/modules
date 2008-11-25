<?php
/**
 * Xaraya HTML Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage HTML Module
 * @link http://xaraya.com/index.php/release/779.html
 * @author John Cox
 */

/**
 * Get all tags
 *
 * @public
 * @author John Cox
 * @author Richard Cave
 * @param $args['type'] (optional) type of the tag to get
 * @return array link array, or false on failure
 * @throws BAD_PARAM
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
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    // Set table names
    $htmltable = $xartable['html'];
    $htmltypestable = $xartable['htmltypes'];

    // Check if type was passed to function
    if (!empty($type)) {
        // Set WHERE clause to retrieve by tag type
        // Get ID of type
        $query = "SELECT xar_id
                  FROM $htmltypestable
                  WHERE xar_type = ?";
        $result =& $dbconn->Execute($query,array($type));
        if (!$result) return;

        // Get type
        list($typeid) = $result->fields;
        $result->Close();

        $where = " WHERE $htmltable.xar_tid = ? ";
        $bindvars = array((int) $typeid);
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

    if (isset($bindvars) && !empty($bindvars)) {
        $result =& $dbconn->Execute($query, $bindvars);
    } else {
        $result =& $dbconn->Execute($query);
    }

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
