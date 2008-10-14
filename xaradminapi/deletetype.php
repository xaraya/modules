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
 * Delete a tag type
 *
 * @public
 * @author Richard Cave
 * @param $args['id'] ID of the tag type
 * @return bool true on success, false on failure
 * @throws BAD_PARAM, MISSING_DATA
 */
function html_adminapi_deletetype($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($id) || !is_numeric($id)) {
        $msg = xarML('Invalid Parameter #(1) for #(2) function #(3)() in module #(4)', 'id', 'adminapi', 'deletetype', 'html');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // The user API function is called
    $type = xarModAPIFunc('html',
                          'user',
                          'gettype',
                          array('id' => $id));

    if ($type == false) {
        $msg = xarML('No Such tag type present');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Security Check
    if(!xarSecurityCheck('DeleteHTML')) return;

    // Get datbase setup
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    // Set table name
    $htmltable = $xartable['html'];
    $htmltypestable = $xartable['htmltypes'];

    // Delete the tag type
    $query = "DELETE FROM $htmltypestable WHERE xar_id = ?";
    $result =& $dbconn->Execute($query,array($id));
    if (!$result) return;

    // Also delete the associated tags from the xar_html table
    $query = "DELETE FROM $htmltable WHERE xar_tid = ?";
    $result =& $dbconn->Execute($query,array($id));
    if (!$result) return;


    // If this is a tag type HTML, then
    // also delete the tags from the config vars
    if ($type['type'] == 'html') {
        $allowedhtml = array();
        // Set the config vars to an empty array
        xarConfigVars::set(null,'Site.Core.AllowableHTML', $allowedhtml);
    }
    // Let any hooks know that we have deleted a tag type
    xarModCallHooks('item', 'deletetype', $id, '');
    // Let the calling process know that we have finished successfully
    return true;
}
?>