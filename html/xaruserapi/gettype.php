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
 * Get a specific tag type
 *
 * @public
 * @author Richard Cave 
 * @param $args['id'] id of tag type to get (optional)
 * @param $args['type'] tag type to get (optional)
 * @returns array
 * @return link array, or false on failure
 * @raise BAD_PARAM
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
                  WHERE $htmltypestable.xar_id = " . xarVarPrepForStore($id);
    } else {
        // Get tag type by type
        $query = "SELECT $htmltypestable.xar_id,
                         $htmltypestable.xar_type
                  FROM  $htmltypestable
                  WHERE $htmltypestable.xar_type = '" . xarVarPrepForStore($type) . "'";
    }


    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($id, $type) = $result->fields;
    $result->Close();

    $tagtype = array('id'        => $id,
                     'type'     => $type);

    return $tagtype;
}

?>
