<?php
/**
 * HTML Module
 *
 * @package modules
 * @subpackage html module
 * @category Third Party Xaraya Module
 * @version 1.5.0
 * @copyright see the html/credits.html file in this release
 * @link http://www.xaraya.com/index.php/release/779.html
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
    if (!xarSecurity::check('ReadHTML')) {
        return;
    }

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();

    // Set table names
    $htmltable = $xartable['html'];
    $htmltypestable = $xartable['htmltypes'];

    // Check if type was passed to function
    if (!empty($type)) {
        // Set WHERE clause to retrieve by tag type
        // Get ID of type
        $query = "SELECT id
                  FROM $htmltypestable
                  WHERE type = ?";
        $result =& $dbconn->Execute($query, array($type));
        if (!$result) {
            return;
        }

        // Get type
        list($typeid) = $result->fields;
        $result->Close();

        $where = " WHERE $htmltable.tid = ? ";
        $bindvars = array((int) $typeid);
    } else {
        $where = " WHERE $htmltable.tid = $htmltypestable.id";
    }

    // Create query
    $query = "SELECT $htmltable.id,
                     $htmltable.tid,
                     $htmltypestable.type,
                     $htmltable.tag,
                     $htmltable.allowed
              FROM $htmltable, $htmltypestable";
    $query .= $where;
    $query .= " ORDER BY $htmltypestable.type, $htmltable.tag";

    if (isset($bindvars) && !empty($bindvars)) {
        $result =& $dbconn->Execute($query, $bindvars);
    } else {
        $result =& $dbconn->Execute($query);
    }

    if (!$result) {
        return;
    }

    // Set empty array
    $tags = array();

    // Put tags into an array
    for (; !$result->EOF; $result->MoveNext()) {
        list($id,
             $tid,
             $type,
             $tag,
             $allowed) = $result->fields;

        $tags[] = array('id'        => $id,
                         'tid'       => $tid,
                         'type'      => $type,
                         'tag'       => $tag,
                         'allowed'   => $allowed);
    }

    // Close result set
    $result->Close();
    return $tags;
}
