<?php
/**
* Create a new publication
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
 * Create a new publication
 */
function ebulletin_admin_create($args)
{
    // security check
#    if (!xarSecConfirmAuthKey()) return;

    extract($args);

    // get HTTP vars
    if (!xarVarFetch('id', 'id', $id, $id, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('invalid', 'str:1:', $invalid, '', XARVAR_NOT_REQUIRED)) return;
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
    if (!xarVarFetch('startday', 'int', $startday, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('endday', 'int', $endday, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('defaulttheme', 'str:1:', $defaulttheme, '', XARVAR_NOT_REQUIRED)) return;

    // Argument check
    $invalid = array();
    $email_regexp = '/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i';
    if (empty($template)) {
        $invalid['template'] = 1;
        $template = '';
    }
    if (empty($name) || !is_string($name)) {
        $invalid['name'] = 1;
        $name = '';
    }
    if (empty($from) || !is_string($from) || !preg_match($email_regexp, $from)) {
        $invalid['from'] = 1;
        $from = '';
    }
    if (!empty($replyto) && !preg_match($email_regexp, $replyto)) {
        $invalid['replyto'] = 1;
        $replyto = '';
    }
    if (empty($subject) || !is_string($subject)) {
        $invalid['subject'] = 1;
        $subject = '';
    }
    if (!isset($startday) || !is_numeric($startday)) {
        $invalid['startday'] = 1;
        $startday = 0;
    }
    if (!isset($endday) || !is_numeric($endday)) {
        $invalid['endday'] = 1;
        $endday = 7;
    }
    if ($endday < $startday) {
        $invalid['range'] = 1;
    }

    // assemble array of data
    $data = array();
    $data['template'] = $template;
    $data['name'] = $name;
    $data['description'] = $description;
    $data['public'] = $public;
    $data['from'] = $from;
    $data['fromname'] = $fromname;
    $data['replyto'] = $replyto;
    $data['replytoname'] = $replytoname;
    $data['subject'] = $subject;
    $data['html'] = $html;
    $data['startday'] = $startday;
    $data['endday'] = $endday;
    $data['defaulttheme'] = $defaulttheme;

    // check if we have any errors
    if (count($invalid) > 0) {
        $data['invalid'] = $invalid;
        return xarModFunc('ebulletin', 'admin', 'new', $data);
    }

    // let API function do the creating
    $id = xarModAPIFunc('ebulletin', 'admin', 'create', $data);
    if (xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // set status message and redirect to publications view page
    xarSessionSetVar('statusmsg', xarML('Publication successfully created!'));
    xarResponseRedirect(xarModURL('ebulletin', 'admin', 'modify', array('id' => $id)));

    // success
    return true;
}

?>
