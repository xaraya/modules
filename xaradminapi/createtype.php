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
 * Create a new HTML tag
 *
 * @public
 * @author Richard Cave
 * @param $args['tagtype'] type of tag to create
 * @return int html ID on success, false on failure
 * @throws BAD_PARAM
 */
function html_adminapi_createtype($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    $invalid = array();
    if (!isset($tagtype) || !is_string($tagtype)) {
        $invalid[] = 'tagtype';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid Parameter #(1) for #(2) function #(3)() in module #(4)',
                     join(', ',$invalid), 'adminapi', 'createtype', 'html');
        throw new BadParameterException(null,$msg);
    }

    // Security Check
    if(!xarSecurity::check('AddHTML')) return;

    // Trim input
    $tagtype = trim($tagtype);
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    $htmltypestable = $xartable['htmltypes'];
    // Make sure $type is lowercase
    $tagtype = strtolower($tagtype);
    // Check for existence of tag type
    $query = "SELECT id
              FROM $htmltypestable
              WHERE type = ?";
    $result =& $dbconn->Execute($query,array($tagtype));
    if (!$result) return false;

    if ($result->RecordCount() > 0) {
        $msg = xarML('Tag type `#(1)` already exists!', $tagtype );
        throw new DuplicateException();
    }

    // Get next ID in table
    $nextId = $dbconn->GenId($htmltypestable);
    // Add item
    $query = "INSERT INTO $htmltypestable (
              id,
              type)
            VALUES (
                    ?,
                    ?)";

    $result =& $dbconn->Execute($query,array($nextId, $tagtype));
    if (!$result) return;
    // Get the ID of the item that we inserted
    $id = $dbconn->PO_Insert_ID($htmltypestable, 'id');
    // Let any hooks know that we have created a new tag type
    $item['module'] = 'html';
    $item['itemid'] = $id;
    xarModHooks::call('item', 'createtype', $id, $item);
    // Return the id of the newly created tag to the calling process
    return $id;
}
?>