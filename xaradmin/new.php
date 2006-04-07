<?php
/**
* Display GUI for creating a new publication
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
* Display GUI for creating a new publication
*/
function ebulletin_admin_new($args)
{
    // security check
    if (!xarSecurityCheck('AddeBulletin')) return;

    extract($args);

    // get HTTP vars
    if (!xarVarFetch('invalid', 'array', $invalid, array(), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('template', 'str:1:', $template, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('name', 'str:1:', $name, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('description', 'str:1:', $description, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('public', 'checkbox', $public, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('from', 'str:1:', $from, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('fromname', 'str:1:', $fromname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('replyto', 'str:1:', $replyto, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('replytoname', 'str:1:', $replytoname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('subject', 'str:1:', $subject, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('html', 'checkbox', $html, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('startday', 'int', $startday, 0, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('endday', 'int', $endday, 7, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('defaulttheme', 'str:1:', $defaulttheme, '', XARVAR_NOT_REQUIRED)) return;

    $themes = xarModAPIFunc('themes', 'admin', 'getlist', array('Class' => 0));

    // get hooks
    $hookoutput = xarModCallHooks('item', 'new', '', array('module' => 'ebulletin'));

    // set template vars
    $data = array();
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
    $data['authid']     = xarSecGenAuthKey();
    $data['invalid']    = $invalid;
    $data['themes']     = &$themes;
    $data['hookoutput'] = $hookoutput;

    return $data;
}

?>
