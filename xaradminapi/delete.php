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
 * Delete an html tag
 *
 * @public
 * @author John Cox
 * @author Richard Cave
 * @param $args[] ID of the html
 * @return bool true on success, false on failure
 * @throws BAD_PARAM, MISSING_DATA
 */
function html_adminapi_delete($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($id) || !is_numeric($id)) {
        $msg = xarML('Invalid Parameter #(1) for #(2) function #(3)() in module #(4)', , 'adminapi', 'delete', 'html');
        throw new BadParameterException(null,$msg);
    }

    // The user API function is called
    $html = xarModAPIFunc('html',
                          'user',
                          'gettag',
                          array('id' => $id));

    if ($html == false) {
        $msg = xarML('No Such HTML tag Present', 'html');
        throw new BadParameterException(null,$msg);
    }

    // Security Check
    if(!xarSecurityCheck('ManageHTML')) return;
    // Get datbase setup
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $htmltable = $xartable['html'];

    // Delete the tag
    $query = "DELETE FROM $htmltable WHERE id = ?";
    $result =& $dbconn->Execute($query,array($id));
    if (!$result) return;

    // If this is an html tag, then
    // also delete the tag from the config vars
    $tagtype = xarModAPIFunc('html',
                             'user',
                             'gettype',
                             array('id' => $html['tid']));

    if ($tagtype['type'] == 'html') {
        $allowedhtml = array();
        // Get the current tags from config vars
        foreach (xarConfigVars::get(null,'Site.Core.AllowableHTML') as $key => $value) {
            // Remove the deleted html tag from the config vars
            if ($key != $html['tag']) {
                $allowedhtml[$key] = $value;
            }
        }
        // Set the config vars
        xarConfigVars::set(null,'Site.Core.AllowableHTML', $allowedhtml);
    }
    // Let any hooks know that we have deleted a html
    xarModCallHooks('item', 'delete', $id, '');
    // Let the calling process know that we have finished successfully
    return true;
}
?>