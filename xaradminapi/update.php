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
 * Update an html tag
 *
 * @public
 * @author John Cox
 * @author Richard Cave
 * @param $args['id'] the ID of the html tag
 * @param $args['allowed'] the flag for the html tag
 * @throws BAD_PARAM
 */
function html_adminapi_update($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'id';
    }
    if (!isset($allowed) || !is_numeric($allowed)) {
        $invalid[] = 'allowed';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid Parameter #(1) for #(2) function #(3)() in module #(4)', join(', ',$invalid), 'adminapi', 'update', 'html');
        throw new BadParameterException(null,$msg);
    }

    // Security Check
    if(!xarSecurity::check('EditHTML')) return;
    // Get datbase setup
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $htmltable = $xartable['html'];

    // Update the html tag
    $query = "UPDATE $htmltable
              SET   allowed = ?
              WHERE id = ?";
    $result =& $dbconn->Execute($query,array($allowed, $id));
    if (!$result) return;
    // Let any hooks know that we have deleted a html tag
    xarModHooks::call('item', 'update', $id, '');
    // Let the calling process know that we have finished successfully
    return true;
}
?>