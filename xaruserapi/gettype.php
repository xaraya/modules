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
 * Get a specific tag type
 *
 * @public
 * @author Richard Cave
 * @param $args['id'] id of tag type to get (optional)
 * @param $args['type'] tag type to get (optional)
 * @return array link array, or false on failure
 * @throws BAD_PARAM
 */
function html_userapi_gettype($args)
{
    // Extract arguments
    extract($args);

    // Argument check
    if (!isset($id) || !is_numeric($id)) {
        $id = 0;
    }
    if (!isset($type) || !is_string($type)) {
        $type = '';
    }

    // Security Check
    if (!xarSecurityCheck('ReadHTML')) {
        return;
    }

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();

    // Set table names
    $htmltypestable = $xartable['htmltypes'];

    // Select by id or type
    if ($id > 0) {
        // Get tag type by id
        $query = "SELECT $htmltypestable.id,
                         $htmltypestable.type
                  FROM  $htmltypestable
                  WHERE $htmltypestable.id = ?";
        $result =& $dbconn->Execute($query, array($id));
    } else {
        // Get tag type by type
        $query = "SELECT $htmltypestable.id,
                         $htmltypestable.type
                  FROM  $htmltypestable
                  WHERE $htmltypestable.type = ?";
        $result =& $dbconn->Execute($query, array($type));
    }
    if (!$result) {
        return;
    }
    list($id, $type) = $result->fields;
    $result->Close();
    $tagtype = array('id'        => $id,
                     'type'     => $type);
    return $tagtype;
}
