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
 * Edit a tag
 *
 * @author Richard Cave
 * @param $args['id'] ID of the tag
 * @param $args['tag'] the name of the tag
 * @return bool true on success, false on failure
 * @throws BAD_PARAM, MISSING_DATA
 */
function html_adminapi_edit($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'id';
    }
    if (!isset($tag) || !is_string($tag)) {
        $invalid[] = 'tag';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid Parameter #(1) for #(2) function #(3)() in module #(4)', join(', ',$invalid), 'adminapi', 'edit', 'html');
        $msg = xarML('Invalid Parameter Count');
        throw new BadParameterException(null,$msg);
    }

    // The user API function is called
    $html = xarMod::apiFunc('html',
                          'user',
                          'gettag',
                          array('id' => $id));

    if ($html == false) {
        $msg = xarML('No such tag present');
        throw new BadParameterException(null,$msg);
    }

    // Security Check
    if(!xarSecurity::check('EditHTML')) return;

    // Get datbase setup
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $htmltable = $xartable['html'];

    // Make sure $tag is lowercase
    $tag = strtolower($tag);

    // Update the html
    $query = "UPDATE $htmltable
              SET tag = ?
              WHERE id = ?";
    $result =& $dbconn->Execute($query,array($tag, $id));
    if (!$result) return;

    // If this is an html tag, then
    // also edit the item in the config vars
    $tagtype = xarMod::apiFunc('html',
                             'user',
                             'gettype',
                             array('id' => $html['tid']));

    if ($tagtype['type'] == 'html') {
        $allowedhtml = array();
        // Get the current html tags from config vars
        foreach (xarConfigVars::get(null,'Site.Core.AllowableHTML') as $key => $value) {
            // Update html tag from the config vars
            if ($key != $html['tag']) {
            $allowedhtml[$key] = $value;
            }
        }
        // Add the new html tag
        $allowedhtml[$tag] = $html['allowed'];
        // Set the config vars
        xarConfigVars::set(null,'Site.Core.AllowableHTML', $allowedhtml);
    }
    // Let any hooks know that we have deleted a html
    xarModHooks::call('item', 'edit', $id, '');
    // Let the calling process know that we have finished successfully
    return true;
}
?>