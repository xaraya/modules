<?php
/**
* GUI for modifying a publication
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
 * modify an item
 * This is a standard function that is called whenever an administrator
 * wishes to modify a current module item
 *
 * @param  $ 'id' the id of the item to be modified
 */
function ebulletin_admin_modify($args)
{
    extract($args);

    // get HTTP vars
    if (!xarVarFetch('id', 'id', $id)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'array', $invalid, $invalid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('template', 'str:1:', $template, $template, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name', 'str:1:', $name, $name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('description', 'str:1:', $description, $description, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('from', 'str:1:', $from, $from, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('fromname', 'str:1:', $fromname, $fromname, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('replyto', 'str:1:', $replyto, $replyto, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('replytoname', 'str:1:', $replytoname, $replytoname, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('subject', 'str:1:', $subject, $subject, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startday', 'int:1', $startday, $startday, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('endday', 'int:1', $endday, $endday, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('defaulttheme', 'str:1', $defaulttheme, $defaulttheme, XARVAR_NOT_REQUIRED)) return;

    // make sure we have an ID
    if (!empty($objectid)) $id = $objectid;

    // retrieve this publication
    $pub = xarModAPIFunc('ebulletin', 'user', 'get', array('id' => $id));
    if (!isset($pub) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // security check
    if (!xarSecurityCheck('EditeBulletin', 1, 'Publication', "$pub[name]:$id")) return;

    // get publication vars
    if (!isset($template))     $template = $pub['template'];
    if (!isset($name))         $name = $pub['name'];
    if (!isset($description))  $description = $pub['description'];
    if (!isset($public))       $public = $pub['public'];
    if (!isset($from))         $from = $pub['from'];
    if (!isset($fromname))     $fromname = $pub['fromname'];
    if (!isset($replyto))      $replyto = $pub['replyto'];
    if (!isset($replytoname))  $replytoname = $pub['replytoname'];
    if (!isset($subject))      $subject = $pub['subject'];
    if (!isset($html))         $html = $pub['html'];
    if (!isset($startday))     $startday = $pub['startday'];
    if (!isset($endday))       $endday = $pub['endday'];
    if (!isset($defaulttheme)) $defaulttheme = $pub['theme'];

    // get other vars
    $authid = xarSecGenAuthKey();
    $themes = xarModAPIFunc('themes', 'admin', 'getlist', array('Class' => 0));

    // get hooks
    $item = $pub;
    $item['module'] = 'ebulletin';
    $hookoutput = xarModCallHooks('item', 'modify', $id, $item);

    // set template vars
    $data = array();
    $data['id']           = $id;
    $data['template']     = $template;
    $data['name']         = $name;
    $data['description']  = $description;
    $data['public']       = $public;
    $data['from']         = $from;
    $data['fromname']     = $fromname;
    $data['replyto']      = $replyto;
    $data['replytoname']  = $replytoname;
    $data['subject']      = $subject;
    $data['html']         = $html;
    $data['startday']     = $startday;
    $data['endday']       = $endday;
    $data['defaulttheme'] = $defaulttheme;

    // set other vars
    $data['authid']    = $authid;
    $data['invalid']   = $invalid;
    $data['themes']    = &$themes;
    $data['hookitems'] = &$hookoutput;

    return $data;
}

?>
