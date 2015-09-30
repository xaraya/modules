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
 * Create a new tag
 *
 * @public
 * @author John Cox
 * @author Richard Cave
 * @param $args['tag'] tag to create
 * @param $args['type'] type of tag to create
 * @param $args['allowed'] state of tag on creation
 * @return int html ID on success, false on failure
 * @throws BAD_PARAM
 */
function html_adminapi_create($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    $invalid = array();
    if (!isset($tag) || !is_string($tag)) {
        $invalid[] = 'tag';
    }
    if (!isset($type) || !is_string($type)) {
        $invalid[] = 'type';
    }
    if (!isset($allowed)) {
        // Set allowed to default 0 if not present
        $allowed = 0;
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid Parameter #(1) for #(2) function #(3)() in module #(4)', join(', ',$invalid), 'adminapi', 'create', 'html');
        throw new BadParameterException(null,$msg);
    }

    // Security Check
    if(!xarSecurityCheck('AddHTML')) return;

    // Trim input
    $type = trim($type);

    // Make sure type is lowercase
    $type = strtolower($type);

    // Get ID of type
    $tagtype = xarModAPIFunc('html',
                             'user',
                             'gettype',
                             array('type' => $type));

    // Add item
    $dbconn = xarDB::getConn();
    $xartable =& xarDB::getTables();
    sys::import('xaraya.structures.query');
    $q = new Query('SELECT', $xartable['html']);
    $q->eq('tid', $tagtype['id']);
    $q->eq('tag', $tag);
    $q->run();
    $result = $q->row();
    if (!empty($q->row())) {
        // This tag already exists, just update
        $q = new Query('UPDATE', $xartable['html']);
        $q->addfield('tid', $tagtype['id']);
        $q->addfield('tag', $tag);
        $q->addfield('allowed', $allowed);
        $q->eq('id', $result['id']);
        if (!$q->run()) return;
        return $result['id'];
    } else {
        // New tag, create it
        $q = new Query('INSERT', $xartable['html']);
        $q->addfield('tid', $tagtype['id']);
        $q->addfield('tag', $tag);
        $q->addfield('allowed', $allowed);
        if (!$q->run()) return;
    }

    // Get the ID of the item that we inserted
    $itemid = $dbconn->PO_Insert_ID($xartable['html'], 'id');

    // If this is an html tag, then
    // also add the tag to config vars
    if ($tagtype['type'] == 'html') {
        // Get the current html tags from config vars
        $allowedhtml = xarConfigVars::get(null,'Site.Core.AllowableHTML');
        // Add the new html tag
        $allowedhtml[$tag] = $allowed;
        error_log($tag . " " . $allowed);
        // Set the config vars
        xarConfigVars::set(null,'Site.Core.AllowableHTML', $allowedhtml);
    }
    // Let any hooks know that we have created a new tag
    xarModCallHooks('item', 'create', $itemid, 'id');
    // Return the id of the newly created tag to the calling process
    return $itemid;
}
?>