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
 * Edit a tag type
 *
 * @author Richard Cave
 * @param $args['id'] ID of the tag type to change
 * @param $args['tagtype'] the tag type
 * @return bool true on success, false on failure
 * @throws BAD_PARAM, MISSING_DATA
 */
function html_adminapi_edittype($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = [];
    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'id';
    }
    if (!isset($tagtype) || !is_string($tagtype)) {
        $invalid[] = 'tag type';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid Parameter #(1) for #(2) function #(3)() in module #(4)', join(', ', $invalid), 'adminapi', 'edittype', 'html');
        throw new BadParameterException(null, $msg);
    }

    // The user API function is called
    $type = xarMod::apiFunc(
        'html',
        'user',
        'gettype',
        ['id' => $id]
    );

    if ($type == false) {
        $msg = xarML('No such tag  type present.');
        throw new BadParameterException(null, $msg);
    }

    // Security Check
    if (!xarSecurity::check('EditHTML')) {
        return;
    }

    // Get datbase setup
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $htmltypestable = $xartable['htmltypes'];

    // Make sure tag type is lowercase
    $tagtype = strtolower($tagtype);

    // Update the tag type
    $query = "UPDATE $htmltypestable
              SET type = ?
              WHERE id = ?";
    $result =& $dbconn->Execute($query, [$tagtype, $id]);
    if (!$result) {
        return;
    }
    // Let any hooks know that we have deleted a html
    xarModHooks::call('item', 'edittype', $id, '');
    // Let the calling process know that we have finished successfully
    return true;
}
