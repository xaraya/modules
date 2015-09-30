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
 * Get a specific tag
 *
 * @public
 * @author John Cox
 * @author Richard Cave
 * @param $args['id'] id of tag to get
 * @return array link array, or false on failure
 * @throws BAD_PARAM
 */
function html_userapi_gettag($args)
{
    // Extract arguments
    extract($args);

    // Argument check
    if (!isset($id) || !is_numeric($id)) {
        $msg = xarML('Invalid Parameter #(1) for #(2) function #(3)() in module #(4)', 'id', 'userapi', 'get', 'html');
        throw new BadParameterException(null,$msg);
    }

    // Security Check
    if(!xarSecurityCheck('ReadHTML')) return;

    // Get database setup
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $htmltable = $xartable['html'];
    $htmltypestable = $xartable['htmltypes'];

    // Get tag
    $query = "SELECT $htmltable.id,
                     $htmltable.tid,
                     $htmltypestable.type,
                     $htmltable.tag,
                     $htmltable.allowed
              FROM $htmltable, $htmltypestable
              WHERE $htmltable.id = ?
              AND $htmltable.tid = $htmltypestable.id";

        $result =& $dbconn->Execute($query,array($id));
    if (!$result) return;
    list($id,
         $tid,
         $type,
         $tag,
         $allowed) = $result->fields;
    $result->Close();
    $tag = array('id'       => $id,
                 'tid'      => $tid,
                 'type'     => $type,
                 'tag'      => $tag,
                 'allowed'  => $allowed);
    return $tag;
}
?>