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
    if(!xarSecurityCheck('ReadHTML')) return;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Set table names
    $htmltypestable = $xartable['htmltypes'];

    // Select by id or type
    if ($id > 0) {
        // Get tag type by id
        $query = "SELECT $htmltypestable.xar_id,
                         $htmltypestable.xar_type
                  FROM  $htmltypestable
                  WHERE $htmltypestable.xar_id = ?";
        $result =& $dbconn->Execute($query,array($id));
    } else {
        // Get tag type by type
        $query = "SELECT $htmltypestable.xar_id,
                         $htmltypestable.xar_type
                  FROM  $htmltypestable
                  WHERE $htmltypestable.xar_type = ?";
        $result =& $dbconn->Execute($query,array($type));
    }
    if (!$result) return;
    list($id, $type) = $result->fields;
    $result->Close();
    $tagtype = array('id'        => $id,
                     'type'     => $type);
    return $tagtype;
}
?>