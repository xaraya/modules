<?php
/**
* Update a publication
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
 * update an ebulletin item
 *
 * @author the eBulletin module development team
 * @param  $args['pid'] the ID of the item
 * @param  $args['name'] the name of the item to be created
 * @param  $args['to'] the to of the item to be created
 * @param  $args['from'] the from of the item to be created
 * @param  $args['replyto'] the replyto of the item to be created
 * @param  $args['subject'] the subject of the item to be created
 * @param  $args['body'] the body of the item to be created
 * @param  $args['range'] the range of the item to be created
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function ebulletin_adminapi_update($args)
{
    extract($args);

    // validate vars
    $invalid = array();
    $email_regexp = '/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i';
    if (empty($id) || !is_numeric($id)) {
        $invalid[] = 'id';
    }
    if (empty($template) || !is_string($template)) {
        $invalid[] = 'template';
    }
    if (empty($name) || !is_string($name)) {
        $invalid[] = 'name';
    }
    if (empty($from) || !is_string($from) || !preg_match($email_regexp, $from)) {
        $invalid[] = 'from';
    }
    if (!empty($replyto) && !preg_match($email_regexp, $replyto)) {
        $invalid[] = 'replyto';
    }
    if (empty($subject) || !is_string($subject)) {
        $invalid[] = 'subject';
    }
    if (!isset($startday) || !is_numeric($startday)) {
        $invalid[] = 'startday';
    }
    if (!isset($endday) || !is_numeric($endday)) {
        $invalid[] = 'endday';
    }
    if (isset($defaulttheme) && !is_string($defaulttheme)) {
        $invalid[] = 'defaulttheme';
    }

    // throw error if bad data
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'adminapi', 'update', 'eBulletin');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    // retrieve this publication
    $pub = xarModAPIFunc('ebulletin', 'user', 'get', array('id' => $id));
    if (!isset($pub) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security checks
    if (!xarSecurityCheck('AddeBulletin', 1, 'Publication', "$pub[name]:$id")) return;
    if (!xarSecurityCheck('AddeBulletin', 1, 'Publication', "$name:$id")) return;

    // sanitize db input
    $public = empty($public) ? '0' : '1';
    $html = empty($html) ? '0' : '1';
    if (empty($theme)) $theme = '';

    // prepare for database
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();
    $pubtable = $xartable['ebulletin'];

    // generate query
    $query = "
        UPDATE $pubtable
        SET xar_template    = ?,
            xar_name        = ?,
            xar_desc        = ?,
            xar_public      = ?,
            xar_from        = ?,
            xar_fromname    = ?,
            xar_replyto     = ?,
            xar_replytoname = ?,
            xar_subject     = ?,
            xar_html        = ?,
            xar_startday    = ?,
            xar_endday      = ?,
            xar_theme       = ?
        WHERE xar_id = ?";
    $bindvars = array(
        $template, $name, $description, $public, $from, $fromname, $replyto, $replytoname,
        $subject, $html, $startday, $endday, $defaulttheme, $id
    );
    $result = $dbconn->Execute($query, $bindvars);

    // if query failed, return
    if (!$result) return;

    // call update hooks
    $item = $pub;
    $item['module'] = 'ebulletin';
    $item['itemid'] = $id;
    xarModCallHooks('item', 'update', $id, $item);

    // success
    return true;
}

?>
